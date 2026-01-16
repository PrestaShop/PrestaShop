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

namespace PrestaShop\PrestaShop\Core\Trait;

/**
 * Trait DirtyTrait
 * Provides functionality to track modified (dirty) properties of an object.
 * You need to manually call markDirty() method when a property is modified.
 */
trait DirtyTrait
{
    /**
     * Indicates whether the object has been modified.
     */
    protected array $dirtyProperties = [];

    /**
     * Marks a specific property as dirty (modified).
     *
     * @param string $propertyName
     *
     * @return void
     */
    public function markDirty(string $propertyName): void
    {
        $this->dirtyProperties[$propertyName] = true;
    }

    /**
     * Checks if a specific property is dirty (modified).
     *
     * @param string $propertyName
     *
     * @return bool
     */
    public function isDirty(string $propertyName): bool
    {
        return isset($this->dirtyProperties[$propertyName]) && $this->dirtyProperties[$propertyName];
    }

    /**
     * Retrieves a list of all dirty (modified) properties.
     *
     * @return array
     */
    public function getDirtyProperties(): array
    {
        return array_keys($this->dirtyProperties);
    }
}
