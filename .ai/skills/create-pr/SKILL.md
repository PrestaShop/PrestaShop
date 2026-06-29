---
name: create-pr
description: >
  Creates a PrestaShop pull request with the required metadata table.
  Triggers when asked to create, open, submit, or push a PR, or when
  mentioning "pull request" in the context of contributing.
allowed-tools: Read, Grep, Glob, Bash, Agent
---

# Create a PrestaShop Pull Request

## Purpose

Guide a contributor through creating a pull request that complies with the PrestaShop contribution guidelines, including the mandatory metadata table.

## Steps

### 1. Gather context

- Read the PR template from `.github/PULL_REQUEST_TEMPLATE.md` — this is the **single source of truth** for the table format.
- Run `git log` and `git diff` against the target branch to understand what changed.
- Identify the target branch from git (see branching rules in `.ai/CONTEXT.md`).

### 2. Auto-fill fields

From code analysis and git context, infer as much as possible:

| Field | How to infer |
|-------|-------------|
| **Branch** | Current git branch or merge target |
| **Description** | Summarize from commits and diff. Be specific (versions, browser/server config, module/theme). |
| **Type** | `bug fix` · `improvement` · `new feature` · `refacto` — infer from changes |
| **Category** | `FO` · `BO` · `CO` · `IN` · `WS` · `TE` · `LO` · `ME` · `PM` — infer from files changed. See [category reference](https://devdocs.prestashop-project.org/9/contribute/contribution-guidelines/pull-requests/#type--category). **`ME` (Merge) is reserved exclusively for PRs that only import one git branch into another (e.g. an upward `9.1.x` → `develop` merge) — never use it for a normal feature/bugfix/refacto PR.** For documentation/tooling PRs (e.g. `.ai/` context files) use `PM` (Project Management). |
| **BC breaks** | Analyze public API changes → `yes` / `no` |
| **Deprecations** | Check for new `@deprecated` annotations → `yes` / `no` |
| **How to test** | Write step-by-step verification instructions from the PR context |
| **Fixed issue** | Extract from conversation, branch name (e.g. `fix/12345`), or commit messages. Format: `Fixes #<number>` |

### 3. Ask before guessing

For any **required** field (Branch, Description, Type, Category, BC breaks, Deprecations) that cannot be confidently determined, **ask the user**. Do not guess.

**Optional fields** (UI Tests, Related PRs, Sponsor company) — fill when known, leave the template placeholder otherwise.

### 4. Create the PR

Use GitHub MCP tools (`mcp__github__create_pull_request`) or `gh pr create` CLI to create the PR with the filled-in template as the body.

## Output

The created PR URL, displayed to the user.
