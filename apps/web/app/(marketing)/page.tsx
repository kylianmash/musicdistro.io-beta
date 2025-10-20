import Link from 'next/link';
import { t } from '@musicdistro/i18n';

import { AIComposerCard } from './ai-composer-card';

export default function MarketingPage() {
  return (
    <main className="mx-auto flex min-h-screen max-w-5xl flex-col gap-12 px-8 py-24">
      <header className="flex flex-col gap-4">
        <span className="text-sm uppercase tracking-[0.4em] text-emerald-400">MusicDistro</span>
        <h1 className="text-5xl font-semibold text-white">{t('studio.title')}</h1>
        <p className="max-w-2xl text-lg text-slate-300">{t('studio.subtitle')}</p>
        <div className="flex items-center gap-4">
          <Link
            href="/studio"
            className="rounded-md bg-emerald-500 px-6 py-3 text-sm font-semibold uppercase tracking-widest text-slate-900 shadow-lg transition hover:bg-emerald-400"
          >
            Launch Studio
          </Link>
          <Link href="#features" className="text-sm font-medium text-slate-300 hover:text-white">
            Explore features
          </Link>
        </div>
      </header>
      <section id="features" className="grid gap-6 md:grid-cols-2">
        <div className="md:col-span-2">
          <AIComposerCard />
        </div>
        <article className="rounded-2xl bg-studio-surface p-6 panel-shadow">
          <h2 className="text-lg font-semibold text-white">Real-time timeline</h2>
          <p className="text-sm text-slate-300">
            Create unlimited audio and MIDI tracks, warp audio, edit automation, and stay perfectly in sync with Tone.js transport.
          </p>
        </article>
        <article className="rounded-2xl bg-studio-surface p-6 panel-shadow">
          <h2 className="text-lg font-semibold text-white">Collaborative ready</h2>
          <p className="text-sm text-slate-300">
            Built on CRDT-friendly state slices ready for multi-user editing, with presence and chat docks planned in the roadmap.
          </p>
        </article>
        <article className="rounded-2xl bg-studio-surface p-6 panel-shadow">
          <h2 className="text-lg font-semibold text-white">Plugin ecosystem</h2>
          <p className="text-sm text-slate-300">
            PolySynth, EQ8, and Convolution Reverb ship on day one with a skinnable SDK for extending the rack with custom DSP.
          </p>
        </article>
        <article className="rounded-2xl bg-studio-surface p-6 panel-shadow">
          <h2 className="text-lg font-semibold text-white">Export & offline</h2>
          <p className="text-sm text-slate-300">
            Progressive Web App with offline caching and mixdown rendering to pristine 44.1 kHz stereo WAV straight from the browser.
          </p>
        </article>
      </section>
      <footer className="mt-auto text-xs uppercase tracking-widest text-slate-500">v0.1 — Crafted for the web.</footer>
    </main>
  );
}
