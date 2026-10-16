# .ai/ — AI Context Architecture

## Purpose

This folder is the **single source of truth** for all AI-assisted development context in the PrestaShop project. It provides consistent guidance to every AI coding tool used by contributors — whether that's Claude Code, Cursor, GitHub Copilot, Windsurf, Gemini CLI, or a web-based assistant like ChatGPT or Claude.ai.

## Why centralized?

Each AI tool has its own configuration format (`CLAUDE.md`, `.cursorrules`, `.github/copilot-instructions.md`, `AGENTS.md`, `GEMINI.md`, `.windsurfrules`…). Maintaining one context file per tool per domain leads to fragmentation and drift. Instead:

- **This `.ai/` folder** holds all the context, organized by domain and component.
- **Pointer files at the repository root** (`CLAUDE.md`, `AGENTS.md`, `.cursorrules`, etc.) are lightweight bridges that reference this folder. They contain no context themselves — only references.
- **One place to update** — when conventions change, you update the `.ai/` file. Pointer files never need to change.

## Folder structure

```
.ai/
├── CONTEXT.md              # Root context: project-wide rules, architecture, and index of all sub-contexts
├── STRUCTURE.md            # This file — explains the architecture of the .ai/ folder
│
├── skills/                 # Cross-cutting skills not tied to a single domain or component
│   └── {skill-name}/
│       └── SKILL.md
│
├── rules/                  # Path-scoped rules: source of the per-tool copies the generator writes (.claude/rules symlinks to it)
│   └── {name}.md
│
├── Domain/                 # Business domain contexts (maps to src/Core/Domain/)
│   ├── {DomainName}/
│   │   ├── CONTEXT.md      # Domain-specific conventions, patterns, do/don't rules
│   │   └── skills/         # (optional) Domain-scoped skills
│   │       └── {skill-name}/SKILL.md
│   └── ...
│
├── Component/              # Cross-cutting component contexts (maps to shared infrastructure)
│   ├── {ComponentName}/
│   │   ├── CONTEXT.md      # Component-specific conventions, usage patterns, do/don't rules
│   │   └── skills/         # (optional) Component-scoped skills
│   │       └── {skill-name}/SKILL.md
│   └── ...
│
└── generated/              # Pre-built index snapshots (cqrs.md, routes.md, entities.md, hooks.md)
                            # Useful when no PHP runtime is available (web-based assistants, CI).
                            # When PHP is available, prefer live console commands (see CONTEXT.md).
```

## File conventions

| File | Purpose | Target size |
|------|---------|-------------|
| `CONTEXT.md` | Conventions, patterns, do/don't rules for a domain or component | < 200 lines |
| `SKILL.md` | Step-by-step task template an AI agent can follow to accomplish a recurring task | Prefer concise — no hard limit, but shorter skills consume less context |
| `STRUCTURE.md` | This file — architecture documentation | N/A |
| `GOTCHAS.md` | Cross-domain naming traps, identity pitfalls, and legacy mismatches | N/A |
| `MULTISTORE.md` | Cross-cutting multi-store guide: ShopConstraint, scoped config, multi-shop repositories | N/A |

### CONTEXT.md template

Every `CONTEXT.md` follows this structure:

```markdown
# {Domain or Component Name}

## Purpose
[1-2 sentences: what this domain/component does, what it does NOT do]

## Layers
[Table: Layer name | Path — paths only, no class inventories]

## Non-obvious patterns
[Bullet points for things that aren't discoverable by reading the directory structure:
surprising abstractions, delegation chains, legacy gotchas, cross-domain flows]

## Canonical examples
- [File path — 1-line description]

## Related
- [Links to related domains, components, or documentation]
```

**What NOT to include:** class name inventories (commands, queries, exceptions, handlers, value objects). These can always be found by grepping or globbing — listing them wastes context tokens without adding value.

### SKILL.md project conventions

`SKILL.md` files in this project follow two PrestaShop-specific conventions on top of the standard skill format.

#### Description rule

