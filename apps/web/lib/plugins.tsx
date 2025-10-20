import { Knob, Slider } from '@musicdistro/ui';
import type { PluginDefinition, PluginUIProps } from '@musicdistro/types';
import * as Tone from 'tone';

interface PolySynthParams {
  attack: number;
  decay: number;
  sustain: number;
  release: number;
  cutoff: number;
}

const PolySynthUI = ({ params, setParam }: PluginUIProps<PolySynthParams>) => (
  <div className="grid gap-4 rounded-xl bg-studio-panel/60 p-6">
    <div className="flex items-center justify-between">
      <Knob label="ATT" value={params.attack} min={0.001} max={1} onChange={(value) => setParam('attack', value)} />
      <Knob label="DEC" value={params.decay} min={0.01} max={2} onChange={(value) => setParam('decay', value)} />
      <Knob label="SUS" value={params.sustain} min={0} max={1} onChange={(value) => setParam('sustain', value)} />
      <Knob label="REL" value={params.release} min={0.01} max={4} onChange={(value) => setParam('release', value)} />
    </div>
    <Slider label="CUTOFF" value={params.cutoff} min={200} max={8000} onChange={(value) => setParam('cutoff', value)} />
  </div>
);

export const PolySynthPlugin: PluginDefinition<PolySynthParams> = {
  id: 'polysynth',
  name: 'Poly Synth',
  category: 'instrument',
  parameters: [
    { id: 'attack', name: 'Attack', min: 0.001, max: 1, defaultValue: 0.01 },
    { id: 'decay', name: 'Decay', min: 0.01, max: 2, defaultValue: 0.3 },
    { id: 'sustain', name: 'Sustain', min: 0, max: 1, defaultValue: 0.6 },
    { id: 'release', name: 'Release', min: 0.01, max: 4, defaultValue: 1.2 },
    { id: 'cutoff', name: 'Cutoff', min: 200, max: 8000, defaultValue: 2800 },
  ],
  defaultParams: {
    attack: 0.01,
    decay: 0.3,
    sustain: 0.6,
    release: 1.2,
    cutoff: 2800,
  },
  async createAudioNode(context, params) {
    const synth = new Tone.PolySynth(Tone.Synth).connect(context.destination);
    synth.set({
      envelope: {
        attack: params.attack,
        decay: params.decay,
        sustain: params.sustain,
        release: params.release,
      },
      filter: {
        type: 'lowpass',
        frequency: params.cutoff,
      },
    });
    return synth;
  },
  createUI: () => PolySynthUI,
};

interface EQParams {
  low: number;
  mid: number;
  high: number;
}

const EQUI = ({ params, setParam }: PluginUIProps<EQParams>) => (
  <div className="flex gap-6 rounded-xl bg-studio-panel/60 p-6">
    <Knob label="LOW" value={params.low} min={-12} max={12} onChange={(value) => setParam('low', value)} />
    <Knob label="MID" value={params.mid} min={-12} max={12} onChange={(value) => setParam('mid', value)} />
    <Knob label="HIGH" value={params.high} min={-12} max={12} onChange={(value) => setParam('high', value)} />
  </div>
);

export const EQ8Plugin: PluginDefinition<EQParams> = {
  id: 'eq8',
  name: 'EQ Eight',
  category: 'effect',
  parameters: [
    { id: 'low', name: 'Low', min: -12, max: 12, defaultValue: 0 },
    { id: 'mid', name: 'Mid', min: -12, max: 12, defaultValue: 0 },
    { id: 'high', name: 'High', min: -12, max: 12, defaultValue: 0 },
  ],
  defaultParams: { low: 0, mid: 0, high: 0 },
  async createAudioNode(context, params) {
    const eq = new Tone.EQ3({
      low: params.low,
      mid: params.mid,
      high: params.high,
    }).connect(context.destination);
    return eq;
  },
  createUI: () => EQUI,
};

interface ReverbParams {
  decay: number;
  wet: number;
}

const ReverbUI = ({ params, setParam }: PluginUIProps<ReverbParams>) => (
  <div className="flex gap-6 rounded-xl bg-studio-panel/60 p-6">
    <Slider label="DECAY" value={params.decay} min={0.2} max={8} onChange={(value) => setParam('decay', value)} />
    <Slider label="MIX" value={params.wet} min={0} max={1} onChange={(value) => setParam('wet', value)} />
  </div>
);

export const ReverbPlugin: PluginDefinition<ReverbParams> = {
  id: 'reverb',
  name: 'Aurora Reverb',
  category: 'effect',
  parameters: [
    { id: 'decay', name: 'Decay', min: 0.2, max: 8, defaultValue: 2.5 },
    { id: 'wet', name: 'Wet', min: 0, max: 1, defaultValue: 0.35 },
  ],
  defaultParams: {
    decay: 2.5,
    wet: 0.35,
  },
  async createAudioNode(context, params) {
    const reverb = new Tone.Reverb({ decay: params.decay, wet: params.wet }).connect(context.destination);
    return reverb;
  },
  createUI: () => ReverbUI,
};

