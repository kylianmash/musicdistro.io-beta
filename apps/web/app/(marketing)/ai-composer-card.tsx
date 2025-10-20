'use client';

import { Dialog, Transition } from '@headlessui/react';
import { Fragment, useMemo, useState } from 'react';

const COMPOSER_ENDPOINT =
  (process.env.NEXT_PUBLIC_BACKEND ?? '').toLowerCase() === 'php' ? '/ai-composer.php' : '/api/ai-music';

interface ComposerResponse {
  ok: boolean;
  provider?: string;
  requestId?: string | null;
  jobId?: string | null;
  status?: string | null;
  previewUrl?: string | null;
  audioUrls?: string[];
  lyrics?: string | null;
  message?: string | null;
  error?: string | null;
  details?: unknown;
  raw?: unknown;
}

interface GenerationResult extends ComposerResponse {
  requestId: string | null;
  jobId: string | null;
  previewUrl: string | null;
  audioUrls?: string[];
  lyrics?: string | null;
  message?: string | null;
  error?: string | null;
}

function mapComposerResponse(payload: ComposerResponse): GenerationResult {
  const requestId = payload.requestId ?? payload.jobId ?? null;
  const jobId = payload.jobId ?? requestId;
  const audioUrls = Array.isArray(payload.audioUrls) ? payload.audioUrls.filter((url) => typeof url === 'string' && url) : [];
  const previewUrlCandidate = payload.previewUrl ?? (audioUrls.length > 0 ? audioUrls[0] ?? null : null);

  return {
    ok: payload.ok,
    provider: payload.provider,
    requestId,
    jobId,
    status: payload.status ?? null,
    previewUrl: previewUrlCandidate ?? null,
    audioUrls: audioUrls.length > 0 ? audioUrls : undefined,
    lyrics: payload.lyrics ?? null,
    message: payload.message ?? (payload.ok ? null : payload.error ?? null),
    error: payload.error ?? null,
    details: payload.details,
    raw: payload.raw,
  };
}

function buildStyleDescriptor(...sections: Array<string | undefined>) {
  return sections
    .map((value) => value?.trim())
    .filter((value): value is string => Boolean(value && value.length > 0))
    .join(' | ');
}

interface DebugEntry {
  stage: string;
  timestamp: string;
  status?: number;
  ok?: boolean;
  message?: string;
  payload?: unknown;
  error?: string;
}

const STYLE_PRESETS = [
  {
    id: 'hyperpop-euphoria',
    name: 'Hyperpop Euphoria',
    description: 'Crystalline leads, glitchy drums, euphoric drops.',
    prompt: 'Explosive hyperpop anthem with shimmering synth leads, glitch drums, and euphoric chorus energy.',
  },
  {
    id: 'afrobeats-sunset',
    name: 'Afrobeats Sunset',
    description: 'Warm guitars, syncopated percussion, velvet bass.',
    prompt: 'Afrobeats groove with palm-muted guitars, syncopated percussion, and a velvet midnight bassline.',
  },
  {
    id: 'cinematic-wave',
    name: 'Cinematic Wave',
    description: 'Swelling pads, widescreen drums, emotive arcs.',
    prompt: 'Cinematic synthwave journey with widescreen pads, emotive arcs, and thunderous halftime drums.',
  },
  {
    id: 'trap-dystopia',
    name: 'Neo Trap Dystopia',
    description: '808 gravity, ghostly choirs, industrial grit.',
    prompt: 'Dark futuristic trap with colossal 808s, ghostly choirs, metallic plucks, and aggressive stabs.',
  },
];

const VOICE_PRESETS = [
  { id: 'lumen-femme', label: 'Lumen • Ethereal Femme', description: 'Glassine soprano presence with modern shimmer.' },
  { id: 'noir-tenor', label: 'Noir • Velvet Tenor', description: 'Smoky tenor ideal for R&B and moody pop.' },
  { id: 'solstice-duo', label: 'Solstice • Dual Harmony', description: 'Layered duet voice for instant call-and-response.' },
  { id: 'rap-seraph', label: 'Seraph • Melodic Rapper', description: 'Expressive rap vocal with melodic inflections.' },
];

const INSTRUMENTATION_CHIPS = [
  'Lush polysynth pads, glittering arpeggios, sidechained supersaw stacks.',
  'Palm-muted guitars, afrobeats percussion, warm sub bass and log drums.',
  'Analog synth basslines, gated snares, nostalgic VHS textures and field noise.',
  'Orchestral swells with hybrid drums, reversed piano motifs, cinematic rises.',
  'Future bass chords, granular vocal chops, detuned bells, rolling 808 glide.',
];

