'use client';

import { audioEngine } from '@musicdistro/audio-engine';
import type { Clip, InstrumentType, MidiClip, MidiNote, ProjectSnapshot, Track, TrackType } from '@musicdistro/types';
import { create } from 'zustand';
import { nanoid } from 'nanoid';
import { useTransportStore } from './transport-store';

interface ProjectStore {
  tracks: Track[];
  clips: Clip[];
  selectedTrackId: string | null;
  selectedClipIds: string[];
  createTrack: (type: TrackType, instrument?: InstrumentType) => Track;
  deleteTrack: (trackId: string) => void;
  addAudioClip: (trackId: string, fileUrl: string, length: number, name?: string) => void;
  addMidiClip: (trackId: string, start: number, end: number, instrument?: InstrumentType) => void;
  updateClipPosition: (clipId: string, start: number, end: number) => void;
  setClipLoop: (clipId: string, enabled: boolean) => void;
  duplicateClip: (clipId: string) => void;
  deleteClip: (clipId: string) => void;
  updateClipNotes: (clipId: string, notes: MidiNote[]) => void;
  updateClipSettings: (clipId: string, data: Partial<Clip>) => void;
  renameTrack: (trackId: string, name: string) => void;
  renameClip: (clipId: string, name: string) => void;
  setTrackVolume: (trackId: string, volume: number) => void;
  setTrackPan: (trackId: string, pan: number) => void;
  toggleTrackMute: (trackId: string) => void;
  toggleTrackSolo: (trackId: string) => void;
  toggleTrackArm: (trackId: string) => void;
  setTrackInstrument: (trackId: string, instrument: InstrumentType) => void;
  selectTrack: (trackId: string | null) => void;
  selectClips: (clipIds: string[]) => void;
  hydrate: (snapshot: ProjectSnapshot) => void;
  serialize: () => ProjectSnapshot;
}

const TRACK_COLORS = ['#0EA5E9', '#A855F7', '#22D3EE', '#34D399', '#F87171', '#F59E0B'];
const DEFAULT_AUTOMATION = [];