interface DelayParams {
  time: number;
  feedback: number;
  wet: number;
}

const DelayUI = ({ params, setParam }: PluginUIProps<DelayParams>) => (
  <div className="flex gap-4 rounded-xl bg-studio-panel/60 p-6">
    <Slider label="Time" min={0.05} max={1} value={params.time} step={0.01} onChange={(value) => setParam('time', value)} />
    <Slider label="Feedback" min={0} max={0.95} value={params.feedback} step={0.01} onChange={(value) => setParam('feedback', value)} />
    <Slider label="Mix" min={0} max={1} value={params.wet} step={0.01} onChange={(value) => setParam('wet', value)} />
  </div>
);

export const DelayPlugin: PluginDefinition<DelayParams> = {
  id: 'delay',
  name: 'EchoSpace',
  category: 'effect',
  parameters: [
    { id: 'time', name: 'Delay', min: 0.05, max: 1, defaultValue: 0.25 },
    { id: 'feedback', name: 'Feedback', min: 0, max: 0.95, defaultValue: 0.3 },
    { id: 'wet', name: 'Mix', min: 0, max: 1, defaultValue: 0.25 },
  ],
  defaultParams: {
    time: 0.25,
    feedback: 0.3,
    wet: 0.25,
  },
  async createAudioNode(context, params) {
    const delay = new Tone.FeedbackDelay({ delayTime: params.time, feedback: params.feedback, wet: params.wet }).connect(context.destination);
    return delay;
  },
  createUI: () => DelayUI,
};

interface CompressorParams {
  threshold: number;
  ratio: number;
  attack: number;
  release: number;
}

const CompressorUI = ({ params, setParam }: PluginUIProps<CompressorParams>) => (
  <div className="grid gap-4 rounded-xl bg-studio-panel/60 p-6">
    <Slider label="Threshold" min={-60} max={0} step={1} value={params.threshold} onChange={(value) => setParam('threshold', value)} />
    <Slider label="Ratio" min={1} max={12} step={0.5} value={params.ratio} onChange={(value) => setParam('ratio', value)} />
    <Slider label="Attack" min={0.001} max={0.3} step={0.001} value={params.attack} onChange={(value) => setParam('attack', value)} />
    <Slider label="Release" min={0.05} max={1} step={0.01} value={params.release} onChange={(value) => setParam('release', value)} />
  </div>
);

export const CompressorPlugin: PluginDefinition<CompressorParams> = {
  id: 'compressor',
  name: 'Dynamics',
  category: 'effect',
  parameters: [
    { id: 'threshold', name: 'Threshold', min: -60, max: 0, defaultValue: -24 },
    { id: 'ratio', name: 'Ratio', min: 1, max: 12, defaultValue: 4 },
    { id: 'attack', name: 'Attack', min: 0.001, max: 0.3, defaultValue: 0.02 },
    { id: 'release', name: 'Release', min: 0.05, max: 1, defaultValue: 0.25 },
  ],
  defaultParams: {
    threshold: -24,
    ratio: 4,
    attack: 0.02,
    release: 0.25,
  },
  async createAudioNode(context, params) {
    const compressor = new Tone.Compressor({
      threshold: params.threshold,
      ratio: params.ratio,
      attack: params.attack,
      release: params.release,
    }).connect(context.destination);
    return compressor;
  },
  createUI: () => CompressorUI,
};

interface DistortionParams {
  amount: number;
  wet: number;
}

const DistortionUI = ({ params, setParam }: PluginUIProps<DistortionParams>) => (
  <div className="flex gap-4 rounded-xl bg-studio-panel/60 p-6">
    <Slider label="Amount" min={0} max={1} step={0.01} value={params.amount} onChange={(value) => setParam('amount', value)} />
    <Slider label="Mix" min={0} max={1} step={0.01} value={params.wet} onChange={(value) => setParam('wet', value)} />
  </div>
);

export const DistortionPlugin: PluginDefinition<DistortionParams> = {
  id: 'distortion',
  name: 'Grit Drive',
  category: 'effect',
  parameters: [
    { id: 'amount', name: 'Amount', min: 0, max: 1, defaultValue: 0.2 },
    { id: 'wet', name: 'Wet', min: 0, max: 1, defaultValue: 0.3 },
  ],
  defaultParams: {
    amount: 0.2,
    wet: 0.3,
  },
  async createAudioNode(context, params) {
    const distortion = new Tone.Distortion(params.amount);
    distortion.wet.value = params.wet;
    distortion.connect(context.destination);
    return distortion;
  },
  createUI: () => DistortionUI,
};

export const builtinPlugins = [PolySynthPlugin, EQ8Plugin, ReverbPlugin, DelayPlugin, CompressorPlugin, DistortionPlugin];
