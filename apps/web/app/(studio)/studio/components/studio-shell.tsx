'use client';

import Link from 'next/link';
import { t } from '@musicdistro/i18n';
import { Button } from '@musicdistro/ui';
import { useTransportStore } from '@/stores/transport-store';
import { useProjectStore } from '@/stores/project-store';
import { audioEngine } from '@musicdistro/audio-engine';
import { useEffect, useState } from 'react';
import dynamic from 'next/dynamic';
import { exportMixdown } from '@/lib/export';
import { saveProject } from '@/lib/api';
import { TransportControls } from './transport-controls';
import InspectorPanel from './inspector-panel';
import { Mixer } from './mixer';
import { bootstrapSession, useSessionStore } from '@/stores/session-store';
import { useCollaboration } from '@/hooks/use-collaboration';

const PluginDock = dynamic(() => import('./plugin-dock'), { ssr: false });

interface StudioShellProps {
  children: React.ReactNode;
}

export function StudioShell({ children }: StudioShellProps) {
  const bpm = useTransportStore((state) => state.bpm);
  const createTrack = useProjectStore((state) => state.createTrack);
  const tracks = useProjectStore((state) => state.tracks);
  const clips = useProjectStore((state) => state.clips);
  const serialize = useProjectStore((state) => state.serialize);
  const user = useSessionStore((state) => state.user);
  const token = useSessionStore((state) => state.token);
  const [projectId, setProjectId] = useState<string | null>(null);
  const transport = useTransportStore((state) => ({
    bpm: state.bpm,
    timeSignature: state.timeSignature,
    loop: state.loop,
    grid: state.grid,
    metronomeEnabled: state.metronomeEnabled,
  }));
  const { broadcast } = useCollaboration(projectId);

  useEffect(() => {
    audioEngine.setTransport({ bpm });
  }, [bpm]);

  useEffect(() => {
    bootstrapSession();
    if (typeof window !== 'undefined') {
      const rawProject = window.localStorage.getItem('musicdistro.project');
      if (rawProject) {
        try {
          const snapshot = JSON.parse(rawProject);
          if (snapshot?.tracks) {
            useProjectStore.getState().hydrate(snapshot);
            if (snapshot.id && snapshot.id !== 'local') {
              setProjectId(snapshot.id);
            }
          }
        } catch (error) {
          console.warn('Failed to restore project', error);
        }
      }
    }
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker
        .register('/service-worker.js')
        .catch((error) => console.error('SW registration failed', error));
    }
  }, []);

  useEffect(() => {
    const snapshot = serialize();
    snapshot.transport = {
      ...snapshot.transport,
      bpm: transport.bpm,
      timeSignature: transport.timeSignature,
      loop: transport.loop,
      grid: transport.grid,
      metronomeEnabled: transport.metronomeEnabled,
    };

    if (typeof window !== 'undefined') {
      window.localStorage.setItem('musicdistro.project', JSON.stringify({ ...snapshot, id: projectId ?? 'local' }));
    }

    if (!token) return;

    const timeout = window.setTimeout(() => {
      void saveProject(token, snapshot, projectId ?? undefined)
        .then((response) => {
          let resolvedId = projectId;
          if (!projectId) {
            const saved = response.project as { id?: string };
            if (saved?.id) {
              setProjectId(saved.id);
              resolvedId = saved.id;
            }
          }
          if (resolvedId) {
            broadcast({ ...snapshot, id: resolvedId });
          }
        })
        .catch((error) => {
          console.warn('Autosave failed', error);
        });
    }, 1200);

    return () => window.clearTimeout(timeout);
  }, [tracks, clips, transport, token, projectId, serialize]);

  return (
    <div className="flex min-h-screen flex-col bg-studio-background">
      <header className="border-b border-white/5 bg-studio-surface/70 px-6 py-4">
        <div className="flex items-center justify-between">
          <Link href="/" className="text-sm font-semibold uppercase tracking-[0.4em] text-emerald-400">
            MusicDistro
          </Link>
          <div className="flex items-center gap-4 text-xs text-slate-300">
            <span className="font-mono">BPM {bpm.toFixed(1)}</span>
            <span className="font-mono">Tracks {tracks.length}</span>
            {user ? <span className="font-mono text-emerald-300">{user.displayName}</span> : null}
            <Button
              variant="secondary"
              size="sm"
              onClick={async () => {
                try {
                  const blob = await exportMixdown(clips, 'wav');
                  const url = URL.createObjectURL(blob);
                  const anchor = document.createElement('a');
                  anchor.href = url;
                  anchor.download = 'musicdistro-mix.wav';
                  anchor.click();
                  URL.revokeObjectURL(url);
                } catch (error) {
                  console.error('Export failed', error);
                }
              }}
            >
              {t('studio.export')}
            </Button>
            <Button
              variant="ghost"
              size="sm"
              onClick={async () => {
                try {
                  const blob = await exportMixdown(clips, 'mp3');
                  const url = URL.createObjectURL(blob);
                  const anchor = document.createElement('a');
                  anchor.href = url;
                  anchor.download = 'musicdistro-mix.mp3';
                  anchor.click();
                  URL.revokeObjectURL(url);
                } catch (error) {
                  console.error('Export failed', error);
                }
              }}
            >
              Export MP3
            </Button>
          </div>
        </div>
        <div className="mt-4">
          <TransportControls />
        </div>
      </header>
      <main className="flex flex-1 overflow-hidden">
        <aside className="hidden w-72 flex-col border-r border-white/5 bg-studio-surface/80 p-6 lg:flex">
          <div className="space-y-4">
            <h2 className="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Quick Actions</h2>
            <p className="text-sm text-slate-400">
              Use the floating controls inside the timeline to add tracks, duplicate clips, and control zoom. Audio files can be
              dropped anywhere in the arrangement view.
            </p>
            <Button variant="ghost" onClick={() => createTrack('audio')}>
              {t('studio.newTrack')}
            </Button>
          </div>
        </aside>
        <section className="flex-1 overflow-hidden bg-studio-background/95">{children}</section>
        <aside className="hidden w-[360px] border-l border-white/5 bg-studio-surface/80 p-6 xl:flex">
          <InspectorPanel />
        </aside>
      </main>
      <footer className="border-t border-white/5 bg-studio-surface/90 px-6 py-8">
        <div className="flex flex-col gap-6">
          <div>
            <p className="mb-3 text-xs uppercase tracking-[0.3em] text-slate-500">{t('studio.mixer')}</p>
            <Mixer />
          </div>
          <div>
            <p className="mb-3 text-xs uppercase tracking-[0.3em] text-slate-500">Creative Effects</p>
            <PluginDock />
          </div>
        </div>
      </footer>
    </div>
  );
}