export const useProjectStore = create<ProjectStore>((set, get) => ({
  tracks: [],
  clips: [],
  selectedTrackId: null,
  selectedClipIds: [],
  createTrack(type, instrument = 'analog') {
    const id = nanoid();
    const order = get().tracks.length;
    const color = TRACK_COLORS[order % TRACK_COLORS.length];
    const track: Track = {
      id,
      name: `${type === 'audio' ? 'Audio' : type === 'midi' ? 'MIDI' : 'Instrument'} ${order + 1}`,
      type,
      color,
      order,
      armed: type !== 'audio',
      muted: false,
      solo: false,
      volume: -6,
      pan: 0,
      instrument: type === 'audio' ? undefined : instrument,
      plugins: [],
      automation: DEFAULT_AUTOMATION,
    };

    set((state) => ({
      tracks: [...state.tracks, track],
      selectedTrackId: track.id,
    }));

    void audioEngine.loadTrack(track, get().clips, track.plugins);
    return track;
  },
  deleteTrack(trackId) {
    const state = get();
    const tracks = state.tracks.filter((track) => track.id !== trackId);
    const clips = state.clips.filter((clip) => clip.trackId !== trackId);
    set({ tracks, clips, selectedTrackId: null, selectedClipIds: [] });
  },
  addAudioClip(trackId, fileUrl, length, name = 'Imported clip') {
    const clipId = nanoid();
    const clip: Clip = {
      id: clipId,
      name,
      trackId,
      start: 0,
      end: length,
      offset: 0,
      color: '#22D3EE',
      isLoop: false,
      kind: 'audio',
      fileId: fileUrl,
      warpMode: 'beats',
      gain: 0,
      transpose: 0,
    };

    set((state) => ({ clips: [...state.clips, clip], selectedClipIds: [clipId] }));
    const track = get().tracks.find((t) => t.id === trackId);
    if (track) {
      void audioEngine.loadTrack(track, get().clips, track.plugins);
    }
  },
  addMidiClip(trackId, start, end, instrument) {
    const clipId = nanoid();
    const clip: MidiClip = {
      id: clipId,
      name: 'MIDI clip',
      trackId,
      start,
      end,
      offset: 0,
      color: '#F59E0B',
      isLoop: false,
      kind: 'midi',
      instrument: instrument ?? (get().tracks.find((track) => track.id === trackId)?.instrument ?? 'analog'),
      notes: [],
    };

    set((state) => ({ clips: [...state.clips, clip], selectedClipIds: [clipId] }));
    const track = get().tracks.find((t) => t.id === trackId);
    if (track) {
      void audioEngine.loadTrack(track, get().clips, track.plugins);
    }
  },
  updateClipPosition(clipId, start, end) {
    set((state) => ({
      clips: state.clips.map((clip) => (clip.id === clipId ? { ...clip, start, end } : clip)),
    }));
    const clip = get().clips.find((c) => c.id === clipId);
    if (clip) {
      const track = get().tracks.find((t) => t.id === clip.trackId);
      if (track) {
        void audioEngine.loadTrack(track, get().clips, track.plugins);
      }
    }
  },
  setClipLoop(clipId, enabled) {
    set((state) => ({
      clips: state.clips.map((clip) => (clip.id === clipId ? { ...clip, isLoop: enabled } : clip)),
    }));
    const clip = get().clips.find((c) => c.id === clipId);
    if (clip) {
      const track = get().tracks.find((t) => t.id === clip.trackId);
      if (track) {
        void audioEngine.loadTrack(track, get().clips, track.plugins);
      }
    }
  },
  duplicateClip(clipId) {
    const clip = get().clips.find((c) => c.id === clipId);
    if (!clip) return;
    const newId = nanoid();
    const delta = clip.end - clip.start;
    const duplicate: Clip = {
      ...clip,
      id: newId,
      start: clip.end,
      end: clip.end + delta,
      name: `${clip.name} copy`,
    };
    set((state) => ({ clips: [...state.clips, duplicate], selectedClipIds: [newId] }));
    const track = get().tracks.find((t) => t.id === clip.trackId);
    if (track) {
      void audioEngine.loadTrack(track, get().clips, track.plugins);
    }
  },
  deleteClip(clipId) {
    const clip = get().clips.find((c) => c.id === clipId);
    set((state) => ({ clips: state.clips.filter((c) => c.id !== clipId), selectedClipIds: [] }));
    if (clip) {
      const track = get().tracks.find((t) => t.id === clip.trackId);
      if (track) {
        void audioEngine.loadTrack(track, get().clips, track.plugins);
      }
    }
  },
  updateClipNotes(clipId, notes) {
    set((state) => ({
      clips: state.clips.map((clip) => (clip.id === clipId && clip.kind === 'midi' ? { ...clip, notes } : clip)),
    }));
    const clip = get().clips.find((c) => c.id === clipId);
    if (clip) {
      const track = get().tracks.find((t) => t.id === clip.trackId);
      if (track) {
        void audioEngine.loadTrack(track, get().clips, track.plugins);
      }
    }
  },
  updateClipSettings(clipId, data) {
    set((state) => ({
      clips: state.clips.map((clip) => (clip.id === clipId ? { ...clip, ...data } : clip)),
    }));
    const clip = get().clips.find((c) => c.id === clipId);
    if (clip) {
      const track = get().tracks.find((t) => t.id === clip.trackId);
      if (track) {
        void audioEngine.loadTrack(track, get().clips, track.plugins);
      }
    }
  },
  renameTrack(trackId, name) {
    set((state) => ({ tracks: state.tracks.map((track) => (track.id === trackId ? { ...track, name } : track)) }));
  },
  renameClip(clipId, name) {
    set((state) => ({ clips: state.clips.map((clip) => (clip.id === clipId ? { ...clip, name } : clip)) }));
  },
  setTrackVolume(trackId, volume) {
    set((state) => ({
      tracks: state.tracks.map((track) => (track.id === trackId ? { ...track, volume } : track)),
    }));
    audioEngine.updateChannelMix(trackId, volume, get().tracks.find((t) => t.id === trackId)?.pan ?? 0);
  },
  setTrackPan(trackId, pan) {
    set((state) => ({
      tracks: state.tracks.map((track) => (track.id === trackId ? { ...track, pan } : track)),
    }));
    audioEngine.updateChannelMix(trackId, get().tracks.find((t) => t.id === trackId)?.volume ?? -6, pan);
  },
  toggleTrackMute(trackId) {
    set((state) => ({
      tracks: state.tracks.map((track) => (track.id === trackId ? { ...track, muted: !track.muted } : track)),
    }));
    const track = get().tracks.find((t) => t.id === trackId);
    if (track) {
      void audioEngine.loadTrack(track, get().clips, track.plugins);
    }
  },
  toggleTrackSolo(trackId) {
    const state = get();
    const track = state.tracks.find((t) => t.id === trackId);
    if (!track) return;

    const solo = !track.solo;
    const tracks = state.tracks.map((t) => ({ ...t, solo: t.id === trackId ? solo : false }));
    set({ tracks });
    tracks.forEach((updated) => {
      void audioEngine.loadTrack(updated, get().clips, updated.plugins);
    });
  },
  toggleTrackArm(trackId) {
    set((state) => ({
      tracks: state.tracks.map((track) => (track.id === trackId ? { ...track, armed: !track.armed } : track)),
    }));
  },
  setTrackInstrument(trackId, instrument) {
    set((state) => ({
      tracks: state.tracks.map((track) => (track.id === trackId ? { ...track, instrument } : track)),
    }));
    const track = get().tracks.find((t) => t.id === trackId);
    if (track) {
      void audioEngine.loadTrack(track, get().clips, track.plugins);
    }
  },
  selectTrack(trackId) {
    set({ selectedTrackId: trackId });
  },
  selectClips(clipIds) {
    set({ selectedClipIds: clipIds });
  },
  hydrate(snapshot) {
    set({
      tracks: snapshot.tracks,
      clips: snapshot.clips,
      selectedTrackId: snapshot.tracks[0]?.id ?? null,
      selectedClipIds: [],
    });
    useTransportStore.setState({
      bpm: snapshot.transport.bpm,
      timeSignature: snapshot.transport.timeSignature,
      loop: snapshot.transport.loop,
      grid: snapshot.transport.grid,
      metronomeEnabled: snapshot.transport.metronomeEnabled,
      position: snapshot.transport.position,
    });
    snapshot.tracks.forEach((track) => {
      void audioEngine.loadTrack(track, snapshot.clips, track.plugins);
    });
  },
  serialize() {
    const tracks = get().tracks;
    const clips = get().clips;
    const transportState = useTransportStore.getState();
    return {
      id: 'local',
      name: 'Untitled Project',
      createdAt: new Date().toISOString(),
      transport: {
        bpm: transportState.bpm,
        timeSignature: transportState.timeSignature,
        position: transportState.position,
        isPlaying: transportState.isPlaying,
        loop: transportState.loop,
        grid: transportState.grid,
        metronomeEnabled: transportState.metronomeEnabled,
      },
      tracks,
      clips,
    } satisfies ProjectSnapshot;
  },
}));
