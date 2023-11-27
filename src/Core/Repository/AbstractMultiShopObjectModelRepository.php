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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Repository;

use function bqSQL;
use Db;
use DbQuery;
use ObjectModel;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopAssociationNotFound;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopDefinitionNotFound;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShopDatabaseException;
use PrestaShopException;
use Shop;

/**
 * This abstract class is an extension of the AbstractObjectModelRepository that provides additional helper functions
 * to deal with multi shop entities. It provides additional function which rely on mandatory shop IDs. When you use
 * this class you shouldn't rely on single shop functions anymore, your repository should be oriented as a multi shop
 * one and always require some shop parameters (single shop becomes only an edge case of you generic multi shop
 * behaviour).
 */
class AbstractMultiShopObjectModelRepository extends AbstractObjectModelRepository
{
    /**
     * @param int $id
     * @param string $objectModelClass
     * @param string $exceptionClass
     * @param ShopId $shopId
     * @param string $shopAssociationClass
     *
     * @return ObjectModel
     *
     * @throws CoreException
     * @throws ShopAssociationNotFound
     */
    protected function getObjectModelForShop(int $id, string $objectModelClass, string $exceptionClass, ShopId $shopId, string $shopAssociationClass = ShopAssociationNotFound::class): ObjectModel
    {
        $objectModel = $this->fetchObjectModel($id, $objectModelClass, $exceptionClass, $shopId->getValue());

        // The object is fetched before checking the association, so that the NotFoundException has the priority over the NoAssociationException
        $this->checkShopAssociation($id, $objectModelClass, $shopId, $shopAssociationClass);

        // Force id_shop_list right away so that DB modification use the appropriate shop and not the one from context
        $objectModel->id_shop_list = [$shopId->getValue()];

        return $objectModel;
    }

    /**
     * @param ObjectModel $objectModel
     * @param ShopId[] $shopIds
     * @param string $exceptionClass
     * @param int $errorCode
     *
     * @return int
     */
    protected function addObjectModelToShops(ObjectModel $objectModel, array $shopIds, string $exceptionClass, int $errorCode = 0): int
    {
        // Force internal shop list which is used as an override of the one from Context when generating the SQL queries
        // this way we can control exactly which shop is updated
        $objectModel->id_shop_list = array_map(function (ShopId $shopId): int {
            return $shopId->getValue();
        }, $shopIds);

        return $this->addObjectModel($objectModel, $exceptionClass, $errorCode);
    }

    /**
     * @param ObjectModel $objectModel
     * @param ShopId[] $shopIds
     * @param string $exceptionClass
     * @param int $errorCode
     *
     * @throws CoreException
     */
    protected function updateObjectModelForShops(
        ObjectModel $objectModel,
        array $shopIds,
        string $exceptionClass,
        int $errorCode = 0
    ): void {
        // Force internal shop list which is used as an override of the one from Context when generating the SQL queries
        // this way we can control exactly which shop is updated
        $objectModel->id_shop_list = array_map(function (ShopId $shopId): int {
            return $shopId->getValue();
        }, $shopIds);

        $this->updateObjectModel($objectModel, $exceptionClass, $errorCode);
    }

    /**
     * @param ObjectModel $objectModel
     * @param array<int|string, string|int[]> $propertiesToUpdate
     * @param ShopId[] $shopIds
     * @param string $exceptionClass
     * @param int $errorCode
     *
     * @throws CoreException
     */
    protected function partiallyUpdateObjectModelForShops(
        ObjectModel $objectModel,
        array $propertiesToUpdate,
        array $shopIds,
        string $exceptionClass,
        int $errorCode = 0
    ): void {
        $objectModel->setFieldsToUpdate($this->formatPropertiesToUpdate($propertiesToUpdate));
        $this->updateObjectModelForShops($objectModel, $shopIds, $exceptionClass, $errorCode);
    }

    /**
     * @param int $id
     * @param string $objectModelClassName
     * @param ShopId $shopId
     *
     * @return bool
     */
    protected function hasShopAssociation(int $id, string $objectModelClassName, ShopId $shopId): bool
    {
        $modelDefinition = $objectModelClassName::$definition;
        $objectTable = $modelDefinition['table'];
        $primaryColumn = $modelDefinition['primary'];

        $query = new DbQuery();
        if (Shop::isTableAssociated($objectTable)) {
            $query
                ->select('e.`' . bqSQL($primaryColumn) . '` as id')
                ->from(bqSQL($objectTable) . '_shop', 'e')
                ->where('e.`' . bqSQL($primaryColumn) . '` = ' . $id)
                ->where('e.`id_shop` = ' . $shopId->getValue())
            ;
        } elseif (!empty($modelDefinition['multilang_shop'])) {
            $query
                ->select('e.`' . bqSQL($primaryColumn) . '` as id')
                ->from(bqSQL($objectTable) . '_lang', 'e')
                ->where('e.`' . bqSQL($primaryColumn) . '` = ' . $id)
                ->where('e.`id_shop` = ' . $shopId->getValue())
            ;
        } else {
            throw new ShopDefinitionNotFound(sprintf(
                'Entity %s has no multishop feature',
                $objectModelClassName
            ));
        }

        try {
            $row = Db::getInstance()->getRow($query, false);
        } catch (PrestaShopDatabaseException|PrestaShopException $e) {
            $row = false;
        }

        return !empty($row['id']);
    }

