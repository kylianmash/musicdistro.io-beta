export type TrackType = 'audio' | 'instrument' | 'midi' | 'bus' | 'master';
export type InstrumentType = 'analog' | 'piano' | 'drumkit';
export type GridResolution = '1/1' | '1/2' | '1/4' | '1/8' | '1/16';

export interface ClipBase {
  id: string;
  name: string;
  trackId: string;
  start: number;
  end: number;
  offset: number;
  color: string;
  isLoop?: boolean;
  muted?: boolean;
}

export interface AudioClip extends ClipBase {
  kind: 'audio';
  fileId: string;
  fadeIn?: number;
  fadeOut?: number;
  warpMode: 'beats' | 'tones' | 'pro';
  gain: number;
  transpose: number;
}

export interface MidiNote {
  id: string;
  pitch: number;
  velocity: number;
  start: number;
  duration: number;
}

export interface MidiClip extends ClipBase {
  kind: 'midi';
  instrument: InstrumentType;
  notes: MidiNote[];
  midiJson?: unknown;
}

export type Clip = AudioClip | MidiClip;

export interface AutomationPoint {
  id: string;
  time: number;
  value: number;
}

export interface AutomationLane {
  id: string;
  parameter: 'volume' | 'pan';
  points: AutomationPoint[];
}

export interface TransportState {
  bpm: number;
  timeSignature: [number, number];
  position: number;
  isPlaying: boolean;
  loop: {
    enabled: boolean;
    start: number;
    end: number;
  };
  grid: GridResolution;
  metronomeEnabled: boolean;
}

export interface MixerChannel {
  id: string;
  trackId: string;
  volume: number;
  pan: number;
  meters: {
    peak: number;
    rms: number;
  };
  sends: Array<{
    busId: string;
    amount: number;
  }>;
}

export interface ProjectSnapshot {
  id: string;
  name: string;
  createdAt: string;
  transport: TransportState;
  tracks: Track[];
  clips: Clip[];
}

export interface Track {
  id: string;
  name: string;
  type: TrackType;
  color: string;
  order: number;
  armed: boolean;
  muted: boolean;
  solo: boolean;
  volume: number;
  pan: number;
  instrument?: InstrumentType;
  plugins: TrackPluginInstance[];
  automation: AutomationLane[];
}

export interface TrackPluginInstance {
  id: string;
  type: string;
  bypassed: boolean;
  params: Record<string, number>;
  presetId?: string;
}

export interface PluginParameterDefinition {
  id: string;
  name: string;
  min: number;
  max: number;
  defaultValue: number;
  step?: number;
  unit?: string;
}

export interface PluginUIProps<TParams extends Record<string, number>> {
  params: TParams;
  setParam: (id: keyof TParams, value: number) => void;
  automationActive?: boolean;
}

import type { ComponentType } from 'react';

export interface PluginDefinition<TParams extends Record<string, number>> {
  id: string;
  name: string;
  category: 'instrument' | 'effect';
  parameters: PluginParameterDefinition[];
  defaultParams: TParams;
  createAudioNode: (context: BaseAudioContext, params: TParams) => Promise<AudioNode> | AudioNode;
  createUI?: () => ComponentType<PluginUIProps<TParams>>;
}

export interface AudioDeviceInfo {
  id: string;
  label: string;
  type: 'input' | 'output';
}

export interface ProjectMetadata {
  id: string;
  name: string;
  bpm: number;
  timeSignature: string;
  lastUpdated: string;
}

export interface LoopLibraryItem {
  id: string;
  name: string;
  bpm: number;
  key: string;
  tags: string[];
  url: string;
  previewUrl: string;
  waveformData: number[];
}

export interface UserProfile {
  id: string;
  email: string;
  displayName: string;
}

export interface ProjectDocument {
  id: string;
  userId: string;
  name: string;
  description?: string;
  transport: TransportState;
  tracks: Track[];
  clips: Clip[];
  createdAt: string;
  updatedAt: string;
}

export type ThemeMode = 'dark' | 'light';
