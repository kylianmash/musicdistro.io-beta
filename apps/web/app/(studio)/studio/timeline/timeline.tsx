'use client';

import { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { useDropzone } from 'react-dropzone';
import clsx from 'clsx';
import { useProjectStore } from '@/stores/project-store';
import { useTransportStore } from '@/stores/transport-store';
import type { Clip } from '@musicdistro/types';
import { audioEngine } from '@musicdistro/audio-engine';
import { Button, Slider } from '@musicdistro/ui';

const GRID_DIVISIONS: Record<string, number> = {
  '1/1': 4,
  '1/2': 2,
  '1/4': 1,
  '1/8': 0.5,
  '1/16': 0.25,
};

interface DragState {
  clipId: string;
  type: 'move' | 'resize-start' | 'resize-end';
  originX: number;
  originStart: number;
  originEnd: number;
}

interface LoopDragState {
  originX: number;
  start: number;
}

function quantize(seconds: number, bpm: number, grid: keyof typeof GRID_DIVISIONS) {
  const secondsPerBeat = 60 / bpm;
  const quantum = (GRID_DIVISIONS[grid] ?? 1) * secondsPerBeat;
  return Math.max(0, Math.round(seconds / quantum) * quantum);
}

const TRACK_HEIGHT = 96;

export default function Timeline() {
  const containerRef = useRef<HTMLDivElement | null>(null);
  const [horizontalZoom, setHorizontalZoom] = useState(1);
  const [verticalZoom, setVerticalZoom] = useState(1);
  const [dragState, setDragState] = useState<DragState | null>(null);
  const [loopDrag, setLoopDrag] = useState<LoopDragState | null>(null);
  const [hoverLine, setHoverLine] = useState<number | null>(null);

  const tracks = useProjectStore((state) => state.tracks);
  const clips = useProjectStore((state) => state.clips);
  const selectedClipIds = useProjectStore((state) => state.selectedClipIds);
  const selectedTrackId = useProjectStore((state) => state.selectedTrackId);
  const createTrack = useProjectStore((state) => state.createTrack);
  const addAudioClip = useProjectStore((state) => state.addAudioClip);
  const addMidiClip = useProjectStore((state) => state.addMidiClip);
  const updateClipPosition = useProjectStore((state) => state.updateClipPosition);
  const duplicateClip = useProjectStore((state) => state.duplicateClip);
  const deleteClip = useProjectStore((state) => state.deleteClip);
  const setClipLoop = useProjectStore((state) => state.setClipLoop);
  const selectClips = useProjectStore((state) => state.selectClips);
  const selectTrack = useProjectStore((state) => state.selectTrack);
  const setTrackVolume = useProjectStore((state) => state.setTrackVolume);
  const setTrackPan = useProjectStore((state) => state.setTrackPan);
  const toggleMute = useProjectStore((state) => state.toggleTrackMute);
  const toggleSolo = useProjectStore((state) => state.toggleTrackSolo);

  const { bpm, grid, loop, setLoop, setPosition } = useTransportStore();

  const pxPerSecond = 120 * horizontalZoom;
  const secondsPerBeat = 60 / bpm;
  const secondsPerBar = secondsPerBeat * 4;
  const trackHeight = TRACK_HEIGHT * verticalZoom;

  const projectLength = useMemo(() => {
    if (!clips.length) return 32 * secondsPerBeat;
    return Math.max(32 * secondsPerBeat, Math.max(...clips.map((clip) => clip.end)) + secondsPerBar);
  }, [clips, secondsPerBeat, secondsPerBar]);

  const bars = useMemo(() => {
    const barCount = Math.ceil(projectLength / secondsPerBar);
    return Array.from({ length: barCount }, (_, index) => index + 1);
  }, [projectLength, secondsPerBar]);

  const onDrop = useCallback(
    async (acceptedFiles: File[], _fileRejections: unknown, event: DropEvent) => {
      if (!acceptedFiles.length || !tracks.length) return;
      const [file] = acceptedFiles;
      const url = URL.createObjectURL(file);
      const duration = await decodeDuration(file);

      let targetTrackId = tracks[0]?.id;
      if ('clientY' in event && containerRef.current) {
        const rect = containerRef.current.getBoundingClientRect();
        const relativeY = event.clientY - rect.top + containerRef.current.scrollTop - 40;
        const index = Math.floor(relativeY / trackHeight);
        if (tracks[index]) {
          targetTrackId = tracks[index].id;
        }
      }

      if (targetTrackId) {
        addAudioClip(targetTrackId, url, duration, file.name);
        audioEngine.setTransport({});
      }
    },
    [tracks, trackHeight, addAudioClip]
  );

  const { getInputProps, getRootProps, isDragActive } = useDropzone({
    onDrop,
    accept: { 'audio/*': [] },
    noClick: true,
  });

  const [bootstrapped, setBootstrapped] = useState(false);

  useEffect(() => {
    if (tracks.length === 0 && !bootstrapped) {
      createTrack('audio');
      createTrack('instrument', 'piano');
      setBootstrapped(true);
    } else if (tracks.length > 0 && !bootstrapped) {
      setBootstrapped(true);
    }
  }, [tracks.length, createTrack, bootstrapped]);

  useEffect(() => {
    if (!dragState) return;
    const handleMove = (event: PointerEvent) => {
      const deltaSeconds = (event.clientX - dragState.originX) / pxPerSecond;
      const clip = clips.find((c) => c.id === dragState.clipId);
      if (!clip) return;
      const quantizedDelta = quantize(deltaSeconds + dragState.originStart, bpm, grid) - dragState.originStart;
      const minLength = secondsPerBeat * 0.25;
      if (dragState.type === 'move') {
        const newStart = Math.max(0, dragState.originStart + quantizedDelta);
        const newEnd = Math.max(newStart + minLength, dragState.originEnd + quantizedDelta);
        updateClipPosition(dragState.clipId, newStart, newEnd);
      }
      if (dragState.type === 'resize-start') {
        const newStart = Math.min(dragState.originEnd - minLength, Math.max(0, dragState.originStart + quantizedDelta));
        updateClipPosition(dragState.clipId, newStart, dragState.originEnd);
      }
      if (dragState.type === 'resize-end') {
        const newEnd = Math.max(dragState.originStart + minLength, dragState.originEnd + quantizedDelta);
        updateClipPosition(dragState.clipId, dragState.originStart, newEnd);
      }
    };
    const handleUp = () => setDragState(null);
    window.addEventListener('pointermove', handleMove);
    window.addEventListener('pointerup', handleUp, { once: true });
    return () => {
      window.removeEventListener('pointermove', handleMove);
      window.removeEventListener('pointerup', handleUp);
    };
  }, [dragState, pxPerSecond, clips, bpm, grid, updateClipPosition, secondsPerBeat]);

  useEffect(() => {
    if (!loopDrag) return;
    const handleMove = (event: PointerEvent) => {
      if (!containerRef.current) return;
      const rect = containerRef.current.getBoundingClientRect();
      const seconds = (event.clientX - rect.left + containerRef.current.scrollLeft) / pxPerSecond;
      const quantized = quantize(seconds, bpm, grid);
      const start = Math.min(loopDrag.start, quantized);
      const end = Math.max(loopDrag.start, quantized);
      setLoop(true, start, end);
    };
    const handleUp = () => setLoopDrag(null);
    window.addEventListener('pointermove', handleMove);
    window.addEventListener('pointerup', handleUp, { once: true });
    return () => {
      window.removeEventListener('pointermove', handleMove);
      window.removeEventListener('pointerup', handleUp);
    };
  }, [loopDrag, bpm, grid, pxPerSecond, setLoop]);

  const handleBackgroundClick = () => {
    selectClips([]);
    selectTrack(null);
  };

  const renderClip = (clip: Clip) => {
    const left = clip.start * pxPerSecond;
    const width = Math.max((clip.end - clip.start) * pxPerSecond, 48);
    const isSelected = selectedClipIds.includes(clip.id);
    return (
      <div
        key={clip.id}
        role="button"
        tabIndex={0}
        className={clsx(
          'group absolute top-2 h-[72px] rounded-lg border border-white/10 text-xs font-semibold shadow-lg transition',
          isSelected ? 'ring-2 ring-emerald-400/80' : 'ring-1 ring-transparent'
        )}
        style={{
          left,
          width,
          background: `${clip.color}22`,
          borderColor: clip.color,
        }}
        onClick={(event) => {
          event.stopPropagation();
          selectClips([clip.id]);
          selectTrack(clip.trackId);
        }}
        onDoubleClick={(event) => {
          event.stopPropagation();
          if (clip.kind === 'midi') {
            selectClips([clip.id]);
            selectTrack(clip.trackId);
          }
        }}
      >
        <div className="flex h-full items-center justify-between px-3 text-slate-200">
          <span className="truncate text-sm">{clip.name}</span>
          <span className="font-mono text-[11px] text-slate-300">{(clip.end - clip.start).toFixed(2)}s</span>
        </div>
        <div className="absolute bottom-2 right-3 flex gap-2 text-[10px] font-semibold uppercase tracking-[0.3em] text-slate-300">
          <button
            type="button"
            className={clsx(
              'rounded-full px-2 py-1 transition',
              clip.isLoop ? 'bg-emerald-500/30 text-emerald-200' : 'bg-black/30 hover:bg-black/50'
            )}
            onClick={(event) => {
              event.stopPropagation();
              setClipLoop(clip.id, !clip.isLoop);
            }}
          >
            Loop
          </button>
        </div>
        <div
          className="absolute inset-y-0 left-0 w-2 cursor-ew-resize rounded-l-lg bg-black/10 opacity-0 transition group-hover:opacity-100"
          onPointerDown={(event) => {
            event.stopPropagation();
            setDragState({
              clipId: clip.id,
              type: 'resize-start',
              originX: event.clientX,
              originStart: clip.start,
              originEnd: clip.end,
            });
          }}
        />
        <div
          className="absolute inset-y-0 right-0 w-2 cursor-ew-resize rounded-r-lg bg-black/10 opacity-0 transition group-hover:opacity-100"
          onPointerDown={(event) => {
            event.stopPropagation();
            setDragState({
              clipId: clip.id,
              type: 'resize-end',
              originX: event.clientX,
              originStart: clip.start,
              originEnd: clip.end,
            });
          }}
        />
        <div
          className="absolute inset-0 cursor-grab"
          onPointerDown={(event) => {
            event.stopPropagation();
            setDragState({
              clipId: clip.id,
              type: 'move',
              originX: event.clientX,
              originStart: clip.start,
              originEnd: clip.end,
            });
          }}
        />
      </div>
    );
  };

  return (
    <div className="relative flex h-full w-full flex-col" {...getRootProps()}>
      <input {...getInputProps()} />
      <div className="flex items-center justify-between border-b border-white/5 bg-studio-surface/60 px-6 py-3 text-xs uppercase tracking-[0.3em] text-slate-400">
        <div className="flex items-center gap-3 text-slate-200">
          <Button variant="secondary" size="sm" onClick={() => createTrack('audio')} type="button">
            + Audio Track
          </Button>
          <Button variant="ghost" size="sm" onClick={() => createTrack('instrument', 'analog')} type="button">
            + Instrument
          </Button>
          <Button variant="ghost" size="sm" onClick={() => selectedTrackId && addMidiClip(selectedTrackId, 0, 4)} disabled={!selectedTrackId} type="button">
            + MIDI Clip
          </Button>
          <span className="text-[11px] text-slate-400">Drop audio files (WAV / MP3)</span>
        </div>
        <div className="flex items-center gap-5 text-[11px] text-slate-400">
          <span>BPM {bpm.toFixed(1)}</span>
          <span>Grid {grid}</span>
          <span>Loop {loop.enabled ? `${loop.start.toFixed(1)}s - ${loop.end.toFixed(1)}s` : 'Off'}</span>
        </div>
      </div>
      <div className="flex flex-1 overflow-hidden">
        <aside className="w-64 border-r border-white/5 bg-studio-surface/70">
          <div className="flex items-center justify-between px-4 py-3 text-[11px] uppercase tracking-[0.3em] text-slate-500">
            <span>Tracks</span>
            <span>{tracks.length}</span>
          </div>
          <div className="space-y-2 p-4">
            {tracks.map((track) => (
              <button
                key={track.id}
                type="button"
                onClick={() => selectTrack(track.id)}
                className={clsx(
                  'w-full rounded-xl border border-white/5 bg-studio-surface/60 p-3 text-left transition hover:border-emerald-400/70',
                  selectedTrackId === track.id && 'border-emerald-400/60 bg-emerald-400/10'
                )}
              >
                <div className="flex items-center justify-between text-sm text-white">
                  <span>{track.name}</span>
                  <span className="text-[10px] uppercase tracking-[0.3em] text-emerald-300">{track.type}</span>
                </div>
                <div className="mt-3 grid grid-cols-2 gap-3">
                  <Slider
                    label="VOL"
                    min={-36}
                    max={6}
                    step={0.1}
                    value={track.volume}
                    onChange={(value) => setTrackVolume(track.id, value)}
                  />
                  <Slider label="PAN" min={-1} max={1} step={0.01} value={track.pan} onChange={(value) => setTrackPan(track.id, value)} />
                </div>
                <div className="mt-3 flex items-center gap-2 text-[10px] font-semibold uppercase tracking-[0.4em]">
                  <button
                    type="button"
                    onClick={(event) => {
                      event.stopPropagation();
                      toggleMute(track.id);
                    }}
                    className={clsx(
                      'rounded-full px-2 py-1 transition',
                      track.muted ? 'bg-rose-500/20 text-rose-300' : 'bg-white/5 text-slate-400 hover:text-white'
                    )}
                  >
                    M
                  </button>
                  <button
                    type="button"
                    onClick={(event) => {
                      event.stopPropagation();
                      toggleSolo(track.id);
                    }}
                    className={clsx(
                      'rounded-full px-2 py-1 transition',
                      track.solo ? 'bg-emerald-500/20 text-emerald-300' : 'bg-white/5 text-slate-400 hover:text-white'
                    )}
                  >
                    S
                  </button>
                </div>
              </button>
            ))}
          </div>
        </aside>
        <section className="relative flex-1 overflow-hidden" ref={containerRef}>
          <div
            className={clsx(
              'relative h-full overflow-auto bg-studio-background/95',
              'timeline-grid',
              isDragActive && 'ring-2 ring-emerald-400/70'
            )}
            onClick={handleBackgroundClick}
            onMouseMove={(event) => {
              if (!containerRef.current) return;
              const rect = containerRef.current.getBoundingClientRect();
              setHoverLine(event.clientX - rect.left + containerRef.current.scrollLeft);
            }}
            onMouseLeave={() => setHoverLine(null)}
          >
            <div className="relative min-h-full" style={{ width: projectLength * pxPerSecond + 400 }}>
              <div
                className="sticky top-0 z-20 flex h-12 items-center border-b border-white/10 bg-studio-surface/90 px-4 font-mono text-[11px] text-slate-400"
                onPointerDown={(event) => {
                  if (!containerRef.current) return;
                  const rect = containerRef.current.getBoundingClientRect();
                  const seconds = (event.clientX - rect.left + containerRef.current.scrollLeft) / pxPerSecond;
                  const quantized = quantize(seconds, bpm, grid);
                  setPosition(quantized);
                  setLoopDrag({ originX: event.clientX, start: quantized });
                }}
              >
                {bars.map((bar) => {
                  const left = (bar - 1) * secondsPerBar * pxPerSecond;
                  const width = secondsPerBar * pxPerSecond;
                  return (
                    <div key={bar} className="relative flex items-center" style={{ width }}>
                      <span>Bar {bar}</span>
                      <div className="absolute inset-y-0 right-0 w-px bg-white/5" />
                      <GridLines pxPerSecond={pxPerSecond} secondsPerBeat={secondsPerBeat} grid={grid} />
                    </div>
                  );
                })}
              </div>
              <div className="relative">
                {tracks.map((track, index) => (
                  <div
                    key={track.id}
                    className="relative border-b border-white/5"
                    style={{ height: trackHeight, backgroundColor: index % 2 === 0 ? '#0f172a88' : '#111827aa' }}
                  >
                    {clips.filter((clip) => clip.trackId === track.id).map((clip) => renderClip(clip))}
                  </div>
                ))}
              </div>
              {loop.enabled ? (
                <div
                  className="pointer-events-none absolute top-12 z-10 h-[calc(100%-12px)] bg-emerald-400/10"
                  style={{
                    left: loop.start * pxPerSecond,
                    width: Math.max(2, (loop.end - loop.start) * pxPerSecond),
                  }}
                >
                  <div className="absolute top-0 h-6 w-full border-x border-emerald-400/80 bg-emerald-400/10" />
                </div>
              ) : null}
              {hoverLine !== null ? (
                <div className="pointer-events-none absolute top-0 bottom-0 w-px bg-emerald-400/60" style={{ left: hoverLine }} />
              ) : null}
            </div>
          </div>
        </section>
      </div>
      <div className="pointer-events-none absolute bottom-8 right-8 z-30 flex flex-col gap-3">
        <Button
          className="pointer-events-auto shadow-lg"
          size="lg"
          onClick={() => createTrack('audio')}
          variant="primary"
        >
          Add Track
        </Button>
        <Button
          className="pointer-events-auto shadow-lg"
          size="lg"
          variant="secondary"
          onClick={() => {
            if (selectedClipIds[0]) duplicateClip(selectedClipIds[0]);
          }}
          disabled={!selectedClipIds.length}
        >
          Duplicate Clip
        </Button>
        <Button
          className="pointer-events-auto shadow-lg"
          size="lg"
          variant="ghost"
          onClick={() => {
            if (selectedClipIds[0]) deleteClip(selectedClipIds[0]);
          }}
          disabled={!selectedClipIds.length}
        >
          Delete Clip
        </Button>
      </div>
      <div className="absolute bottom-8 left-8 z-30 flex flex-col gap-3 rounded-2xl bg-studio-surface/80 p-4 shadow-lg">
        <div className="flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[0.4em] text-slate-400">
          <span>Zoom</span>
          <span>{horizontalZoom.toFixed(2)}x</span>
        </div>
        <Slider label="Horizontal" min={0.5} max={3} step={0.1} value={horizontalZoom} onChange={setHorizontalZoom} />
        <Slider label="Vertical" min={0.5} max={2} step={0.1} value={verticalZoom} onChange={setVerticalZoom} />
      </div>
    </div>
  );
}

type DropEvent = Parameters<NonNullable<Parameters<typeof useDropzone>[0]['onDrop']>>[2];

async function decodeDuration(file: File): Promise<number> {
  const arrayBuffer = await file.arrayBuffer();
  const context = new AudioContext();
  const audioBuffer = await context.decodeAudioData(arrayBuffer.slice(0));
  context.close();
  return audioBuffer.duration;
}

interface GridLinesProps {
  pxPerSecond: number;
  secondsPerBeat: number;
  grid: keyof typeof GRID_DIVISIONS;
}

function GridLines({ pxPerSecond, secondsPerBeat, grid }: GridLinesProps) {
  const division = GRID_DIVISIONS[grid] ?? 1;
  const steps = Math.round(4 / division);
  return (
    <div className="absolute inset-0">
      {Array.from({ length: steps - 1 }, (_, index) => {
        const left = (index + 1) * secondsPerBeat * division * pxPerSecond;
        return <div key={index} className="absolute top-0 bottom-0 w-px bg-white/5" style={{ left }} />;
      })}
    </div>
  );
}
