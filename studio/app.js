const audioContext = new (window.AudioContext || window.webkitAudioContext)({ latencyHint: 'interactive' });
const analyser = audioContext.createAnalyser();
const masterGain = audioContext.createGain();
const masterCompressor = audioContext.createDynamicsCompressor();
const masterFilter = audioContext.createBiquadFilter();
const masterDelay = audioContext.createDelay(5.0);
const masterDelayGain = audioContext.createGain();
const masterReverb = audioContext.createConvolver();
const masterReverbGain = audioContext.createGain();
const masterDistortion = audioContext.createWaveShaper();
const metronomeGain = audioContext.createGain();
const metronomeOsc = audioContext.createOscillator();

masterFilter.type = 'lowshelf';
masterFilter.frequency.value = 120;
masterFilter.gain.value = 0;
masterDelay.delayTime.value = 0.3;
masterDelayGain.gain.value = 0.2;
masterReverbGain.gain.value = 0.25;
masterDistortion.curve = makeDistortionCurve(250);
masterDistortion.oversample = '4x';
metronomeGain.gain.value = 0;
metronomeOsc.type = 'square';
metronomeOsc.frequency.value = 1000;
metronomeOsc.start();
metronomeOsc.connect(metronomeGain);
masterGain.gain.value = 0.8;

masterFilter.connect(masterDistortion);
masterDistortion.connect(masterCompressor);
masterCompressor.connect(masterGain);
masterGain.connect(analyser);
analyser.connect(audioContext.destination);
masterDelay.connect(masterDelayGain);
masterDelayGain.connect(masterGain);
masterReverb.connect(masterReverbGain);
masterReverbGain.connect(masterGain);
metronomeGain.connect(masterGain);

masterReverb.buffer = createImpulse(3.5);

const colors = ['#22d3ee', '#34d399', '#f59e0b', '#818cf8', '#f97316', '#f87171', '#38bdf8'];
const instruments = {
  analog: { name: 'Analog Synth', type: 'sawtooth' },
  piano: { name: 'Studio Piano', type: 'sine' },
  drumkit: { name: 'Drum Kit', type: 'square' },
  bass: { name: 'Sub Bass', type: 'triangle' },
};
const noteMap = ['C5', 'B4', 'A4', 'G4', 'F4', 'E4', 'D4', 'C4'];
const midiPitches = [72, 71, 69, 67, 65, 64, 62, 60];

const gridDivisions = {
  '1/1': 4,
  '1/2': 2,
  '1/4': 1,
  '1/8': 0.5,
  '1/16': 0.25,
};

let trackCounter = 1;
let clipCounter = 1;
let noteCounter = 1;
let playStart = 0;
let animationFrame = 0;
let loopDrag = null;
let dragState = null;
let horizontalZoom = 1;
let verticalZoom = 1;

const trackNodes = new Map();
const activeSources = new Set();
const meterBuffer = new Float32Array(256);
const trackMeterElements = new Map();
let masterMeterFill = null;

const state = {
  projectName: 'Untitled Session',
  bpm: 120,
  grid: '1/4',
  tracks: [],
  clips: [],
  selectedTrackId: null,
  selectedClipIds: [],
  loop: { enabled: false, start: 0, end: 4 },
  position: 0,
  isPlaying: false,
  metronomeEnabled: false,
  automation: [],
  memoryUsage: 0,
  cpu: 0,
};

const refs = {
  statusBpm: document.getElementById('status-bpm'),
  statusTracks: document.getElementById('status-tracks'),
  statusClips: document.getElementById('status-clips'),
  statusGrid: document.getElementById('status-grid'),
  playButton: document.getElementById('play-toggle'),
  stopButton: document.getElementById('stop'),
  loopButton: document.getElementById('loop-toggle'),
  metronomeButton: document.getElementById('metronome-toggle'),
  bpmInput: document.getElementById('bpm-input'),
  gridSelect: document.getElementById('grid-select'),
  trackList: document.getElementById('track-list'),
  timelineScroll: document.getElementById('timeline-scroll'),
  timelineGrid: document.getElementById('timeline-grid'),
  timelineRuler: document.getElementById('timeline-ruler'),
  trackRows: document.getElementById('track-rows'),
  loopOverlay: document.getElementById('loop-overlay'),
  playhead: document.getElementById('playhead'),
  playheadReadout: document.getElementById('playhead-readout'),
  loopReadout: document.getElementById('loop-readout'),
  metronomeReadout: document.getElementById('metronome-readout'),
  duplicateClip: document.getElementById('duplicate-clip'),
  deleteClip: document.getElementById('delete-clip'),
  addAudioTrack: document.getElementById('add-audio-track'),
  addInstrumentTrack: document.getElementById('add-instrument-track'),
  addMidiClip: document.getElementById('add-midi-clip'),
  zoomHorizontal: document.getElementById('zoom-horizontal'),
  zoomVertical: document.getElementById('zoom-vertical'),
  zoomLevel: document.getElementById('zoom-level'),
  inspector: document.getElementById('inspector'),
  mixer: document.getElementById('mixer-channels'),
  pluginDock: document.getElementById('plugin-dock'),
  exportWav: document.getElementById('export-wav'),
  exportMp3: document.getElementById('export-mp3'),
  saveProject: document.getElementById('save-project'),
  loadProject: document.getElementById('load-project'),
  projectName: document.getElementById('project-name'),
  resetSession: document.getElementById('reset-session'),
  tempoAutomation: document.getElementById('tempo-automation'),
  applyTempo: document.getElementById('apply-tempo'),
  memoryUsage: document.getElementById('memory-usage'),
  cpuUsage: document.getElementById('cpu-usage'),
};

refs.playButton.addEventListener('click', () => {
  if (state.isPlaying) {
    stop();
  } else {
    play();
  }
});

refs.stopButton.addEventListener('click', () => stop(true));
refs.loopButton.addEventListener('click', () => {
  state.loop.enabled = !state.loop.enabled;
  renderTransport();
});

refs.metronomeButton.addEventListener('click', () => {
  state.metronomeEnabled = !state.metronomeEnabled;
  metronomeGain.gain.value = state.metronomeEnabled ? 0.3 : 0;
  renderTransport();
});

refs.bpmInput.addEventListener('change', (event) => {
  const value = Number.parseFloat(event.target.value);
  if (Number.isFinite(value) && value > 20 && value < 400) {
    state.bpm = value;
    renderTransport();
    refreshAudio();
  }
});

refs.gridSelect.addEventListener('change', (event) => {
  state.grid = event.target.value;
  renderTransport();
  renderTimeline();
});