const DURATIONS = [45, 60, 90];

export function AIComposerCard() {
  const [isOpen, setIsOpen] = useState(false);
  const [mode, setMode] = useState<'write' | 'generate'>('generate');
  const [styleId, setStyleId] = useState(STYLE_PRESETS[0].id);
  const [voiceId, setVoiceId] = useState(VOICE_PRESETS[0].id);
  const [instrumental, setInstrumental] = useState(INSTRUMENTATION_CHIPS[0]);
  const [lyrics, setLyrics] = useState('');
  const [autoBrief, setAutoBrief] = useState(
    'Write an anthem about chasing neon-soaked dreams through a future city skyline.'
  );
  const [duration, setDuration] = useState(DURATIONS[1]);
  const [tempo, setTempo] = useState(115);
  const [status, setStatus] = useState<string | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [isRequestingLyrics, setIsRequestingLyrics] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [result, setResult] = useState<GenerationResult | null>(null);
  const [debugEntries, setDebugEntries] = useState<DebugEntry[]>([]);

  const selectedStyle = useMemo(() => STYLE_PRESETS.find((style) => style.id === styleId) ?? STYLE_PRESETS[0], [styleId]);
  const selectedVoice = useMemo(() => VOICE_PRESETS.find((voice) => voice.id === voiceId) ?? VOICE_PRESETS[0], [voiceId]);

  const closeModal = () => {
    setIsOpen(false);
    setStatus(null);
    setError(null);
    setDebugEntries([]);
  };

  const appendDebugEntry = (entry: Omit<DebugEntry, 'timestamp'> & { timestamp?: string }) => {
    const timestamp = entry.timestamp ?? new Date().toISOString();
    setDebugEntries((previous) => [...previous.slice(-7), { ...entry, timestamp }]);
  };

  const handleLyricsGeneration = async () => {
    setIsRequestingLyrics(true);
    setStatus('Calling Suno lyricist…');
    setError(null);

    try {
      const lyricRequestBody = {
        mode: 'no-custom' as const,
        style: buildStyleDescriptor(
          selectedStyle.prompt,
          instrumental,
          autoBrief,
          selectedVoice.label,
          `Tempo: ${tempo} BPM`
        ),
        duration,
        title: `Lyric Draft • ${selectedStyle.name}`,
      };

      appendDebugEntry({ stage: 'lyrics:request', payload: lyricRequestBody });

      const response = await fetch(COMPOSER_ENDPOINT, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(lyricRequestBody),
      });

      const payload = (await response.json()) as ComposerResponse;

      appendDebugEntry({
        stage: 'lyrics:response',
        status: response.status,
        ok: response.ok && payload?.ok,
        message: payload?.message ?? payload?.error,
        payload,
      });

      if (!response.ok || !payload.ok) {
        throw new Error(payload?.error ?? payload?.message ?? 'Unable to generate lyrics');
      }

      const mapped = mapComposerResponse(payload);

      if (mapped.lyrics) {
        setLyrics(mapped.lyrics);
        setStatus('Lyric draft ready — feel free to tweak before rendering.');
      } else {
        setStatus('Lyric request completed — check Suno for the final text.');
      }

      setResult((previous) => {
        const previewUrl = mapped.previewUrl ?? previous?.previewUrl ?? null;
        const audioUrls = mapped.audioUrls ?? previous?.audioUrls;
        return {
          ...mapped,
          previewUrl,
          audioUrls,
          lyrics: mapped.lyrics ?? previous?.lyrics ?? null,
        };
      });
    } catch (fetchError) {
      setError(fetchError instanceof Error ? fetchError.message : 'Unable to reach lyric service.');
      appendDebugEntry({
        stage: 'lyrics:error',
        message: fetchError instanceof Error ? fetchError.message : undefined,
        error: fetchError instanceof Error ? fetchError.stack ?? fetchError.message : String(fetchError),
      });
    } finally {
      setIsRequestingLyrics(false);
    }
  };

  const handleGeneration = async () => {
    setIsSubmitting(true);
    setStatus('Orchestrating your track with Suno…');
    setError(null);

    try {
      const lyricText = lyrics.trim();
      const generationRequestBody = {
        mode: (mode === 'write' || lyricText.length > 0) ? ('custom' as const) : ('no-custom' as const),
        lyrics: lyricText.length > 0 ? lyricText : undefined,
        style: buildStyleDescriptor(
          selectedStyle.prompt,
          instrumental,
          autoBrief,
          selectedVoice.label,
          `Tempo: ${tempo} BPM`
        ),
        duration,
        title: `MusicDistro • ${selectedStyle.name}`,
      };

      appendDebugEntry({ stage: 'generate:request', payload: generationRequestBody });

      const response = await fetch(COMPOSER_ENDPOINT, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(generationRequestBody),
      });

      const payload = (await response.json()) as ComposerResponse;

      appendDebugEntry({
        stage: 'generate:response',
        status: response.status,
        ok: response.ok && payload?.ok,
        message: payload?.message ?? payload?.error,
        payload,
      });

      if (!response.ok || !payload.ok) {
        throw new Error(payload?.error ?? payload?.message ?? 'Generation failed');
      }

      const mapped = mapComposerResponse(payload);
      setResult(mapped);

      setStatus(
        mapped.requestId
          ? `Track request ${mapped.requestId} queued — we will stream the preview once ready.`
          : 'Track request queued — we will stream the preview once ready.'
      );
    } catch (fetchError) {
      setError(fetchError instanceof Error ? fetchError.message : 'Unable to reach Suno.');
      appendDebugEntry({
        stage: 'generate:error',
        message: fetchError instanceof Error ? fetchError.message : undefined,
        error: fetchError instanceof Error ? fetchError.stack ?? fetchError.message : String(fetchError),
      });
    } finally {
      setIsSubmitting(false);
    }
  };

  const resetForm = () => {
    setMode('generate');
    setStyleId(STYLE_PRESETS[0].id);
    setVoiceId(VOICE_PRESETS[0].id);
    setInstrumental(INSTRUMENTATION_CHIPS[0]);
    setLyrics('');
    setAutoBrief('Write an anthem about chasing neon-soaked dreams through a future city skyline.');
    setDuration(DURATIONS[1]);
    setTempo(115);
    setResult(null);
    setStatus(null);
    setError(null);
    setIsSubmitting(false);
    setIsRequestingLyrics(false);
    setDebugEntries([]);
  };

  return (
    <article className="relative overflow-hidden rounded-2xl border border-white/5 bg-[radial-gradient(circle_at_top,_rgba(34,197,94,0.18),_rgba(15,17,21,0.9))] p-8 panel-shadow">
      <div className="pointer-events-none absolute inset-x-12 top-[-140px] h-[280px] rounded-full bg-emerald-500/10 blur-3xl" />
      <div className="pointer-events-none absolute -bottom-32 right-12 h-64 w-64 rounded-full bg-sky-500/20 blur-3xl" />
      <div className="relative z-10 flex flex-col gap-6">
        <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
          <div className="max-w-xl">
            <span className="text-xs uppercase tracking-[0.3em] text-emerald-400">AI Music Copilot</span>
            <h2 className="mt-3 text-3xl font-semibold text-white sm:text-4xl">Generate Suno-grade songs without leaving MusicDistro</h2>
            <p className="mt-3 text-sm text-slate-300">
              Craft lyric prompts, audition vocal personas, and describe your instrumental palette. Our Suno-powered pipeline
              turns your direction into fully-produced previews in under a minute.
            </p>
          </div>
          <button
            type="button"
            onClick={() => setIsOpen(true)}
            className="inline-flex items-center justify-center rounded-full bg-white/90 px-6 py-3 text-sm font-semibold uppercase tracking-[0.2em] text-slate-900 shadow-lg shadow-emerald-500/20 transition hover:bg-white"
          >
            Launch AI composer
          </button>
        </div>
        <div className="grid gap-4 md:grid-cols-3">
          <div className="rounded-xl border border-white/10 bg-white/5 p-4 backdrop-blur">
            <p className="text-xs uppercase tracking-[0.3em] text-emerald-300">Lyrics</p>
            <p className="mt-2 text-sm text-slate-100">Auto-generate verses or paste your own writing with instant structure guidance.</p>
          </div>
          <div className="rounded-xl border border-white/10 bg-white/5 p-4 backdrop-blur">
            <p className="text-xs uppercase tracking-[0.3em] text-emerald-300">Style &amp; Mood</p>
            <p className="mt-2 text-sm text-slate-100">Choose curated presets or blend custom aesthetics and energy levels.</p>
          </div>
          <div className="rounded-xl border border-white/10 bg-white/5 p-4 backdrop-blur">
            <p className="text-xs uppercase tracking-[0.3em] text-emerald-300">Voices</p>
            <p className="mt-2 text-sm text-slate-100">Preview AI vocalists, then pick the tone that fits your story.</p>
          </div>
        </div>
        {result?.lyrics && (
          <div className="rounded-xl border border-emerald-500/40 bg-emerald-500/10 p-5 text-sm text-emerald-50">
            <p className="text-xs uppercase tracking-[0.3em] text-emerald-300">Latest AI Lyric Draft</p>
            <pre className="mt-3 whitespace-pre-wrap font-sans leading-6 text-emerald-50/90">{result.lyrics}</pre>
          </div>
        )}
        {result?.previewUrl && (
          <div className="flex flex-col gap-3 rounded-xl border border-white/10 bg-slate-900/80 p-5">
            <div className="flex items-center justify-between">
              <span className="text-xs uppercase tracking-[0.3em] text-emerald-300">Preview</span>
              {result.jobId && <span className="text-xs text-slate-400">Job #{result.jobId}</span>}
            </div>
            <audio className="w-full" controls src={result.previewUrl} />
          </div>
        )}
      </div>

      <Transition appear show={isOpen} as={Fragment}>
        <Dialog as="div" className="relative z-50" onClose={closeModal}>
          <Transition.Child
            as={Fragment}
            enter="ease-out duration-300"
            enterFrom="opacity-0"
            enterTo="opacity-100"
            leave="ease-in duration-200"
            leaveFrom="opacity-100"
            leaveTo="opacity-0"
          >
            <div className="fixed inset-0 bg-slate-950/80 backdrop-blur" />
          </Transition.Child>

          <div className="fixed inset-0 overflow-y-auto px-4 py-10">
            <div className="mx-auto flex max-w-5xl items-start justify-center">
              <Transition.Child
                as={Fragment}
                enter="ease-out duration-300"
                enterFrom="opacity-0 translate-y-6"
                enterTo="opacity-100 translate-y-0"
                leave="ease-in duration-200"
                leaveFrom="opacity-100 translate-y-0"
                leaveTo="opacity-0 translate-y-6"
              >
                <Dialog.Panel className="w-full overflow-hidden rounded-3xl border border-white/10 bg-slate-950/95 shadow-2xl backdrop-blur-xl">
                  <div className="flex flex-col gap-8 p-10">
                    <header className="flex flex-col gap-2">
                      <Dialog.Title className="text-3xl font-semibold text-white">
                        Compose with the MusicDistro × Suno Foundry
                      </Dialog.Title>
                      <p className="text-sm text-slate-300">
                        Give the AI clear creative direction — we will stream the render back to your session once Suno finishes
                        processing the job.
                      </p>
                    </header>

                    <div className="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
                      <section className="flex flex-col gap-6">
                        <div className="flex gap-2 rounded-full border border-white/10 bg-white/5 p-1 text-xs uppercase tracking-[0.35em]">
                          <button
                            type="button"
                            onClick={() => setMode('generate')}
                            className={`flex-1 rounded-full px-4 py-2 transition ${
                              mode === 'generate' ? 'bg-emerald-400 text-slate-900 shadow-lg' : 'text-slate-300'
                            }`}
                          >
                            AI Writes Lyrics
                          </button>
                          <button
                            type="button"
                            onClick={() => setMode('write')}
                            className={`flex-1 rounded-full px-4 py-2 transition ${
                              mode === 'write' ? 'bg-emerald-400 text-slate-900 shadow-lg' : 'text-slate-300'
                            }`}
                          >
                            Use My Lyrics
                          </button>
                        </div>

                        {mode === 'generate' ? (
                          <div className="grid gap-4 lg:grid-cols-2">
                            <div className="flex flex-col gap-2">
                              <label className="text-xs font-semibold uppercase tracking-[0.3em] text-slate-200">
                                Tell the AI the story to write
                              </label>
                              <textarea
                                value={autoBrief}
                                onChange={(event) => setAutoBrief(event.target.value)}
                                rows={6}
                                className="min-h-[150px] rounded-2xl border border-white/10 bg-slate-900/60 p-4 text-sm text-slate-100 shadow-inner placeholder:text-slate-500 focus:border-emerald-400 focus:outline-none"
                                placeholder="Describe the scene, energy, and key lyrics you want."
                              />
                              <p className="text-xs text-slate-400">
                                Mention themes, imagery, or narrative beats. The richer the brief, the better the lyric draft.
                              </p>
                            </div>
                            <div className="flex flex-col gap-3">
                              <div className="flex items-center justify-between">
                                <label className="text-xs font-semibold uppercase tracking-[0.3em] text-slate-200">
                                  Lyric draft preview
                                </label>
                                <button
                                  type="button"
                                onClick={handleLyricsGeneration}
                                disabled={isRequestingLyrics || isSubmitting}
                                className="rounded-full border border-emerald-300/40 bg-emerald-400/10 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.28em] text-emerald-200 transition hover:bg-emerald-400/20 disabled:cursor-not-allowed disabled:opacity-60"
                              >
                                {isRequestingLyrics ? 'Working…' : 'Generate lyrics'}
                              </button>
                              </div>
                              <textarea
                                value={lyrics}
                                onChange={(event) => setLyrics(event.target.value)}
                                rows={6}
                                className="min-h-[150px] rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-sm text-emerald-50 shadow-inner placeholder:text-emerald-200/60 focus:border-emerald-300 focus:outline-none"
                                placeholder="AI generated lyrics will appear here for you to refine."
                              />
                              <p className="text-xs text-emerald-200/70">
                                Edit anything you like — we send your final text to Suno so the vocal matches perfectly.
                              </p>
                            </div>
                          </div>
                        ) : (
                          <div className="flex flex-col gap-2">
                            <label className="text-xs font-semibold uppercase tracking-[0.3em] text-slate-200">Paste your lyrics</label>
                            <textarea
                              value={lyrics}
                              onChange={(event) => setLyrics(event.target.value)}
                              rows={8}
                              className="min-h-[180px] rounded-2xl border border-white/10 bg-slate-900/60 p-4 text-sm text-slate-100 shadow-inner placeholder:text-slate-500 focus:border-emerald-400 focus:outline-none"
                              placeholder="Verse 1\nPre-Chorus\nChorus\nBridge"
                            />
                            <p className="text-xs text-slate-400">
                              Break lines into sections so Suno understands the phrasing you want in the performance.
                            </p>
                          </div>
                        )}

                        <div className="flex flex-col gap-3">
                          <label className="text-xs font-semibold uppercase tracking-[0.3em] text-slate-200">
                            Instrumental direction
                          </label>
                          <textarea
                            value={instrumental}
                            onChange={(event) => setInstrumental(event.target.value)}
                            rows={4}
                            className="rounded-2xl border border-white/10 bg-slate-900/60 p-4 text-sm text-slate-100 shadow-inner placeholder:text-slate-500 focus:border-emerald-400 focus:outline-none"
                            placeholder="Detail the instrumentation, energy, and transitions you want."
                          />
                          <div className="flex flex-wrap gap-2">
                            {INSTRUMENTATION_CHIPS.map((chip) => (
                              <button
                                key={chip}
                                type="button"
                                onClick={() => setInstrumental(chip)}
                                className={`rounded-full border px-3 py-1 text-xs transition ${
                                  instrumental === chip
                                    ? 'border-emerald-400 bg-emerald-400/20 text-emerald-100'
                                    : 'border-white/10 bg-white/5 text-slate-300 hover:border-emerald-300/40 hover:text-emerald-100'
                                }`}
                              >
                                {chip}
                              </button>
                            ))}
                          </div>
                        </div>
                      </section>

                      <aside className="flex flex-col gap-6 rounded-3xl border border-white/10 bg-white/5 p-6 text-sm text-slate-200">
                        <div>
                          <h3 className="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-300">Style Blueprint</h3>
                          <div className="mt-4 space-y-3">
                            {STYLE_PRESETS.map((style) => (
                              <button
                                key={style.id}
                                type="button"
                                onClick={() => setStyleId(style.id)}
                                className={`w-full rounded-2xl border px-4 py-3 text-left transition ${
                                  styleId === style.id
                                    ? 'border-emerald-400 bg-emerald-500/10 text-emerald-100 shadow-lg'
                                    : 'border-white/10 bg-slate-900/50 text-slate-200 hover:border-emerald-300/50 hover:text-emerald-100'
                                }`}
                              >
                                <p className="text-sm font-semibold text-white">{style.name}</p>
                                <p className="mt-1 text-xs text-slate-300">{style.description}</p>
                              </button>
                            ))}
                          </div>
                          <p className="mt-3 text-xs text-slate-400">
                            We merge your notes with the preset prompt:{' '}
                            <span className="text-emerald-200">{selectedStyle.prompt}</span>
                          </p>
                        </div>

                        <div>
                          <h3 className="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-300">Voice Palette</h3>
                          <div className="mt-4 space-y-2">
                            {VOICE_PRESETS.map((voice) => (
                              <button
                                key={voice.id}
                                type="button"
                                onClick={() => setVoiceId(voice.id)}
                                className={`w-full rounded-2xl border px-4 py-3 text-left transition ${
                                  voiceId === voice.id
                                    ? 'border-emerald-400 bg-emerald-500/10 text-emerald-100 shadow-lg'
                                    : 'border-white/10 bg-slate-900/50 text-slate-200 hover:border-emerald-300/50 hover:text-emerald-100'
                                }`}
                              >
                                <p className="text-sm font-semibold text-white">{voice.label}</p>
                                <p className="mt-1 text-xs text-slate-400">{voice.description}</p>
                              </button>
                            ))}
                          </div>
                        </div>

                        <div className="grid gap-4">
                          <label className="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-300">
                            Duration (seconds)
                          </label>
                          <div className="flex gap-2">
                            {DURATIONS.map((value) => (
                              <button
                                key={value}
                                type="button"
                                onClick={() => setDuration(value)}
                                className={`flex-1 rounded-2xl border px-3 py-3 text-sm font-semibold transition ${
                                  duration === value
                                    ? 'border-emerald-400 bg-emerald-400/20 text-emerald-100'
                                    : 'border-white/10 bg-slate-900/50 text-slate-300 hover:border-emerald-300/50 hover:text-emerald-100'
                                }`}
                              >
                                {value}s
                              </button>
                            ))}
                          </div>
                        </div>

                        <div className="flex flex-col gap-2">
                          <label className="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-300">Tempo</label>
                          <input
                            type="range"
                            min={70}
                            max={160}
                            value={tempo}
                            onChange={(event) => setTempo(Number(event.target.value))}
                            className="w-full accent-emerald-400"
                          />
                          <span className="text-xs text-slate-400">{tempo} BPM target</span>
                        </div>
                      </aside>
                    </div>

                    <footer className="flex flex-col gap-4 border-t border-white/5 pt-6 sm:flex-row sm:items-center sm:justify-between">
                      <div className="space-y-1 text-xs text-slate-400">
                        {status && <p className="text-emerald-200">{status}</p>}
                        {error && <p className="text-rose-300">{error}</p>}
                        {!status && !error && (
                          <p>Generation uses your Suno credits. Jobs appear in the Studio timeline automatically once ready.</p>
                        )}
                      </div>
                      <div className="flex flex-col gap-2 sm:flex-row">
                        <button
                          type="button"
                          onClick={resetForm}
                          className="rounded-full border border-white/20 px-6 py-3 text-xs font-semibold uppercase tracking-[0.3em] text-slate-200 transition hover:border-emerald-300/40 hover:text-emerald-100"
                        >
                          Reset
                        </button>
                        <button
                          type="button"
                          onClick={handleGeneration}
                          disabled={
                            isSubmitting || isRequestingLyrics || (!lyrics && mode === 'write')
                          }
                          className="rounded-full bg-emerald-400 px-8 py-3 text-xs font-semibold uppercase tracking-[0.35em] text-slate-950 shadow-lg shadow-emerald-500/30 transition hover:bg-emerald-300 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                          {isSubmitting ? 'Generating…' : 'Render with Suno'}
                        </button>
                      </div>
                    </footer>
                    {debugEntries.length > 0 && (
                      <section className="rounded-2xl border border-emerald-500/40 bg-slate-900/70 p-4 text-left text-xs text-emerald-200/80">
                        <details open>
                          <summary className="cursor-pointer text-emerald-300">API debug log</summary>
                          <div className="mt-3 space-y-3">
                            {debugEntries.map((entry, index) => (
                              <pre
                                // eslint-disable-next-line react/no-array-index-key
                                key={`${entry.stage}-${entry.timestamp}-${index}`}
                                className="max-h-64 overflow-auto whitespace-pre-wrap rounded-xl border border-white/5 bg-black/40 p-3 text-[11px] leading-5"
                              >
                                {JSON.stringify(entry, null, 2)}
                              </pre>
                            ))}
                          </div>
                        </details>
                      </section>
                    )}
                  </div>
                </Dialog.Panel>
              </Transition.Child>
            </div>
          </div>
        </Dialog>
      </Transition>
    </article>
  );
}
