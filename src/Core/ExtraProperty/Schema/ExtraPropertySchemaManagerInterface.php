<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Schema;

use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\ExtraPropertyRegistryException;

/**
 * Manages the DDL (Data Definition Language) operations on extra storage tables.
 *
 * Responsible for creating, altering, and dropping the *_extra / *_extra_lang / *_extra_shop
 * tables and their custom columns. May be decorated with a cache-invalidation layer.
 */
interface ExtraPropertySchemaManagerInterface
{
    /**
     * Ensures that the extra table and its custom column exist and match the definition.
     * Creates the table (copying the PK from the base entity table) if needed.
     * Creates the column (using the SQL definition built from the ExtraPropertyDefinition) if needed.
     * When the column already exists, synchronises its definition with the declared one
     * (size, NULL clause, ENUM literals, DEFAULT) via ALTER TABLE … MODIFY COLUMN — the
     * caller is responsible for rejecting destructive changes beforehand.
     * Synchronises the SQL index strategy on the column.
     *
     * @param ExtraPropertyDefinition $definition Fully configured definition including type, scope, column name, and index strategy
     *
     * @return bool true when the storage column was newly added by this call, false when it
     *              already existed and was only synchronised — callers use this to know whether
     *              a compensating dropExtraColumnIfExists() would remove pre-existing data
     *
     * @throws ExtraPropertyRegistryException code BASE_TABLE_NOT_FOUND when the native base table to mirror
     *                                        does not exist, SCHEMA_FAILURE when it has no primary key
     *
     * Raw database driver exceptions may also escape from the DDL statements themselves —
     * callers are expected to wrap them (see ExtraPropertyRegistry)
     */
    public function ensureExtraTableAndColumn(ExtraPropertyDefinition $definition): bool;

    /**
     * Drops the custom column from the extra table when table and column exist.
     * Also drops the extra table itself when it becomes empty after the column removal.
     *
     * @param ExtraPropertyDefinition $definition Definition identifying the column to drop
     */
    public function dropExtraColumnIfExists(ExtraPropertyDefinition $definition): void;
}