refs.addAudioTrack.addEventListener('click', () => createTrack('audio'));
refs.addInstrumentTrack.addEventListener('click', () => createTrack('instrument', 'analog'));
refs.addMidiClip.addEventListener('click', () => {
  if (!state.selectedTrackId) return;
  const track = getTrack(state.selectedTrackId);
  if (!track) return;
  const start = Math.max(0, Math.floor(state.position));
  const clip = createMidiClip(track.id, start, start + 4);
  selectClip(clip.id);
});

refs.zoomHorizontal.addEventListener('input', (event) => {
  horizontalZoom = Number.parseFloat(event.target.value);
  refs.zoomLevel.textContent = `${horizontalZoom.toFixed(2)}x`;
  renderTimeline();
});

refs.zoomVertical.addEventListener('input', (event) => {
  verticalZoom = Number.parseFloat(event.target.value);
  renderTimeline();
});

refs.duplicateClip.addEventListener('click', () => {
  if (!state.selectedClipIds.length) return;
  const original = getClip(state.selectedClipIds[0]);
  if (!original) return;
  duplicateClip(original.id);
});

refs.deleteClip.addEventListener('click', () => {
  if (!state.selectedClipIds.length) return;
  state.selectedClipIds.slice().forEach((clipId) => deleteClip(clipId));
  selectClip(null);
});

refs.exportWav.addEventListener('click', () => exportMixdown('wav'));
refs.exportMp3.addEventListener('click', () => exportMixdown('mp3'));
refs.saveProject.addEventListener('click', saveSnapshot);
refs.loadProject.addEventListener('click', loadSnapshot);
refs.projectName.addEventListener('input', (event) => {
  state.projectName = event.target.value;
});

refs.resetSession.addEventListener('click', () => {
  if (confirm('Reset the current session?')) {
    resetSession();
  }
});

refs.applyTempo.addEventListener('click', applyTempoAutomation);

function createImpulse(duration = 2.5) {
  const rate = audioContext.sampleRate;
  const length = rate * duration;
  const impulse = audioContext.createBuffer(2, length, rate);
  for (let channel = 0; channel < impulse.numberOfChannels; channel++) {
    const channelData = impulse.getChannelData(channel);
    for (let i = 0; i < length; i++) {
      channelData[i] = (Math.random() * 2 - 1) * Math.pow(1 - i / length, 2.5);
    }
  }
  return impulse;
}

function makeDistortionCurve(amount) {
  const k = typeof amount === 'number' ? amount : 50;
  const samples = 44100;
  const curve = new Float32Array(samples);
  const deg = Math.PI / 180;
  for (let i = 0; i < samples; i++) {
    const x = (i * 2) / samples - 1;
    curve[i] = ((3 + k) * x * 20 * deg) / (Math.PI + k * Math.abs(x));
  }
  return curve;
}

function resetSession() {
  stop(true);
  state.tracks.length = 0;
  state.clips.length = 0;
  state.selectedTrackId = null;
  state.selectedClipIds = [];
  trackNodes.forEach((nodes) => nodes.channel && nodes.channel.disconnect());
  trackNodes.clear();
  trackCounter = 1;
  clipCounter = 1;
  noteCounter = 1;
  createTrack('audio');
  createTrack('instrument', 'piano');
  renderAll();
}

function applyTempoAutomation() {
  const text = refs.tempoAutomation.value.trim();
  if (!text) {
    state.automation = [];
    return;
  }
  const lines = text.split(/\n+/);
  const automation = [];
  for (const line of lines) {
    const [beatStr, valueStr] = line.split(':');
    const beat = Number.parseFloat(beatStr);
    const value = Number.parseFloat(valueStr);
    if (Number.isFinite(beat) && Number.isFinite(value)) {
      automation.push({ beat, value });
    }
  }
  automation.sort((a, b) => a.beat - b.beat);
  state.automation = automation;
  refreshAudio();
}

function saveSnapshot() {
  const snapshot = {
    projectName: state.projectName,
    bpm: state.bpm,
    grid: state.grid,
    loop: state.loop,
    tracks: state.tracks.map((track) => ({ ...track })),
    clips: state.clips.map((clip) => ({
      ...clip,
      buffer: undefined,
      audioData: clip.buffer ? bufferToBase64(clip.buffer) : null,
    })),
    automation: state.automation,
    version: 2,
  };
  window.localStorage.setItem('musicdistro.studio', JSON.stringify(snapshot));
  alert('Session saved locally.');
}

async function loadSnapshot() {
  const raw = window.localStorage.getItem('musicdistro.studio');
  if (!raw) {
    alert('No saved session found.');
    return;
  }
  try {
    const snapshot = JSON.parse(raw);
    stop(true);
    state.projectName = snapshot.projectName ?? 'Untitled Session';
    state.bpm = snapshot.bpm ?? 120;
    state.grid = snapshot.grid ?? '1/4';
    state.loop = snapshot.loop ?? { enabled: false, start: 0, end: 4 };
    state.tracks = snapshot.tracks ?? [];
    state.clips = [];
    state.automation = snapshot.automation ?? [];
    trackNodes.forEach((nodes) => nodes.channel && nodes.channel.disconnect());
    trackNodes.clear();
    const promises = (snapshot.clips ?? []).map(async (clip) => {
      if (clip.kind === 'audio' && clip.audioData) {
        const buffer = await base64ToBuffer(clip.audioData);
        clip.buffer = buffer;
      }
      state.clips.push(clip);
    });
    await Promise.all(promises);
    state.selectedTrackId = state.tracks[0]?.id ?? null;
    refs.projectName.value = state.projectName;
    refs.bpmInput.value = state.bpm.toFixed(1);
    refs.gridSelect.value = state.grid;
    refreshAudio();
    renderAll();
  } catch (error) {
    console.error('Failed to load session', error);
    alert('Failed to load session data.');
  }
}

function bufferToBase64(buffer) {
  const array = [];
  for (let channel = 0; channel < buffer.numberOfChannels; channel++) {
    array.push(buffer.getChannelData(channel));
  }
  const interleaved = new Float32Array(buffer.length * buffer.numberOfChannels);
  let offset = 0;
  for (let i = 0; i < buffer.length; i++) {
    for (let channel = 0; channel < buffer.numberOfChannels; channel++) {
      interleaved[offset++] = array[channel][i];
    }
  }
  const bytes = new Uint8Array(interleaved.buffer);
  let binary = '';
  for (let i = 0; i < bytes.byteLength; i++) {
    binary += String.fromCharCode(bytes[i]);
  }
  return window.btoa(binary);
}

async function base64ToBuffer(base64) {
  const binary = window.atob(base64);
  const bytes = new Uint8Array(binary.length);
  for (let i = 0; i < binary.length; i++) {
    bytes[i] = binary.charCodeAt(i);
  }
  const audioBuffer = audioContext.createBuffer(2, bytes.length / 8, audioContext.sampleRate);
  const floatArray = new Float32Array(bytes.buffer);
  for (let channel = 0; channel < audioBuffer.numberOfChannels; channel++) {
    const channelData = audioBuffer.getChannelData(channel);
    for (let i = 0; i < audioBuffer.length; i++) {
      channelData[i] = floatArray[i * audioBuffer.numberOfChannels + channel] ?? 0;
    }
  }
  return audioBuffer;
}

