---
name: create-skill
description: >
  Creates a new SKILL.md in the correct location within this project's .ai/ structure.
  Trigger when the user asks to "create a skill", "add a skill for [task]", "write a skill
  to [do something]", or "add a new skill".
---

# Create a New Skill

## Where to place it

Choose the location based on scope — in order of priority:

| Priority | Condition | Path |
|----------|-----------|------|
| 1 | User explicitly provided a path | Use that path |
| 2 | Skill is tied to a specific component | `.ai/Component/{Name}/skills/{skill-name}/SKILL.md` |
| 3 | Skill is tied to a specific domain | `.ai/Domain/{Name}/skills/{skill-name}/SKILL.md` |
| 4 | Cross-cutting (spans multiple domains/components) | `.ai/skills/{skill-name}/SKILL.md` |

After writing the file:
1. Add a `## Skills` section (or entry) to the corresponding `CONTEXT.md` — root `.ai/CONTEXT.md` for cross-cutting skills, or `.ai/Component/{Name}/CONTEXT.md` / `.ai/Domain/{Name}/CONTEXT.md` for scoped skills. This is the agnostic discovery mechanism for all non-Claude tools.
2. Create a symlink in `.claude/skills/` pointing to the skill **directory** (not the file). The path **must be relative**. This enables Claude Code auto-discovery.
   ```
   cd .claude/skills && ln -s ../../<skill-dir-path-from-repo-root> <skill-name>
   ```
---

## CONTEXT.md vs SKILL.md — no duplication rule

CONTEXT.md owns conventions (rules, patterns, constraints — the "why" and "what"). SKILL.md owns procedures (steps, code templates, checklists — the "how"). Content must live in exactly one place.

| | CONTEXT.md | SKILL.md |
|---|---|---|
| **Contains** | Conventions, rules, patterns | Procedures, step-by-step instructions, code templates |
| **Audience** | Any AI tool or human | An AI agent executing a specific task |
| **Duplication** | Authoritative source — never restated elsewhere | **References** CONTEXT.md conventions, never **restates** them |

### When creating or modifying a skill

For every rule or convention you're about to write in a skill, apply this decision:

1. **Read the parent CONTEXT.md first** — before writing any content in a skill, read the component (or domain) CONTEXT.md to know what's already documented there
2. **Ask: "Does this apply to all skills in this component?"** — if yes, it belongs in CONTEXT.md, not the skill. Example: "all handlers use `#[AsCommandHandler]`" → CONTEXT.md
3. **Ask: "Is this specific to this one task?"** — if yes, it stays in the skill. Example: "the edit handler checks null before each field" → skill
4. **If a convention is missing from CONTEXT.md and should be there** — add it to CONTEXT.md first, then reference it from the skill. Never write it only in the skill
5. **Reference, don't restate** — when a skill needs to remind the reader of a convention, write: `See [Component/CONTEXT.md](../../CONTEXT.md#section) for X convention.` Do not copy the rule text into the skill

### When reviewing an existing skill

Check the skill's `## Rules` section and any inline convention statements:

1. For each rule in the skill, check if the same rule exists in the parent CONTEXT.md
2. If it does → **delete it from the skill** and replace with a reference
3. If it doesn't but should → **move it to CONTEXT.md** and replace with a reference
4. If it's genuinely task-specific → keep it in the skill

### What NOT to put in CONTEXT.md

Not everything belongs in CONTEXT.md either. Skip:
- Class inventories, file listings — anything `grep` or `glob` can answer
- Code templates and step-by-step procedures — those are the skill's job
- Content already in the root `.ai/CONTEXT.md` (project-wide coding standards, testing framework)

### Cross-references and cascade risk

The `## Related` section in CONTEXT.md files links to other contexts. Use it sparingly — every link is a potential cascade where an AI agent follows A → B → C and ends up loading all contexts, which defeats the purpose of splitting them.

Only link when the relationship is **non-obvious** (architectural surprise, coexistence gotcha). Do not link for obvious usage relationships ("Controller uses CQRS") or just to mention a class name that's greppable. When in doubt, omit the link.

---

## SKILL.md format reference

### Frontmatter (YAML between `---` markers)

