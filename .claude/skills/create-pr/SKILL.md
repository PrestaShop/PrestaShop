---
name: create-pr
description: Creates a PrestaShop pull request with the required metadata table. Triggers when asked to create, open, submit, or push a PR, or when mentioning "pull request" in the context of contributing.
---

Helps a contributor create a pull request for the PrestaShop open source project.

## PR Description Format

Every PR description **must** start with the metadata table from `.github/PULL_REQUEST_TEMPLATE.md`. Additional context goes **after** the table.

```
| Questions         | Answers
| ----------------- | -------------------------------------------------------
| Branch?           | {branch}
| Description?      | {description}
| Type?             | {type}
| Category?         | {category}
| BC breaks?        | {bc_breaks}
| Deprecations?     | {deprecations}
| How to test?      | {how_to_test}
| UI Tests          | {ui_tests}
| Fixed issue or discussion? | {fixed_issue}
| Related PRs       | {related_prs}
| Sponsor company   | {sponsor}
```

## Field Rules

### Required (must always be filled)

- **Branch**: Auto-fill from current git branch (`develop`, `9.1.x`, `9.0.x`, `8.2.x`). Ask if unsure.
- **Description**: Summarize what the PR does. Be specific (versions, browser/server config, module/theme). Ask if lacking context.
- **Type**: `bug fix` | `improvement` | `new feature` | `refacto`. Infer from changes, ask otherwise.
- **Category**: `FO` | `BO` | `CO` | `IN` | `WS` | `TE` | `LO` | `ME` | `PM`. Infer from files changed, ask otherwise. See [category reference](https://devdocs.prestashop-project.org/9/contribute/contribution-guidelines/pull-requests/#type--category).
- **BC breaks**: `yes` | `no`. Analyze changes. Ask if unsure.
- **Deprecations**: `yes` | `no`. Check for new deprecations. Ask if unsure.

### Recommended

- **How to test**: Step-by-step verification instructions. Write from the PR context, ask if unclear.

### Optional (fill when known, leave empty otherwise)

- **UI Tests**: Link to test runs. Usually pasted by contributor later.
- **Fixed issue or discussion**: `Fixes #<number>` format. Auto-fill from conversation context, branch name (e.g. `fix/12345`), or commit messages. Multiple: `Fixes #123, Fixes #456`.
- **Related PRs**: Links to PRs in other repos (theme, modules, autoupgrade).
- **Sponsor company**: Contributor's company or customer name.

## Workflow

1. **Auto-fill** what you can: branch (git), type/category (code analysis), BC breaks/deprecations, issues (context).
2. **Ask before guessing** any required field you cannot confidently determine. Do not guess.
3. **Create the PR** using GitHub MCP tools or `gh` CLI.