function createTrack(type, instrument = 'analog') {
  const id = `track-${trackCounter++}`;
  const track = {
    id,
    name: type === 'audio' ? `Audio ${state.tracks.length + 1}` : `Instrument ${state.tracks.length + 1}`,
    type,
    color: colors[state.tracks.length % colors.length],
    volume: -6,
    pan: 0,
    muted: false,
    solo: false,
    armed: type !== 'audio',
    instrument,
    plugins: {
      eq: { low: 0, mid: 0, high: 0 },
      reverb: 0.25,
      delay: 0.15,
      distortion: 0,
    },
  };
  state.tracks.push(track);
  state.selectedTrackId = id;
  ensureTrackNodes(track);
  renderAll();
  return track;
}

function ensureTrackNodes(track) {
  if (trackNodes.has(track.id)) return trackNodes.get(track.id);
  const input = audioContext.createGain();
  const eqLow = audioContext.createBiquadFilter();
  eqLow.type = 'lowshelf';
  eqLow.frequency.value = 200;
  const eqMid = audioContext.createBiquadFilter();
  eqMid.type = 'peaking';
  eqMid.frequency.value = 1200;
  eqMid.Q.value = 1;
  const eqHigh = audioContext.createBiquadFilter();
  eqHigh.type = 'highshelf';
  eqHigh.frequency.value = 6000;
  const panNode = audioContext.createStereoPanner();
  const gainNode = audioContext.createGain();
  gainNode.gain.value = dbToGain(track.volume);
  const sendReverb = audioContext.createGain();
  const sendDelay = audioContext.createGain();
  const sendDistortion = audioContext.createGain();

  input.connect(eqLow);
  eqLow.connect(eqMid);
  eqMid.connect(eqHigh);
  eqHigh.connect(panNode);
  panNode.connect(gainNode);
  gainNode.connect(masterFilter);
  sendReverb.connect(masterReverb);
  sendDelay.connect(masterDelay);
  sendDistortion.connect(masterDistortion);

  const nodes = { input, eqLow, eqMid, eqHigh, panNode, gainNode, sendReverb, sendDelay, sendDistortion };
  trackNodes.set(track.id, nodes);
  return nodes;
}

function dbToGain(db) {
  return Math.pow(10, db / 20);
}

function getTrack(trackId) {
  return state.tracks.find((track) => track.id === trackId) ?? null;
}

function getClip(clipId) {
  return state.clips.find((clip) => clip.id === clipId) ?? null;
}

function createMidiClip(trackId, start, end) {
  const clip = {
    id: `clip-${clipCounter++}`,
    trackId,
    kind: 'midi',
    name: 'MIDI Clip',
    start,
    end,
    color: '#f59e0b',
    isLoop: false,
    instrument: getTrack(trackId)?.instrument ?? 'analog',
    notes: [],
  };
  state.clips.push(clip);
  refreshAudio();
  renderAll();
  return clip;
}

async function addAudioClip(trackId, file, buffer, start = 0) {
  const clip = {
    id: `clip-${clipCounter++}`,
    trackId,
    kind: 'audio',
    name: file.name.replace(/\.[^.]+$/, ''),
    start,
    end: start + buffer.duration,
    color: '#22d3ee',
    isLoop: false,
    buffer,
    fileName: file.name,
    gain: 0,
    transpose: 0,
    fadeIn: 0.01,
    fadeOut: 0.01,
  };
  state.clips.push(clip);
  selectClip(clip.id);
  refreshAudio();
  renderAll();
  return clip;
}

function duplicateClip(clipId) {
  const clip = getClip(clipId);
  if (!clip) return;
  const delta = clip.end - clip.start;
  const start = clip.end;
  if (clip.kind === 'audio') {
    const duplicate = {
      ...clip,
      id: `clip-${clipCounter++}`,
      start,
      end: start + delta,
    };
    state.clips.push(duplicate);
    selectClip(duplicate.id);
  } else {
    const duplicate = {
      ...clip,
      id: `clip-${clipCounter++}`,
      start,
      end: start + delta,
      notes: clip.notes.map((note) => ({ ...note, id: `note-${noteCounter++}` })),
    };
    state.clips.push(duplicate);
    selectClip(duplicate.id);
  }
  refreshAudio();
  renderAll();
}

function deleteClip(clipId) {
  const clip = getClip(clipId);
  if (!clip) return;
  const index = state.clips.indexOf(clip);
  if (index >= 0) {
    state.clips.splice(index, 1);
  }
  refreshAudio();
  renderAll();
}

function setClipLoop(clipId, enabled) {
  const clip = getClip(clipId);
  if (!clip) return;
  clip.isLoop = enabled;
  refreshAudio();
  renderTimeline();
}

function updateClipPosition(clipId, start, end) {
  const clip = getClip(clipId);
  if (!clip) return;
  clip.start = Math.max(0, start);
  clip.end = Math.max(clip.start + 0.125, end);
  refreshAudio();
  renderTimeline();
}

function updateClipSettings(clipId, data) {
  const clip = getClip(clipId);
  if (!clip) return;
  Object.assign(clip, data);
  refreshAudio();
  renderTimeline();
}

function updateMidiNotes(clipId, notes) {
  const clip = getClip(clipId);
  if (!clip || clip.kind !== 'midi') return;
  clip.notes = notes;
  refreshAudio();
  renderTimeline();
}

function selectTrack(trackId) {
  state.selectedTrackId = trackId;
  renderTrackList();
  renderInspector();
}

function selectClip(clipId) {
  state.selectedClipIds = clipId ? [clipId] : [];
  if (clipId) {
    const clip = getClip(clipId);
    if (clip) {
      state.selectedTrackId = clip.trackId;
    }
  }
  renderTimeline();
  renderInspector();
}
function getTotalDuration() {
  if (!state.clips.length) return 8;
  return Math.max(8, Math.max(...state.clips.map((clip) => clip.end)) + 4);
}

function quantize(seconds) {
  const secondsPerBeat = 60 / state.bpm;
  const division = gridDivisions[state.grid] ?? 1;
  const quantum = secondsPerBeat * division;
  return Math.max(0, Math.round(seconds / quantum) * quantum);
}

function renderAll() {
  renderTransport();
  renderTrackList();
  renderTimeline();
  renderInspector();
  renderMixer();
  renderPlugins();
  updateSystemStats();
}

