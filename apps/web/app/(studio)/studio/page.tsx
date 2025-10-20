'use client';

import dynamic from 'next/dynamic';
import { Suspense } from 'react';
import { StudioShell } from './components/studio-shell';

const Timeline = dynamic(() => import('./timeline/timeline'), { ssr: false, loading: () => <div>Loading timeline…</div> });

export default function StudioPage() {
  return (
    <StudioShell>
      <Suspense fallback={<div className="p-6 text-sm text-slate-400">Preparing audio engine…</div>}>
        <Timeline />
      </Suspense>
    </StudioShell>
  );
}
