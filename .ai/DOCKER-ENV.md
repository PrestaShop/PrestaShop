# Docker Development Environment

**Scope: the Docker setup shipped in this repository** (`.docker/`, `docker-compose.yml`). If you serve the shop from your own stack (MAMP, XAMPP, a local nginx/php-fpm, a remote host), only the sections marked *(any environment)* apply, and the `docker compose exec` prefix drops off the commands in them.

How the bootstrap decides to install, how to get back to a clean shop, and how the test database is built. The failure modes below are silent: the environment comes up and only misbehaves later.

## The install gate: `app/config/parameters.php`

`.docker/docker_run_git.sh:118` decides everything from one file test:

```sh
if [ ! -f ./app/config/parameters.php ]; then
    if [ $PS_INSTALL_AUTO = 1 ]; then
        if [ $PS_ERASE_DB = 1 ]; then   # drop + recreate the database
```

The `PS_ERASE_DB` block is **nested inside** the "`parameters.php` absent" branch. Two consequences that catch people out:

| Action | Result |
|--------|--------|
| `PS_ERASE_DB=1` alone, `parameters.php` present | Nothing happens. The script prints "PrestaShop Core already installed" and skips the whole block |
| `docker compose down -v` alone, `parameters.php` present | **Broken shop.** Empty database, but the script still skips the install. Every page 500s on missing tables |
| `parameters.php` removed + volume dropped (or `PS_ERASE_DB=1`) | Clean reinstall |

`parameters.php` and `parameters.yml` are gitignored and live in the bind-mounted repository, so they survive `docker compose down` and container recreation. Only removing them triggers a reinstall.

## Resetting to a clean shop

Three levels, in increasing cost. Take the cheapest one that covers the symptom.

| Level | Throws away | Keeps | Cost |
|-------|-------------|-------|------|
| Caches | `var/cache/*` | everything else | seconds |
| Clean shop | the database and `parameters.php` | `vendor/`, `node_modules/`, the built assets, the image | one shop reinstall |
| Full rebuild | every untracked and gitignored file, the volumes and the image | nothing | `composer install` plus a build of all five assets |

### Clean shop

Both halves are required, database and `parameters.php`:

```sh
docker compose down -v
rm -f app/config/parameters.php app/config/parameters.yml
docker compose up -d
```

To keep the volume instead, set `PS_ERASE_DB=1` and remove `parameters.php`: the bootstrap drops and recreates the database itself.

`docker compose down` without `-v` removes the containers but keeps the named volume, so the shop database survives. That is the right choice when freeing resources rather than resetting.

### Full rebuild

Only when the image itself is suspect: the PHP version moved in `.docker/Dockerfile`, `USER_ID` or `GROUP_ID` changed, `INSTALL_XDEBUG` was toggled, or `vendor/` is in a state no `composer install` recovers from.

**Preview the clean before running it, every time.** The two exclusions below are what this repository is known to need, not a complete list. Which files are gitignored depends on the local environment: another checkout may hold `.vscode/`, a personal `phpunit.xml`, a scratch SQL dump or fixtures that exist nowhere else, and nothing in git flags them as at risk. On a built checkout the raw dry run runs to several thousand lines, so read it by top level entry first:

```sh
LC_ALL=C git clean -dxn -e .idea -e docker-compose.override.yml \
  | sed 's|^Would remove ||' | cut -d/ -f1 | sort -u
```

`LC_ALL=C` pins the `Would remove` prefix, which is translated in other locales. Add a `-e` for every entry worth keeping, then rebuild:

```sh
git clean -dfx -e .idea -e docker-compose.override.yml
docker compose down -v
docker compose build --no-cache
docker compose up -d --force-recreate
```

Whoever runs this against a checkout that is not their own, a setup script or an AI agent included, shows that list and waits for an explicit confirmation before running the `git clean -dfx`. Nothing it removes can be restored from the repository.

