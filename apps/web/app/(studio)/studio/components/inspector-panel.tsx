'use client';

import { useMemo } from 'react';
import clsx from 'clsx';
import { Slider } from '@musicdistro/ui';
import { useProjectStore } from '@/stores/project-store';
import { useTransportStore } from '@/stores/transport-store';
import type { Clip, MidiClip, MidiNote, Track } from '@musicdistro/types';

const INSTRUMENT_OPTIONS = [
  { id: 'analog', label: 'Analog Synth' },
  { id: 'piano', label: 'Studio Piano' },
  { id: 'drumkit', label: 'Drum Machine' },
] as const;

const NOTE_ROWS = [
  { name: 'C5', midi: 72 },
  { name: 'B4', midi: 71 },
  { name: 'A4', midi: 69 },
  { name: 'G4', midi: 67 },
  { name: 'F4', midi: 65 },
  { name: 'E4', midi: 64 },
  { name: 'D4', midi: 62 },
  { name: 'C4', midi: 60 },
];

export function InspectorPanel() {
  const tracks = useProjectStore((state) => state.tracks);
  const clips = useProjectStore((state) => state.clips);
  const selectedTrackId = useProjectStore((state) => state.selectedTrackId);
  const selectedClipIds = useProjectStore((state) => state.selectedClipIds);
  const renameTrack = useProjectStore((state) => state.renameTrack);
  const renameClip = useProjectStore((state) => state.renameClip);
  const updateClipSettings = useProjectStore((state) => state.updateClipSettings);
  const updateClipNotes = useProjectStore((state) => state.updateClipNotes);
  const setTrackInstrument = useProjectStore((state) => state.setTrackInstrument);
  const setTrackVolume = useProjectStore((state) => state.setTrackVolume);
  const setTrackPan = useProjectStore((state) => state.setTrackPan);
  const { bpm } = useTransportStore();

  const track = tracks.find((candidate) => candidate.id === selectedTrackId) ?? null;
  const clip = useMemo(() => {
    if (!selectedClipIds.length) return null;
    return clips.find((candidate) => candidate.id === selectedClipIds[0]) ?? null;
  }, [clips, selectedClipIds]);

  const secondsPerBeat = 60 / bpm;

  return (
    <div className="flex h-full flex-col gap-6">
      <section>
        <h3 className="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Track</h3>
        {track ? <TrackInspector track={track} onRename={renameTrack} onInstrument={setTrackInstrument} onVolume={setTrackVolume} onPan={setTrackPan} /> : <EmptyPlaceholder message="Select a track to edit its settings." />}
      </section>
      <section>
        <h3 className="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Clip</h3>
        {clip ? (
          <ClipInspector
            clip={clip}
            onRename={renameClip}
            onUpdate={updateClipSettings}
            onEditNotes={updateClipNotes}
            onInstrumentAssign={setTrackInstrument}
            track={track}
            secondsPerBeat={secondsPerBeat}
          />
        ) : (
          <EmptyPlaceholder message="Choose a clip to access editing controls." />
        )}
      </section>
    </div>
  );
}

interface TrackInspectorProps {
  track: Track;
  onRename: (trackId: string, name: string) => void;
  onInstrument: (trackId: string, instrument: Track['instrument']) => void;
  onVolume: (trackId: string, volume: number) => void;
  onPan: (trackId: string, pan: number) => void;
}

function TrackInspector({ track, onRename, onInstrument, onVolume, onPan }: TrackInspectorProps) {
  return (
    <div className="mt-4 space-y-4 rounded-2xl bg-studio-panel/80 p-4 text-slate-200">
      <label className="flex flex-col gap-2 text-sm">
        <span className="text-[11px] uppercase tracking-[0.3em] text-slate-400">Name</span>
        <input
          className="rounded-lg border border-white/10 bg-black/30 px-3 py-2 text-sm text-white focus:border-emerald-400 focus:outline-none"
          value={track.name}
          onChange={(event) => onRename(track.id, event.target.value)}
        />
      </label>
      {track.type !== 'audio' ? (
        <label className="flex flex-col gap-2 text-sm">
          <span className="text-[11px] uppercase tracking-[0.3em] text-slate-400">Instrument</span>
          <select
            className="rounded-lg border border-white/10 bg-black/30 px-3 py-2 text-sm text-white focus:border-emerald-400 focus:outline-none"
            value={track.instrument}
            onChange={(event) => onInstrument(track.id, event.target.value as Track['instrument'])}
          >
            {INSTRUMENT_OPTIONS.map((instrument) => (
              <option key={instrument.id} value={instrument.id}>
                {instrument.label}
              </option>
            ))}
          </select>
        </label>
      ) : null}
      <div className="grid grid-cols-2 gap-4">
        <Slider label="Volume" min={-36} max={6} step={0.1} value={track.volume} onChange={(value) => onVolume(track.id, value)} />
        <Slider label="Pan" min={-1} max={1} step={0.01} value={track.pan} onChange={(value) => onPan(track.id, value)} />
      </div>
    </div>
  );
}

interface ClipInspectorProps {
  clip: Clip;
  onRename: (clipId: string, name: string) => void;
  onUpdate: (clipId: string, data: Partial<Clip>) => void;
  onEditNotes: (clipId: string, notes: MidiNote[]) => void;
  onInstrumentAssign: (trackId: string, instrument: Track['instrument']) => void;
  track: Track | null;
  secondsPerBeat: number;
}

