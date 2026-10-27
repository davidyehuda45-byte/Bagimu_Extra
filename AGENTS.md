# AGENTS.md — Panduan AI Workspace

Dokumen panduan konteks kerja, token guardrail, dan daftar custom skills untuk Antigravity AI.

## Aturan Utama (Token Guardrail)
1. **Targeted Reading**: Hanya baca file yang secara eksplisit diminta atau relevan langsung dengan tugas. Dilarang melakukan repo-wide scanning.
2. **Folder Terlarang**: Jangan pernah membaca direktori `node_modules/`, `vendor/`, `.git/`, `storage/`, `build/`, `dist/`, serta file lock (`composer.lock`, `package-lock.json`).
3. **Design System**: Aksen utama menggunakan palet **Biru** (`#1e3a8a`, `#2563eb`, `blue-600/700/800/900`).
4. **Git Safety**: Tidak melakukan commit/push tanpa instruksi eksplisit dari pengguna.

## Daftar Kumpulan Custom Skills
| Skill | Lokasi Global | Fungsi Utama |
|---|---|---|
| `context-sync` | `~/.gemini/config/skills/context-sync/SKILL.md` | Auto-update file konteks/aturan proyek tanpa scan penuh |
| `scaffold-crud` | `~/.gemini/config/skills/scaffold-crud/SKILL.md` | Full-stack CRUD generator berbasis 1 modul pembanding |
| `api-bridge` | `~/.gemini/config/skills/api-bridge/SKILL.md` | Generate Type-safe DTO & API Client (TypeScript / Dart) |
| `security-audit` | `~/.gemini/config/skills/security-audit/SKILL.md` | Audit keamanan & bug kritis pada file perubahan git |
| `smart-pr` | `~/.gemini/config/skills/smart-pr/SKILL.md` | Generate Conventional Commit & PR description GitHub |
| `spec-to-tasks` | `~/.gemini/config/skills/spec-to-tasks/SKILL.md` | Breakdown ide fitur jadi skema DB, API, UI & checklist tim |
| `deploy-prep` | `~/.gemini/config/skills/deploy-prep/SKILL.md` | Generator Docker, compose, reverse proxy & optimasi server |
| `tech-translator` | `~/.gemini/config/skills/tech-translator/SKILL.md` | Terjemahkan progres teknis ke laporan formal & User Manual |
