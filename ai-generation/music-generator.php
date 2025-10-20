<?php
declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';
require_authentication();

$siteName = site_name();
$faviconUrl = site_favicon_url();
$dashboardLogoUrl = dashboard_logo_url();
$user = current_user();

$displayName = trim((string) (($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')));
if ($displayName === '') {
    $displayName = (string) ($user['email'] ?? '');
}

?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(current_language(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" dir="<?= htmlspecialchars(language_direction(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MusicDistro AI Generator – <?= htmlspecialchars($siteName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></title>
  <meta name="robots" content="noindex,follow">
  <link rel="icon" href="<?= htmlspecialchars($faviconUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      color-scheme: dark;
      --page-bg: radial-gradient(circle at top left, rgba(14, 165, 233, 0.25), transparent 55%),
                 radial-gradient(circle at bottom right, rgba(168, 85, 247, 0.22), transparent 60%),
                 #020617;
      --panel-bg: rgba(13, 19, 36, 0.88);
      --panel-border: rgba(148, 163, 184, 0.22);
      --panel-shadow: 0 40px 90px rgba(2, 6, 23, 0.55);
      --text-primary: #f8fafc;
      --text-muted: rgba(226, 232, 240, 0.72);
      --accent: linear-gradient(135deg, #22d3ee, #8b5cf6, #ec4899);
      --accent-border: rgba(14, 165, 233, 0.65);
      --chip-bg: rgba(15, 23, 42, 0.85);
      --chip-border: rgba(96, 165, 250, 0.45);
      --chip-hover: rgba(96, 165, 250, 0.25);
      --input-bg: rgba(15, 23, 42, 0.82);
      --input-border: rgba(148, 163, 184, 0.32);
      --input-focus: rgba(129, 140, 248, 0.45);
      --success: #34d399;
      --error: #f87171;
      --radius-xl: 36px;
      --radius-lg: 24px;
      --radius-md: 18px;
      --radius-sm: 12px;
      --transition: 180ms ease;
    }

    *, *::before, *::after {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      min-height: 100vh;
      font-family: 'Manrope', sans-serif;
      background: var(--page-bg);
      color: var(--text-primary);
      padding: clamp(1.5rem, 3vw, 4rem);
      display: flex;
      flex-direction: column;
      align-items: stretch;
    }

    header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1.5rem;
      margin-bottom: clamp(2rem, 4vw, 3.5rem);
      flex-wrap: wrap;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 1.1rem;
    }

    .brand img {
      width: clamp(44px, 7vw, 60px);
      height: clamp(44px, 7vw, 60px);
      object-fit: contain;
      border-radius: 16px;
      background: rgba(15, 23, 42, 0.55);
      padding: 0.45rem;
      border: 1px solid rgba(148, 163, 184, 0.3);
    }

    .brand h1 {
      font-size: clamp(1.6rem, 3vw, 2.4rem);
      font-weight: 700;
      margin: 0;
      display: flex;
      flex-direction: column;
      gap: 0.3rem;
    }

    .brand span {
      font-size: clamp(0.9rem, 1.8vw, 1rem);
      font-weight: 500;
      color: var(--text-muted);
    }

    .profile-chip {
      padding: 0.65rem 1.1rem;
      border-radius: var(--radius-lg);
      background: rgba(15, 23, 42, 0.7);
      border: 1px solid rgba(148, 163, 184, 0.25);
      display: flex;
      align-items: center;
      gap: 0.6rem;
      font-size: 0.95rem;
      color: var(--text-muted);
    }

    .profile-chip svg {
      width: 18px;
      height: 18px;
      color: rgba(129, 140, 248, 0.8);
    }

    main {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: clamp(1.5rem, 2.5vw, 2.5rem);
    }

    .panel {
      background: var(--panel-bg);
      border: 1px solid var(--panel-border);
      border-radius: var(--radius-xl);
      padding: clamp(1.6rem, 3vw, 2.4rem);
      box-shadow: var(--panel-shadow);
      display: flex;
      flex-direction: column;
      gap: 1.4rem;
      backdrop-filter: blur(24px);
      position: relative;
      overflow: hidden;
    }

    .panel::after {
      content: "";
      position: absolute;
      inset: 0;
      pointer-events: none;
      background: linear-gradient(145deg, rgba(34, 211, 238, 0.07), transparent 40%);
    }

    .panel h2 {
      font-size: clamp(1.2rem, 2vw, 1.6rem);
      margin: 0;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
    }

    .panel h2 span {
      font-size: 0.9rem;
      font-weight: 500;
      color: var(--text-muted);
    }

    .input-group {
      display: flex;
      flex-direction: column;
      gap: 0.65rem;
    }

    label {
      font-size: 0.92rem;
      font-weight: 600;
      color: var(--text-primary);
    }

    input[type="text"],
    textarea,
    select {
      width: 100%;
      border-radius: var(--radius-md);
      border: 1px solid var(--input-border);
      background: var(--input-bg);
      color: var(--text-primary);
      font-size: 1rem;
      padding: 0.95rem 1.1rem;
      transition: border var(--transition), box-shadow var(--transition), transform var(--transition);
      resize: vertical;
    }

    textarea {
      min-height: 140px;
    }

    input:focus,
    textarea:focus,
    select:focus {
      outline: none;
      border-color: var(--input-focus);
      box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.18);
    }

    .helper {
      font-size: 0.85rem;
      color: var(--text-muted);
      line-height: 1.5;
    }

    .chip-row {
      display: flex;
      flex-wrap: wrap;
      gap: 0.65rem;
    }

    .chip {
      background: var(--chip-bg);
      border: 1px solid rgba(148, 163, 184, 0.25);
      padding: 0.55rem 0.95rem;
      border-radius: var(--radius-sm);
      font-size: 0.85rem;
      color: var(--text-muted);
      cursor: pointer;
      transition: background var(--transition), border-color var(--transition), transform var(--transition);
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
    }

    .chip:hover {
      background: var(--chip-hover);
      border-color: var(--chip-border);
      transform: translateY(-1px);
    }

    .chip[data-active="true"] {
      border-color: rgba(129, 140, 248, 0.7);
      color: var(--text-primary);
      background: rgba(79, 70, 229, 0.25);
    }

    .lyric-modes {
      display: inline-flex;
      background: rgba(15, 23, 42, 0.72);
      border: 1px solid rgba(148, 163, 184, 0.24);
      border-radius: var(--radius-lg);
      overflow: hidden;
      position: relative;
    }

    .lyric-modes button {
      border: none;
      background: transparent;
      color: var(--text-muted);
      padding: 0.75rem 1.2rem;
      font-size: 0.95rem;
      font-weight: 600;
      cursor: pointer;
      transition: color var(--transition), background var(--transition);
    }

    .lyric-modes button[data-active="true"] {
      color: var(--text-primary);
      background: linear-gradient(135deg, rgba(34, 211, 238, 0.22), rgba(129, 140, 248, 0.25));
    }

    .action-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      flex-wrap: wrap;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.6rem;
      border-radius: var(--radius-md);
      border: 1px solid transparent;
      padding: 0.85rem 1.4rem;
      font-size: 0.95rem;
      font-weight: 600;
      cursor: pointer;
      transition: transform var(--transition), box-shadow var(--transition), border-color var(--transition), background var(--transition);
      background: var(--accent);
      color: #0f172a;
      box-shadow: 0 18px 35px rgba(8, 47, 73, 0.35);
    }

    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 22px 45px rgba(8, 47, 73, 0.45);
    }

    .btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }

    .btn-secondary {
      background: rgba(15, 23, 42, 0.9);
      color: var(--text-primary);
      border-color: rgba(148, 163, 184, 0.35);
      box-shadow: none;
    }

    .btn-secondary:hover {
      border-color: rgba(129, 140, 248, 0.65);
      background: rgba(30, 41, 59, 0.9);
    }

    .status-pill {
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      padding: 0.55rem 0.85rem;
      border-radius: 999px;
      background: rgba(34, 211, 238, 0.16);
      border: 1px solid rgba(34, 211, 238, 0.35);
      font-size: 0.85rem;
      color: var(--text-primary);
    }

    .status-pill[data-status="error"] {
      background: rgba(248, 113, 113, 0.18);
      border-color: rgba(248, 113, 113, 0.35);
    }

    .status-pill[data-status="success"] {
      background: rgba(52, 211, 153, 0.2);
      border-color: rgba(52, 211, 153, 0.35);
    }

    .results {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 1rem;
    }

    .result-card {
      border: 1px solid rgba(148, 163, 184, 0.24);
      background: rgba(15, 23, 42, 0.78);
      border-radius: var(--radius-lg);
      padding: 1.2rem;
      display: flex;
      flex-direction: column;
      gap: 0.8rem;
    }

    .result-card strong {
      font-size: 0.95rem;
    }

    .result-card audio {
      width: 100%;
    }

    .empty-state {
      border: 1px dashed rgba(148, 163, 184, 0.28);
      border-radius: var(--radius-lg);
      padding: 1.6rem;
      text-align: center;
      color: var(--text-muted);
      font-size: 0.95rem;
      background: rgba(15, 23, 42, 0.6);
    }

    @media (max-width: 720px) {
      body {
        padding: 1.25rem;
      }
      header {
        flex-direction: column;
        align-items: flex-start;
      }
      .action-bar {
        flex-direction: column;
        align-items: stretch;
      }
      .action-bar .btn,
      .action-bar .btn-secondary {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <header>
    <div class="brand">
      <img src="<?= htmlspecialchars($dashboardLogoUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" alt="<?= htmlspecialchars($siteName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> logo">
      <h1>
        MusicDistro × Suno Foundry
        <span>Compose futuristic records in minutes. Welcome back, <?= htmlspecialchars($displayName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>.</span>
      </h1>
    </div>
    <div class="profile-chip">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 0115 0" /></svg>
      <?= htmlspecialchars($displayName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
    </div>
  </header>
  <main>
    <section class="panel" id="creativePanel">
      <h2>
        Creative Brief
        <span>Describe the story, vibe, and key lyrics.</span>
      </h2>
      <div class="input-group">
        <label for="titleInput">Song title (optional)</label>
        <input type="text" id="titleInput" name="title" placeholder="Neon Skyline Reverie">
      </div>
      <div class="input-group">
        <label for="storyInput">Narrative / vibe</label>
        <textarea id="storyInput" placeholder="Paint the scene, emotions, and audience energy."></textarea>
        <p class="helper">Mention motifs, moods, or references. The richer the description, the better Suno can steer the arrangement.</p>
      </div>
      <div class="input-group">
        <label>Lyric workflow</label>
        <div class="lyric-modes" role="tablist">
          <button type="button" data-lyric-mode="ai" data-active="true">AI drafts the lyrics</button>
          <button type="button" data-lyric-mode="manual">I will paste lyrics</button>
        </div>
      </div>
      <div class="input-group" data-lyrics-panel="ai">
        <label for="lyricsPreview">AI lyric sandbox</label>
        <textarea id="lyricsPreview" placeholder="AI will craft a draft here for you to refine"></textarea>
        <p class="helper">We use Suno's lyric engine to map your story to a structured song. Edit any lines you want to keep before rendering audio.</p>
        <div class="action-bar">
          <button class="btn-secondary" type="button" id="generateLyricsBtn">
            <span class="btn-label">Generate lyrics</span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 12h15m0 0l-5.25-5.25M19.5 12l-5.25 5.25" /></svg>
          </button>
          <span class="status-pill" data-lyrics-status hidden>Ready to generate</span>
        </div>
      </div>
      <div class="input-group" data-lyrics-panel="manual" hidden>
        <label for="lyricsInput">Paste your lyrics</label>
        <textarea id="lyricsInput" placeholder="Verse 1
Pre-Chorus
Chorus
Bridge"></textarea>
        <p class="helper">Break lines into sections so Suno understands the phrasing. Feel free to include ad-libs or cues.</p>
      </div>
    </section>

    <section class="panel" id="productionPanel">
      <h2>
        Production Blueprint
        <span>Choose style, instrumentation, and voice.</span>
      </h2>
      <div class="input-group">
        <label>Featured styles</label>
        <div class="chip-row" id="styleChips"></div>
        <p class="helper">Layer any extra direction below—we blend it with the preset prompt you pick.</p>
      </div>
      <div class="input-group">
        <label for="instrumentalInput">Instrumental direction</label>
        <textarea id="instrumentalInput" placeholder="Detail the instrumentation, rhythm, transitions, and ear candy you expect."></textarea>
        <p class="helper">Click any inspiration chips below to drop instant ideas.</p>
        <div class="chip-row" id="instrumentChips"></div>
      </div>
      <div class="input-group">
        <label>Lead voice</label>
        <div class="chip-row" id="voiceChips"></div>
      </div>
      <div class="input-group">
        <label for="durationInput">Duration (seconds)</label>
        <input type="text" id="durationInput" inputmode="numeric" pattern="[0-9]{2,3}" value="60">
        <p class="helper">Recommended 30–120 seconds. Longer renders may take more time.</p>
      </div>
      <div class="action-bar">
        <button class="btn" type="button" id="renderBtn">
          <span class="btn-label">Render with Suno</span>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m6-6H6" /></svg>
        </button>
        <span class="status-pill" data-render-status hidden>Waiting to render</span>
      </div>
    </section>

    <section class="panel" id="resultPanel">
      <h2>
        Sessions & renders
        <span>Preview assets and copy job IDs.</span>
      </h2>
      <div class="results" id="results"></div>
      <div class="empty-state" id="emptyState">No renders yet — craft a brief and hit <strong>Render with Suno</strong>.</div>
    </section>
  </main>

  <script>
    const STYLE_PRESETS = [
      {
        id: 'hyperpop-euphoria',
        name: 'Hyperpop Euphoria',
        description: 'Crystalline leads, glitch drums, euphoric drops.',
        prompt: 'Explosive hyperpop anthem with shimmering synth leads, glitch percussion, euphoric vocal chops, and adrenaline-soaked drops.'
      },
      {
        id: 'afrobeats-sunset',
        name: 'Afrobeats Sunset',
        description: 'Palm-muted guitars, syncopated percussion, velvet bass.',
        prompt: 'Afrobeats groove with palm-muted guitars, syncopated percussion, velvet midnight bass, and glowing sunset ambience.'
      },
      {
        id: 'cinematic-wave',
        name: 'Cinematic Wave',
        description: 'Swelling pads, widescreen drums, emotive arcs.',
        prompt: 'Cinematic synthwave journey with widescreen pads, emotive arcs, thunderous halftime drums, and neon-soaked textures.'
      },
      {
        id: 'trap-dystopia',
        name: 'Neo Trap Dystopia',
        description: '808 gravity, ghostly choirs, industrial grit.',
        prompt: 'Dark futuristic trap with colossal 808s, ghostly choirs, metallic plucks, and aggressive industrial stabs.'
      },
      {
        id: 'lofi-midnight',
        name: 'Lo-Fi Midnight Drive',
        description: 'Dusty keys, vinyl crackle, head-nod swing.',
        prompt: 'Late-night lo-fi beat with dusty Rhodes keys, vinyl crackle, swing drums, and warm analog bass under moonlit city lights.'
      }
    ];

    const INSTRUMENT_CHIPS = [
      'Lush polysynth pads, glittering arpeggios, sidechained supersaws.',
      'Palm-muted guitars, afrobeats percussion, warm sub bass and log drums.',
      'Analog synth basslines, gated snares, nostalgic VHS textures and field noise.',
      'Orchestral swells with hybrid drums, reversed piano motifs, cinematic rises.',
      'Future bass chords, granular vocal chops, detuned bells, rolling 808 glide.',
      'Broken-beat drums, resonant Moog bass, crystalline vocal chops, morphing risers.',
      'Live sax hooks, soulful electric piano, dusty MPC swing, tape-saturated drums.'
    ];

    const VOICE_PRESETS = [
      { id: 'lumen-femme', label: 'Lumen • Ethereal Femme', description: 'Glassine soprano presence with modern shimmer.' },
      { id: 'noir-tenor', label: 'Noir • Velvet Tenor', description: 'Shadowy crooner tone with smoky grit.' },
      { id: 'astra-andro', label: 'Astra • Androgynous Dreamer', description: 'Celestial alto register with airy head voice.' },
      { id: 'onyx-raptor', label: 'Onyx • Cyber Rapper', description: 'Holographic rap cadence with vocoder layers.' }
    ];

    const state = {
      lyricMode: 'ai',
      selectedStyle: STYLE_PRESETS[0],
      selectedVoice: VOICE_PRESETS[0],
      lyricStatusTimeout: null,
      renderStatusTimeout: null
    };

    const qs = (sel, parent = document) => parent.querySelector(sel);
    const qsa = (sel, parent = document) => Array.from(parent.querySelectorAll(sel));

    const styleRow = qs('#styleChips');
    const instrumentRow = qs('#instrumentChips');
    const voiceRow = qs('#voiceChips');
    const durationInput = qs('#durationInput');
    const storyInput = qs('#storyInput');
    const titleInput = qs('#titleInput');
    const instrumentalInput = qs('#instrumentalInput');
    const lyricsPreview = qs('#lyricsPreview');
    const lyricsInput = qs('#lyricsInput');
    const lyricStatus = qs('[data-lyrics-status]');
    const renderStatus = qs('[data-render-status]');
    const resultsContainer = qs('#results');
    const emptyState = qs('#emptyState');

    function renderStyleChips() {
      styleRow.innerHTML = '';
      STYLE_PRESETS.forEach((preset) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'chip';
        btn.dataset.value = preset.id;
        btn.innerHTML = `<strong>${preset.name}</strong> <span>${preset.description}</span>`;
        if (state.selectedStyle && state.selectedStyle.id === preset.id) {
          btn.dataset.active = 'true';
        }
        btn.addEventListener('click', () => {
          state.selectedStyle = preset;
          renderStyleChips();
        });
        styleRow.appendChild(btn);
      });
    }

    function renderInstrumentChips() {
      instrumentRow.innerHTML = '';
      INSTRUMENT_CHIPS.forEach((chipText) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'chip';
        btn.textContent = chipText;
        btn.addEventListener('click', () => {
          const current = instrumentalInput.value.trim();
          instrumentalInput.value = current ? `${current}\n${chipText}` : chipText;
          instrumentalInput.dispatchEvent(new Event('input'));
        });
        instrumentRow.appendChild(btn);
      });
    }

    function renderVoiceChips() {
      voiceRow.innerHTML = '';
      VOICE_PRESETS.forEach((voice) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'chip';
        btn.dataset.value = voice.id;
        btn.innerHTML = `<strong>${voice.label}</strong> <span>${voice.description}</span>`;
        if (state.selectedVoice && state.selectedVoice.id === voice.id) {
          btn.dataset.active = 'true';
        }
        btn.addEventListener('click', () => {
          state.selectedVoice = voice;
          renderVoiceChips();
        });
        voiceRow.appendChild(btn);
      });
    }

    function setLyricMode(nextMode) {
      state.lyricMode = nextMode;
      qsa('[data-lyric-mode]').forEach((button) => {
        button.dataset.active = button.dataset.lyricMode === nextMode ? 'true' : 'false';
      });
      qsa('[data-lyrics-panel]').forEach((panel) => {
        const panelMode = panel.getAttribute('data-lyrics-panel');
        if (panelMode === nextMode) {
          panel.hidden = false;
        } else {
          panel.hidden = true;
        }
      });
    }

    qsa('[data-lyric-mode]').forEach((btn) => {
      btn.addEventListener('click', () => setLyricMode(btn.dataset.lyricMode || 'ai'));
    });

    function updateStatus(el, message, status = 'default') {
      if (!el) return;
      el.textContent = message;
      if (status === 'hidden') {
        el.hidden = true;
        return;
      }
      el.dataset.status = status;
      el.hidden = false;
    }

    function resetLyricStatus(delay = 2600) {
      if (state.lyricStatusTimeout) {
        clearTimeout(state.lyricStatusTimeout);
      }
      state.lyricStatusTimeout = setTimeout(() => updateStatus(lyricStatus, 'Ready to generate'), delay);
    }

    function resetRenderStatus(delay = 3200) {
      if (state.renderStatusTimeout) {
        clearTimeout(state.renderStatusTimeout);
      }
      state.renderStatusTimeout = setTimeout(() => updateStatus(renderStatus, 'Waiting to render'), delay);
    }

    async function handleGenerateLyrics() {
      const story = storyInput.value.trim();
      const stylePrompt = state.selectedStyle ? state.selectedStyle.prompt : '';
      if (!story) {
        updateStatus(lyricStatus, 'Ajoute un brief narratif avant de générer.', 'error');
        resetLyricStatus(3000);
        return;
      }

      updateStatus(lyricStatus, 'Génération des paroles…', 'default');
      const button = qs('#generateLyricsBtn');
      button.disabled = true;

      try {
        const response = await fetch('/ai-generation/suno-lyrics.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            story,
            style: stylePrompt,
            voice: state.selectedVoice ? state.selectedVoice.label : null
          })
        });

        if (!response.ok) {
          const errorPayload = await response.json().catch(() => null);
          const message = errorPayload && errorPayload.error ? errorPayload.error : 'Unable to generate lyrics.';
          throw new Error(message);
        }

        const payload = await response.json();
        if (payload && payload.lyrics) {
          lyricsPreview.value = payload.lyrics;
          updateStatus(lyricStatus, 'Paroles générées avec succès ✨', 'success');
        } else {
          throw new Error('Réponse inattendue du service de paroles.');
        }
      } catch (error) {
        console.error(error);
        updateStatus(lyricStatus, error instanceof Error ? error.message : 'Erreur lors de la génération.', 'error');
      } finally {
        button.disabled = false;
        resetLyricStatus();
      }
    }

    async function handleRender() {
      const durationValue = parseInt(durationInput.value.trim(), 10);
      if (!Number.isFinite(durationValue) || durationValue < 20) {
        updateStatus(renderStatus, 'Fixe une durée valide (≥ 20s).', 'error');
        resetRenderStatus(3200);
        return;
      }

      const story = storyInput.value.trim();
      const instrumentation = instrumentalInput.value.trim();
      const title = titleInput.value.trim();
      const lyricSource = state.lyricMode === 'manual' ? lyricsInput.value.trim() : lyricsPreview.value.trim();

      const compositeStyleParts = [];
      if (state.selectedStyle) {
        compositeStyleParts.push(state.selectedStyle.prompt);
      }
      if (story) {
        compositeStyleParts.push(`Narrative direction: ${story}`);
      }
      if (instrumentation) {
        compositeStyleParts.push(`Instrumentation: ${instrumentation}`);
      }
      if (state.selectedVoice) {
        compositeStyleParts.push(`Lead vocal preference: ${state.selectedVoice.label}`);
      }

      const payload = {
        mode: lyricSource ? 'custom' : 'no-custom',
        style: compositeStyleParts.join(' \n\n '),
        duration: durationValue,
        lyrics: lyricSource || undefined,
        title: title || undefined
      };

      updateStatus(renderStatus, 'Connexion à Suno…', 'default');
      const button = qs('#renderBtn');
      button.disabled = true;

      try {
        const response = await fetch('/ai-composer.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });

        const json = await response.json().catch(() => null);
        if (!response.ok || !json) {
          const errorMessage = json && json.error ? json.error : 'La génération a échoué.';
          throw new Error(errorMessage);
        }

        if (json.ok) {
          updateStatus(renderStatus, 'Piste générée ! 🚀', 'success');
          pushResult(json);
        } else {
          throw new Error(json.error || 'Réponse inattendue.');
        }
      } catch (error) {
        console.error(error);
        updateStatus(renderStatus, error instanceof Error ? error.message : 'Erreur de rendu.', 'error');
      } finally {
        button.disabled = false;
        resetRenderStatus(4000);
      }
    }

    function pushResult(payload) {
      if (emptyState) {
        emptyState.hidden = true;
      }

      const card = document.createElement('article');
      card.className = 'result-card';

      const header = document.createElement('div');
      header.innerHTML = `<strong>Job ID:</strong> <code>${payload.requestId || payload.jobId || '—'}</code>`;
      card.appendChild(header);

      if (payload.message) {
        const message = document.createElement('p');
        message.textContent = payload.message;
        message.className = 'helper';
        card.appendChild(message);
      }

      if (Array.isArray(payload.audioUrls) && payload.audioUrls.length) {
        payload.audioUrls.forEach((url, index) => {
          const label = document.createElement('p');
          label.innerHTML = `<strong>Render ${index + 1}</strong>`;
          card.appendChild(label);

          const audio = document.createElement('audio');
          audio.controls = true;
          audio.src = url;
          card.appendChild(audio);
        });
      } else if (payload.previewUrl) {
        const audio = document.createElement('audio');
        audio.controls = true;
        audio.src = payload.previewUrl;
        card.appendChild(audio);
      }

      if (payload.lyrics) {
        const lyricsBlock = document.createElement('pre');
        lyricsBlock.textContent = payload.lyrics;
        lyricsBlock.style.whiteSpace = 'pre-wrap';
        lyricsBlock.style.fontSize = '0.85rem';
        lyricsBlock.style.color = 'var(--text-muted)';
        lyricsBlock.style.border = '1px solid rgba(148, 163, 184, 0.2)';
        lyricsBlock.style.borderRadius = 'var(--radius-sm)';
        lyricsBlock.style.padding = '0.85rem';
        card.appendChild(lyricsBlock);
      }

      resultsContainer.prepend(card);
    }

    qs('#generateLyricsBtn').addEventListener('click', handleGenerateLyrics);
    qs('#renderBtn').addEventListener('click', handleRender);

    renderStyleChips();
    renderInstrumentChips();
    renderVoiceChips();
    setLyricMode('ai');
  </script>
</body>
</html>
