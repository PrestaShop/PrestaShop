# PrestaShop - Claude Code Instructions

> **Keep this file lean.** This file is loaded into the AI context for every conversation in this repository. To avoid wasting context tokens, keep instructions here short and actionable. For detailed workflows, create a skill in `.claude/skills/` instead. Do not duplicate information that can be found in the codebase, git history, or existing documentation.

## Branching & Versioning

PrestaShop follows [SemVer](https://semver.org/). Active branches merge upward: `9.1.x` → `develop`. Target the lowest applicable branch.

- **`9.1.x`**: Current stable (patch releases). Bug fixes and minor improvements only — no new features.
- **`develop`**: Next minor (9.2.0). New features and improvements go here. No breaking changes.
- **`8.2.x`**: LTS, security fixes only. Rarely modified.

Breaking changes are only allowed in major versions. See [ADR 0017](https://github.com/PrestaShop/adr/blob/master/0017-backward-compatibility-promise.md) for the backward compatibility promise. More architecture decisions at https://github.com/PrestaShop/adr.
