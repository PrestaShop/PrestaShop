---
name: update-symfony-components
description: >
  Update Symfony 6.4.x components in composer.lock and open the maintenance PR.
  Triggers: "update Symfony", "upgrade Symfony components", "new Symfony release",
  "Symfony 6.4.X is out", "bump Symfony".
argument-hint: "[6.4.X]  (optional — auto-detected from GitHub if omitted)"
needs: []
produces: "updated composer.lock + GitHub PR opened"
allowed-tools: Bash, Read, WebFetch
---

# Update Symfony Components

## Purpose

Automate the routine maintenance PR that updates Symfony 6.4.x components in `composer.lock` after each upstream Symfony patch release. The `composer.json` constraints (`~6.4.0`) already allow all 6.4.x versions — only `composer.lock` changes.

## Steps

### 1. Resolve the target version

- If an argument was provided (e.g. `6.4.38`), use it directly.
- Otherwise, fetch the GitHub releases API and extract the latest `v6.4.*` tag:
  ```
  WebFetch https://api.github.com/repos/symfony/symfony/releases?per_page=20
  ```
  Look for the `tag_name` field of the first release whose tag matches `v6.4.*`.

### 2. Identify the upstream remote

Find the git remote that points to the canonical `PrestaShop/PrestaShop` repository (not the user's fork):

```bash
git remote -v | grep -i "github\.com[/:]PrestaShop/PrestaShop" | head -1 | awk '{print $1}'
```

If no remote matches, ask the user to provide the remote name (common values: `upstream`, `prestashop`).

Store this value as `{upstream-remote}` for the remaining steps.

### 3. Determine the target branch

Always ask the user which branch the PR should target:

> Which branch should this PR target?
> - `9.1.x` — current stable (patch releases)
> - `develop` — next minor (9.2.0)

Then fetch the upstream branch to ensure the local state is current:

```bash
git fetch {upstream-remote} {target-branch}
```

Detect the open milestone for that branch:

```bash
gh api "repos/PrestaShop/PrestaShop/milestones" --jq '[.[] | select(.state=="open") | .title] | sort | reverse | .[0]'
```

If the result is ambiguous or empty, ask the user.

### 4. Guard: already up to date

Check the version of `symfony/console` directly from the upstream branch (not the local fork):

```bash
git show {upstream-remote}/{target-branch}:composer.lock | grep -A3 '"name": "symfony/console"' | grep '"version"'
```

If the locked version already equals the target version, report "Already up to date." and stop.

### 5. Create a working branch

Record the current branch before switching, so it can be restored at the end:

```bash
ORIGIN_BRANCH=$(git branch --show-current)
git checkout -b deps/symfony-6.4.{X} {upstream-remote}/{target-branch}
```

Basing directly on `{upstream-remote}/{target-branch}` ensures the working tree reflects the canonical upstream state, regardless of whether the local fork is in sync.

### 6. Run composer update

```bash
composer update "symfony/*" --with-dependencies 2>&1
```

This updates all packages constrained to `~6.4.0` in `composer.json`.
`symfony/ux-*` and `symfony/contracts` packages follow a different versioning cadence and are **not** included.

### 7. Verify the update

```bash
git diff --stat composer.lock
```

- If the diff is empty, warn that nothing changed (the version may already be in the local cache) and investigate.
- Otherwise, display the list of updated packages and their new versions.

### 8. Commit

```bash
git add composer.lock
git commit -m "Update Symfony components to v6.4.{X}"
```

### 9. Create the PR

Read `.github/PULL_REQUEST_TEMPLATE.md` to confirm the current table format, then create the PR against the upstream repository:

```bash
gh pr create \
  --repo PrestaShop/PrestaShop \
  --base {target-branch} \
  --title "Update Symfony components after {version} release" \
  --body "$(cat <<'EOF'
| Questions         | Answers
| ----------------- | -------------------------------------------------------
| Branch?           | {target-branch}
| Description?      | Update Symfony components following the {version} release.
| Type?             | improvement
| Category?         | CO
| BC breaks?        | no
| Deprecations?     | no
| How to test?      | Run the UI test suite. [Visit the UI Tests repo and follow instructions](https://github.com/PrestaShop/ga.tests.ui.pr/).
| UI Tests          | N/A
| Fixed issue or discussion?     | N/A
| Related PRs       | N/A
| Sponsor company   | N/A
EOF
)"
```

### 10. Return to the original branch

```bash
git checkout $ORIGIN_BRANCH
```

This restores the branch from which the skill was invoked (typically `develop`, where the `.ai/` context lives).

## Output

The created PR URL, displayed to the user.