function renderTransport() {
  refs.statusBpm.textContent = `BPM ${state.bpm.toFixed(1)}`;
  refs.statusTracks.textContent = `Tracks ${state.tracks.length}`;
  refs.statusClips.textContent = `Clips ${state.clips.length}`;
  refs.statusGrid.textContent = `Grid ${state.grid}`;
  refs.loopButton.classList.toggle('btn-primary', state.loop.enabled);
  refs.loopReadout.textContent = state.loop.enabled
    ? `Loop ${state.loop.start.toFixed(2)}s – ${state.loop.end.toFixed(2)}s`
    : 'Loop OFF';
  refs.metronomeReadout.textContent = state.metronomeEnabled ? 'Metronome ON' : 'Metronome OFF';
  refs.playButton.textContent = state.isPlaying ? 'STOP' : 'PLAY';
  refs.bpmInput.value = state.bpm.toFixed(1);
}

function renderTrackList() {
  refs.trackList.innerHTML = '';
  if (!state.tracks.length) {
    const empty = document.createElement('div');
    empty.className = 'empty-state';
    empty.textContent = 'Add a track to begin';
    refs.trackList.appendChild(empty);
    return;
  }
  state.tracks.forEach((track) => {
    const card = document.createElement('div');
    card.className = 'track-card';
    if (track.id === state.selectedTrackId) {
      card.classList.add('active');
    }
    card.addEventListener('click', () => selectTrack(track.id));

    const header = document.createElement('header');
    header.innerHTML = `<strong>${track.name}</strong><span class="badge">${track.type}</span>`;
    card.appendChild(header);

    const sliders = document.createElement('div');
    sliders.className = 'track-controls';

    const volumeLabel = document.createElement('label');
    volumeLabel.innerHTML = `
      <span class="grid-label">Volume</span>
      <input type="range" min="-36" max="6" step="0.5" value="${track.volume}" />
    `;
    volumeLabel.querySelector('input').addEventListener('input', (event) => {
      track.volume = Number.parseFloat(event.target.value);
      const nodes = ensureTrackNodes(track);
      nodes.gainNode.gain.value = dbToGain(track.volume);
      renderMixer();
    });

    const panLabel = document.createElement('label');
    panLabel.innerHTML = `
      <span class="grid-label">Pan</span>
      <input type="range" min="-1" max="1" step="0.01" value="${track.pan}" />
    `;
    panLabel.querySelector('input').addEventListener('input', (event) => {
      track.pan = Number.parseFloat(event.target.value);
      const nodes = ensureTrackNodes(track);
      nodes.panNode.pan.value = track.pan;
    });

    sliders.appendChild(volumeLabel);
    sliders.appendChild(panLabel);
    card.appendChild(sliders);

    const toggles = document.createElement('div');
    toggles.className = 'track-toggles';
    const muteButton = document.createElement('button');
    muteButton.textContent = 'M';
    muteButton.classList.toggle('active-mute', track.muted);
    muteButton.addEventListener('click', (event) => {
      event.stopPropagation();
      track.muted = !track.muted;
      refreshAudio();
      renderTrackList();
    });

    const soloButton = document.createElement('button');
    soloButton.textContent = 'S';
    soloButton.classList.toggle('active-solo', track.solo);
    soloButton.addEventListener('click', (event) => {
      event.stopPropagation();
      const newSolo = !track.solo;
      state.tracks.forEach((candidate) => {
        candidate.solo = candidate.id === track.id ? newSolo : false;
      });
      refreshAudio();
      renderTrackList();
    });

    toggles.appendChild(muteButton);
    toggles.appendChild(soloButton);
    card.appendChild(toggles);

    refs.trackList.appendChild(card);
  });
}

function renderTimeline() {
  const rows = refs.trackRows;
  const ruler = refs.timelineRuler;
  rows.innerHTML = '';
  ruler.innerHTML = '';

  const projectLength = getTotalDuration();
  const secondsPerBeat = 60 / state.bpm;
  const secondsPerBar = secondsPerBeat * 4;
  const pxPerSecond = 120 * horizontalZoom;

  refs.timelineGrid.style.minWidth = `${projectLength * pxPerSecond + 400}px`;

  const bars = Math.ceil(projectLength / secondsPerBar);
  for (let bar = 0; bar < bars; bar++) {
    const measure = document.createElement('div');
    measure.className = 'measure';
    measure.style.width = `${secondsPerBar * pxPerSecond}px`;
    measure.textContent = `Bar ${bar + 1}`;

    const division = gridDivisions[state.grid] ?? 1;
    const steps = Math.round(4 / division);
    for (let step = 1; step < steps; step++) {
      const line = document.createElement('div');
      line.className = 'grid-line';
      line.style.left = `${(step * secondsPerBeat * division) * pxPerSecond}px`;
      measure.appendChild(line);
    }

    measure.addEventListener('pointerdown', (event) => {
      const rect = ruler.getBoundingClientRect();
      const seconds = ((event.clientX - rect.left) + refs.timelineScroll.scrollLeft) / pxPerSecond;
      if (event.shiftKey) {
        const quantized = quantize(seconds);
        loopDrag = { start: quantized };
        state.loop.enabled = true;
        state.loop.start = quantized;
        state.loop.end = quantize(quantized + secondsPerBeat * 4);
        renderTransport();
        renderTimeline();
        window.addEventListener('pointermove', onLoopDragMove);
        window.addEventListener('pointerup', onLoopDragEnd, { once: true });
      } else {
        state.position = quantize(seconds);
        renderPlayhead();
      }
    });

    ruler.appendChild(measure);
  }

  state.tracks.forEach((track, index) => {
    const row = document.createElement('div');
    row.className = 'track-row';
    row.style.height = `${120 * verticalZoom}px`;
    row.dataset.trackId = track.id;

    const clips = state.clips.filter((clip) => clip.trackId === track.id);
    clips.forEach((clip) => {
      const element = document.createElement('div');
      element.className = 'clip';
      element.dataset.clipId = clip.id;
      if (state.selectedClipIds.includes(clip.id)) {
        element.classList.add('selected');
      }
      const left = clip.start * pxPerSecond;
      const width = Math.max((clip.end - clip.start) * pxPerSecond, 48);
      element.style.left = `${left}px`;
      element.style.width = `${width}px`;
      element.style.background = `${clip.color}24`;
      element.style.borderColor = clip.color;
      element.innerHTML = `
        <div class="meta"><span>${clip.name}</span><span>${(clip.end - clip.start).toFixed(2)}s</span></div>
        <div class="meta"><span>${clip.kind}</span><span>${clip.isLoop ? 'Loop' : ''}</span></div>
        <div class="handles"><div class="handle" data-side="start"></div><div class="handle" data-side="end"></div></div>
      `;

      element.addEventListener('click', (event) => {
        event.stopPropagation();
        selectClip(clip.id);
      });

      element.addEventListener('dblclick', (event) => {
        event.stopPropagation();
        selectClip(clip.id);
        renderInspector(true);
      });

      element.querySelectorAll('.handle').forEach((handle) => {
        handle.addEventListener('pointerdown', (event) => {
          event.stopPropagation();
          event.preventDefault();
          dragState = {
            type: handle.dataset.side === 'start' ? 'resize-start' : 'resize-end',
            clipId: clip.id,
            originX: event.clientX,
            originStart: clip.start,
            originEnd: clip.end,
          };
          window.addEventListener('pointermove', onDragMove);
          window.addEventListener('pointerup', onDragEnd, { once: true });
        });
      });

      element.addEventListener('pointerdown', (event) => {
        event.stopPropagation();
        dragState = {
          type: 'move',
          clipId: clip.id,
          originX: event.clientX,
          originStart: clip.start,
          originEnd: clip.end,
        };
        window.addEventListener('pointermove', onDragMove);
        window.addEventListener('pointerup', onDragEnd, { once: true });
      });

      row.appendChild(element);
    });

    row.addEventListener('click', () => selectTrack(track.id));
    rows.appendChild(row);
  });

  if (state.loop.enabled) {
    refs.loopOverlay.style.display = 'block';
    refs.loopOverlay.style.left = `${state.loop.start * pxPerSecond}px`;
    refs.loopOverlay.style.width = `${Math.max(2, (state.loop.end - state.loop.start) * pxPerSecond)}px`;
  } else {
    refs.loopOverlay.style.display = 'none';
  }

  renderPlayhead();
}

