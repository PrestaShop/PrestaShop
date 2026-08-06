---
name: link-apiresources-fork
description: >
  Pair the core PR with a ps_apiresources fork branch for coordinated review,
  or revert that pairing to upstream dev-dev once both PRs are merged. Edits
  composer.json (repository URL + dev-<branch> pin) and refreshes composer.lock.
  Trigger: "pair core with ps_apiresources branch", "link ps_apiresources fork
  branch", "use my ps_apiresources fork in the core PR", "revert ps_apiresources
  to dev-dev", "unlink the ps_apiresources fork".
needs: []
produces: "Modified core composer.json + composer.lock pointing at the fork's branch (link mode), or reverted to upstream dev-dev (cleanup mode)."
---

# link-apiresources-fork

Read @.ai/Component/AdminAPI/CONTEXT.md for the architectural background.

This skill coordinates a core PR with a matching `ps_apiresources` PR — required when a change to PrestaShop core depends on (or breaks the contract of) the API module, so both sides must compile together during review. The sibling [`setup-apiresources-dev`](../setup-apiresources-dev/SKILL.md) skill sets up a local dev environment when needed; this skill works without it (e.g. when integrating an existing module PR into a core PR).

**Workflow recap:** the contributor pushes the module branch to a fork → this skill points the core's `composer.json` at that fork branch and refreshes `composer.lock` → both PRs reference each other → the core PR is the one whose CI must be green (the module PR's CI will be red until the core PR merges) → after the core PR merges, close-reopen the module PR for fresh CI → merge the module PR → run this skill in **cleanup mode** to revert the core back to upstream `dev-dev`.

The skill branches at runtime — every choice is an explicit `AskUserQuestion`. Never auto-decide anything that mutates `composer.json`, `composer.lock`, or `modules/ps_apiresources/`.

## 0. Detect the current state

Inspect the two relevant entries in `composer.json`:

```bash
grep -nE '"prestashop/ps_apiresources"|"ps_apiresources":[[:space:]]*\{' composer.json
```

- `repositories.ps_apiresources.url` — upstream `https://github.com/PrestaShop/ps_apiresources`, or a fork URL?
- `require.prestashop/ps_apiresources` — `dev-dev`, or `dev-<branch>`?

| Detected state | Natural mode |
|----------------|--------------|
| Both at upstream defaults | **Link** |
| Either pointing at a fork / non-`dev-dev` branch | **Cleanup** (or relink to a different fork/branch) |

## 1. Pick the mode (`AskUserQuestion`)

Show the detected state, then ask:

- **Link to a fork branch** — pin `repositories.ps_apiresources` to a contributor fork and pin the dependency to `dev-<branch>`.
- **Cleanup (revert to upstream dev-dev)** — restore upstream URL + `dev-dev`.

If the user picks the "natural" action for the detected state, proceed. If they pick the inverse (e.g. cleanup when the state is already upstream defaults), confirm — the skill should still no-op gracefully but warn there's nothing to do.

## 2. Local-clone protection (always runs before any `composer update`)

`composer update` will overwrite whatever sits at `modules/ps_apiresources/` with the locked version. Detect the current state and protect accordingly:

```bash
test -L modules/ps_apiresources && echo SYMLINK
test -d modules/ps_apiresources/.git && echo IN_PLACE_CLONE
```

| State | Action |
|-------|--------|
| **Symlink to external clone** | Run `readlink modules/ps_apiresources` to record the target path. Composer will replace the symlink with its own checkout — that's fine, the external clone is untouched. Offer at the end (step 3g / 4g) to restore the symlink. |
| **In-place clone (real `.git/`)** | High-risk. Surface uncommitted/unpushed work first, then offer move-and-symlink. See below. |
| **Composer-installed plain dir (no `.git/`)** | No protection needed — composer overwrites normally. |

### 2a. In-place clone — safety check

```bash
git -C modules/ps_apiresources status --short
git -C modules/ps_apiresources log @{u}..HEAD --oneline 2>/dev/null
```

If anything appears (uncommitted files, unpushed commits), warn explicitly: "composer update will destroy this work."

### 2b. In-place clone — move-and-symlink offer (recommended)

`AskUserQuestion`:

> Your in-place clone at `modules/ps_apiresources/` will be overwritten by `composer update`. Move it to an external path first and replace it with a symlink? (Recommended — preserves your local commits and uncommitted work.)

If yes, `AskUserQuestion` for the absolute target path. Validate:

```bash
test -d "<parent of target>"     # parent dir must exist
test ! -e "<target>"             # target must not exist (or must be empty)
```

Reject relative paths and unexpanded `~`. Then:

```bash
mv modules/ps_apiresources <target>
ln -s <target> modules/ps_apiresources
```

Record `<target>` so step 3g / 4g can offer to restore the symlink.

