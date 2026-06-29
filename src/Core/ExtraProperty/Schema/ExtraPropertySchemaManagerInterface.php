<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Schema;

use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;

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
     */
    public function ensureExtraTableAndColumn(ExtraPropertyDefinition $definition): void;

    /**
     * Drops the custom column from the extra table when table and column exist.
     * Also drops the extra table itself when it becomes empty after the column removal.
     *
     * @param ExtraPropertyDefinition $definition Definition identifying the column to drop
     */
    public function dropExtraColumnIfExists(ExtraPropertyDefinition $definition): void;
}
