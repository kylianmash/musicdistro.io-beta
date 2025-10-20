const API_BASE = process.env.NEXT_PUBLIC_API_URL ?? 'http://localhost:4000';

type FetchOptions = RequestInit & { token?: string };

async function request<T>(path: string, options: FetchOptions = {}): Promise<T> {
  const headers = new Headers(options.headers);
  headers.set('Content-Type', 'application/json');
  if (options.token) {
    headers.set('Authorization', `Bearer ${options.token}`);
  }

  const response = await fetch(`${API_BASE}${path}`, {
    ...options,
    headers,
  });

  if (!response.ok) {
    const error = await response.json().catch(() => ({}));
    throw new Error(error.message ?? 'API request failed');
  }

  return response.json() as Promise<T>;
}

export interface LoginPayload {
  email: string;
  password: string;
}

export interface RegisterPayload extends LoginPayload {
  displayName: string;
}

export async function login(payload: LoginPayload) {
  return request<{ token: string; user: { id: string; email: string; displayName: string } }>('/auth/login', {
    method: 'POST',
    body: JSON.stringify(payload),
  });
}

export async function register(payload: RegisterPayload) {
  return request<{ token: string; user: { id: string; email: string; displayName: string } }>('/auth/register', {
    method: 'POST',
    body: JSON.stringify(payload),
  });
}

export async function fetchProjects(token: string) {
  return request<{ projects: unknown[] }>('/projects', { token });
}

export async function fetchProject(token: string, id: string) {
  return request<{ project: unknown }>(`/projects/${id}`, { token });
}

export async function saveProject(token: string, payload: unknown, projectId?: string) {
  const method = projectId ? 'PUT' : 'POST';
  const path = projectId ? `/projects/${projectId}` : '/projects';
  return request<{ project: unknown }>(path, {
    method,
    body: JSON.stringify(payload),
    token,
  });
}
