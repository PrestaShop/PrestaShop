<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Import;

use PrestaShop\PrestaShop\Core\Import\Exception\NotSupportedImportTypeException;

/**
 * Class Entity defines available import entities.
 */
final class Entity
{
    const TYPE_CATEGORIES = 0;
    const TYPE_PRODUCTS = 1;
    const TYPE_COMBINATIONS = 2;
    const TYPE_CUSTOMERS = 3;
    const TYPE_ADDRESSES = 4;
    const TYPE_MANUFACTURERS = 5;
    const TYPE_SUPPLIERS = 6;
    const TYPE_ALIAS = 7;
    const TYPE_STORE_CONTACTS = 8;

    const AVAILABLE_TYPES = [
        'categories' => self::TYPE_CATEGORIES,
        'products' => self::TYPE_PRODUCTS,
        'combinations' => self::TYPE_COMBINATIONS,
        'customers' => self::TYPE_CUSTOMERS,
        'addresses' => self::TYPE_ADDRESSES,
        'manufacturers' => self::TYPE_MANUFACTURERS,
        'suppliers' => self::TYPE_SUPPLIERS,
        'alias' => self::TYPE_ALIAS,
        'contacts' => self::TYPE_STORE_CONTACTS,
    ];

    /**
     * Get import entity type from name.
     *
     * @param string $importType
     *
     * @return string
     */
    public static function getFromName($importType)
    {
        if (array_key_exists($importType, self::AVAILABLE_TYPES)) {
            return self::AVAILABLE_TYPES[$importType];
        }

        throw new NotSupportedImportTypeException(
            sprintf('Import type with name "%s" is not supported.', $importType)
        );
    }

    /**
     * Class is not supposed to be initialized.
     */
    private function __construct()
    {
    }
}