function renderPlayhead() {
  if (!state.isPlaying) {
    refs.playhead.style.display = 'block';
    const pxPerSecond = 120 * horizontalZoom;
    refs.playhead.style.left = `${state.position * pxPerSecond}px`;
  }
  refs.playheadReadout.textContent = formatTime(state.position);
}

function formatTime(seconds) {
  const minutes = Math.floor(seconds / 60);
  const remainder = seconds % 60;
  return `${String(minutes).padStart(2, '0')}:${remainder.toFixed(1).padStart(4, '0')}`;
}

function onDragMove(event) {
  if (!dragState) return;
  const pxPerSecond = 120 * horizontalZoom;
  const deltaSeconds = (event.clientX - dragState.originX) / pxPerSecond;
  const clip = getClip(dragState.clipId);
  if (!clip) return;
  if (dragState.type === 'move') {
    const newStart = quantize(dragState.originStart + deltaSeconds);
    const newEnd = newStart + (dragState.originEnd - dragState.originStart);
    updateClipPosition(clip.id, newStart, newEnd);
  }
  if (dragState.type === 'resize-start') {
    const newStart = quantize(dragState.originStart + deltaSeconds);
    updateClipPosition(clip.id, Math.min(newStart, clip.end - 0.125), clip.end);
  }
  if (dragState.type === 'resize-end') {
    const newEnd = quantize(dragState.originEnd + deltaSeconds);
    updateClipPosition(clip.id, clip.start, Math.max(newEnd, clip.start + 0.125));
  }
}

function onDragEnd() {
  dragState = null;
  window.removeEventListener('pointermove', onDragMove);
}

function onLoopDragMove(event) {
  if (!loopDrag) return;
  const pxPerSecond = 120 * horizontalZoom;
  const rect = refs.timelineRuler.getBoundingClientRect();
  const seconds = ((event.clientX - rect.left) + refs.timelineScroll.scrollLeft) / pxPerSecond;
  const quantized = quantize(seconds);
  state.loop.end = Math.max(quantized, loopDrag.start + 0.25);
  state.loop.start = Math.min(loopDrag.start, state.loop.end - 0.25);
  renderTransport();
  renderTimeline();
}

function onLoopDragEnd() {
  loopDrag = null;
  window.removeEventListener('pointermove', onLoopDragMove);
}
function renderInspector(focus = false) {
  refs.inspector.innerHTML = '';
  const track = state.selectedTrackId ? getTrack(state.selectedTrackId) : null;
  const clip = state.selectedClipIds.length ? getClip(state.selectedClipIds[0]) : null;

  const trackCard = document.createElement('div');
  trackCard.className = 'card';
  trackCard.innerHTML = '<h3 class="grid-label">Track</h3>';
  if (track) {
    const nameField = document.createElement('label');
    nameField.innerHTML = `Name<input type="text" value="${track.name}" />`;
    nameField.querySelector('input').addEventListener('input', (event) => {
      track.name = event.target.value;
      renderTrackList();
      renderTimeline();
    });
    trackCard.appendChild(nameField);

    if (track.type !== 'audio') {
      const instrumentField = document.createElement('label');
      const select = document.createElement('select');
      Object.entries(instruments).forEach(([id, info]) => {
        const option = document.createElement('option');
        option.value = id;
        option.textContent = info.name;
        if (id === track.instrument) option.selected = true;
        select.appendChild(option);
      });
      select.addEventListener('change', (event) => {
        track.instrument = event.target.value;
        state.clips
          .filter((candidate) => candidate.trackId === track.id && candidate.kind === 'midi')
          .forEach((candidate) => {
            candidate.instrument = track.instrument;
          });
        refreshAudio();
      });
      instrumentField.innerHTML = 'Instrument';
      instrumentField.appendChild(select);
      trackCard.appendChild(instrumentField);
    }
  } else {
    const empty = document.createElement('p');
    empty.className = 'empty-state';
    empty.textContent = 'Select a track to edit settings.';
    trackCard.appendChild(empty);
  }
  refs.inspector.appendChild(trackCard);

  const clipCard = document.createElement('div');
  clipCard.className = 'card';
  clipCard.innerHTML = '<h3 class="grid-label">Clip</h3>';
  if (clip) {
    const nameField = document.createElement('label');
    nameField.innerHTML = `Name<input type="text" value="${clip.name}" />`;
    nameField.querySelector('input').addEventListener('input', (event) => {
      clip.name = event.target.value;
      renderTimeline();
    });
    clipCard.appendChild(nameField);

    const info = document.createElement('div');
    info.className = 'flex flex-between';
    info.innerHTML = `
      <span class="tag">Start ${clip.start.toFixed(2)}s</span>
      <span class="tag">Length ${(clip.end - clip.start).toFixed(2)}s</span>
    `;
    clipCard.appendChild(info);

    const loopToggle = document.createElement('button');
    loopToggle.textContent = clip.isLoop ? 'Disable loop' : 'Enable loop';
    loopToggle.className = clip.isLoop ? 'btn-primary' : 'btn-ghost';
    loopToggle.addEventListener('click', () => {
      setClipLoop(clip.id, !clip.isLoop);
      renderInspector();
    });
    clipCard.appendChild(loopToggle);

    if (clip.kind === 'audio') {
      const gainField = document.createElement('label');
      gainField.innerHTML = `Gain<input type="range" min="-24" max="6" step="0.5" value="${clip.gain ?? 0}" />`;
      gainField.querySelector('input').addEventListener('input', (event) => {
        clip.gain = Number.parseFloat(event.target.value);
        refreshAudio();
      });
      clipCard.appendChild(gainField);

      const transposeField = document.createElement('label');
      transposeField.innerHTML = `Transpose<input type="range" min="-12" max="12" step="1" value="${clip.transpose ?? 0}" />`;
      transposeField.querySelector('input').addEventListener('input', (event) => {
        clip.transpose = Number.parseFloat(event.target.value);
        refreshAudio();
      });
      clipCard.appendChild(transposeField);
    }

    if (clip.kind === 'midi') {
      const instrumentField = document.createElement('label');
      instrumentField.innerHTML = 'Instrument';
      const select = document.createElement('select');
      Object.entries(instruments).forEach(([id, info]) => {
        const option = document.createElement('option');
        option.value = id;
        option.textContent = info.name;
        if (id === clip.instrument) option.selected = true;
        select.appendChild(option);
      });
      select.addEventListener('change', (event) => {
        clip.instrument = event.target.value;
        const track = getTrack(clip.trackId);
        if (track) {
          track.instrument = clip.instrument;
        }
        refreshAudio();
        renderTrackList();
      });
      instrumentField.appendChild(select);
      clipCard.appendChild(instrumentField);

      const grid = document.createElement('div');
      grid.className = 'midi-grid';
      const steps = 16;
      const stepDuration = (clip.end - clip.start) / steps;
      midiPitches.forEach((pitch, rowIndex) => {
        for (let column = 0; column < steps; column++) {
          const cell = document.createElement('button');
          cell.type = 'button';
          cell.className = 'midi-cell';
          if (column === 0) {
            cell.textContent = noteMap[rowIndex];
          }
          const start = clip.start + column * stepDuration;
          const active = clip.notes.some((note) => note.pitch === pitch && Math.abs(note.start - start) < stepDuration / 2);
          if (active) cell.classList.add('active');
          cell.addEventListener('click', () => {
            toggleMidiNote(clip, pitch, start, stepDuration);
          });
          grid.appendChild(cell);
        }
      });
      clipCard.appendChild(grid);
    }
  } else {
    const empty = document.createElement('p');
    empty.className = 'empty-state';
    empty.textContent = 'Select a clip to edit details.';
    clipCard.appendChild(empty);
  }
  refs.inspector.appendChild(clipCard);

  if (focus) {
    clipCard.scrollIntoView({ behavior: 'smooth' });
  }
}