    /**
     * @param int $id
     * @param string $objectModelClassName
     * @param ShopId $shopId
     * @param string $shopAssociationExceptionClass
     *
     * @throws ShopAssociationNotFound
     */
    protected function checkShopAssociation(
        int $id,
        string $objectModelClassName,
        ShopId $shopId,
        string $shopAssociationExceptionClass = ShopAssociationNotFound::class
    ): void {
        if (!$this->hasShopAssociation($id, $objectModelClassName, $shopId)) {
            throw new $shopAssociationExceptionClass(sprintf(
                'Could not find association between %s %d and Shop %d',
                $objectModelClassName,
                $id,
                $shopId->getValue()
            ));
        }
    }

    /**
     * @param ObjectModel $objectModel
     * @param ShopId[] $shopIds
     * @param string $exceptionClass
     * @param int $errorCode
     *
     * @throws CoreException
     */
    protected function deleteObjectModelFromShops(ObjectModel $objectModel, array $shopIds, string $exceptionClass, int $errorCode = 0): void
    {
        if (empty($shopIds)) {
            throw new InvalidArgumentException('The shopIds should not be empty');
        }
        try {
            // Force internal shop list which is used as an override of the one from Context when generating the SQL queries
            // this way we can control exactly which shop is deleted
            $objectModel->id_shop_list = array_map(static function (ShopId $shopId): int {
                return $shopId->getValue();
            }, $shopIds);

            if (!$objectModel->delete()) {
                throw new $exceptionClass(
                    sprintf('Failed to delete %s #%d', $objectModel::class, $objectModel->id),
                    $errorCode
                );
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf(
                    'Error occurred when trying to delete %s #%d [%s]',
                    $objectModel::class,
                    $objectModel->id,
                    $e->getMessage()
                ),
                0,
                $e
            );
        }
    }

    /**
     * @param int $id
     * @param string $objectModelClassName
     *
     * @return int[]
     *
     * @throws ShopDefinitionNotFound
     */
    protected function getObjectModelAssociatedShopIds(int $id, string $objectModelClassName): array
    {
        $modelDefinition = $objectModelClassName::$definition;
        $objectTable = $modelDefinition['table'];
        $primaryColumn = $modelDefinition['primary'];

        $query = new DbQuery();
        $primaryColumn = 'e.`' . bqSQL($primaryColumn) . '`';
        $shopColumn = 'e.`id_shop`';
        if (Shop::isTableAssociated($objectTable)) {
            $query
                ->select($shopColumn . ' AS id_shop')
                ->where($primaryColumn . ' = ' . $id)
                ->from(bqSQL($objectTable) . '_shop', 'e')
                ->groupBy($shopColumn)
            ;
        } elseif (!empty($modelDefinition['multilang_shop'])) {
            $query
                ->select($shopColumn . ' AS id_shop')
                ->where($primaryColumn . ' = ' . $id)
                ->from(bqSQL($objectTable) . '_lang', 'e')
                ->groupBy($shopColumn)
            ;
        } else {
            throw new ShopDefinitionNotFound(sprintf(
                'Entity %s has no multishop feature',
                $objectModelClassName
            ));
        }

        try {
            $rows = Db::getInstance()->executeS($query);

            return array_map(fn (array $row) => (int) $row['id_shop'], $rows);
        } catch (PrestaShopDatabaseException|PrestaShopException $e) {
            return [];
        }
    }

    /**
     * This function assigns stores ids to the specified object if they are not already and it removes existing associations
     * if they are not wanted anymore.
     *
     * @throws PrestaShopDatabaseException
     */
    protected function updateObjectModelShopAssociations(
        int $id,
        string $objectModelClassName,
        array $updatedShopIds
    ): void {
        if (empty($updatedShopIds)) {
            return;
        }

        $modelDefinition = $objectModelClassName::$definition;
        $tableName = (string) $modelDefinition['table'];
        $primaryKeyName = (string) $modelDefinition['primary'];

        $associatedShopIds = $this->getObjectModelAssociatedShopIds($id, $objectModelClassName);

        $shopIdsToAdd = [];
        foreach ($updatedShopIds as $shopId) {
            if (!in_array($shopId, $associatedShopIds)) {
                $shopIdsToAdd[] = $shopId;
            }
        }
        $shopIdsToRemove = [];
        foreach ($associatedShopIds as $shopId) {
            if (!in_array($shopId, $updatedShopIds)) {
                $shopIdsToRemove[] = $shopId;
            }
        }

        if (!empty($shopIdsToRemove)) {
            Db::getInstance()->delete(
                $tableName . '_shop',
                '`' . $primaryKeyName . '` = ' . $id . ' AND `id_shop` IN (' . implode(',', $shopIdsToRemove) . ')'
            );
        }

        if (!empty($shopIdsToAdd)) {
            $insert = [];
            foreach ($shopIdsToAdd as $shopId) {
                $insert[] = [
                    $primaryKeyName => $id,
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
}
