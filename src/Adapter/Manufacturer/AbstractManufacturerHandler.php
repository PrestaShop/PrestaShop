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

namespace PrestaShop\PrestaShop\Adapter\Manufacturer;

use Context;
use Db;
use Manufacturer;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use PrestaShopDatabaseException;
use Shop;

/**
 * Provides reusable methods for manufacturer command/query handlers
 */
abstract class AbstractManufacturerHandler
{
    /**
     * Validates that requested manufacturer was found
     *
     * @param ManufacturerId $manufacturerId
     * @param Manufacturer $manufacturer
     *
     * @throws ManufacturerNotFoundException
     */
    protected function assertManufacturerWasFound(ManufacturerId $manufacturerId, Manufacturer $manufacturer)
    {
        if ($manufacturer->id !== $manufacturerId->getValue()) {
            throw new ManufacturerNotFoundException(
                sprintf('Manufacturer with id "%s" was not found.', $manufacturerId->getValue())
            );
        }
    }

    /**
     * Associates given manufacturer with shops
     *
     * @param int $manufacturerId
     * @param array $shopAssociation
     *
     * @throws PrestaShopDatabaseException
     */
    protected function associateWithShops($manufacturerId, array $shopAssociation)
    {
        if (!Shop::isFeatureActive()) {
            return;
        }

        $manufacturerTable = Manufacturer::$definition['table'];

        if (!Shop::isTableAssociated($manufacturerTable)) {
            return;
        }

        // Get list of shop id we want to exclude from asso deletion
        $excludeIds = $shopAssociation;
        foreach (Db::getInstance()->executeS('SELECT id_shop FROM ' . _DB_PREFIX_ . 'shop') as $row) {
            if (!Context::getContext()->employee->hasAuthOnShop($row['id_shop'])) {
                $excludeIds[] = $row['id_shop'];
            }
        }

        $excludeShopsCondition = $excludeIds ?
            ' AND id_shop NOT IN (' . implode(', ', array_map('intval', $excludeIds)) . ')' :
            ''
        ;

        Db::getInstance()->delete(
            $manufacturerTable . '_shop',
            '`id_manufacturer` = ' . (int) $manufacturerId . $excludeShopsCondition
        );

        $insert = [];
        foreach ($shopAssociation as $shopId) {
            $insert[] = [
                'id_manufacturer' => (int) $manufacturerId,
                'id_shop' => (int) $shopId,
            ];
        }

        Db::getInstance()->insert(
            $manufacturerTable . '_shop',
            $insert,
            false,
            true,
            Db::INSERT_IGNORE
        );
    }
}
