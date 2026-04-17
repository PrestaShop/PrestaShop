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

## SKILL.md format reference

### Frontmatter (YAML between `---` markers)

| Field | Required | Description |
|-------|----------|-------------|
| `name` | No (defaults to dir name) | Lowercase, hyphens, max 64 chars |
| `description` | **Required** | What it does + trigger phrases. Max ~250 chars before truncation — front-load the key use case. If the skill requires arguments, describe them here. |
| `argument-hint` | No | Shown in autocomplete, e.g. `[domain-name]` |
| `allowed-tools` | No | Tools usable without permission prompt, e.g. `Read, Grep, Glob` |
| `disable-model-invocation` | No | `true` = I cannot auto-invoke; only explicit `/name` call works |
| `user-invocable` | No | `false` = hidden from `/` menu; I can still invoke automatically |
| `paths` | No | Glob patterns that scope auto-activation, e.g. `src/**/*.php` |
| `effort` | No | `low` / `medium` / `high` / `max` |
| `context` | No | Omit for inline execution (default). `fork` = run as isolated subagent |
| `agent` | No | Subagent type when `context: fork` — `Explore`, `Plan`, `general-purpose` |
| `model` | No | Override model for this skill |

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
- [ ] Corresponding `CONTEXT.md` updated with a `## Skills` entry (agnostic discovery)
- [ ] Symlink created in `.claude/skills/` pointing to the skill directory (Claude Code discovery)
