import * as Tone from 'tone';
import type {
  AutomationLane,
  Clip,
  InstrumentType,
  MidiClip,
  MixerChannel,
  Track,
  TrackPluginInstance,
  TransportState,
} from '@musicdistro/types';

interface LoadedTrack {
  input: Tone.Gain;
  channel: Tone.Channel;
  instrument?: Tone.PolySynth | Tone.Sampler | Tone.MembraneSynth;
  parts: Tone.Part[];
  clips: Clip[];
  automation: AutomationLane[];
}

const DEFAULT_VOLUME = -6;

export class AudioEngine {
  private initialized = false;
  private tracks = new Map<string, LoadedTrack>();
  private masterChannel: Tone.Gain;
  private reverbBus: Tone.Reverb;
  private delayBus: Tone.FeedbackDelay;
  private analyser: Tone.Analyser;
  private metronome: Tone.MembraneSynth;
  private metronomeGain: Tone.Gain;
  private metronomeLoop: Tone.Loop | null = null;
  private metronomeEnabled = false;

  constructor() {
    this.masterChannel = new Tone.Gain(1).toDestination();
    this.reverbBus = new Tone.Reverb({ decay: 4, wet: 0.15 }).connect(this.masterChannel);
    this.delayBus = new Tone.FeedbackDelay({ delayTime: '8n', feedback: 0.2, wet: 0.1 }).connect(this.masterChannel);
    this.analyser = new Tone.Analyser('fft', 64);
    this.masterChannel.connect(this.analyser);
    this.metronomeGain = new Tone.Gain(0.6).connect(this.masterChannel);
    this.metronome = new Tone.MembraneSynth({ volume: -6 }).connect(this.metronomeGain);
    Tone.getContext().lookAhead = 0.05;
  }

  async init() {
    if (this.initialized) return;
    await Tone.start();
    await Tone.loaded();
    this.initialized = true;
  }

  getAnalyser() {
    return this.analyser;
  }

  async setTransport(state: Partial<TransportState>) {
    await this.init();

    if (typeof state.bpm === 'number') {
      Tone.Transport.bpm.rampTo(state.bpm, 0.1);
    }

    if (state.timeSignature) {
      Tone.Transport.timeSignature = state.timeSignature[0] / state.timeSignature[1];
    }

    if (typeof state.position === 'number') {
      Tone.Transport.seconds = state.position;
    }

    if (state.loop) {
      Tone.Transport.loop = state.loop.enabled;
      Tone.Transport.loopStart = state.loop.start;
      Tone.Transport.loopEnd = state.loop.end;
    }

    if (typeof state.metronomeEnabled === 'boolean') {
      this.toggleMetronome(state.metronomeEnabled);
    }
  }

  async play() {
    await this.init();
    await Tone.Transport.start();
    if (this.metronomeEnabled) {
      this.ensureMetronomeLoop();
      this.metronomeLoop?.start(0);
    }
  }

  async stop() {
    await Tone.Transport.stop();
    this.metronomeLoop?.stop(0);
  }

  async loadTrack(track: Track, clips: Clip[], plugins: TrackPluginInstance[]): Promise<void> {
    await this.init();

    const existing = this.tracks.get(track.id);
    if (existing) {
      existing.parts.forEach((part) => part.dispose());
      existing.instrument?.dispose();
      existing.channel.dispose();
      existing.input.dispose();
      this.tracks.delete(track.id);
    }

    const input = new Tone.Gain(1);
    const channel = new Tone.Channel({ volume: track.volume ?? DEFAULT_VOLUME, pan: track.pan ?? 0 });

    const pluginNodes = this.instantiatePlugins(plugins);
    Tone.connectSeries(input, ...pluginNodes, channel, this.masterChannel);

    channel.send('global-reverb', 0).connect(this.reverbBus);
    channel.send('global-delay', -12).connect(this.delayBus);

    const parts: Tone.Part[] = [];
    let instrument: Tone.PolySynth | Tone.Sampler | Tone.MembraneSynth | undefined;

    clips
      .filter((clip) => clip.trackId === track.id && !clip.muted)
      .forEach((clip) => {
        if (clip.kind === 'audio') {
          const player = new Tone.Player({ url: clip.fileId, loop: clip.isLoop ?? false });
          player.playbackRate = this.calculatePlaybackRate(clip);
          player.volume.value = clip.gain ?? 0;
          player.fadeIn = clip.fadeIn ?? 0.01;
          player.fadeOut = clip.fadeOut ?? 0.01;
          player.sync().start(clip.start, clip.offset);
          player.connect(input);
        } else if (clip.kind === 'midi') {
          if (!instrument) {
            instrument = this.createInstrument(track.instrument ?? 'analog');
            instrument.connect(input);
          }
          const part = this.createMidiPart(clip as MidiClip, instrument);
          part.start(clip.start);
          parts.push(part);
        }
      });

    this.tracks.set(track.id, {
      input,
      channel,
      instrument,
      parts,
      clips: clips.filter((clip) => clip.trackId === track.id),
      automation: track.automation,
    });
  }

  updateChannelMix(trackId: string, volume: number, pan: number) {
    const track = this.tracks.get(trackId);
    if (!track) return;
    track.channel.volume.rampTo(volume, 0.1);
    track.channel.pan.rampTo(pan, 0.1);
  }

  setMasterVolume(value: number) {
    this.masterChannel.gain.rampTo(value, 0.1);
  }

