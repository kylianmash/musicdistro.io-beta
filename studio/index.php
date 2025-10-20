<?php
declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

$language = current_language();
$direction = language_direction();
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" dir="<?= htmlspecialchars($direction, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MusicDistro Studio</title>
    <meta name="description" content="Compose, arrange, and mix right in your browser with MusicDistro Studio." />
    <link rel="icon" type="image/svg+xml" href="/assets/musicdistro-icon.svg" />
    <link rel="manifest" href="/studio/manifest.json" />
    <style>
      :root {
        color-scheme: dark;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        --background: #020617;
        --surface: rgba(15, 23, 42, 0.75);
        --panel: rgba(15, 23, 42, 0.55);
        --border: rgba(148, 163, 184, 0.18);
        --border-strong: rgba(148, 163, 184, 0.32);
        --text: #e2e8f0;
        --text-muted: #94a3b8;
        --emerald: #34d399;
        --emerald-strong: #10b981;
        --danger: #f87171;
        --grid-primary: rgba(148, 163, 184, 0.22);
        --grid-secondary: rgba(71, 85, 105, 0.35);
        --clip-shadow: rgba(16, 185, 129, 0.3);
      }

      * {
        box-sizing: border-box;
      }

      html,
      body {
        margin: 0;
        min-height: 100vh;
        background: radial-gradient(circle at top, rgba(14, 116, 144, 0.16), transparent 55%), var(--background);
        color: var(--text);
      }

      a {
        color: inherit;
        text-decoration: none;
      }

      button {
        appearance: none;
        border: none;
        border-radius: 0.75rem;
        padding: 0.55rem 1.4rem;
        font-size: 0.85rem;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.25s ease, background 0.2s ease;
        background: rgba(30, 41, 59, 0.8);
        color: var(--text);
        border: 1px solid rgba(148, 163, 184, 0.18);
      }

      button:hover {
        transform: translateY(-1px);
        border-color: rgba(148, 163, 184, 0.35);
      }

      button:disabled {
        opacity: 0.55;
        cursor: not-allowed;
        transform: none;
      }

      .btn-primary {
        background: var(--emerald);
        color: #02131c;
        border-color: transparent;
        box-shadow: 0 18px 45px rgba(52, 211, 153, 0.28);
      }

      .btn-primary:hover {
        background: var(--emerald-strong);
      }

      .btn-ghost {
        background: transparent;
        border-style: dashed;
        border-color: rgba(148, 163, 184, 0.35);
        color: var(--text-muted);
      }

      .btn-ghost:hover {
        color: var(--text);
        border-color: rgba(148, 163, 184, 0.55);
      }

      .btn-icon {
        padding: 0.45rem 0.8rem;
        border-radius: 0.65rem;
        font-size: 0.75rem;
        letter-spacing: 0.25em;
        background: rgba(15, 23, 42, 0.75);
      }

      header {
        padding: 1rem 2.25rem;
        border-bottom: 1px solid var(--border);
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.85), rgba(2, 6, 23, 0.9));
        backdrop-filter: blur(12px);
        position: sticky;
        top: 0;
        z-index: 50;
      }

      header .header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 2rem;
        flex-wrap: wrap;
      }

      header .brand {
        display: flex;
        align-items: center;
        gap: 1.5rem;
      }

      header .brand span {
        font-size: 0.75rem;
        letter-spacing: 0.4em;
        text-transform: uppercase;
        color: var(--emerald);
        font-weight: 600;
      }

      header .status-pill {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        border-radius: 999px;
        padding: 0.55rem 1.5rem;
        background: rgba(15, 23, 42, 0.85);
        font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, monospace;
        font-size: 0.75rem;
        color: var(--text-muted);
      }

      header .status-pill span {
        color: var(--text);
      }

      .transport {
        margin-top: 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
      }

      .transport button {
        padding: 0.5rem 1rem;
        letter-spacing: 0.3em;
        font-size: 0.72rem;
      }

      .transport .meters {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, monospace;
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-left: auto;
      }

      .layout {
        display: grid;
        grid-template-columns: 280px minmax(0, 1fr) 320px;
        min-height: calc(100vh - 220px);
        border-bottom: 1px solid var(--border);
      }

      aside {
        background: rgba(15, 23, 42, 0.55);
        padding: 1.75rem 1.5rem;
        border-right: 1px solid var(--border);
        overflow-y: auto;
      }

      aside h2 {
        margin: 0;
        font-size: 0.72rem;
        letter-spacing: 0.32em;
        text-transform: uppercase;
        color: var(--text-muted);
      }

      aside p {
        font-size: 0.85rem;
        line-height: 1.7;
        color: rgba(148, 163, 184, 0.92);
      }

      aside:last-of-type {
        border-right: none;
        border-left: 1px solid var(--border);
      }

      .timeline-shell {
        position: relative;
        display: flex;
        flex-direction: column;
        background: rgba(2, 6, 23, 0.88);
        overflow: hidden;
      }

      .timeline-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border);
        background: rgba(15, 23, 42, 0.75);
        gap: 1rem;
      }

      .timeline-toolbar .left,
      .timeline-toolbar .right {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
      }

      .timeline-toolbar .hint {
        font-size: 0.75rem;
        color: var(--text-muted);
      }

      .arrangement {
        position: relative;
        flex: 1;
        display: flex;
        overflow: hidden;
      }

      .track-sidebar {
        width: 240px;
        border-right: 1px solid var(--border);
        background: rgba(15, 23, 42, 0.7);
        overflow-y: auto;
      }

      .track-sidebar h3 {
        margin: 0;
        padding: 0.75rem 1rem;
        font-size: 0.72rem;
        letter-spacing: 0.3em;
        color: var(--text-muted);
        text-transform: uppercase;
      }

      .track-list {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
        padding: 1rem;
      }

      .track-card {
        border-radius: 1rem;
        padding: 0.9rem;
        border: 1px solid rgba(148, 163, 184, 0.18);
        background: rgba(15, 23, 42, 0.6);
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        transition: border-color 0.2s ease, background 0.2s ease;
      }

      .track-card.active {
        border-color: rgba(52, 211, 153, 0.65);
        background: rgba(16, 185, 129, 0.08);
      }

      .track-card header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0;
        border: none;
        background: transparent;
        position: static;
      }

      .track-card header .badge {
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
        background: rgba(52, 211, 153, 0.18);
        color: var(--emerald);
        font-size: 0.65rem;
        letter-spacing: 0.3em;
        text-transform: uppercase;
      }

      .track-controls {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.75rem;
      }

      .track-toggles {
        display: flex;
        gap: 0.55rem;
        font-size: 0.65rem;
        letter-spacing: 0.35em;
      }

      .track-toggles button {
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.75);
        border: 1px solid rgba(148, 163, 184, 0.2);
      }

      .track-toggles button.active-mute {
        background: rgba(248, 113, 113, 0.22);
        color: #fecaca;
        border-color: transparent;
      }

      .track-toggles button.active-solo {
        background: rgba(52, 211, 153, 0.2);
        color: var(--emerald);
        border-color: transparent;
      }

      .timeline-scroll {
        flex: 1;
        position: relative;
        overflow: auto;
        background: rgba(2, 6, 23, 0.95);
      }

      .timeline-grid {
        position: relative;
      }

      .timeline-ruler {
        position: sticky;
        top: 0;
        display: flex;
        align-items: center;
        background: rgba(15, 23, 42, 0.92);
        border-bottom: 1px solid rgba(148, 163, 184, 0.15);
        height: 48px;
        z-index: 5;
      }

      .timeline-ruler .measure {
        position: relative;
        height: 100%;
        border-right: 1px solid rgba(148, 163, 184, 0.12);
        font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, monospace;
        font-size: 0.7rem;
        color: rgba(148, 163, 184, 0.7);
        display: flex;
        align-items: center;
        padding-left: 0.75rem;
      }

      .timeline-ruler .grid-line {
        position: absolute;
        top: 28px;
        bottom: 0;
        width: 1px;
        background: rgba(148, 163, 184, 0.18);
      }

      .track-rows {
        position: relative;
      }

      .track-row {
        position: relative;
        min-height: 120px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.12);
        background: rgba(15, 23, 42, 0.48);
      }

      .track-row:nth-child(odd) {
        background: rgba(11, 20, 38, 0.5);
      }

      .clip {
        position: absolute;
        top: 24px;
        height: 72px;
        border-radius: 0.85rem;
        border: 1px solid transparent;
        color: var(--text);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 0.85rem 1rem;
        box-shadow: 0 18px 40px var(--clip-shadow);
        cursor: pointer;
        transition: transform 0.15s ease, border-color 0.2s ease;
      }

      .clip.selected {
        transform: translateY(-2px);
        border-color: rgba(52, 211, 153, 0.75);
        box-shadow: 0 20px 42px rgba(16, 185, 129, 0.35);
      }

      .clip .meta {
        font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, monospace;
        font-size: 0.65rem;
        color: rgba(226, 232, 240, 0.8);
        display: flex;
        align-items: center;
        justify-content: space-between;
        text-transform: uppercase;
        letter-spacing: 0.25em;
      }

      .clip .handles {
        position: absolute;
        inset: 0;
        display: flex;
        justify-content: space-between;
        pointer-events: none;
      }

      .clip .handle {
        width: 8px;
        pointer-events: auto;
        cursor: ew-resize;
        background: rgba(15, 23, 42, 0.35);
        border-radius: 999px;
        margin: 6px;
        opacity: 0;
        transition: opacity 0.15s ease;
      }

      .clip:hover .handle,
      .clip.selected .handle {
        opacity: 1;
      }

      .timeline-floaters {
        position: absolute;
        right: 1.5rem;
        bottom: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        z-index: 20;
      }

      .zoom-widget {
        position: absolute;
        left: 1.5rem;
        bottom: 1.5rem;
        padding: 1rem;
        border-radius: 1.5rem;
        background: rgba(15, 23, 42, 0.7);
        border: 1px solid rgba(148, 163, 184, 0.2);
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        min-width: 220px;
      }

      .zoom-widget header {
        padding: 0;
        border: none;
        background: transparent;
        position: static;
        display: flex;
        justify-content: space-between;
        align-items: center;
      }

      .zoom-widget header span {
        font-size: 0.65rem;
        letter-spacing: 0.35em;
        color: var(--text-muted);
        text-transform: uppercase;
      }

      .zoom-widget input[type="range"] {
        width: 100%;
      }

      input[type="range"] {
        appearance: none;
        background: rgba(30, 41, 59, 0.85);
        height: 6px;
        border-radius: 999px;
        outline: none;
      }

      input[type="range"]::-webkit-slider-thumb {
        appearance: none;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: var(--emerald);
        cursor: pointer;
      }

      input[type="range"]::-moz-range-thumb {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: var(--emerald);
        border: none;
        cursor: pointer;
      }

      .loop-overlay {
        position: absolute;
        top: 48px;
        bottom: 0;
        background: rgba(16, 185, 129, 0.12);
        border-left: 1px dashed rgba(16, 185, 129, 0.65);
        border-right: 1px dashed rgba(16, 185, 129, 0.65);
        pointer-events: none;
      }

      .playhead {
        position: absolute;
        top: 48px;
        bottom: 0;
        width: 2px;
        background: rgba(52, 211, 153, 0.9);
        pointer-events: none;
        z-index: 10;
      }

      footer {
        padding: 2rem 2.25rem 3rem;
        background: rgba(15, 23, 42, 0.7);
        border-top: 1px solid var(--border);
      }

      footer h3 {
        margin: 0 0 1.25rem 0;
        font-size: 0.72rem;
        letter-spacing: 0.32em;
        text-transform: uppercase;
        color: rgba(148, 163, 184, 0.85);
      }

      .mixer {
        overflow-x: auto;
        padding-bottom: 1rem;
      }

      .mixer .channels {
        display: flex;
        gap: 1.5rem;
        min-width: max-content;
      }

      .channel-card {
        min-width: 200px;
        border-radius: 1.5rem;
        padding: 1.25rem 1.1rem;
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(148, 163, 184, 0.18);
        display: flex;
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
      }

      .meter {
        height: 160px;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(148, 163, 184, 0.15);
        overflow: hidden;
        position: relative;
      }

      .meter-fill {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(180deg, rgba(52, 211, 153, 0.75), rgba(15, 118, 110, 0.5));
        height: 20%;
      }

      .plugin-dock {
        display: grid;
        gap: 1rem;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      }

      .plugin-card {
        padding: 1.4rem 1.35rem;
        border-radius: 1.35rem;
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(148, 163, 184, 0.18);
        display: flex;
        flex-direction: column;
        gap: 1rem;
      }

      .plugin-card header {
        padding: 0;
        border: none;
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: space-between;
      }

      .plugin-card header span {
        font-size: 0.65rem;
        letter-spacing: 0.35em;
        color: rgba(94, 234, 212, 0.85);
      }

      .plugin-controls {
        display: grid;
        gap: 0.75rem;
        font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, monospace;
        font-size: 0.75rem;
        color: rgba(148, 163, 184, 0.85);
      }

      .plugin-controls label {
        display: flex;
        flex-direction: column;
        gap: 0.55rem;
      }

      .plugin-controls input[type="range"] {
        width: 100%;
      }

      .inspector-section {
        margin-top: 1.25rem;
      }

      .inspector-section:first-of-type {
        margin-top: 0;
      }

      .inspector-panel {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
      }

      .inspector-panel .card {
        border-radius: 1.25rem;
        padding: 1.1rem 1rem;
        border: 1px solid rgba(148, 163, 184, 0.2);
        background: rgba(15, 23, 42, 0.55);
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
      }

      .inspector-panel label {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        font-size: 0.82rem;
      }

      .inspector-panel input,
      .inspector-panel select,
      .inspector-panel textarea {
        border-radius: 0.75rem;
        border: 1px solid rgba(148, 163, 184, 0.25);
        background: rgba(15, 23, 42, 0.65);
        color: var(--text);
        padding: 0.65rem 0.9rem;
        font-size: 0.85rem;
        outline: none;
      }

      .inspector-panel input:focus,
      .inspector-panel select:focus,
      .inspector-panel textarea:focus {
        border-color: rgba(52, 211, 153, 0.5);
      }

      .inspector-panel .grid-label {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.35em;
        color: rgba(148, 163, 184, 0.75);
      }

      .midi-grid {
        display: grid;
        grid-template-columns: repeat(16, minmax(0, 1fr));
        gap: 1px;
        border-radius: 1rem;
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, 0.2);
        background: rgba(15, 23, 42, 0.6);
      }

      .midi-cell {
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.68rem;
        letter-spacing: 0.15em;
        color: rgba(148, 163, 184, 0.75);
        cursor: pointer;
        transition: background 0.15s ease, color 0.15s ease;
      }

      .midi-cell:nth-child(16n + 1) {
        background: rgba(15, 23, 42, 0.75);
      }

      .midi-cell.active {
        background: rgba(52, 211, 153, 0.75);
        color: #02131c;
        font-weight: 600;
      }

      .empty-state {
        padding: 1rem;
        border-radius: 1rem;
        border: 1px dashed rgba(148, 163, 184, 0.3);
        background: rgba(15, 23, 42, 0.45);
        color: rgba(148, 163, 184, 0.75);
        font-size: 0.85rem;
        text-align: center;
      }

      .tag {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.7rem;
        border-radius: 999px;
        font-size: 0.68rem;
        letter-spacing: 0.28em;
        text-transform: uppercase;
        background: rgba(30, 41, 59, 0.75);
        color: var(--text-muted);
      }

      .flex {
        display: flex;
      }

      .flex-between {
        display: flex;
        justify-content: space-between;
        align-items: center;
      }

      .flex-column {
        display: flex;
        flex-direction: column;
      }

      .gap-sm {
        gap: 0.5rem;
      }

      @media (max-width: 1280px) {
        .layout {
          grid-template-columns: minmax(0, 1fr);
        }

        aside:first-of-type,
        aside:last-of-type {
          display: none;
        }
      }

      @media (max-width: 1024px) {
        header {
          padding: 1rem;
        }

        .layout {
          min-height: auto;
        }
      }
    </style>
  </head>
  <body>
    <header>
      <div class="header-row">
        <div class="brand">
          <span>MusicDistro</span>
          <div>
            <h1 style="margin:0;font-size:1.85rem;font-weight:600;color:var(--text);">Studio</h1>
            <p style="margin:0.35rem 0 0;font-size:0.95rem;color:rgba(148,163,184,0.85);max-width:520px;">
              Multitrack production environment with live audio, MIDI sequencing, real-time effects, and mixdown rendering right in your browser.
            </p>
          </div>
        </div>
        <div class="status-pill">
          <span id="status-bpm">BPM 120.0</span>
          <span id="status-tracks">Tracks 0</span>
          <span id="status-clips">Clips 0</span>
          <span id="status-grid">Grid 1/4</span>
        </div>
        <div class="controls">
          <button class="btn-ghost" id="load-project">Load</button>
          <button class="btn-ghost" id="save-project">Save</button>
          <button class="btn-primary" id="export-wav">Export WAV</button>
          <button class="btn-ghost" id="export-mp3">Export MP3</button>
        </div>
      </div>
      <div class="transport">
        <div class="flex gap-sm" style="align-items:center;">
          <button id="play-toggle" class="btn-icon">PLAY</button>
          <button id="stop" class="btn-icon">STOP</button>
          <button id="loop-toggle" class="btn-icon">LOOP</button>
          <button id="metronome-toggle" class="btn-icon">CLIK</button>
          <button id="add-audio-track" class="btn-icon">+ AUDIO</button>
          <button id="add-instrument-track" class="btn-icon">+ INST</button>
          <button id="add-midi-clip" class="btn-icon">+ MIDI</button>
          <label class="tag">BPM
            <input id="bpm-input" type="number" value="120" min="40" max="240" step="0.5" style="width:80px;margin-left:0.5rem;background:transparent;border:none;color:var(--emerald);font-family:'JetBrains Mono',monospace;font-size:0.85rem;" />
          </label>
          <label class="tag">Grid
            <select id="grid-select" style="background:transparent;border:none;color:var(--text);font-size:0.8rem;">
              <option value="1/1">1/1</option>
              <option value="1/2">1/2</option>
              <option value="1/4" selected>1/4</option>
              <option value="1/8">1/8</option>
              <option value="1/16">1/16</option>
            </select>
          </label>
        </div>
        <div class="meters">
          <span id="playhead-readout">00:00.0</span>
          <span id="loop-readout">Loop OFF</span>
          <span id="metronome-readout">Metronome OFF</span>
        </div>
      </div>
    </header>

    <div class="layout">
      <aside>
        <h2>Quick actions</h2>
        <p>
          Drop WAV or MP3 files anywhere in the arrangement, record automation, or sketch ideas with the built-in instruments. Drag clips to move, resize with the edge handles, duplicate with the floating actions, and toggle looping per clip.
        </p>
        <div class="inspector-section">
          <h2>Session tools</h2>
          <div class="inspector-panel">
            <div class="card">
              <label>Project name
                <input id="project-name" type="text" value="Untitled Session" />
              </label>
              <button id="reset-session" class="btn-ghost">Reset session</button>
            </div>
            <div class="card">
              <label>Tempo automation (BPM)
                <textarea id="tempo-automation" rows="4" placeholder="Beat:Value (one per line)"></textarea>
              </label>
              <button id="apply-tempo" class="btn-ghost">Apply tempo map</button>
            </div>
          </div>
        </div>
      </aside>

      <section class="timeline-shell">
        <div class="timeline-toolbar">
          <div class="left">
            <span class="hint">Drop audio to import • Shift + drag to draw loop • Double-click clip for inspector focus</span>
          </div>
          <div class="right">
            <span class="tag" id="memory-usage">RAM 0 MB</span>
            <span class="tag" id="cpu-usage">CPU 0%</span>
          </div>
        </div>
        <div class="arrangement">
          <div class="track-sidebar">
            <h3>Tracks</h3>
            <div class="track-list" id="track-list"></div>
          </div>
          <div class="timeline-scroll" id="timeline-scroll">
            <div class="timeline-grid" id="timeline-grid">
              <div class="timeline-ruler" id="timeline-ruler"></div>
              <div class="track-rows" id="track-rows"></div>
              <div class="loop-overlay" id="loop-overlay" style="display:none;"></div>
              <div class="playhead" id="playhead" style="display:none;"></div>
            </div>
          </div>
          <div class="timeline-floaters">
            <button id="duplicate-clip" class="btn-primary">Duplicate Clip</button>
            <button id="delete-clip" class="btn-ghost">Delete Clip</button>
          </div>
          <div class="zoom-widget">
            <header>
              <span>Zoom</span>
              <strong id="zoom-level" style="font-family:'JetBrains Mono',monospace;font-size:0.85rem;color:var(--emerald);">1.0x</strong>
            </header>
            <label>Horizontal
              <input type="range" id="zoom-horizontal" min="0.5" max="3" step="0.1" value="1" />
            </label>
            <label>Vertical
              <input type="range" id="zoom-vertical" min="0.6" max="1.6" step="0.05" value="1" />
            </label>
          </div>
        </div>
      </section>

      <aside>
        <h2>Inspector</h2>
        <div class="inspector-panel" id="inspector"></div>
      </aside>
    </div>

    <footer>
      <div class="mixer">
        <h3>Mixer</h3>
        <div class="channels" id="mixer-channels"></div>
      </div>
      <div class="plugin-suite" style="margin-top:2rem;">
        <h3>Creative effects</h3>
        <div class="plugin-dock" id="plugin-dock"></div>
      </div>
    </footer>

    <script src="https://unpkg.com/lamejs@1.2.0/lame.min.js"></script>
    <script src="/studio/app.js" type="module"></script>
  </body>
</html>
