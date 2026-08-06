---
name: setup-apiresources-dev
description: >
  Configure modules/ps_apiresources as a development-ready git checkout (symlink
  to existing local clone or fresh clone with upstream + fork remotes), then
  provision the local test DB. Trigger: "set up ps_apiresources for development",
  "develop on the API module", "fix the API integration tests", "work on
  ps_apiresources locally".
needs: []
produces: "modules/ps_apiresources/ as a git checkout (in-place or symlinked) with upstream + fork remotes wired, and a provisioned local test DB ready for `composer run-api-module-tests-local`."
---

# setup-apiresources-dev

Read @.ai/Component/AdminAPI/CONTEXT.md for the architectural background.

This skill prepares `modules/ps_apiresources/` for local development. Composer ships the module as a read-only dependency, so a contributor wanting to fix and push tests has to wire up an actual git checkout.

The skill branches at runtime — every choice is an explicit `AskUserQuestion`. Do NOT auto-decide anything that touches `modules/ps_apiresources/` without confirmation.

## 1. Detect the current state of `modules/ps_apiresources`

Run from the project root:

```bash
ls -la modules/ps_apiresources 2>&1 | head -2
```

Branch on the result:

- **Missing** (no entry): run `composer install` — `ps_apiresources` is a dev dependency. Re-run the detection.
- **Symlink** (`test -L modules/ps_apiresources`): verify the target is a git directory (`test -d "$(readlink modules/ps_apiresources)/.git"`). If yes and the target already has `upstream` + a personal fork remote, **skip to step 6** — already dev-ready.
- **Plain directory**: check for a `.git/` entry inside.
  - **`.git/` present**: it's already a git checkout. If `upstream` + fork remotes are wired, **skip to step 6**. Otherwise, jump to step 4 to wire remotes (skip the clone step).
  - **`.git/` absent**: composer-installed, NOT dev-ready. Continue to step 2.

If the directory is a non-empty plain folder without `.git/`, run `git -C modules/ps_apiresources status 2>&1` first — composer-installed dirs return an error, which is the safe signal. **Never `rm -rf` a directory until you've confirmed it has no untracked work.**

## 2. Pick the setup mode (`AskUserQuestion`)

Ask the user:

> `modules/ps_apiresources` isn't a development checkout yet. How do you want to set it up?

Three options:

- **Clone into an external folder, then symlink** (recommended) → Branch C. The git checkout lives outside the project tree, safer because `composer install` / `composer update` only ever replace the symlink, never the external clone.
- **Symlink to a clone I already have elsewhere** → Branch A.
- **Clone fresh directly under modules/** → Branch B. Simpler but riskier — composer can wipe the in-place checkout.

## 3. Branch A — symlink to an existing local clone

3a. **Ask for the absolute path** via `AskUserQuestion`. Validate before doing anything destructive:

```bash
test -d "<absolute-path>" || echo "path does not exist"
test -d "<absolute-path>/.git" || echo "not a git directory"
```

If either check fails, refuse and re-ask. Reject relative paths and unexpanded `~` — `ln -s` does not expand them.

3b. **Confirm replacement** via `AskUserQuestion`:

> The composer-installed `modules/ps_apiresources/` folder will be deleted and replaced by a symlink to `<absolute-path>`. Proceed?

Only proceed if the existing entry is a directory (not already a symlink).

3c. **Replace**:

```bash
rm -rf modules/ps_apiresources
ln -s <absolute-path> modules/ps_apiresources
```

Skip to **step 6**.

## 4. Branch B — fresh clone

4a. **Pick the protocol** via `AskUserQuestion`:

> Use SSH or HTTPS for the upstream remote?

Defaults to SSH if `~/.ssh/id_*` keys exist OR if the parent project's `origin` matches `git@github.com:`. Run `git -C . remote get-url origin` to check.

4b. **Confirm replacement** via `AskUserQuestion` (same as 3b — only if the existing entry is a composer-installed directory).

4c. **Clone upstream**:

```bash
rm -rf modules/ps_apiresources
git clone git@github.com:PrestaShop/ps_apiresources.git modules/ps_apiresources       # SSH
# or
git clone https://github.com/PrestaShop/ps_apiresources.git modules/ps_apiresources   # HTTPS
```

4d. **Deduce the contributor's fork URL.** Read the parent project's remotes:

```bash
git -C . remote -v
```

If `origin` (or any remote) points to `git@github.com:<user>/PrestaShop.git`, the natural fork URL is `git@github.com:<user>/ps_apiresources.git` (HTTPS equivalent: `https://github.com/<user>/ps_apiresources.git`). Match the protocol chosen in 4a.

**Confirm via `AskUserQuestion`** — present the deduced URL as the recommended option, but always allow the user to override (e.g. corp fork, fork-of-fork, different account). Never auto-apply.

4e. **Wire the remotes.** Convention used in this skill: `fork` = the contributor's fork, `upstream` = PrestaShop's main repo. The names are explicit on purpose so contributors don't confuse the upstream with their own fork — this differs from the parent project where `origin` = fork, but it makes the two remotes self-documenting.

```bash
git -C modules/ps_apiresources remote rename origin upstream
git -C modules/ps_apiresources remote add fork <fork-url>
git -C modules/ps_apiresources fetch fork || true   # tolerate fork not existing yet
```

If `git fetch fork` fails because the fork doesn't exist on GitHub yet, treat as a soft warning and tell the user to create it at `https://github.com/<user>/ps_apiresources/fork`. Do NOT block setup — `fork` can be added before the fork exists.

## 5. Branch C — clone into external folder + symlink (recommended)

Same operations as Branch B, but the clone lands at an external path and `modules/ps_apiresources` becomes a symlink to it. Run the Branch B steps with `<external-path>` substituted for `modules/ps_apiresources`, then symlink at the end.

5a. **Ask for the absolute external path** via `AskUserQuestion`:

> Where should the ps_apiresources clone live on disk? Pick a path outside this PrestaShop project tree (e.g. `~/dev/ps_apiresources`).

Validate: reject relative paths and unexpanded `~`; the parent directory must exist; the target itself must NOT exist or must be empty.

5b. **Run Branch B steps 4a, 4c, 4d, 4e** with `<external-path>` substituted for `modules/ps_apiresources`. Note: 4c's `rm -rf modules/ps_apiresources` line does NOT apply here (the external path is empty); only the `git clone <upstream-url> <external-path>` runs.

5c. **Confirm replacement** of `modules/ps_apiresources/` via `AskUserQuestion` — only if the existing entry is a composer-installed plain dir.

5d. **Replace with a symlink**:

```bash
rm -rf modules/ps_apiresources
ln -s <external-path> modules/ps_apiresources
```

Skip to **step 6**.

## 6. Verify the git setup

```bash
git -C modules/ps_apiresources remote -v        # must show both upstream + fork
git -C modules/ps_apiresources status           # must be clean / on a known branch
```

Abort and report if remotes are missing or the working tree isn't clean.

## 7. Pick the work branch

Any new fix or improvement on `ps_apiresources` MUST start from `upstream/dev` HEAD — that's the only way to be in sync with the latest upstream state. `AskUserQuestion`:

> What branch will you work on?
>
> - Existing branch — already on my fork; the skill will fetch and check it out.
> - New branch — the skill will create one from `upstream/dev` HEAD.
> - Skip — I'll handle the branch myself.

Refuse to proceed in any path if the working tree isn't clean (`git -C modules/ps_apiresources status --porcelain` non-empty). Ask the user to commit or stash first.

**Existing branch path** — `AskUserQuestion` for the branch name, then:

```bash
git -C modules/ps_apiresources fetch fork
git -C modules/ps_apiresources switch <branch-name>
# If no local branch yet: git -C modules/ps_apiresources switch -c <branch-name> fork/<branch-name>
```

**New branch path** — `AskUserQuestion` for the branch name. Propose the parent core project's current branch name as the default (read via `git -C . branch --show-current`) so the module branch mirrors the core PR's branch; the user can override (e.g. pick a different naming convention or scope).

Then:

```bash
git -C modules/ps_apiresources fetch upstream
git -C modules/ps_apiresources switch --no-track -c <branch-name> upstream/dev
```

`--no-track` is critical: a plain `switch -c <name> upstream/dev` auto-binds the new branch's upstream to `upstream/dev`, so a later `git push` would target the main PrestaShop repository directly. The work branch must stay local-only until the contributor pushes it to their fork (`git push -u fork HEAD`), at which point it tracks `fork/<branch>` — never `upstream`.

## 8. Provision the local test DB

The test DB is shared across all PrestaShop test suites (Integration, Behat, ApiPlatform, ps_apiresources). If the contributor has run any test setup before, it's already there — re-creating it is slow and unnecessary. Always check first:

```bash
composer check-test-db
```

- **Exit 0** — `Test environment is ready.` Skip provisioning, but still run `composer dump-autoload --dev --working-dir=modules/ps_apiresources` once to make sure the module's autoload is current after we just rewired its directory. Continue to step 9.
- **Exit non-zero** — at least one of: dump files missing, per-table dumps missing, or DB unreachable. `AskUserQuestion` to confirm before resetting any existing local test DB, then:

  ```bash
  composer create-test-db
  composer dump-autoload --dev --working-dir=modules/ps_apiresources
  ```

The full suite is NOT run here — it's slow and unnecessary as a setup check. A single test class is enough to confirm the env (next step).

## 9. Verify the dev setup

Run a single test class as a smoke test:

```bash
_PS_ROOT_DIR_=$(pwd) php -d date.timezone=UTC ./vendor/phpunit/phpunit/phpunit \
    -c modules/ps_apiresources/tests/Integration/phpunit-local.xml \
    --filter=AddressEndpointTest
```

Look for `OK (...)` in the output. `FAILURES!` or `ERRORS!` means the env isn't ready — capture the error and report. The full suite (`composer run-api-module-tests-local`) is available as a day-to-day command (step 10) but isn't run here.

## 10. Day-to-day commands

After this skill completes, refresh the module autoload once:

```bash
composer dump-autoload --dev --working-dir=modules/ps_apiresources
```

For day-to-day test commands (re-run the suite, run a single test class) see [`Component/AdminAPI/CONTEXT.md` → Conventions](../../CONTEXT.md#conventions). The `_PS_ROOT_DIR_` inlining requirement documented there is critical when the module is symlinked — a stale `_PS_ROOT_DIR_` exported from another shell silently misroutes everything.

## 11. Next steps / related

- For new endpoints: invoke `/ps-api-endpoint`, or read [`skills/ps-api-endpoint/SKILL.md`](../ps-api-endpoint/SKILL.md) (chained symlink — the canonical file lives in `modules/ps_apiresources/.claude/skills/ps-api-endpoint/`).
- For pairing this module with a fork branch in a core PR: invoke `/link-apiresources-fork`.
