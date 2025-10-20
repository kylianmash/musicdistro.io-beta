'use client';

import { useEffect, useState } from 'react';
import clsx from 'clsx';
import { Slider } from '@musicdistro/ui';
import { useProjectStore } from '@/stores/project-store';
import { audioEngine } from '@musicdistro/audio-engine';

interface MeterValue {
  peak: number;
  rms: number;
}

export function Mixer() {
  const tracks = useProjectStore((state) => state.tracks);
  const setTrackVolume = useProjectStore((state) => state.setTrackVolume);
  const setTrackPan = useProjectStore((state) => state.setTrackPan);
  const toggleTrackMute = useProjectStore((state) => state.toggleTrackMute);
  const toggleTrackSolo = useProjectStore((state) => state.toggleTrackSolo);
  const [masterVolume, setMasterVolume] = useState(1);
  const [meters, setMeters] = useState<Record<string, MeterValue>>({});

  useEffect(() => {
    const interval = setInterval(() => {
      const snapshot = audioEngine.getMixerSnapshot();
      const next: Record<string, MeterValue> = {};
      snapshot.forEach((channel) => {
        next[channel.trackId] = channel.meters;
      });
      setMeters(next);
    }, 200);

    return () => clearInterval(interval);
  }, []);

  return (
    <div className="overflow-x-auto">
      <div className="flex gap-6">
        {tracks.map((track) => {
          const meter = meters[track.id] ?? { peak: track.volume, rms: track.volume - 6 };
          return (
            <div key={track.id} className="flex min-w-[160px] flex-col items-center gap-4 rounded-3xl border border-white/5 bg-studio-panel/70 p-4 text-slate-200">
              <span className="text-sm font-semibold">{track.name}</span>
              <div className="flex flex-col items-center gap-3">
                <div className="h-32 w-4 overflow-hidden rounded-full border border-white/10 bg-black/50">
                  <div
                    className="w-full bg-emerald-400/70"
                    style={{ height: `${Math.min(100, Math.max(0, (meter.peak + 36) * 1.5))}%` }}
                  />
                </div>
                <div className="text-[10px] uppercase tracking-[0.3em] text-slate-400">
                  <p>Peak {meter.peak.toFixed(1)} dB</p>
                </div>
              </div>
              <Slider label="Volume" min={-36} max={6} step={0.5} value={track.volume} onChange={(value) => setTrackVolume(track.id, value)} />
              <Slider label="Pan" min={-1} max={1} step={0.01} value={track.pan} onChange={(value) => setTrackPan(track.id, value)} />
              <div className="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.3em]">
                <button
                  type="button"
                  className={clsx('rounded-full px-2 py-1 transition', track.muted ? 'bg-rose-500/20 text-rose-300' : 'bg-black/30 hover:bg-black/50')}
                  onClick={() => toggleTrackMute(track.id)}
                >
                  M
                </button>
                <button
                  type="button"
                  className={clsx('rounded-full px-2 py-1 transition', track.solo ? 'bg-emerald-500/30 text-emerald-200' : 'bg-black/30 hover:bg-black/50')}
                  onClick={() => toggleTrackSolo(track.id)}
                >
                  S
                </button>
              </div>
            </div>
          );
        })}
        <div className="flex min-w-[160px] flex-col items-center gap-4 rounded-3xl border border-white/5 bg-studio-panel/70 p-4 text-slate-200">
          <span className="text-sm font-semibold">Master</span>
          <Slider
            label="Level"
            min={0}
            max={1.5}
            step={0.01}
            value={masterVolume}
            onChange={(value) => {
              setMasterVolume(value);
              audioEngine.setMasterVolume(value);
            }}
          />
        </div>
      </div>
    </div>
  );
}

export default Mixer;
