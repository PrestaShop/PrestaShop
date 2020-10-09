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

namespace PrestaShop\PrestaShop\Core\Import\EntityField;

use ArrayAccess;
use Countable;
use IteratorAggregate;

/**
 * Interface EntityFieldCollectionInterface describes a collection of entity fields.
 */
interface EntityFieldCollectionInterface extends ArrayAccess, Countable, IteratorAggregate
{
    /**
     * Add an entity field to the collection.
     *
     * @param EntityFieldInterface $entityField
     *
     * @return self
     */
    public function addEntityField(EntityFieldInterface $entityField);

    /**
     * Get required fields from the collection.
     *
     * @return array
     */
    public function getRequiredFields();

    /**
     * Creates a collection from array of entity fields.
     *
     * @param array $entityFields array of objects implementing EntityFieldInterface
     *
     * @return self
     */
    public static function createFromArray(array $entityFields);

    /**
     * Converts the collection to array.
     *
     * @return array
     */
    public function toArray();
}
