<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Domain;

use Context;
use Db;
use ObjectModel;
use PrestaShopDatabaseException;
use Shop;

/**
 * Class AbstractObjectModelLegacyHandler
 */
abstract class AbstractObjectModelLegacyHandler
{
    /**
     * This function assigns stores ids to the given object. It removes previously set shop ids and adds new ids instead.
     *
     * @param ObjectModel $objectModel
     * @param array $shopAssociation
     *
     * @throws PrestaShopDatabaseException
     */
    protected function associateWithShops(ObjectModel $objectModel, array $shopAssociation)
    {
        if (!Shop::isFeatureActive()) {
            return;
        }

        $tableName = (string) $objectModel::$definition['table'];
        $primaryKeyName = (string) $objectModel::$definition['primary'];
        $primaryKeyValue = (int) $objectModel->id;

        if (!Shop::isTableAssociated($tableName)) {
            return;
        }

        // Get list of shop id we want to exclude from asso deletion
        $excludeIds = $shopAssociation;
        foreach (Db::getInstance()->executeS('SELECT id_shop FROM ' . _DB_PREFIX_ . 'shop') as $row) {
            if (!Context::getContext()->employee->hasAuthOnShop($row['id_shop'])) {
                $excludeIds[] = $row['id_shop'];
            }
        }

        $excludeShopsCondtion = $excludeIds ?
            ' AND id_shop NOT IN (' . implode(', ', array_map('intval', $excludeIds)) . ')' :
            ''
        ;

        Db::getInstance()->delete(
            $tableName . '_shop',
            '`' . $primaryKeyName . '` = ' . $primaryKeyValue . $excludeShopsCondtion
        );

        $insert = [];
        foreach ($shopAssociation as $shopId) {
            $insert[] = [
                $primaryKeyName => $primaryKeyValue,
                'id_shop' => (int) $shopId,
            ];
        }

        Db::getInstance()->insert(
            $tableName . '_shop',
            $insert,
            false,
            true,
            Db::INSERT_IGNORE
        );
    }
}