function toggleMidiNote(clip, pitch, start, duration) {
  const existing = clip.notes.find((note) => note.pitch === pitch && Math.abs(note.start - start) < duration / 2);
  if (existing) {
    clip.notes = clip.notes.filter((note) => note !== existing);
  } else {
    clip.notes = [
      ...clip.notes,
      {
        id: `note-${noteCounter++}`,
        pitch,
        start,
        duration,
        velocity: 100,
      },
    ];
  }
  refreshAudio();
  renderInspector();
  renderTimeline();
}

function renderMixer() {
  refs.mixer.innerHTML = '';
  trackMeterElements.clear();
  const masterCard = document.createElement('div');
  masterCard.className = 'channel-card';
  masterCard.innerHTML = `<strong>Master</strong>`;
  const masterMeter = document.createElement('div');
  masterMeter.className = 'meter';
  const fill = document.createElement('div');
  fill.className = 'meter-fill';
  masterMeter.appendChild(fill);
  masterCard.appendChild(masterMeter);
  masterMeterFill = fill;

  const level = document.createElement('label');
  level.innerHTML = `Level<input type="range" min="0" max="1.5" step="0.01" value="${masterGain.gain.value}" />`;
  level.querySelector('input').addEventListener('input', (event) => {
    masterGain.gain.value = Number.parseFloat(event.target.value);
  });
  masterCard.appendChild(level);

  refs.mixer.appendChild(masterCard);

  state.tracks.forEach((track) => {
    const card = document.createElement('div');
    card.className = 'channel-card';
    card.innerHTML = `<strong>${track.name}</strong>`;

    const meter = document.createElement('div');
    meter.className = 'meter';
    const meterFill = document.createElement('div');
    meterFill.className = 'meter-fill';
    meter.appendChild(meterFill);
    card.appendChild(meter);
    trackMeterElements.set(track.id, meterFill);

    const volume = document.createElement('label');
    volume.innerHTML = `Volume<input type="range" min="-36" max="6" step="0.5" value="${track.volume}" />`;
    volume.querySelector('input').addEventListener('input', (event) => {
      track.volume = Number.parseFloat(event.target.value);
      const nodes = ensureTrackNodes(track);
      nodes.gainNode.gain.value = dbToGain(track.volume);
    });
    card.appendChild(volume);

    const pan = document.createElement('label');
    pan.innerHTML = `Pan<input type="range" min="-1" max="1" step="0.01" value="${track.pan}" />`;
    pan.querySelector('input').addEventListener('input', (event) => {
      track.pan = Number.parseFloat(event.target.value);
      const nodes = ensureTrackNodes(track);
      nodes.panNode.pan.value = track.pan;
    });
    card.appendChild(pan);

    const toggles = document.createElement('div');
    toggles.className = 'track-toggles';
    const mute = document.createElement('button');
    mute.textContent = 'M';
    mute.classList.toggle('active-mute', track.muted);
    mute.addEventListener('click', () => {
      track.muted = !track.muted;
      refreshAudio();
      renderMixer();
      renderTimeline();
    });
    const solo = document.createElement('button');
    solo.textContent = 'S';
    solo.classList.toggle('active-solo', track.solo);
    solo.addEventListener('click', () => {
      const newSolo = !track.solo;
      state.tracks.forEach((candidate) => {
        candidate.solo = candidate.id === track.id ? newSolo : false;
      });
      refreshAudio();
      renderMixer();
      renderTimeline();
    });
    toggles.appendChild(mute);
    toggles.appendChild(solo);
    card.appendChild(toggles);

    refs.mixer.appendChild(card);
  });
}

