# MusicDistro Studio Monorepo

This repository now ships the first public iteration of **MusicDistro Studio** – a browser-native digital audio workstation that
enables artists to record, arrange, mix, and export songs without leaving the web. The codebase is organised as a pnpm
workspace so the audio engine, UI toolkit, and application shell can evolve independently.

## Workspace layout

```
.
├── apps
│   └── web                # Next.js 14 App Router frontend (Studio + marketing)
├── packages
│   ├── audio-engine       # Tone.js powered runtime with track graph + transport helpers
│   ├── i18n               # Minimal EN/FR/ES translation dictionary
│   ├── types              # Shared TypeScript contracts for tracks, clips, plugins
│   ├── ui                 # Tailwind + shadcn-inspired design primitives (buttons, knobs, sliders)
│   └── workers            # Web worker friendly helpers (waveform rendering prototype)
├── pnpm-workspace.yaml    # Workspace declaration
└── package.json           # Root scripts proxying to pnpm filters
```

## Getting started

1. Install dependencies with pnpm (v8+):

```bash
pnpm install
```

2. Launch the Studio in development mode:

```bash
pnpm dev
```

Open http://localhost:3000 to reach the marketing splash page, then click **Launch Studio** to enter the DAW environment.

## SunoAPI integration

The AI composer now proxies every request to [api.sunoapi.com](https://api.sunoapi.com/api/v1) from the backend so the browser never touches the third-party service directly.

1. Duplicate `.env.example` as `.env.local` (already committed with placeholder values) and set `SUNO_API_KEY` to your SunoAPI key.
2. Restart the Next.js dev server so the API route picks up the new environment values.
3. (Optional) On shared hosting (O2Switch), copy the same variables into the PHP environment or update `ai-composer.php`'s sibling `.env.local`.

### Manual checks

- **Next.js route**

  ```bash
  curl -X POST http://localhost:3000/api/ai-music \
    -H "Content-Type: application/json" \
    -d '{
      "mode":"custom",
      "lyrics":"A dark moody electro track with haunting pads",
      "style":"electro",
      "duration":60
    }'
  ```

- **PHP fallback**

  ```bash
  curl -X POST https://your-domain.example.com/ai-composer.php \
    -H "Content-Type: application/json" \
    -d '{"mode":"custom","style":"electro","duration":60}'
  ```

Both endpoints respond with `{ ok: true, provider: "sunoapi.com", requestId, raw }` on success, mirroring the payload consumed by the AI composer UI.

### Available scripts

- `pnpm dev` – start Next.js in development (App Router + React 18 streaming).
- `pnpm build` – build all workspaces.
- `pnpm lint` – run ESLint across the repo.
- `pnpm test` – execute Vitest unit tests.
- `pnpm --filter web test:e2e` – run Playwright E2E flows (expects the dev server on port 3000).

## Feature highlight (v0.1)

- **Arrangement timeline** with color-coded tracks, drag & drop audio import, magnetic grid, and hover playhead feedback.
- **Transport & mixer shell** exposing BPM, play/stop, track counter, and quick export to 44.1 kHz stereo WAV using
  `OfflineAudioContext` + `wavefile`.
- **Plugin rack** shipping PolySynth, EQ Eight, and Aurora Reverb with interactive knobs/sliders built on shadcn-inspired UI.
- **Offline-ready PWA** thanks to a lightweight service worker + manifest and IndexedDB-friendly architecture hooks.
- **Loop library seed** of 50 royalty-free placeholders with BPM/key metadata ready for future asset hydration.

## Testing status

- Unit: `vitest` validates Zustand stores.
- E2E: `@playwright/test` smoke tests that `/studio` renders the main controls.

## Known limitations

- Advanced DSP (elastic time-stretch, formant-preserving pitch) still references future WASM modules.
- Real-time collaboration (Y.js + WebRTC) and cloud persistence are outlined but not yet wired.
- Audio/MIDI recording UI is stubbed – monitoring and capture routes remain TODO.
- Plugin automation curves render as placeholders; modulation lanes will arrive in a follow-up.
- Generated loop URLs use a `virtual://` schema. Replace with actual S3/MinIO assets when available.

## Roadmap excerpts

- [ ] Integrate CRDT-based multi-user editing and presence indicators.
- [ ] Bring in Rust/WASM DSP blocks for warping, convolution, and analyzers.
- [ ] Expand plugin SDK with preset management, oversampling, and A/B comparison workflow.
- [ ] Add mastering chains, LUFS targeting, and DDP/Stem export automation.
- [ ] Optimise mobile/tablet responsive layout for touch-first editing.

---

Legacy PHP distribution pages remain untouched and continue to serve the MusicDistro marketing site. The Studio card on the
homepage links directly to the new `/studio` experience.
