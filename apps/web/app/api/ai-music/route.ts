import { NextResponse } from 'next/server';
import { z } from 'zod';

const createInputSchema = z.object({
  lyrics: z.union([z.string(), z.undefined()]).optional(),
  style: z.union([z.string(), z.undefined()]).optional(),
  duration: z.union([z.number(), z.string(), z.undefined()]).optional(),
  mode: z.union([z.literal('custom'), z.literal('no-custom'), z.undefined()]).optional(),
  webhook_url: z.union([z.string(), z.undefined()]).optional(),
  webhook_secret: z.union([z.string(), z.undefined()]).optional(),
  title: z.union([z.string(), z.undefined()]).optional(),
});

const RETRYABLE_STATUS = new Set([502, 503, 504]);
const MAX_ATTEMPTS = 3;
const DEFAULT_TIMEOUT_MS = 60_000;

function resolveBaseUrl() {
  const fallback = 'https://api.sunoapi.com/api/v1';
  const raw = typeof process.env.SUNO_API_BASE_URL === 'string' ? process.env.SUNO_API_BASE_URL : fallback;
  const trimmed = raw.trim();
  if (!trimmed) {
    return fallback;
  }
  return trimmed.replace(/\/+$/u, '');
}

function resolvePath() {
  const raw = typeof process.env.SUNO_API_GENERATE_PATH === 'string' ? process.env.SUNO_API_GENERATE_PATH : '/suno/create';
  const trimmed = raw.trim();
  if (!trimmed) {
    return '/suno/create';
  }
  if (trimmed.startsWith('http')) {
    return trimmed;
  }
  return trimmed.startsWith('/') ? trimmed : `/${trimmed}`;
}

function resolveTimeout() {
  const raw = typeof process.env.SUNO_API_TIMEOUT_MS === 'string' ? process.env.SUNO_API_TIMEOUT_MS : undefined;
  if (!raw) return DEFAULT_TIMEOUT_MS;
  const parsed = Number.parseInt(raw, 10);
  if (!Number.isFinite(parsed) || parsed <= 0) {
    return DEFAULT_TIMEOUT_MS;
  }
  return parsed;
}

function sanitizePayload<T extends Record<string, unknown>>(payload: T) {
  return Object.fromEntries(
    Object.entries(payload).filter(([, value]) => value !== undefined && value !== null && value !== '')
  );
}

function normalizeString(value: unknown) {
  if (typeof value !== 'string') return undefined;
  const trimmed = value.trim();
  return trimmed.length > 0 ? trimmed : undefined;
}

function normalizeDuration(value: unknown) {
  if (typeof value === 'number' && Number.isFinite(value)) {
    return Math.max(1, Math.trunc(value));
  }
  if (typeof value === 'string') {
    const trimmed = value.trim();
    if (trimmed === '') return undefined;
    const parsed = Number.parseInt(trimmed, 10);
    if (Number.isFinite(parsed)) {
      return Math.max(1, parsed);
    }
  }
  return undefined;
}

function collectAudioUrls(data: Record<string, unknown>) {
  const urls = new Set<string>();
  const addIfValid = (value: unknown) => {
    if (typeof value === 'string') {
      const trimmed = value.trim();
      if (trimmed.startsWith('http')) {
        urls.add(trimmed);
      }
    }
  };

  addIfValid(data.preview_url);
  addIfValid(data.audio_url);
  addIfValid(data.audio_url_mp3);
  addIfValid(data.audio_url_hq);
  addIfValid(data.audio_url_320);
  addIfValid(data.audio_url_128);

  if (Array.isArray(data.audio_urls)) {
    for (const value of data.audio_urls) {
      addIfValid(value);
    }
  }

  if (Array.isArray(data.clips)) {
    for (const clip of data.clips) {
      if (clip && typeof clip === 'object') {
        addIfValid((clip as Record<string, unknown>).audio_url);
        addIfValid((clip as Record<string, unknown>).preview_url);
      }
    }
  }

  const nestedData = data.data;
  if (nestedData && typeof nestedData === 'object') {
    const record = nestedData as Record<string, unknown>;
    addIfValid(record.preview_url);
    addIfValid(record.audio_url);
    if (Array.isArray(record.audio_urls)) {
      for (const value of record.audio_urls) {
        addIfValid(value);
      }
    }
  }

  return Array.from(urls);
}

async function delay(ms: number) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