**Keep `docker-compose.override.yml` out of the `git clean`.** It is gitignored, so `-x` takes it, and `USER_ID`, `GROUP_ID`, the port mapping, `INSTALL_XDEBUG` and `DISABLE_MAKE` go with it. `USER_ID` and `GROUP_ID` are build args consumed by `groupmod` and `usermod` (`Dockerfile:12-13` and `:21-22`), so the `build` on the next line then bakes the default uid into the image and every file the container writes comes back owned by the wrong user. `.env.local` is gitignored too. `.env` is tracked, so it survives.

`--no-cache` only reaches what the image holds: the base PHP image, the uid and gid remap, xdebug, the timezone. **Node is not in the image**, nvm installs it at container start (`docker_run_git.sh:41-49`), so no image rebuild ever fixes a node or an asset problem. A plain `docker compose build` already picks up changed build args on its own; `--no-cache` is for refreshing the base image and the `apt` layer.

The cost is in the first line, not the third: `git clean -x` takes `vendor/`, `node_modules/` and every built asset, so the next start redoes `composer install` and builds all five assets from scratch. Read what `git clean -x` costs, further down, before reaching for this.

## The asset build gate

`.docker/docker_run_git.sh:77-81` runs `tools/assets/build.sh` on **every container start**, unless `DISABLE_MAKE=1`. It does not rebuild everything each time: exactly like the install gate, it decides from one output file per theme (`build.sh:61-88`).

| Asset | Build directory | Output file the gate tests |
|-------|-----------------|----------------------------|
| `admin-default` | `admin-dev/themes/default` | `admin-dev/themes/default/public/theme.css` |
| `admin-new-theme` | `admin-dev/themes/new-theme` | `admin-dev/themes/new-theme/public/theme.css` |
| `front-core` | `themes` | `themes/core.js` |
| `front-classic` | `themes/classic/_dev` | `themes/classic/assets/css/theme.css` |
| `front-hummingbird` | `themes/hummingbird` | `themes/hummingbird/assets/css/theme.css` |

**The gate tests the output, never the sources.** Switching branch to review a pull request changes the SCSS, TypeScript and Vue sources, but `theme.css` is still there, so the bootstrap prints "already exists" and keeps serving the assets built from the branch you left. Nothing warns about it: the back office loads, with the previous branch's styling and JavaScript. The same applies on the way back, so a rebuild is owed in both directions.

Check whether a switch owes you one:

```sh
git diff --stat <previous-ref> -- admin-dev/themes themes
```

Any `.scss`, `.ts`, `.js` or `.vue` file in that list means the built assets are stale.

## Rebuilding assets

Target one asset rather than all five. Back-office work usually only needs `admin-new-theme`:

```sh
docker compose exec -u www-data prestashop-git \
  bash -lc 'cd admin-dev/themes/new-theme && npm run build'
```

`bash -lc` matters: node is installed through nvm and only reaches `PATH` from `/etc/profile`.

While iterating on a page, `npm run dev` in the same directory watches the sources and rebuilds on save.

`tools/assets/build.sh admin-new-theme --force` reaches the same result through the bootstrap, but its `build()` runs `rm -rf node_modules` then `npm ci` first (`build.sh:49-55`), so it costs minutes where `npm run build` costs seconds. Keep `--force` for the case it is meant for: the dependencies themselves changed, which shows up as `package-lock.json` moving in the diff.

### Interrupted builds leave a `buildLock`

`build()` creates a `buildLock` file in the theme directory and removes it once the build returns. `wait-build.sh` polls for it to disappear. A build killed part way (`docker compose down` during boot, an interrupted `build.sh`) leaves the lock behind, and the next container start then waits on it forever, printing `buildLock still present wait a bit more`.

Three of the four are **not gitignored**, so they also surface as untracked files in `git status` and can land in a pull request:

```sh
rm -f admin-dev/themes/default/buildLock \
      admin-dev/themes/new-theme/buildLock \
      themes/buildLock \
      themes/classic/_dev/buildLock
```

