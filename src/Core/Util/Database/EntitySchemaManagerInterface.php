<?php

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Util\Database;

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
     */
    public function create(string $entityClassName, bool $dropIfExist = true): bool;

    /**
     * Update the table schema for the given entity class name.
     *
     * @param string $entityClassName the class name of the entity
     *
     * @return bool true on success, false on failure
     */
    public function update(string $entityClassName): bool;

    /**
     * Drop the table for the given entity class name.
     *
     * @param string $entityClassName the class name of the entity
     *
     * @return bool true on success, false on failure
     */
    public function drop(string $entityClassName): bool;

    /**
     * Create tables for multiple entities.
     *
     * @param array $entitiesClassesName an array of entity class names
     * @param bool $dropIfExist Whether to drop tables if they already exist. Defaults to true.
     *
     * @return bool true on success, false if any operation failed
     */
    public function createMultiple(array $entitiesClassesName, bool $dropIfExist = true): bool;

    /**
     * Update tables for multiple entities.
     *
     * @param array $entitiesClassesName an array of entity class names
     *
     * @return bool true on success, false if any operation failed
     */
    public function updateMultiple(array $entitiesClassesName): bool;

    /**
     * Drop tables for multiple entities.
     *
     * @param array $entitiesClassesName an array of entity class names
     *
     * @return bool true on success, false if any operation failed
     */
    public function dropMultiple(array $entitiesClassesName): bool;
}