  updateTrackAutomation(trackId: string, automation: AutomationLane[]) {
    const loaded = this.tracks.get(trackId);
    if (!loaded) return;
    loaded.automation = automation;
  }

  getMixerSnapshot(): MixerChannel[] {
    return Array.from(this.tracks.entries()).map(([trackId, { channel }]) => ({
      id: `mix-${trackId}`,
      trackId,
      volume: channel.volume.value,
      pan: channel.pan.value,
      meters: {
        peak: channel.volume.value,
        rms: channel.volume.value - 6,
      },
      sends: [
        {
          busId: 'reverb',
          amount: 0.15,
        },
        {
          busId: 'delay',
          amount: 0.1,
        },
      ],
    }));
  }

  async dispose() {
    this.tracks.forEach((loaded) => {
      loaded.parts.forEach((part) => part.dispose());
      loaded.instrument?.dispose();
      loaded.channel.dispose();
      loaded.input.dispose();
    });
    this.tracks.clear();
    this.metronomeLoop?.dispose();
    this.metronomeLoop = null;
    this.metronome.dispose();
    this.metronomeGain.dispose();
    await Tone.Transport.stop();
    await Tone.getContext().close();
    this.initialized = false;
  }

  private instantiatePlugins(plugins: TrackPluginInstance[]): Tone.ToneAudioNode[] {
    const nodes: Tone.ToneAudioNode[] = [];

    plugins
      .filter((plugin) => !plugin.bypassed)
      .forEach((plugin) => {
        switch (plugin.type) {
          case 'eq8': {
            const eq = new Tone.EQ3({ low: plugin.params.low ?? 0, mid: plugin.params.mid ?? 0, high: plugin.params.high ?? 0 });
            nodes.push(eq);
            break;
          }
          case 'reverb': {
            const reverb = new Tone.Reverb({ decay: plugin.params.decay ?? 2, wet: plugin.params.wet ?? 0.2 });
            nodes.push(reverb);
            break;
          }
          case 'delay': {
            const delay = new Tone.FeedbackDelay({ delayTime: plugin.params.time ?? 0.3, feedback: plugin.params.feedback ?? 0.2, wet: plugin.params.wet ?? 0.2 });
            nodes.push(delay);
            break;
          }
          case 'compressor': {
            const compressor = new Tone.Compressor({
              threshold: plugin.params.threshold ?? -24,
              ratio: plugin.params.ratio ?? 4,
              attack: plugin.params.attack ?? 0.01,
              release: plugin.params.release ?? 0.25,
            });
            nodes.push(compressor);
            break;
          }
          case 'distortion': {
            const distortion = new Tone.Distortion(plugin.params.amount ?? 0.2);
            distortion.wet.value = plugin.params.wet ?? 0.3;
            nodes.push(distortion);
            break;
          }
          default:
            break;
        }
      });

    return nodes;
  }

  private calculatePlaybackRate(clip: Clip): number {
    if (clip.kind !== 'audio') return 1;
    const transpose = clip.transpose ?? 0;
    return Math.pow(2, transpose / 12);
  }

  private createInstrument(type: InstrumentType) {
    if (type === 'piano') {
      const synth = new Tone.Sampler({
        urls: {
          A1: 'A1.mp3',
          A2: 'A2.mp3',
        },
        baseUrl: 'https://tonejs.github.io/audio/casio/',
      });
      return synth;
    }

    if (type === 'drumkit') {
      return new Tone.MembraneSynth({ pitchDecay: 0.05, octaves: 6, oscillator: { type: 'sine' } });
    }

    return new Tone.PolySynth(Tone.Synth, {
      volume: -8,
      options: {
        oscillator: { type: 'sawtooth' },
        envelope: { attack: 0.02, decay: 0.2, sustain: 0.6, release: 1.2 },
      },
    });
  }

  private createMidiPart(clip: MidiClip, instrument: Tone.PolySynth | Tone.Sampler | Tone.MembraneSynth) {
    const events = clip.notes.map((note) => ({
      time: note.start,
      duration: note.duration,
      pitch: note.pitch,
      velocity: note.velocity,
    }));

    const part = new Tone.Part((time, value: typeof events[number]) => {
      const velocity = Math.max(0, Math.min(1, value.velocity / 127));
      const frequency = Tone.Frequency(value.pitch, 'midi');
      if ('triggerAttackRelease' in instrument) {
        instrument.triggerAttackRelease(frequency, value.duration, time, velocity);
      } else if ('triggerAttack' in instrument) {
        instrument.triggerAttack(frequency, time, velocity);
        instrument.triggerRelease(time + value.duration);
      }
    }, events);

    part.loop = clip.isLoop ?? false;
    part.loopEnd = clip.end - clip.start;
    part.probability = 1;

    return part;
  }

  private ensureMetronomeLoop() {
    if (this.metronomeLoop) return;
    this.metronomeLoop = new Tone.Loop((time) => {
      const subdivision = Tone.Transport.position.split(':')[1];
      const pitch = subdivision === '0' ? 'G5' : 'D5';
      this.metronome.triggerAttackRelease(pitch, '16n', time, subdivision === '0' ? 0.9 : 0.6);
    }, '4n');
  }

  private toggleMetronome(enabled: boolean) {
    this.metronomeEnabled = enabled;
    if (!enabled) {
      this.metronomeLoop?.stop(0);
      return;
    }

    this.ensureMetronomeLoop();
    if (Tone.Transport.state === 'started') {
      this.metronomeLoop?.start(0);
    }
  }
}

export const audioEngine = new AudioEngine();