`build()` also starts by removing `node_modules`, so an interrupted build leaves that theme without its dependencies. `npm ci` in the theme directory restores it, without touching the other four.

### Skipping the build entirely

`DISABLE_MAKE=1` skips the node install, `composer install` and the asset build in one go (`docker_run_git.sh:37`). Restarts become near instant once everything is built, at the cost of never picking up a dependency change on its own. Set it in `docker-compose.override.yml`, which is gitignored.

## Working on several branches from one clone

Reviewing a pull request against `9.2.x` and then one against `develop` does not need a second clone. It needs knowing which moving parts follow the branch on their own and which do not.

| Part | On a container restart | Why |
|------|------------------------|-----|
| `vendor/` | follows the branch | `composer install` runs on every start (`docker_run_git.sh:73`), unconditionally |
| The image | usually nothing to do | check it rather than assume it: `git diff <other-branch> -- docker-compose.yml .docker/` |
| Assets | stale | the gate above tests the output file, never the sources |
| `var/cache/` | stale | the compiled container still points at the other branch's services |
| The database | stale | nothing migrates the schema at runtime |

`DISABLE_MAKE=1` costs you the first row as well as the asset build. Both sit in the same block.

### The switch

Set `PS_ERASE_DB=1` once in `docker-compose.override.yml`, then:

```sh
git switch <branch>
rm -f app/config/parameters.php app/config/parameters.yml
rm -rf var/cache/* var/logs/*
docker compose restart prestashop-git
```

Removing `parameters.php` reopens the install gate, and `PS_ERASE_DB=1` has the bootstrap drop and recreate the database itself, so nothing needs a volume rebuild and the MySQL container stays up. The restart re-runs the entrypoint, which is what picks `vendor/` back up. A new environment variable needs one `docker compose up -d` to be read, a `restart` will not see it.

Rebuild the assets on top when the diff touches them, which `git diff --stat <previous-branch> -- admin-dev/themes themes` tells you. Integration suites need `composer create-test-db` again: the dumps carry the version in their filename, so two branches never reuse each other's.

### Why reinstalling beats carrying one database across branches

The schema is only ever created by the branch's own `install-dev/data/db_structure.sql`. The core repository carries no upgrade scripts, those live in the autoupgrade module, so a database installed from one branch and used on another is simply missing whatever the other branch added since.

Nothing stops you, and that is the problem. `PS_VERSION_DB` is written once by the installer (`Install.php:456`) and never read at runtime, so no version check refuses to boot. A feature flag with no row in `ps_feature_flag` reads as disabled rather than raising (`FeatureFlagRepository::isEnabled`). The shop comes up, and a page is quietly absent or a column quietly missing: the same silent failure as everything else in this file.

Reinstalling on each switch removes the question instead of leaving you to track how far two branches have drifted. The price is shop state. The installer puts the demo fixtures back, but anything set up by hand is gone, including enabled feature flags:

```sh
docker compose exec -u www-data prestashop-git \
  php bin/console prestashop:feature-flag enable <name>
```

The day a switch costs more shop setup than it saves is the day to keep one database per branch: `parameters.php` is gitignored, so one copy per branch with its own `database_name`, against the same MySQL container, is enough. Not before.

## The test database *(any environment)*

**Always use `composer create-test-db`, never the raw script.** The composer script chains two files, and only the second one produces what the test resetters need:

```json
"create-test-db": [
  "@php ./tests/bin/create-test-db.php",
  "@php ./tests/bin/create-test-tables-dump.php"
]
```

`create-test-db.php` calls `DatabaseDump::create()`, which only writes the whole-database dump. The per-table dumps and checksums come from `dumpTables()` in `create-test-tables-dump.php`. Running the first script alone leaves every resetter failing with:

```
Cannot find dump for table ps_lang, you need to run 'composer create-test-db'
```