function renderPlugins() {
  const plugins = [
    {
      id: 'eq8',
      name: 'EQ Eight',
      category: 'EQ',
      render: () => {
        const wrapper = document.createElement('div');
        wrapper.className = 'plugin-controls';
        const low = createPluginSlider('Low Shelf', masterFilter.gain.value, -12, 12, 0.5, (value) => {
          masterFilter.gain.value = value;
        });
        const delay = createPluginSlider('Delay', masterDelayGain.gain.value, 0, 1, 0.01, (value) => {
          masterDelayGain.gain.value = value;
        });
        const reverb = createPluginSlider('Reverb', masterReverbGain.gain.value, 0, 1, 0.01, (value) => {
          masterReverbGain.gain.value = value;
        });
        const distortion = createPluginSlider('Drive', masterGain.gain.value, 0, 1.5, 0.01, (value) => {
          masterGain.gain.value = value;
        });
        wrapper.appendChild(low);
        wrapper.appendChild(delay);
        wrapper.appendChild(reverb);
        wrapper.appendChild(distortion);
        return wrapper;
      },
    },
    {
      id: 'compressor',
      name: 'Bus Compressor',
      category: 'Dynamics',
      render: () => {
        const wrapper = document.createElement('div');
        wrapper.className = 'plugin-controls';
        const threshold = createPluginSlider('Threshold', masterCompressor.threshold.value, -60, 0, 1, (value) => {
          masterCompressor.threshold.value = value;
        });
        const ratio = createPluginSlider('Ratio', masterCompressor.ratio.value, 1, 20, 0.5, (value) => {
          masterCompressor.ratio.value = value;
        });
        wrapper.appendChild(threshold);
        wrapper.appendChild(ratio);
        return wrapper;
      },
    },
  ];

  refs.pluginDock.innerHTML = '';
  plugins.forEach((plugin) => {
    const card = document.createElement('div');
    card.className = 'plugin-card';
    const header = document.createElement('header');
    header.innerHTML = `<strong>${plugin.name}</strong><span>${plugin.category}</span>`;
    card.appendChild(header);
    card.appendChild(plugin.render());
    refs.pluginDock.appendChild(card);
  });
}

function createPluginSlider(label, value, min, max, step, onChange) {
  const control = document.createElement('label');
  control.innerHTML = `${label}<input type="range" min="${min}" max="${max}" step="${step}" value="${value}" />`;
  control.querySelector('input').addEventListener('input', (event) => {
    onChange(Number.parseFloat(event.target.value));
  });
  return control;
}

function updateSystemStats() {
  if ('memory' in performance && performance.memory) {
    const memory = performance.memory.usedJSHeapSize / 1024 / 1024;
    state.memoryUsage = memory;
  }
  analyser.getFloatFrequencyData(meterBuffer);
  const max = Math.max(...meterBuffer);
  state.cpu = Math.max(0, Math.min(100, (max + 120) * 1.2));
  refs.memoryUsage.textContent = `RAM ${state.memoryUsage.toFixed(0)} MB`;
  refs.cpuUsage.textContent = `CPU ${state.cpu.toFixed(0)}%`;
  const normalized = Math.min(1, Math.max(0, (max + 120) / 80));
  if (masterMeterFill) {
    masterMeterFill.style.height = `${Math.max(4, normalized * 100)}%`;
  }
  state.tracks.forEach((track) => {
    const meter = trackMeterElements.get(track.id);
    if (!meter) return;
    const volumeNormalized = Math.min(1, Math.max(0, (track.volume + 36) / 42));
    meter.style.height = `${Math.max(4, volumeNormalized * 100)}%`;
  });
}

function refreshAudio() {
  activeSources.forEach((node) => {
    try {
      node.stop();
    } catch {}
  });
  activeSources.clear();
  state.tracks.forEach((track) => ensureTrackNodes(track));
  if (state.isPlaying) {
    play();
  }
}

async function play() {
  await audioContext.resume();
  stop(false);
  const startTime = audioContext.currentTime;
  playStart = state.position;
  state.isPlaying = true;
  refs.playButton.textContent = 'STOP';
  scheduleClips(startTime, playStart);
  updatePlayhead();
}

function stop(reset = false) {
  activeSources.forEach((node) => {
    try {
      node.stop();
    } catch {}
  });
  activeSources.clear();
  state.isPlaying = false;
  refs.playButton.textContent = 'PLAY';
  window.cancelAnimationFrame(animationFrame);
  if (reset) {
    state.position = 0;
    renderPlayhead();
  }
}

function scheduleClips(startTime, offset) {
  const secondsPerBeat = 60 / state.bpm;
  const now = audioContext.currentTime;
  const soloed = state.tracks.find((track) => track.solo);
  state.clips.forEach((clip) => {
    const track = getTrack(clip.trackId);
    if (!track) return;
    if (track.muted) return;
    if (soloed && track.id !== soloed.id) return;
    const nodes = ensureTrackNodes(track);
    const reverbSend = audioContext.createGain();
    reverbSend.gain.value = track.plugins?.reverb ?? 0.15;
    const delaySend = audioContext.createGain();
    delaySend.gain.value = track.plugins?.delay ?? 0.1;
    const distortionSend = audioContext.createGain();
    distortionSend.gain.value = track.plugins?.distortion ?? 0;
    if (clip.kind === 'audio' && clip.buffer) {
      const source = audioContext.createBufferSource();
      source.buffer = clip.buffer;
      source.loop = clip.isLoop;
      source.playbackRate.value = Math.pow(2, (clip.transpose ?? 0) / 12);
      const gain = audioContext.createGain();
      gain.gain.value = dbToGain(clip.gain ?? 0);
      source.connect(gain);
      gain.connect(nodes.input);
      gain.connect(reverbSend).connect(nodes.sendReverb);
      gain.connect(delaySend).connect(nodes.sendDelay);
      gain.connect(distortionSend).connect(nodes.sendDistortion);
      const when = startTime + Math.max(0, clip.start - offset);
      source.start(when, Math.max(0, offset - clip.start));
      activeSources.add(source);
    }
    if (clip.kind === 'midi') {
      clip.notes.forEach((note) => {
        const when = startTime + Math.max(0, note.start - offset);
        const duration = Math.max(0.05, note.duration);
        const gain = audioContext.createGain();
        gain.gain.value = 0.25;
        gain.connect(nodes.input);
        gain.connect(reverbSend).connect(nodes.sendReverb);
        gain.connect(delaySend).connect(nodes.sendDelay);
        gain.connect(distortionSend).connect(nodes.sendDistortion);
        const oscillator = audioContext.createOscillator();
        oscillator.type = instruments[clip.instrument]?.type ?? 'sawtooth';
        oscillator.frequency.setValueAtTime(midiToFreq(note.pitch), when);
        oscillator.connect(gain);
        oscillator.start(when);
        oscillator.stop(when + duration);
        activeSources.add(oscillator);
      });
    }
  });

  if (state.metronomeEnabled) {
    scheduleMetronome(startTime, offset, secondsPerBeat);
  }
}

function scheduleMetronome(startTime, offset, secondsPerBeat) {
  const now = audioContext.currentTime;
  for (let i = 0; i < 64; i++) {
    const beatTime = startTime + i * secondsPerBeat;
    if (beatTime < now) continue;
    metronomeGain.gain.setValueAtTime(0.3, beatTime);
    metronomeGain.gain.exponentialRampToValueAtTime(0.001, beatTime + 0.1);
  }
}

