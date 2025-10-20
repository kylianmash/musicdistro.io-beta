'use client';

import { audioEngine } from '@musicdistro/audio-engine';
import type { GridResolution, TransportState } from '@musicdistro/types';
import { create } from 'zustand';

interface TransportStore extends TransportState {
  togglePlay: () => Promise<void>;
  stop: () => Promise<void>;
  setBpm: (bpm: number) => void;
  setGrid: (grid: GridResolution) => void;
  setLoop: (enabled: boolean, start?: number, end?: number) => void;
  setMetronome: (enabled: boolean) => void;
  setPosition: (position: number) => void;
}

export const useTransportStore = create<TransportStore>((set, get) => ({
  bpm: 120,
  timeSignature: [4, 4],
  position: 0,
  isPlaying: false,
  loop: {
    enabled: false,
    start: 0,
    end: 8,
  },
  grid: '1/16',
  metronomeEnabled: false,
  async togglePlay() {
    const { isPlaying } = get();
    if (isPlaying) {
      await audioEngine.stop();
      set({ isPlaying: false });
    } else {
      await audioEngine.play();
      set({ isPlaying: true });
    }
  },
  async stop() {
    await audioEngine.stop();
    set({ isPlaying: false, position: 0 });
    audioEngine.setTransport({ position: 0 }).catch(() => undefined);
  },
  setBpm(bpm: number) {
    audioEngine.setTransport({ bpm }).catch(() => undefined);
    set({ bpm });
  },
  setGrid(grid) {
    set({ grid });
  },
  setLoop(enabled, start, end) {
    const loop = {
      enabled,
      start: typeof start === 'number' ? start : get().loop.start,
      end: typeof end === 'number' ? end : get().loop.end,
    } as TransportState['loop'];
    set({ loop });
    audioEngine.setTransport({ loop }).catch(() => undefined);
  },
  setMetronome(enabled) {
    audioEngine.setTransport({ metronomeEnabled: enabled }).catch(() => undefined);
    set({ metronomeEnabled: enabled });
  },
  setPosition(position) {
    set({ position });
    audioEngine.setTransport({ position }).catch(() => undefined);
  },
}));