function ClipInspector({ clip, onRename, onUpdate, onEditNotes, track, onInstrumentAssign, secondsPerBeat }: ClipInspectorProps) {
  const duration = clip.end - clip.start;
  return (
    <div className="mt-4 space-y-4 rounded-2xl bg-studio-panel/80 p-4 text-slate-200">
      <label className="flex flex-col gap-2 text-sm">
        <span className="text-[11px] uppercase tracking-[0.3em] text-slate-400">Name</span>
        <input
          className="rounded-lg border border-white/10 bg-black/30 px-3 py-2 text-sm text-white focus:border-emerald-400 focus:outline-none"
          value={clip.name}
          onChange={(event) => onRename(clip.id, event.target.value)}
        />
      </label>
      <div className="grid grid-cols-2 gap-4 text-xs text-slate-400">
        <div>
          <span className="font-semibold uppercase tracking-[0.3em]">Start</span>
          <p className="mt-1 font-mono text-sm text-emerald-300">{clip.start.toFixed(2)} s</p>
        </div>
        <div>
          <span className="font-semibold uppercase tracking-[0.3em]">Length</span>
          <p className="mt-1 font-mono text-sm text-emerald-300">{duration.toFixed(2)} s</p>
        </div>
      </div>
      {clip.kind === 'audio' ? (
        <div className="grid grid-cols-2 gap-4">
          <Slider label="Gain" min={-24} max={6} step={0.5} value={clip.gain} onChange={(value) => onUpdate(clip.id, { gain: value })} />
          <Slider
            label="Transpose"
            min={-12}
            max={12}
            step={1}
            value={clip.transpose}
            onChange={(value) => onUpdate(clip.id, { transpose: value })}
          />
        </div>
      ) : null}
      {clip.kind === 'midi' ? (
        <MidiEditor
          clip={clip}
          onInstrumentChange={(instrument) => {
            onUpdate(clip.id, { instrument });
            if (track) {
              onInstrumentAssign(track.id, instrument);
            }
          }}
          onNotesChange={(notes) => onEditNotes(clip.id, notes)}
          secondsPerBeat={secondsPerBeat}
        />
      ) : null}
    </div>
  );
}

interface MidiEditorProps {
  clip: MidiClip;
  onInstrumentChange: (instrument: MidiClip['instrument']) => void;
  onNotesChange: (notes: MidiNote[]) => void;
  secondsPerBeat: number;
}

function MidiEditor({ clip, onInstrumentChange, onNotesChange, secondsPerBeat }: MidiEditorProps) {
  const steps = 16;
  const stepDuration = (clip.end - clip.start) / steps;

  const toggleNote = (midi: number, stepIndex: number) => {
    const start = clip.start + stepIndex * stepDuration;
    const existing = clip.notes.find((note) => note.pitch === midi && Math.abs(note.start - start) < stepDuration / 2);
    if (existing) {
      onNotesChange(clip.notes.filter((note) => note !== existing));
      return;
    }
    const newNote: MidiNote = {
      id: `${clip.id}-${midi}-${stepIndex}`,
      pitch: midi,
      velocity: 100,
      start,
      duration: stepDuration,
    };
    onNotesChange([...clip.notes, newNote]);
  };

  return (
    <div className="space-y-4">
      <label className="flex flex-col gap-2 text-sm">
        <span className="text-[11px] uppercase tracking-[0.3em] text-slate-400">Instrument</span>
        <select
          className="rounded-lg border border-white/10 bg-black/30 px-3 py-2 text-sm text-white focus:border-emerald-400 focus:outline-none"
          value={clip.instrument}
          onChange={(event) => onInstrumentChange(event.target.value as MidiClip['instrument'])}
        >
          {INSTRUMENT_OPTIONS.map((instrument) => (
            <option key={instrument.id} value={instrument.id}>
              {instrument.label}
            </option>
          ))}
        </select>
      </label>
      <div className="rounded-2xl border border-white/10 bg-black/20 p-3">
        <div className="grid" style={{ gridTemplateColumns: `repeat(${steps}, minmax(0, 1fr))` }}>
          {NOTE_ROWS.map((row) => (
            <div key={row.midi} className="contents">
              {Array.from({ length: steps }, (_, column) => {
                const start = clip.start + column * stepDuration;
                const active = clip.notes.some((note) => note.pitch === row.midi && Math.abs(note.start - start) < stepDuration / 2);
                return (
                  <button
                    key={`${row.midi}-${column}`}
                    type="button"
                    className={clsx(
                      'flex h-8 items-center justify-center border border-white/5 text-[10px] font-semibold',
                      active ? 'bg-emerald-400/60 text-slate-900' : column % 4 === 0 ? 'bg-slate-800/70 text-slate-500' : 'bg-slate-900/40 text-slate-600'
                    )}
                    onClick={() => toggleNote(row.midi, column)}
                  >
                    {column === 0 ? row.name : ''}
                  </button>
                );
              })}
            </div>
          ))}
        </div>
      </div>
      <div className="rounded-xl bg-black/20 p-3 text-[11px] uppercase tracking-[0.3em] text-slate-500">
        <p>Notes {clip.notes.length}</p>
        <p className="mt-2 text-emerald-300">Step: {(stepDuration / secondsPerBeat).toFixed(2)} beats</p>
      </div>
    </div>
  );
}

function EmptyPlaceholder({ message }: { message: string }) {
  return <p className="mt-4 rounded-2xl border border-dashed border-white/10 bg-black/10 p-4 text-sm text-slate-500">{message}</p>;
}

export default InspectorPanel;
