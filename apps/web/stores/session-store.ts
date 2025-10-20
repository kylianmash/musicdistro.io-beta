'use client';

import { create } from 'zustand';

interface UserSession {
  id: string;
  email: string;
  displayName: string;
}

interface SessionStore {
  token: string | null;
  user: UserSession | null;
  setSession: (session: { token: string; user: UserSession }) => void;
  clear: () => void;
}

export const useSessionStore = create<SessionStore>((set) => ({
  token: null,
  user: null,
  setSession({ token, user }) {
    set({ token, user });
    if (typeof window !== 'undefined') {
      window.localStorage.setItem('musicdistro.token', token);
      window.localStorage.setItem('musicdistro.user', JSON.stringify(user));
    }
  },
  clear() {
    set({ token: null, user: null });
    if (typeof window !== 'undefined') {
      window.localStorage.removeItem('musicdistro.token');
      window.localStorage.removeItem('musicdistro.user');
    }
  },
}));

export function bootstrapSession() {
  if (typeof window === 'undefined') return;
  const token = window.localStorage.getItem('musicdistro.token');
  const rawUser = window.localStorage.getItem('musicdistro.user');
  if (token && rawUser) {
    try {
      const user = JSON.parse(rawUser) as UserSession;
      useSessionStore.setState({ token, user });
    } catch {
      window.localStorage.removeItem('musicdistro.user');
    }
  }
}
