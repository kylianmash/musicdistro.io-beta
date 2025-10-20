'use client';

import { useCallback, useEffect, useRef } from 'react';
import { io, type Socket } from 'socket.io-client';
import { useProjectStore } from '@/stores/project-store';
import type { ProjectSnapshot } from '@musicdistro/types';

const API_BASE = process.env.NEXT_PUBLIC_API_URL ?? 'http://localhost:4000';

export function useCollaboration(projectId: string | null) {
  const socketRef = useRef<Socket | null>(null);

  useEffect(() => {
    const socket = io(API_BASE, { transports: ['websocket'] });
    socketRef.current = socket;

    socket.on('project:update', (payload: ProjectSnapshot) => {
      if (payload && typeof payload === 'object') {
        useProjectStore.getState().hydrate(payload);
      }
    });

    return () => {
      socket.disconnect();
    };
  }, [projectId]);

  useEffect(() => {
    if (projectId && socketRef.current) {
      socketRef.current.emit('project:join', projectId);
    }
  }, [projectId]);

  const broadcast = useCallback(
    (snapshot: ProjectSnapshot) => {
      if (!projectId) return;
      socketRef.current?.emit('project:update', { projectId, snapshot });
    },
    [projectId]
  );

  return { broadcast };
}