Skill descriptions must front-load **what the skill does + trigger phrases** — nothing else. They are short and shown in skill listings, so every word counts. In particular:

- **Do not write "Read Component/X/CONTEXT.md for conventions" in the description.** That instruction belongs in the skill body. Repeating it in the description wastes characters and is redundant for any tool that reads the body.
- The body of every skill that depends on a component context already starts with `Read @.ai/Component/{Component}/CONTEXT.md for ...` — that is the canonical location.

#### Custom frontmatter fields (`needs`, `produces`, `conditional`, `subagent`)

These fields are **a PrestaShop project convention**, not part of any official skill specification. Claude Code reads frontmatter as plain text and does not interpret these fields as semantic metadata, but they remain visible to AI agents and human readers and they document the skill's place in larger workflows.

| Field | Purpose |
|-------|---------|
| `needs` | List of skill names this skill logically depends on (prerequisites). Use **skill names**, not opaque IDs or step numbers. Empty list `[]` = no dependencies. |
| `produces` | A short string describing the artifact(s) the skill creates — useful when chaining skills in an orchestrator. |
| `conditional` | When to skip the skill entirely. Example: `"only if the grid has bulk actions"`. |
| `subagent` | Declares the skill is well-suited to **sub-agent delegation** when the parent agent supports the primitive (e.g. Claude Code). Values: `recommended` or `optional`. Tool-neutral — non-Claude tools ignore the field and run the skill in-line. |

**Top-down rule:** dependencies are declared **top-down only**. A skill states what it needs; it never declares what needs it. This keeps each skill standalone — it does not know who calls it.

**Standalone rule:** every skill must remain usable independently of any workflow. `needs` documents a logical prerequisite ("you should have a repository before implementing handlers"), not a hard runtime dependency. An orchestrator skill (e.g. [`legacy-to-symfony-migration`](Component/Migration/skills/legacy-to-symfony-migration/SKILL.md)) is responsible for ordering — individual skills just need to be invocable on their own.

**`subagent` semantics.** Sub-agents pay off when input is large, output is structured/small, and the small output is enough for downstream work — i.e. the skill produces a written artifact at a known path. Use `recommended` for read-heavy skills with high payoff (typically audits and context generators); use `optional` for moderate, file-output mechanical work where delegation works but the gain is modest. Like `conditional`, **absence is the default** — the skill runs in-line in the parent context. Orchestrator skills also leave the field absent: they are themselves the parent of any spawned sub-agents.

### Writing guidelines

- **Be concise** — use bullet points and tables, not paragraphs. AI parses structured content more reliably.
- **No code dumps** — link to canonical example files, don't inline full class implementations.
- **Describe patterns, not inventories** — write `Handlers follow Domain/{Action}{Entity}Handler.php` instead of listing every handler file.
- **Don't repeat parent context** — domain/component files describe architecture and relationships only. Coding standards, Do/Don't rules, and testing expectations are project-wide and live exclusively in the root `CONTEXT.md`.
- **No tool-specific syntax** — CONTEXT.md files must work for any AI tool or human reader.

## How AI tools discover this context

### Automatic loading (via pointer files at repo root)

| Tool | Pointer file | How it works |
|------|-------------|--------------|
| **Claude Code** | `CLAUDE.md` + `.claude/rules/*.md` | Uses `@.ai/CONTEXT.md` reference — loaded at session start. Agent reads domain/component files on demand. `.claude/rules/*.md` are symlinks to the shared path-scoped rules of `.ai/rules/` (see below). When `modules/ps_apiresources` is a symlink to a separate clone, the module context still reaches the agent through the routing table and the `admin-api` rule; `CLAUDE_CODE_ADDITIONAL_DIRECTORIES_CLAUDE_MD=1 claude --add-dir <path to the module clone>` also loads the module's own `CLAUDE.md` (`--add-dir` alone loads no `CLAUDE.md`). |
| **Gemini CLI** | `GEMINI.md` | Instructs Gemini to read `.ai/CONTEXT.md` and domain files when relevant. |
| **Cursor** | `.cursor/rules/*.mdc` | One `.mdc` rule per domain/component with glob patterns to auto-attach. |
| **GitHub Copilot** | `.github/copilot-instructions.md` + `.github/instructions/*.instructions.md` | Repo-wide instructions reference `.ai/CONTEXT.md`. Path-specific files use `applyTo` globs. |
| **Windsurf** | `.windsurf/rules/*.md` | Project-wide rules instruct Cascade to read `.ai/` files when working on matching paths. |
| **AGENTS.md** | `AGENTS.md` | Multi-agent systems reference `.ai/CONTEXT.md`. |

