'use client';

import { useEffect, useState } from 'react';
import clsx from 'clsx';
import { Button, Slider } from '@musicdistro/ui';
import { useTransportStore } from '@/stores/transport-store';

const GRID_OPTIONS = ['1/1', '1/2', '1/4', '1/8', '1/16'] as const;

export function TransportControls() {
  const bpm = useTransportStore((state) => state.bpm);
  const grid = useTransportStore((state) => state.grid);
  const loop = useTransportStore((state) => state.loop);
  const metronomeEnabled = useTransportStore((state) => state.metronomeEnabled);
  const isPlaying = useTransportStore((state) => state.isPlaying);
  const togglePlay = useTransportStore((state) => state.togglePlay);
  const stop = useTransportStore((state) => state.stop);
  const setBpm = useTransportStore((state) => state.setBpm);
  const setGrid = useTransportStore((state) => state.setGrid);
  const setLoop = useTransportStore((state) => state.setLoop);
  const setMetronome = useTransportStore((state) => state.setMetronome);

  const [bpmDraft, setBpmDraft] = useState(() => bpm);

  useEffect(() => {
    setBpmDraft(bpm);
  }, [bpm]);

  return (
    <div className="flex w-full items-center gap-4 rounded-2xl border border-white/5 bg-studio-surface/80 px-6 py-4 text-slate-200 shadow-lg">
      <div className="flex items-center gap-3">
        <Button
          variant={isPlaying ? 'secondary' : 'primary'}
          size="lg"
          onClick={() => {
            void togglePlay();
          }}
        >
          {isPlaying ? 'Pause' : 'Play'}
        </Button>
        <Button
          variant="ghost"
          size="lg"
          onClick={() => {
            void stop();
          }}
        >
          Stop
        </Button>
        <button
          type="button"
          className={clsx(
            'rounded-full border px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.3em] transition',
            loop.enabled ? 'border-emerald-400/80 text-emerald-300' : 'border-white/10 text-slate-400 hover:border-emerald-400/60'
          )}
          onClick={() => setLoop(!loop.enabled)}
        >
          Loop
        </button>
      </div>
      <div className="flex items-center gap-4">
        <div className="flex items-center gap-3">
          <span className="text-[11px] uppercase tracking-[0.3em] text-slate-400">BPM</span>
          <input
            type="number"
            min={40}
            max={240}
            step={0.5}
            value={bpmDraft}
            onChange={(event) => setBpmDraft(Number(event.target.value))}
            onBlur={() => setBpm(bpmDraft)}
            className="w-20 rounded-lg border border-white/10 bg-black/40 px-3 py-2 font-mono text-sm focus:border-emerald-400 focus:outline-none"
          />
        </div>
        <div className="w-48">
          <Slider label="Tempo" min={40} max={240} step={0.5} value={bpm} onChange={setBpm} />
        </div>
      </div>
      <div className="flex items-center gap-4">
        <label className="flex flex-col text-[11px] uppercase tracking-[0.3em] text-slate-400">
          Grid
          <select
            className="mt-1 rounded-lg border border-white/10 bg-black/30 px-3 py-2 text-sm text-white focus:border-emerald-400 focus:outline-none"
            value={grid}
            onChange={(event) => setGrid(event.target.value as (typeof GRID_OPTIONS)[number])}
          >
            {GRID_OPTIONS.map((option) => (
              <option key={option} value={option}>
                {option}
              </option>
            ))}
          </select>
        </label>
        <button
          type="button"
          onClick={() => setMetronome(!metronomeEnabled)}
          className={clsx(
            'rounded-full border px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.3em] transition',
            metronomeEnabled ? 'border-emerald-400/80 text-emerald-300' : 'border-white/10 text-slate-400 hover:border-emerald-400/60'
          )}
        >
          Metronome
        </button>
      </div>
      <div className="ml-auto flex items-center gap-4 text-[11px] uppercase tracking-[0.3em] text-slate-400">
        <span>Loop {loop.enabled ? `${loop.start.toFixed(2)}s - ${loop.end.toFixed(2)}s` : 'Off'}</span>
      </div>
    </div>
  );
}

export default TransportControls;