If the user declines move-and-symlink AND the working tree is dirty or has unpushed commits, **STOP**. Do not proceed. Tell the user to push their branch first.

If the working tree is clean AND the branch is pushed, accept the overwrite — the local clone can be re-created from the remote later.

## 3. Mode A — Link to a fork branch

### 3a. Gather inputs (`AskUserQuestion`)

**Pre-check** — if `modules/ps_apiresources/` is a git checkout (in-place clone or symlink to one), inspect what's currently checked out so the skill can offer to reuse it:

```bash
git -C modules/ps_apiresources rev-parse --git-dir > /dev/null 2>&1 \
    && upstream_ref=$(git -C modules/ps_apiresources rev-parse --abbrev-ref --symbolic-full-name @{u} 2>/dev/null) \
    && local_remote=${upstream_ref%%/*} \
    && local_branch=$(git -C modules/ps_apiresources branch --show-current) \
    && local_remote_url=$(git -C modules/ps_apiresources remote get-url "$local_remote" 2>/dev/null)
```

If `local_remote_url` is set and does NOT contain `PrestaShop/ps_apiresources` (i.e. it's a fork, not the upstream), Path C is available — the local checkout is almost certainly what we want to link.

Ask the user — present detected paths first:

> How will you specify the module PR?
>
> - **Use the local checkout** (recommended, when detected) — `<local_remote_url>` / `<local_branch>` from `modules/ps_apiresources/`.
> - **Module PR URL** — paste the URL of the `ps_apiresources` PR; the skill extracts the fork URL and branch name via `gh`.
> - **Fork URL + branch name** (manual) — provide them separately.

When the local checkout is upstream PrestaShop, has no remote tracker, or `modules/ps_apiresources/` is composer-installed, omit the first option.

#### Path A — Module PR URL

`AskUserQuestion` for the URL. Validate it matches `https://github.com/<owner>/ps_apiresources/pull/<n>`.

Resolve the fork and branch via `gh`:

```bash
gh pr view <url> --json headRepositoryOwner,headRepository,headRefName
```

Build:

- Fork URL = `https://github.com/<headRepositoryOwner.login>/<headRepository.name>`
- Branch name = `<headRefName>`

Confirm via `AskUserQuestion`:

> Detected fork URL `<fork>` / branch `<branch>` from PR `<url>`. Use these?

Record the PR URL — it's reused in the reminder at the end (step 3f).

If `gh` is not installed, not authenticated, or the PR is not found, fall back to **Path B**.

A PR can't exist without a pushed branch, so no separate push prerequisite is needed in this path.

#### Path B — Fork URL + branch name (manual)

Push prerequisite — `AskUserQuestion`:

> Has the module branch been pushed to your `ps_apiresources` fork on GitHub?

If no, **STOP**. Composer can't resolve `dev-<branch>` if the branch doesn't exist remotely. Tell the user to push first; do not push for them.

Then `AskUserQuestion` (batch):

- **Fork URL** — default to `https://github.com/<user>/ps_apiresources` deduced from the parent project's remotes (read `git -C . remote -v`; if any remote points at `git@github.com:<user>/PrestaShop.git` or `https://github.com/<user>/PrestaShop.git`, propose the matching `ps_apiresources` URL). HTTPS is preferred for `composer.json` because it works without SSH credentials in CI. Always allow override.
- **Branch name** — e.g. `delete-endpoints-command`. The composer pin becomes `dev-<branch-name>`.
- **Module PR URL** (optional but recommended) — reused in the reminder at the end.

#### Path C — Use the local checkout (when detected)

The skill already has the fork URL and branch from the pre-check. Convert any SSH URL to HTTPS for `composer.json` compatibility:

- `git@github.com:<user>/<repo>(.git)?` → `https://github.com/<user>/<repo>`
- `https://github.com/<user>/<repo>(.git)?` → `https://github.com/<user>/<repo>`

Verify the branch is fully pushed — composer.lock will resolve to the remote HEAD, not the local working tree:

```bash
git -C modules/ps_apiresources fetch <local_remote>
git -C modules/ps_apiresources status -sb | head -1   # look for "[ahead N]"
```

If `[ahead N]` appears, warn via `AskUserQuestion`:

> Local branch `<branch>` is ahead of `<remote>` by N commit(s). composer.lock will pin the **remote** HEAD, so your unpushed commits won't be linked. Push first?

If the user pushes, re-fetch and continue. If they decline, proceed but make it clear composer.lock will lag.

Final confirmation via `AskUserQuestion`:

> Use fork URL `<fork-url>` / branch `<branch>` from the local checkout?

If the user declines, fall back to Path A or B by re-prompting the input-method question.

A PR URL isn't required for this path. Optionally ask for one to use in the "Related PR" reminder at the end (step 3f); skip if the user has none yet.

### 3b. Edit `composer.json` (two surgical replacements)

Both target strings are unique in the file as of HEAD:

1. Inside the `repositories.ps_apiresources` entry: replace `"url": "https://github.com/PrestaShop/ps_apiresources"` with `"url": "<fork-url>"`.
2. Replace `"prestashop/ps_apiresources": "dev-dev"` with `"prestashop/ps_apiresources": "dev-<branch>"`.

Use the `Edit` tool — do **not** use `composer config repositories.ps_apiresources vcs <url>` because it may rewrite key order or strip fields and produce a noisy diff.

### 3c. Run `composer update`

```bash
composer update prestashop/ps_apiresources 2>&1
```

If this fails (404 on the fork, branch doesn't exist, auth required), capture the error, **revert `composer.json` to its pre-skill state**, and report. Don't leave a broken composer.json behind.

### 3d. Verify

```bash
composer show prestashop/ps_apiresources | head -20    # expect dev-<branch> + a fork commit hash
git diff composer.json                                  # expect exactly the 2 intended edits, nothing else
git diff --stat composer.lock                           # expect a small diff, only ps_apiresources package fields
```

If `composer.json` shows changes outside the two intended edits (e.g. composer-normalize reformatted the file), inspect carefully and warn the user before proceeding.

If `composer.lock` shows transitive package movements (other packages updated), warn the user — `composer update prestashop/ps_apiresources` should be surgical.

### 3e. Commit and push (`AskUserQuestion`)

The link only takes effect for CI once GitHub sees the updated `composer.json` + `composer.lock`. Ask:

> Commit `composer.json` + `composer.lock` and push to the core PR branch now?

If yes:

```bash
git add composer.json composer.lock
git commit -m "Link ps_apiresources to dev-<branch> for cross-PR review"
git push
```

If `git push` fails because the branch has no upstream, use `git push -u <remote> <branch>`. Never force-push from this step — the change is additive.

If the user declines, leave the working tree dirty and remind them that the link won't reach CI until they commit + push manually.

### 3f. Reminders (output to user)

- **Add to the core PR description table:** `Related PR: <module PR URL>` (use the PR URL collected via Path A or Path B; if Path B was used and the user didn't provide one, just point at the fork branch).
- **Expected CI state during the transitory phase:**
  - Core PR CI: must be green (it has both sides linked).
  - Module PR CI: will be red until the core PR merges — that's expected, the core PR is the reference.
- **After both PRs are merged**, run this skill again in **cleanup mode** to revert.

### 3g. Symlink-restore offer

If step 2 recorded a symlink target (either pre-existing or freshly created via move-and-symlink):

> Restore the symlink `modules/ps_apiresources -> <target>`? (Recommended if you'll continue editing the module locally — composer's checkout in `modules/` will be replaced by your external clone.)

If yes:

```bash
rm -rf modules/ps_apiresources
ln -s <target> modules/ps_apiresources
```

Note: after restoring the symlink, the module's working state in `modules/` is whatever the user has at `<target>`, **not** the version composer just locked. If the user is on the fork branch locally, those should align — but warn that running tests will use the local working state, not the locked commit.

## 4. Mode B — Cleanup (revert to upstream dev-dev)

### 4a. Local-clone protection

Run step 2 again. Same logic: protect any in-place clone with uncommitted/unpushed work; offer move-and-symlink.

### 4b. Edit `composer.json` (inverse of 3b)

Two surgical replacements:

1. Inside the `repositories.ps_apiresources` entry: replace `"url": "<current fork URL>"` with `"url": "https://github.com/PrestaShop/ps_apiresources"`.
2. Replace `"prestashop/ps_apiresources": "dev-<current branch>"` with `"prestashop/ps_apiresources": "dev-dev"`.

Read the current values from `composer.json` first to compute the exact replacement strings.

### 4c. Run `composer update`

```bash
composer update prestashop/ps_apiresources 2>&1
```

Same revert-on-failure handling as 3c.

### 4d. Verify

Mirror of 3d. Bonus check: `composer show prestashop/ps_apiresources` should now report `dev-dev` again.

### 4e. Commit and push (`AskUserQuestion`)

> Commit `composer.json` + `composer.lock` and push to the core PR branch now?

If yes:

```bash
git add composer.json composer.lock
git commit -m "Revert ps_apiresources to upstream dev-dev"
git push
```

If the user declines, leave the working tree dirty.

### 4f. Reminders

- **Remove** the `Related PR: <module PR URL>` line from the core PR description if both PRs are merged.

### 4g. Symlink-restore offer

Mirror of 3g.

## 5. Next steps / related

- For local module development: invoke `/setup-apiresources-dev`.
- For new endpoints in the module: invoke `/ps-api-endpoint` (chained symlink to the module's own skill).