The message is accurate: it means the composer script, not the file it starts with.

| Script | Purpose |
|--------|---------|
| `composer create-test-db` | Full install of the test shop plus both dump layers |
| `composer create-test-table-dumps` | Refresh only the per-table dumps from the current database |
| `composer restore-test-db` | Restore the test database from the dump |
| `composer check-test-db` | Verify the dump exists before running a suite |

Dumps are written to `sys_get_temp_dir()` (`DatabaseDump.php:101` and `:287`), which is the **container's** `/tmp`, not a volume. They are lost whenever the container is recreated, while the database itself survives in the volume. After recreating containers, re-run `composer create-test-db`.

Dumps also carry the database name and `Version::VERSION` in their filename, so they do not carry across a version bump.

## Test fixtures deleted by the test install *(any environment)*

`composer create-test-db` **deletes tracked files** under `tests/Resources/modules/`, typically:

```
tests/Resources/modules/ps_emailsubscription/mails/fr/*
tests/Resources/modules/translationtest/translations/fr.php
```

`tests/bin/create-test-db.php:48` redefines `_PS_MODULE_DIR_` to `tests/Resources/modules/`, and the install removes the languages it does not keep. `Language::delete()` (`classes/Language.php:628-652`) then walks `_PS_MODULE_DIR_` and deletes each module's `mails/<iso>` directory and per-language translation files, hitting the committed fixtures.

These are tracked files, so `git clean` does not restore them:

```sh
git checkout -- tests/Resources/modules/
```

**Always check `git status` after running the test suite** and restore before committing, or the deletions land in the diff.

## Running commands in the container

`docker compose exec` runs as root by default, which leaves root-owned files in `var/cache` and makes the back office 500 afterwards. Always pass the web user:

```sh
docker compose exec -u www-data prestashop-git php bin/console ...
```

The container's `www-data` is remapped to the host user through the `USER_ID` / `GROUP_ID` build args, so files it writes stay editable on the host. Set them in `docker-compose.override.yml` (gitignored) to match your own uid and gid.

Give PHP an explicit memory ceiling rather than `-d memory_limit=-1`. Static analysis over the whole codebase can exhaust host memory and take the machine down.

## `git clean` and what `-x` costs *(any environment)*

`-x` also removes gitignored files. In this repository that means `vendor/`, `node_modules/`, `var/`, the built back-office theme assets under `admin-dev/themes/*/public/`, the generated `config/themes/*`, `app/config/parameters.php` and `docker-compose.override.yml`. A `git clean -xdf` therefore forces a full `composer install` plus an asset rebuild, drops the shop into the reinstall path, and takes your local compose settings with it. The full rebuild recipe above is the one place where that is the intent, and it excludes the override file for exactly this reason.

Preview before running anything:

```sh
git clean -xdn      # dry run, lists what -x would remove
```

Read that list. Anything gitignored you would miss belongs behind a `-e`.

For everyday cleanup, prefer:

```sh
git clean -df       # untracked files, keeps gitignored build output
rm -rf var/cache/*  # clears the Symfony caches only
```

`var/cache/`, `var/logs/`, `var/sessions/` and `var/modules/` each hold a **tracked `.gitkeep`**. The glob leaves it alone, since `*` matches no leading dot in bash or zsh, but `rm -rf var/cache/` without the glob takes the directory and the `.gitkeep` with it, and the deletion then shows up in `git status` and lands in the next diff.

`var/sessions/` is not worth clearing anyway: `framework.session.handler_id` is null (`app/config/config.yml:60-61`), so sessions go to the PHP native save path inside the container and never to that directory.

## Related

- [CONTAINERS.md](CONTAINERS.md) for the service container and kernel topology (unrelated to Docker containers, despite the name)
- [Component/Behat/CONTEXT.md](Component/Behat/CONTEXT.md) and [Component/Playwright/CONTEXT.md](Component/Playwright/CONTEXT.md) for the suites that depend on the test database
