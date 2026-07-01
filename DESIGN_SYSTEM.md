# One System — Design System

A restrained, enterprise design language. The goal is a deliberately-designed,
trustworthy product — **not** the generic "AI-generated" look (no gradients,
no glassmorphism, no glowing orbs, no rainbow icon colors, no multiple fonts).

## Principles

1. **One neutral foundation + one accent.** Slate neutrals carry the UI; a single
   deep indigo accent (`#4f46e5`) is used sparingly for primary actions, links,
   and active states. No other brand colors.
2. **Flat surfaces.** White/canvas backgrounds, thin 1px borders (`--color-line`),
   soft low shadows. Never gradients, backdrop-blur glass, or glow effects.
3. **Monochrome icons.** Icons render in `--color-accent`, `--color-ink-2`, or
   `--color-muted` only. Never blue/green/purple/orange icon sets.
4. **One type family.** IBM Plex Sans for UI, IBM Plex Mono for code. No Inter,
   figtree, or Instrument Sans.
5. **Generous whitespace, clear hierarchy, sentence case.**
6. **One CSS pipeline.** Everything uses the Vite/Tailwind v4 build (`@vite`).
   The Tailwind Play CDN (`cdn.tailwindcss.com`) is **banned** in all views.

## Tokens (defined in `resources/css/app.css` `@theme`)

| Token | Value | Use |
|---|---|---|
| `--color-canvas` | `#f8fafc` | page background |
| `--color-surface` | `#ffffff` | cards, panels, nav |
| `--color-surface-2` | `#f1f5f9` | subtle fills |
| `--color-line` | `#e6e9ef` | default borders |
| `--color-line-strong` | `#cbd5e1` | emphasis borders, inputs |
| `--color-ink` | `#0f172a` | primary text, dark panels |
| `--color-ink-2` | `#334155` | secondary headings/text |
| `--color-muted` | `#64748b` | muted text |
| `--color-faint` | `#94a3b8` | hints, captions |
| `--color-accent` | `#4f46e5` | primary accent |
| `--color-accent-strong` | `#4338ca` | accent hover |
| `--color-accent-soft` | `#eef2ff` | accent tint backgrounds |
| `--color-accent-line` | `#c7d2fe` | accent borders |

Status colors (`--color-success/danger/warning` + `*-soft`) exist for alerts only,
used muted and sparingly.

In Blade, reference tokens with arbitrary Tailwind values:
`bg-[var(--color-surface)]`, `text-[var(--color-muted)]`, `border-[var(--color-line)]`.

## Component classes (in `app.css`, prefix `os-`)

- `os-container` — centered max-width page container with responsive padding.
- `os-btn` + `os-btn-primary` / `os-btn-secondary` / `os-btn-ghost`; modifiers `os-btn-lg`, `os-btn-block`.
- `os-card` (+ `os-card-pad`, `os-card-hover`) — surface panel.
- `os-icon-tile` (+ `os-icon-tile-ink`) — square monochrome icon container.
- `os-label`, `os-input` — form fields.
- `os-badge` (+ `os-badge-accent`) — pills/status.
- `os-alert` (+ `os-alert-success` / `os-alert-danger` / `os-alert-warning`).
- `os-eyebrow` — uppercase accent kicker.
- `os-section` — vertical section rhythm.
- `os-code-inline`, `os-codeblock` (+ `os-codeblock-head`) — documentation code.

## Per-page head boilerplate (standalone pages only)

```blade
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=ibm-plex-sans:400,500,600,700|ibm-plex-mono:400,500&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

Pages that `@extends` a layout inherit this from the layout — do not duplicate it.

## Hard rules when refactoring a view

- **Never** change behavior: preserve every `wire:*`, `x-*`/Alpine directive,
  `@livewire`/`<livewire:>`, `@csrf`, form `action`/`method`, `route()`/`url()`,
  `{{ }}`/`{!! !!}`, `@if/@foreach/@php`, `RecaptchaV3::*`, and element `id`/`name`.
- Remove `<script src="https://cdn.tailwindcss.com"></script>`.
- On Livewire-enabled pages (`@livewireScripts` present), remove the standalone
  `unpkg alpinejs` CDN — Livewire 3 bundles Alpine. Keep Alpine on non-Livewire
  pages that need it (add to the Vite bundle or a pinned CDN).
- Replace Inter/figtree/Instrument Sans with IBM Plex (via the layout's fonts).
- Replace multi-color icon treatments with monochrome accent/ink.
- Prefer `os-*` component classes; use token-based utilities for everything else.