### Path-scoped rules (`.ai/rules/`)

A rule that must load only when the agent touches a given area (the Admin API, a component with its own conventions) is written **once**, in `.ai/rules/{name}.md`, with the frontmatter Claude Code reads natively and a short body:

```yaml
---
description: "Admin API: ..."
paths:
  - "src/PrestaShopBundle/ApiPlatform/**"
  - "modules/ps_apiresources/**"
---

Read `.ai/Component/AdminAPI/CONTEXT.md` before changing these files. ...
```

The body says which `CONTEXT.md` files to read and why, never the conventions themselves (they live in the contexts). `paths` is the single source of the globs (YAML list form only). The index generator (`sync_rules` in `bash .ai/bin/generate-ai-index.sh`) exposes the rule to every tool:

```
.ai/rules/admin-api.md                         ← the source (hand-maintained)
.claude/rules/admin-api.md                     → symlink to the source (Claude Code reads `paths`)
.cursor/rules/admin-api.mdc                    ← generated: `description`, `globs: a/**,b/**`, `alwaysApply: false`
.windsurf/rules/admin-api.md                   ← generated: `trigger: glob`, `globs: a/**,b/**`, `description`
.github/instructions/admin-api.instructions.md ← generated: `applyTo: "a/**,b/**"`
```

The generated copies carry a "generated from … do not edit" header and the same body; they are committed like `generated/*.md`, and a stale copy is a generator run away. A new rule only needs its `.ai/rules/{name}.md` file, a generator run, and a row in the "Which context to read" table of `.ai/CONTEXT.md` (the table names the rule instead of repeating its globs). `admin-api` is the first rule; the other components keep the always-on pointers until they get their own. Verified with Claude Code; Cursor, Windsurf and Copilot copies follow their documented keys but are not tested here (Windsurf documents one glob per rule, the comma-separated list is an assumption) — report a tool rejecting a copy so the generator can adapt its format.

### Maintenance

The context rots when the code moves. Regular hygiene, ideally after each merge touching the areas concerned:

- `bash .ai/bin/generate-ai-index.sh`: regenerates `generated/*.md`, creates the missing skill symlinks, regenerates the per-tool copies of the `.ai/rules/*.md` rules, lists the module skills without component association. To run at least monthly and after any change to `.ai/rules/` (a GitHub action or a skill can carry it).
- Check the links of the `CONTEXT.md` files (paths, class names, section anchors) against the code; a renamed class or moved file is a stale context.
- Keep the examples in the contexts and skills aligned with the code they name: an outdated example propagates (a file copied from an older one carries its obsolete patterns).
- When a review (human or automated) finds a rule the context does not state, or a false positive it should prevent, update the context in the same PR as the fix (see `modules/ps_apiresources/.ai/REVIEW.md` → "Maintaining the automated review" for the module side).

### Web-based assistants (ChatGPT, Claude.ai, Gemini)

Contributors using web-based AI assistants should copy-paste the relevant `CONTEXT.md` file(s) as their initial system prompt:
1. Always start with `.ai/CONTEXT.md` (project-wide rules)
2. Add the relevant domain or component `CONTEXT.md` for the area they're working on

### How an AI agent should navigate this structure

1. **Start with `.ai/CONTEXT.md`** — it contains project-wide rules and an index of all domain/component contexts.
2. **Identify the relevant domain or component** from the index based on the files being worked on.
3. **Read the specific `CONTEXT.md`** for that domain or component dynamically and only when needed to avoid overloading the AI context.
4. **Check for a matching skill** before performing any recurring task:
   - Cross-cutting skills live in `.ai/skills/{skill-name}/SKILL.md`.
   - Scoped skills live inside their component or domain: `.ai/Component/{Name}/skills/` or `.ai/Domain/{Name}/skills/`.
   - Check the `## Skills` table in `.ai/CONTEXT.md` or the relevant component/domain `CONTEXT.md` for a full list.
   - Read the `SKILL.md` and follow its instructions step by step.

## How to contribute

### Adding context for a new domain

1. Create `.ai/Domain/{DomainName}/CONTEXT.md` using the template above. A skill exists to help: use the `domain-context-generator` skill.
2. Add an entry to the index in `.ai/CONTEXT.md`.
3. Optionally create a path-scoped rule `.ai/rules/{name}.md` so the context loads when matching files are touched (see "Path-scoped rules"), run the index generator for the symlinks, and add the topic to the "Which context to read" table of `.ai/CONTEXT.md`.

### Adding context for a new component

1. Create `.ai/Component/{ComponentName}/CONTEXT.md` using the template above. A skill exists to help: use the `component-context-generator` skill.
2. Add an entry to the index in `.ai/CONTEXT.md`.

### Adding a skill

A skill exists to help: use the `create-skill` skill.

1. **Choose the canonical location** based on scope:
   - Cross-cutting → `.ai/skills/{skill-name}/SKILL.md`
   - Component-scoped → `.ai/Component/{Name}/skills/{skill-name}/SKILL.md`
   - Domain-scoped → `.ai/Domain/{Name}/skills/{skill-name}/SKILL.md`
2. Create a symlink in `.claude/skills/` pointing to the skill directory (for Claude Code auto-discovery).
3. Add a `## Skills` entry to the corresponding `CONTEXT.md`: root `.ai/CONTEXT.md` for cross-cutting skills, or the relevant component/domain `CONTEXT.md` for scoped ones. This table is the agnostic discovery mechanism for all non-Claude tools.

### Adding a module-owned skill

Modules can ship their own AI skills under `<module>/.claude/skills/{skill-name}/SKILL.md` or `<module>/.ai/skills/{skill-name}/SKILL.md`. The skill is owned and maintained by the module, not by this repository — but to be auto-discoverable from the project root it has to be exposed via a chained symlink, because Claude Code does NOT walk into nested `.claude/` directories.

1. **Decide which component the skill belongs to.** This is a human judgment call — the component should be the natural architectural fit (e.g. an API resource skill belongs under `Component/AdminAPI/`). Don't auto-pick.
2. **Curate the component association** with a single symlink:
   ```bash
   ln -s ../../../../modules/<module>/<dotdir>/skills/<skill-name> \
     .ai/Component/{Name}/skills/<skill-name>
   ```
   `<dotdir>` is `.claude` or `.ai` depending on where the module hosts the skill.
3. **Run the index generator** — `bash .ai/bin/generate-ai-index.sh`. It uses `find -L` so it follows the component-level symlink into the module, then auto-creates the standard `.claude/skills/<skill-name> -> ../../.ai/Component/{Name}/skills/<skill-name>` like every other skill.
4. **Add a `## Skills` row** to the component's `CONTEXT.md` marking the skill as module-owned, with a link back to this section.

The chain (`.claude/skills/<name>` → `.ai/Component/{N}/skills/<name>` → module skill dir) resolves whenever the module is present — composer-installed, freshly cloned, or locally symlinked — and dangles harmlessly otherwise.

The index generator also runs `discover_module_skills` to scan `modules/*/.claude/skills/` and `modules/*/.ai/skills/`, listing any module skill that has no component association yet. Contributors see those as suggestions; nothing is auto-attached.

### Updating existing context

Edit the relevant `CONTEXT.md` directly. Pointer files at the repo root should never need modification — they only contain references.