function updatePlayhead() {
  if (!state.isPlaying) return;
  const elapsed = audioContext.currentTime - playStart + 0.0001;
  state.position = playStart + elapsed;
  renderPlayhead();
  animationFrame = window.requestAnimationFrame(updatePlayhead);
  if (state.loop.enabled && state.position > state.loop.end) {
    state.position = state.loop.start;
    play();
    return;
  }
  const projectLength = getTotalDuration();
  if (!state.loop.enabled && state.position >= projectLength) {
    stop(true);
  }
}

async function exportMixdown(format) {
  const duration = getTotalDuration();
  const offline = new OfflineAudioContext(2, Math.ceil(duration * 44100), 44100);
  const gain = offline.createGain();
  gain.gain.value = masterGain.gain.value;
  gain.connect(offline.destination);

  state.clips.forEach((clip) => {
    const track = getTrack(clip.trackId);
    if (!track) return;
    const start = clip.start;
    if (clip.kind === 'audio' && clip.buffer) {
      const source = offline.createBufferSource();
      source.buffer = clip.buffer;
      source.loop = clip.isLoop;
      source.connect(gain);
      source.start(start);
    }
    if (clip.kind === 'midi') {
      clip.notes.forEach((note) => {
        const osc = offline.createOscillator();
        const env = offline.createGain();
        osc.type = instruments[clip.instrument]?.type ?? 'sawtooth';
        osc.frequency.value = midiToFreq(note.pitch);
        env.gain.setValueAtTime(0.2, note.start);
        env.gain.exponentialRampToValueAtTime(0.001, note.start + note.duration);
        osc.connect(env).connect(gain);
        osc.start(note.start);
        osc.stop(note.start + note.duration);
      });
    }
  });

  const rendered = await offline.startRendering();
  if (format === 'wav') {
    const blob = audioBufferToWave(rendered);
    triggerDownload(blob, `${state.projectName.replace(/\s+/g, '-')}.wav`);
    return;
  }
  if (format === 'mp3' && window.lamejs) {
    const blob = audioBufferToMp3(rendered);
    triggerDownload(blob, `${state.projectName.replace(/\s+/g, '-')}.mp3`);
  }
}

function audioBufferToWave(buffer) {
  const numOfChannels = buffer.numberOfChannels;
  const sampleRate = buffer.sampleRate;
  const formatLength = buffer.length * numOfChannels * 2;
  const bufferLength = 44 + formatLength;
  const arrayBuffer = new ArrayBuffer(bufferLength);
  const view = new DataView(arrayBuffer);
  writeString(view, 0, 'RIFF');
  view.setUint32(4, 36 + formatLength, true);
  writeString(view, 8, 'WAVE');
  writeString(view, 12, 'fmt ');
  view.setUint32(16, 16, true);
  view.setUint16(20, 1, true);
  view.setUint16(22, numOfChannels, true);
  view.setUint32(24, sampleRate, true);
  view.setUint32(28, sampleRate * numOfChannels * 2, true);
  view.setUint16(32, numOfChannels * 2, true);
  view.setUint16(34, 16, true);
  writeString(view, 36, 'data');
  view.setUint32(40, formatLength, true);
  const interleaved = interleave(buffer);
  floatTo16BitPCM(view, 44, interleaved);
  return new Blob([view], { type: 'audio/wav' });
}

function audioBufferToMp3(buffer) {
  const samples = buffer.getChannelData(0);
  const mp3Encoder = new lamejs.Mp3Encoder(1, buffer.sampleRate, 128);
  const sampleBlockSize = 1152;
  const chunks = [];
  for (let i = 0; i < samples.length; i += sampleBlockSize) {
    const slice = samples.subarray(i, i + sampleBlockSize);
    const mp3buf = mp3Encoder.encodeBuffer(floatTo16Bit(slice));
    if (mp3buf.length > 0) {
      chunks.push(new Int8Array(mp3buf));
    }
  }
  const mp3buf = mp3Encoder.flush();
  if (mp3buf.length > 0) {
    chunks.push(new Int8Array(mp3buf));
  }
  return new Blob(chunks, { type: 'audio/mpeg' });
}

function floatTo16Bit(float32Array) {
  const buffer = new Int16Array(float32Array.length);
  for (let i = 0; i < float32Array.length; i++) {
    const s = Math.max(-1, Math.min(1, float32Array[i]));
    buffer[i] = s < 0 ? s * 0x8000 : s * 0x7fff;
  }
  return buffer;
}

function interleave(buffer) {
  const numberOfChannels = buffer.numberOfChannels;
  const length = buffer.length * numberOfChannels;
  const result = new Float32Array(length);
  let index = 0;
  const channels = [];
  for (let channel = 0; channel < numberOfChannels; channel++) {
    channels.push(buffer.getChannelData(channel));
  }
  for (let i = 0; i < buffer.length; i++) {
    for (let channel = 0; channel < numberOfChannels; channel++) {
      result[index++] = channels[channel][i];
    }
  }
  return result;
}

function floatTo16BitPCM(output, offset, input) {
  for (let i = 0; i < input.length; i++, offset += 2) {
    let s = Math.max(-1, Math.min(1, input[i]));
    s = s < 0 ? s * 0x8000 : s * 0x7fff;
    output.setInt16(offset, s, true);
  }
}

function writeString(view, offset, string) {
  for (let i = 0; i < string.length; i++) {
    view.setUint8(offset + i, string.charCodeAt(i));
  }
}

function triggerDownload(blob, fileName) {
  const url = URL.createObjectURL(blob);
  const anchor = document.createElement('a');
  anchor.href = url;
  anchor.download = fileName;
  anchor.click();
  URL.revokeObjectURL(url);
}

function setupDragAndDrop() {
  const scrollArea = refs.timelineScroll;
  scrollArea.addEventListener('dragover', (event) => {
    event.preventDefault();
  });
  scrollArea.addEventListener('drop', async (event) => {
    event.preventDefault();
    if (!event.dataTransfer?.files?.length) return;
    const file = Array.from(event.dataTransfer.files).find((candidate) => candidate.type.startsWith('audio/'));
    if (!file) return;
    if (!state.tracks.length) createTrack('audio');
    const trackId = state.selectedTrackId ?? state.tracks[0].id;
    const arrayBuffer = await file.arrayBuffer();
    const audioBuffer = await audioContext.decodeAudioData(arrayBuffer);
    await addAudioClip(trackId, file, audioBuffer, state.position);
  });
}

function midiToFreq(midi) {
  return 440 * Math.pow(2, (midi - 69) / 12);
}

function init() {
  setupDragAndDrop();
  refs.timelineGrid.addEventListener('click', (event) => {
    if (event.target === refs.timelineGrid || event.target === refs.trackRows) {
      selectClip(null);
    }
  });
  createTrack('audio');
  createTrack('instrument', 'piano');
  renderAll();
  window.setInterval(updateSystemStats, 1200);
}

init();
