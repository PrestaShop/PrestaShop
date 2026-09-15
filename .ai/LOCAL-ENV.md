# Local Development Environment

How the Docker environment decides to install, how to get back to a clean shop, and how the test database is built. The failure modes below are silent: the environment comes up and only misbehaves later.

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

Both halves are required, database and `parameters.php`:

```sh
docker compose down -v
rm -f app/config/parameters.php app/config/parameters.yml
docker compose up -d
```

To keep the volume instead, set `PS_ERASE_DB=1` and remove `parameters.php`: the bootstrap drops and recreates the database itself.

`docker compose down` without `-v` removes the containers but keeps the named volume, so the shop database survives. That is the right choice when freeing resources rather than resetting.

## The test database

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

## Test fixtures deleted by the test install

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

## `git clean` and what `-x` costs

`-x` also removes gitignored files. In this repository that means `vendor/`, `node_modules/`, `var/`, the built back-office theme assets under `admin-dev/themes/*/public/`, the generated `config/themes/*`, and `app/config/parameters.php`. A `git clean -xdf` therefore forces a full `composer install` plus an asset rebuild, and drops the shop into the reinstall path.

Preview before running anything:

```sh
git clean -xdn      # dry run, lists what -x would remove
```

For everyday cleanup, prefer:

```sh
git clean -df       # untracked files, keeps gitignored build output
rm -rf var/cache/*  # clears the Symfony caches only
```

## Related

- [CONTAINERS.md](CONTAINERS.md) for the service container and kernel topology (unrelated to Docker containers, despite the name)
- [Component/Behat/CONTEXT.md](Component/Behat/CONTEXT.md) and [Component/Playwright/CONTEXT.md](Component/Playwright/CONTEXT.md) for the suites that depend on the test database
