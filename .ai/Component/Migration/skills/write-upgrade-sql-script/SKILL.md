---
name: write-upgrade-sql-script
description: >
  Author a SQL upgrade script for shops upgrading to a target PrestaShop version.
  Covers schema changes, default fixtures / configuration row updates, and
  feature-flag state transitions.
produces: "An idempotent SQL script in the autoupgrade module's upgrade/sql/ directory"
---

# write-upgrade-sql-script

## Heads-up — cross-repo

Since PrestaShop 9.x, upgrade SQL scripts are **not** in the core repository. They live in the `autoupgrade` module: https://github.com/PrestaShop/autoupgrade (see `upgrade/sql/{version}.sql`). This skill describes *what to write*; the file lands in `autoupgrade`. Agents working in core should hand the change off to a maintainer or open the PR against `autoupgrade` directly.

## When to use this skill

Whenever a change merged into core needs an existing shop to apply a database modification at upgrade time. Common cases:

1. **Schema change** — new table, new column, new index, type change.
2. **Default fixtures / configuration update** — a new row in `ps_configuration`, a new row in `ps_feature_flag`, etc., that newly-installed shops get from the XML/install fixtures but existing shops do not.
3. **Feature-flag state transition** — promoting a flag from beta to stable and enabling it for existing installs.

A new install reads the install fixtures directly; only **upgrading** shops need this script.

## Instructions

1. Determine the target PrestaShop version (e.g., 9.1.0).
2. Open or create `upgrade/sql/{version}.sql` in the `autoupgrade` module repo.
3. Append the SQL needed for your change. Use the section that matches your case below.
4. Make every statement **idempotent** — the script may be run more than once and must not error or corrupt data on re-run.

### Case (a) — schema change

```sql
-- Add column only if it doesn't exist (MySQL 8.0+ supports IF NOT EXISTS on ALTER TABLE ADD COLUMN)
ALTER TABLE `PREFIX_xxx`
    ADD COLUMN IF NOT EXISTS `new_field` INT(11) NOT NULL DEFAULT 0;

-- Create table only if it doesn't exist
CREATE TABLE IF NOT EXISTS `PREFIX_xxx_yyy` (
    `id_xxx` INT(11) UNSIGNED NOT NULL,
    `id_yyy` INT(11) UNSIGNED NOT NULL,
    PRIMARY KEY (`id_xxx`, `id_yyy`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;
```

### Case (b) — default fixtures / configuration update

```sql
-- Insert configuration row only if not present (preserves merchant overrides)
INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`)
SELECT 'PS_NEW_OPTION', '1', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `PREFIX_configuration` WHERE `name` = 'PS_NEW_OPTION');
```

### Case (c) — feature-flag state transition

```sql
-- Promote flag to stable + enable it for existing installs
UPDATE `PREFIX_feature_flag`
   SET `state` = 1, `stability` = 'stable'
 WHERE `name` = '{domain}';

-- If the flag might be missing on very old installs, also INSERT it
INSERT INTO `PREFIX_feature_flag` (`name`, `state`, `stability`, `label_wording`, `label_domain`, `description_wording`, `description_domain`)
SELECT '{domain}', 1, 'stable', 'Use new {Domain} page', 'Admin.Advparameters.Feature', 'Enables the new Symfony page for {Domain}', 'Admin.Advparameters.Help'
WHERE NOT EXISTS (SELECT 1 FROM `PREFIX_feature_flag` WHERE `name` = '{domain}');
```

## Rules

- **Idempotent.** Always — re-running the script must be a no-op.
- **Use `PREFIX_`**, not a hardcoded table prefix — `autoupgrade` substitutes it.
- **Use `ENGINE_TYPE`**, not a hardcoded engine — `autoupgrade` substitutes it.
- **Never `DROP` data without a backup path.** A column rename is two scripts (add new, copy data, drop old in the next minor) — never a single destructive statement at upgrade time.
- **Match the install fixtures.** If you add a row here, the same row must be in the corresponding install fixture (`feature_flag.xml`, `configuration.xml`, etc.) so new installs get it natively.
- **No PHP**, no comments embedded in the middle of statements that would confuse the SQL splitter — keep statements clean and terminated by `;`.