| Field | Required | Description |
|-------|----------|-------------|
| `name` | No (defaults to dir name) | Lowercase, hyphens, max 64 chars |
| `description` | **Required** | What it does + trigger phrases. Max ~250 chars before truncation — front-load the key use case. If the skill requires arguments, describe them here. **Do not** include "Read Component/X/CONTEXT.md for conventions" in the description — that pointer belongs in the skill body, not the description. See [STRUCTURE.md → SKILL.md project conventions](../../STRUCTURE.md#skillmd-project-conventions). |
| `argument-hint` | No | Shown in autocomplete, e.g. `[domain-name]` |
| `allowed-tools` | No | Tools usable without permission prompt, e.g. `Read, Grep, Glob` |
| `disable-model-invocation` | No | `true` = I cannot auto-invoke; only explicit `/name` call works |
| `user-invocable` | No | `false` = hidden from `/` menu; I can still invoke automatically |
| `paths` | No | Glob patterns that scope auto-activation, e.g. `src/**/*.php` |
| `effort` | No | `low` / `medium` / `high` / `max` |
| `context` | No | Omit for inline execution (default). `fork` = run as isolated subagent |
| `agent` | No | Subagent type when `context: fork` — `Explore`, `Plan`, `general-purpose` |
| `model` | No | Override model for this skill |

### Project-specific frontmatter (custom metadata)

This project uses four custom frontmatter fields on top of the standard ones: `needs`, `produces`, `conditional`, `subagent`. They are not interpreted by Claude Code but document the skill's place in larger workflows and serve as machine-readable hints for orchestrators.

See [`STRUCTURE.md` → SKILL.md project conventions](../../STRUCTURE.md#skillmd-project-conventions) for the full definition of these fields, the top-down dependency rule, and the standalone rule. Do not duplicate that documentation here.

**Arguments:** If a skill requires arguments that the user must provide, describe them in the `description` field. At runtime, if a required argument is missing, use `AskUserQuestion` to prompt the user.

### Body

Plain markdown. No required sections — write whatever instructions I need to follow.

**Useful substitutions in body:**
- `$ARGUMENTS` — full argument string passed after the skill name
- `$0`, `$1`, … — individual arguments by position
- `` !`command` `` — runs a shell command before I see the content; output is inlined

### Invocation behaviour

| `disable-model-invocation` | `user-invocable` | Result |
|---|---|---|
| false (default) | true (default) | You can call it; I can auto-invoke it |
| true | true | You can call it; I **cannot** auto-invoke |
| false | false | Hidden from menu; I auto-invoke only |

---

## Minimal template

```markdown
---
name: my-skill
description: >
  One sentence what it does. Trigger phrases: "do X", "create Y", "add Z".
allowed-tools: Read, Grep
---

# My Skill

## Purpose
What this skill accomplishes.

## Steps
1. …
2. …

## Output
Where to write the result.
```

---

## Upstream reference

The canonical Anthropic skill creator (may contain updates not yet reflected here):
https://github.com/anthropics/skills/blob/main/skills/skill-creator/SKILL.md

If that file has evolved, reconcile any new fields or patterns with the project-specific
placement rules above before applying them.

---

## Checklist

- [ ] Directory and `SKILL.md` created at the correct scoped path (component, domain, or cross-cutting)
- [ ] `description` front-loads the use case and lists trigger phrases
- [ ] `description` does **not** restate "Read Component/X/CONTEXT.md…" — that line belongs in the body only
- [ ] If the skill needs the parent CONTEXT.md, the body opens with a single `Read @.ai/Component/{Component}/CONTEXT.md for ...` reference
- [ ] Custom frontmatter fields (`needs`, `produces`, `conditional`, `subagent`) follow [STRUCTURE.md conventions](../../STRUCTURE.md#skillmd-project-conventions) — top-down dependencies, standalone-usable
- [ ] If the skill is read-heavy and produces a structured artifact, consider declaring `subagent: recommended` or `subagent: optional` (see [STRUCTURE.md](../../STRUCTURE.md#skillmd-project-conventions)). Absence is the default — leave the field off if the skill is small, interactive, or cross-references siblings
- [ ] Corresponding `CONTEXT.md` updated with a `## Skills` entry (agnostic discovery)
- [ ] Symlink created in `.claude/skills/` pointing to the skill directory (Claude Code discovery)
