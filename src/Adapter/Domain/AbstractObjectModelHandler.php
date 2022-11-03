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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Domain;

use Context;
use Db;
use ImageManager;
use ObjectModel;
use PrestaShopDatabaseException;
use Shop;

/**
 * Class AbstractObjectModelHandler is responsible for executing legacy code which is common for legacy ObjectModel.
 *
 * @internal
 */
abstract class AbstractObjectModelHandler
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
        if (empty($shopAssociation) || !Shop::isFeatureActive()) {
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

        $excludeShopsCondtion =
            ' AND id_shop NOT IN (' . implode(', ', array_map('intval', $excludeIds)) . ')';

        Db::getInstance()->delete(
            $tableName . '_shop',
            '`' . $primaryKeyName . '` = ' . $primaryKeyValue . $excludeShopsCondtion
        );

        $insert = [];
        foreach ($shopAssociation as $shopId) {
            // Check if context employee has access to the shop before inserting shop association.
            if (Context::getContext()->employee->hasAuthOnShop($shopId)) {
                $insert[] = [
                    $primaryKeyName => $primaryKeyValue,
                    'id_shop' => (int) $shopId,
                ];
            }
        }

        Db::getInstance()->insert(
            $tableName . '_shop',
            $insert,
            false,
            true,
            Db::INSERT_IGNORE
        );
    }

    /**
     * @param ObjectModel $objectModel
     * @param array $multiStoreColumnAssociation - an array key contains shop id while values contains the mapping of
     *                                           column and its value
     */
    protected function updateMultiStoreColumns(ObjectModel $objectModel, array $multiStoreColumnAssociation)
    {
        $tableName = (string) $objectModel::$definition['table'];
        $primaryKey = (string) $objectModel::$definition['primary'];
        $primaryKeyValue = (int) $objectModel->id;

        foreach ($multiStoreColumnAssociation as $shopId => $items) {
            $shop = new Shop($shopId);

            if (0 >= $shop->id || !is_array($items)) {
                continue;
            }

            $update = [];
            foreach ($items as $columnName => $columnValue) {
                $update[$columnName] = pSQL($columnValue);
            }

            Db::getInstance()->update(
                $tableName . '_shop',
                $update,
                $primaryKey . '=' . $primaryKeyValue
            );
        }
    }

    /**
     * @param string $imagePath the original image path
     * @param int $imageId
     * @param string $belongsTo object name to which image belongs (e.g. 'supplier', 'manufacturer')
     *
     * @return string
     */
    protected function getTmpImageTag($imagePath, $imageId, $belongsTo)
    {
        return ImageManager::thumbnail(
            $imagePath,
            $belongsTo . '_' . $imageId . '.jpg',
            350,
            'jpg',
            true,
            true
        );
    }

    /**
     * Calculates and returns image size in kb for provided image path. Returns null if image doesn't exist
     *
     * @param string $imagePath
     *
     * @return float|null
     */
    protected function getImageSize($imagePath)
    {
        return file_exists($imagePath) ? filesize($imagePath) / 1000 : null;
    }
}
