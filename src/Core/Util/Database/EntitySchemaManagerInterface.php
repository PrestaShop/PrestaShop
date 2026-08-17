<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Util\Database;

use PrestaShop\PrestaShop\Core\Exception\DatabaseException;

/**
 * Interface EntitySchemaManagerInterface
 * This interface defines the methods for managing entity schemas in the database (create, update, drop).
 */
interface EntitySchemaManagerInterface
{
    /**
     * Create the table for the given entity class name.
     *
     * @param string $entityClassName the class name of the entity
     * @param bool $dropIfExist Whether to drop the table if it already exists. Defaults to true.
     *
     * @return bool true on success, false on failure
     *
     * @throws DatabaseException
     */
    public function create(string $entityClassName, bool $dropIfExist = true): bool;

    /**
     * Update the table schema for the given entity class name.
     *
     * @param string $entityClassName the class name of the entity
     *
     * @return bool true on success, false on failure
     *
     * @throws DatabaseException
     */
    public function update(string $entityClassName): bool;

    /**
     * Drop the table for the given entity class name.
     *
     * @param string $entityClassName the class name of the entity
     *
     * @return bool true on success, false on failure
     *
     * @throws DatabaseException
     */
    public function drop(string $entityClassName): bool;

    /**
     * Create tables for multiple entities.
     *
     * @param array $entitiesClassesName an array of entity class names
     * @param bool $dropIfExist Whether to drop tables if they already exist. Defaults to true.
     *
     * @return bool true on success, false if any operation failed
     *
     * @throws DatabaseException
     */
    public function createMultiple(array $entitiesClassesName, bool $dropIfExist = true): bool;

    /**
     * Update tables for multiple entities.
     *
     * @param array $entitiesClassesName an array of entity class names
     *
     * @return bool true on success, false if any operation failed
     *
     * @throws DatabaseException
     */
    public function updateMultiple(array $entitiesClassesName): bool;

    /**
     * Drop tables for multiple entities.
     *
     * @param array $entitiesClassesName an array of entity class names
     *
     * @return bool true on success, false if any operation failed
     *
     * @throws DatabaseException
     */
    public function dropMultiple(array $entitiesClassesName): bool;

    /**
     * Adds a new path for entities to the entity manager (Ex.: %kernel.project_dir%/modules/MyModule/src/Entity)
     *
     * @param string $entityPath The path where Doctrine should look for entities
     */
    public function addEntityPath(string $entityPath): void;
}