export async function POST(request: Request) {
  const apiKey = process.env.SUNO_API_KEY;
  if (!apiKey) {
    return NextResponse.json(
      { ok: false, status: 500, error: 'Suno API key is not configured on the server.' },
      { status: 500 }
    );
  }

  let jsonBody: unknown;
  try {
    jsonBody = await request.json();
  } catch (error) {
    return NextResponse.json({ ok: false, status: 400, error: 'Invalid JSON payload.' }, { status: 400 });
  }

  const parsed = createInputSchema.safeParse(jsonBody);
  if (!parsed.success) {
    return NextResponse.json(
      { ok: false, status: 400, error: 'Invalid request payload.', details: parsed.error.flatten() },
      { status: 400 }
    );
  }

  const input = parsed.data;

  const baseUrl = resolveBaseUrl();
  const path = resolvePath();
  const url = path.startsWith('http') ? path : `${baseUrl}${path}`;
  const timeoutMs = resolveTimeout();
  const mode = input.mode ?? 'custom';
  const style = normalizeString(input.style) ?? 'electro';
  const duration = normalizeDuration(input.duration) ?? 60;
  const userAgent = process.env.APP_URL
    ? `MusicDistroAIComposer/1.0 (+ ${process.env.APP_URL})`
    : 'MusicDistroAIComposer/1.0';

  const requestBody = sanitizePayload({
    action: 'create',
    mode,
    lyrics: normalizeString(input.lyrics),
    style,
    duration,
    webhook_url: normalizeString(input.webhook_url),
    webhook_secret: normalizeString(input.webhook_secret),
    title: normalizeString(input.title),
  });

  const isDev = process.env.NODE_ENV !== 'production';

  for (let attempt = 1; attempt <= MAX_ATTEMPTS; attempt += 1) {
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), timeoutMs);

    try {
      const response = await fetch(url, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${apiKey}`,
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-API-Key': apiKey,
          'api-key': apiKey,
          'User-Agent': userAgent,
        },
        body: JSON.stringify(requestBody),
        signal: controller.signal,
      });

      clearTimeout(timeout);

      const contentType = response.headers.get('content-type') ?? '';
      const isJson = contentType.toLowerCase().includes('application/json');
      const body = isJson ? await response.json().catch(() => ({})) : await response.text().catch(() => '');

      if (!response.ok) {
        if (isDev) {
          console.error('[SunoAPI] Request failed', {
            status: response.status,
            url,
            attempt,
            response: body,
          });
        } else {
          console.error(`[SunoAPI] Request failed with status ${response.status}`);
        }

        if (RETRYABLE_STATUS.has(response.status) && attempt < MAX_ATTEMPTS) {
          const backoff = 300 + Math.floor(Math.random() * 500);
          await delay(backoff);
          continue;
        }

        const errorPayload =
          typeof body === 'string'
            ? { ok: false, status: response.status, error: body }
            : {
                ok: false as const,
                status: response.status,
                error:
                  typeof (body as Record<string, unknown>).error === 'string'
                    ? (body as Record<string, unknown>).error
                    : typeof (body as Record<string, unknown>).message === 'string'
                      ? (body as Record<string, unknown>).message
                      : 'Request to Suno API failed.',
                details: body,
              };

        return NextResponse.json(errorPayload, { status: response.status });
      }

      const data = (typeof body === 'string' ? { message: body } : (body as Record<string, unknown>)) ?? {};

      const requestId = (() => {
        if (typeof data.id === 'string') return data.id;
        if (typeof data.task_id === 'string') return data.task_id;
        if (typeof data.request_id === 'string') return data.request_id;
        return null;
      })();

      const audioUrls = collectAudioUrls(data);
      const previewUrl = audioUrls.length > 0 ? audioUrls[0] : null;
      const status = typeof data.status === 'string' ? data.status : null;
      const message = typeof data.message === 'string' ? data.message : null;
      const lyrics = typeof data.lyrics === 'string' ? data.lyrics : null;

      return NextResponse.json({
        ok: true,
        provider: 'sunoapi.com',
        requestId,
        jobId: requestId ?? undefined,
        status,
        message,
        previewUrl,
        audioUrls,
        lyrics,
        raw: data,
      });
    } catch (error) {
      clearTimeout(timeout);

      if (isDev) {
        console.error('[SunoAPI] Network error', { attempt, url, error });
      } else {
        console.error('[SunoAPI] Network error');
      }

      if (attempt < MAX_ATTEMPTS) {
        const backoff = 300 + Math.floor(Math.random() * 500);
        await delay(backoff);
        continue;
      }

      const errorMessage = error instanceof Error ? error.message : 'Unknown network error';
      return NextResponse.json(
        { ok: false, status: 502, error: 'Unable to reach Suno API.', details: errorMessage },
        { status: 502 }
      );
    }
  }

  return NextResponse.json({ ok: false, status: 502, error: 'Suno API request failed.' }, { status: 502 });
}
