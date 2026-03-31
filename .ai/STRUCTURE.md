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
├── Domain/                 # Business domain contexts (maps to src/Core/Domain/)
│   ├── {DomainName}/
│   │   ├── CONTEXT.md      # Domain-specific conventions, patterns, do/don't rules
│   │   └── skills/         # Optional: reusable task templates (e.g., skill.md + script.sh)
│   │       └── {skill-name}/
│   │           ├── SKILL.md
│   │           └── script.sh  # Optional automation script
│   └── ...
│
└── Component/              # Cross-cutting component contexts (maps to shared infrastructure)
    ├── {ComponentName}/
    │   ├── CONTEXT.md      # Component-specific conventions, usage patterns, do/don't rules
    │   └── skills/         # Optional: reusable task templates
    │       └── {skill-name}/
    │           └── SKILL.md
    └── ...
```

## File conventions

| File | Purpose | Target size |
|------|---------|-------------|
| `CONTEXT.md` | Conventions, patterns, do/don't rules for a domain or component | < 200 lines |
| `SKILL.md` | Step-by-step task template an AI agent can follow to accomplish a recurring task | < 150 lines |
| `STRUCTURE.md` | This file — architecture documentation | N/A |

### CONTEXT.md template

Every `CONTEXT.md` follows this structure:

```markdown
# {Domain or Component Name}

## Purpose
[1-2 sentences: what this domain/component does, what it does NOT do]

## Architecture overview
[Key classes, patterns, relationships — structural, not exhaustive]

## Coding standards
[Only rules specific to THIS scope — don't repeat root-level rules]

## Do
- [Accepted patterns with brief rationale]

## Don't
- [Forbidden patterns with brief rationale]

## Testing expectations
[What coverage is expected, which test types, where tests live]

## Canonical examples
- [Links to reference implementation files]

## Related
- [Links to related domains, components, or documentation]
```

### Writing guidelines

- **Be concise** — use bullet points and tables, not paragraphs. AI parses structured content more reliably.
- **No code dumps** — link to canonical example files, don't inline full class implementations.
- **Describe patterns, not inventories** — write `Handlers follow Domain/{Action}{Entity}Handler.php` instead of listing every handler file.
- **Don't repeat parent context** — domain/component files should only contain what's unique to their scope. Project-wide rules live in the root `CONTEXT.md`.
- **No tool-specific syntax** — CONTEXT.md files must work for any AI tool or human reader.

## How AI tools discover this context

### Automatic loading (via pointer files at repo root)

| Tool | Pointer file | How it works |
|------|-------------|--------------|
| **Claude Code** | `CLAUDE.md` | Uses `@.ai/CONTEXT.md` reference — loaded at session start. Agent reads domain/component files on demand. |
| **Gemini CLI** | `GEMINI.md` | Instructs Gemini to read `.ai/CONTEXT.md` and domain files when relevant. |
| **Cursor** | `.cursor/rules/*.mdc` | One `.mdc` rule per domain/component with glob patterns to auto-attach. |
| **GitHub Copilot** | `.github/copilot-instructions.md` + `.github/instructions/*.instructions.md` | Repo-wide instructions reference `.ai/CONTEXT.md`. Path-specific files use `applyTo` globs. |
| **Windsurf** | `.windsurf/rules/*.md` | Project-wide rules instruct Cascade to read `.ai/` files when working on matching paths. |
| **AGENTS.md** | `AGENTS.md` | Multi-agent systems reference `.ai/CONTEXT.md`. |

### Web-based assistants (ChatGPT, Claude.ai, Gemini)

Contributors using web-based AI assistants should copy-paste the relevant `CONTEXT.md` file(s) as their initial system prompt:
1. Always start with `.ai/CONTEXT.md` (project-wide rules)
2. Add the relevant domain or component `CONTEXT.md` for the area they're working on

### How an AI agent should navigate this structure

1. **Start with `.ai/CONTEXT.md`** — it contains project-wide rules and an index of all domain/component contexts.
2. **Identify the relevant domain or component** from the index based on the files being worked on.
3. **Read the specific `CONTEXT.md`** for that domain or component.
4. **Check for skills** if the task matches a known pattern (e.g., "add a field to a form" → `.ai/Component/Forms/skills/add-field-to-form/SKILL.md`).

## How to contribute

### Adding context for a new domain

1. Create `.ai/Domain/{DomainName}/CONTEXT.md` using the template above.
2. Add an entry to the index table in `.ai/CONTEXT.md`.
3. Optionally create `.cursor/rules/{domain}.mdc` and `.github/instructions/{domain}.instructions.md` pointer files.

### Adding context for a new component

1. Create `.ai/Component/{ComponentName}/CONTEXT.md` using the template above.
2. Add an entry to the index table in `.ai/CONTEXT.md`.

### Adding a skill

1. Create `.ai/{Domain|Component}/{Name}/skills/{skill-name}/SKILL.md`.
2. Optionally add a `script.sh` for automation.
3. Reference the skill in the parent `CONTEXT.md` under "Related".

### Updating existing context

Edit the relevant `CONTEXT.md` directly. Pointer files at the repo root should never need modification — they only contain references.
