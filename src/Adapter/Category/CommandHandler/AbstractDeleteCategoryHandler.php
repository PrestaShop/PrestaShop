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

namespace PrestaShop\PrestaShop\Adapter\Category\CommandHandler;

use Configuration;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryDeleteMode;
use Product;
use Shop;

/**
 * Class AbstractDeleteCategoryHandler.
 */
abstract class AbstractDeleteCategoryHandler
{
    /**
     * @deprecated
     * @todo: mark as deprecated properly
     *
     * Handle products category after its deletion.
     *
     * @param int $parentCategoryId
     * @param CategoryDeleteMode $mode
     */
    protected function handleProductsUpdate($parentCategoryId, CategoryDeleteMode $mode)
    {
        $productsWithoutCategory = \Db::getInstance()->executeS('
			SELECT p.`id_product`
			FROM `' . _DB_PREFIX_ . 'product` p
			' . Shop::addSqlAssociation('product', 'p') . '
			WHERE NOT EXISTS (
			    SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp WHERE cp.`id_product` = p.`id_product`
			)
		');

        foreach ($productsWithoutCategory as $productWithoutCategory) {
            $product = new Product((int) $productWithoutCategory['id_product']);

            if ($product->id) {
                if (0 === $parentCategoryId || $mode->shouldRemoveProducts()) {
                    $product->delete();

                    continue;
                }

                if ($mode->shouldDisableProducts()) {
                    $product->active = false;
                }

                $product->id_category_default = $parentCategoryId;
                $product->addToCategories($parentCategoryId);
                $product->save();
            }
        }
    }

    /**
     * @param array<int, int[]> $deletedCategoryIdsByParent
     * @param CategoryDeleteMode $mode
     */
    protected function updateProductCategories(array $deletedCategoryIdsByParent, CategoryDeleteMode $mode): void
    {
        $this->updateProductsWithoutCategories($deletedCategoryIdsByParent, $mode);
        $this->updateProductsByDefaultCategories($deletedCategoryIdsByParent);
    }

    /**
     * @todo: move to repository
     *
     * @return array
     */
    private function findProductsWithoutCategories(): array
    {
        $productsWithoutCategory = \Db::getInstance()->executeS('
			SELECT p.`id_product`
			FROM `' . _DB_PREFIX_ . 'product` p
			' . Shop::addSqlAssociation('product', 'p') . '
			WHERE NOT EXISTS (
			    SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp WHERE cp.`id_product` = p.`id_product`
			)
		');

        return $productsWithoutCategory ?? [];
    }

    /**
     * @todo: move to repository sql part of the method - findProductsInDefaultCategories()?
     *
     * @param array<int, int[]> $deletedCategoryIdsByParent
     *
     * @return array
     */
    private function findProductsByDefaultCategories(array $deletedCategoryIdsByParent): array
    {
        $deletedCategoryIds = [];
        foreach ($deletedCategoryIdsByParent as $deletedIds) {
            $deletedCategoryIds = array_merge($deletedCategoryIds, array_map('intval', $deletedIds));
        }

        $affectedProducts = \Db::getInstance()->executeS('
			SELECT p.`id_product`
			FROM `' . _DB_PREFIX_ . 'product` p
			' . Shop::addSqlAssociation('product', 'p') . '
			WHERE p.id_category_default IN (' . implode(',', $deletedCategoryIds) . ')
		');

        return $affectedProducts ?? [];
    }

    /**
     * @param int $categoryId
     * @param array<int, int[]> $deletedCategoryIdsByParent
     *
     * @return int|null
     */
    private function findCategoryParentId(int $categoryId, array $deletedCategoryIdsByParent): ?int
    {
        $parentCategoryId = null;
        foreach ($deletedCategoryIdsByParent as $parentId => $deletedIds) {
            // find parent id for deleted category
            if (in_array($categoryId, $deletedIds, true)) {
                $parentCategoryId = $parentId;
                break;
            }
        }

        return $parentCategoryId;
    }

    /**
     * @param Product $product
     * @param int $categoryId
     * @param array<int, int[]> $deletedCategoryIdsByParent
     */
    private function addProductDefaultCategory(Product $product, int $categoryId, array $deletedCategoryIdsByParent): void
    {
        // @todo: inject rootCategoryId into constructor
        $rootCategoryId = (int) Configuration::get('PS_ROOT_CATEGORY');

        $parentCategoryId = $this->findCategoryParentId($categoryId, $deletedCategoryIdsByParent);
        $product->id_category_default = $parentCategoryId ?: $rootCategoryId;
        $product->addToCategories($parentCategoryId);
        $product->save();
    }

    /**
     * @param array<int, int[]> $deletedCategoryIdsByParent
     * @param CategoryDeleteMode $mode
     */
    private function updateProductsWithoutCategories(array $deletedCategoryIdsByParent, CategoryDeleteMode $mode): void
    {
        $productsWithoutCategories = $this->findProductsWithoutCategories();

        foreach ($productsWithoutCategories as $productWithoutCategory) {
            //@todo: use ProductRepository->get()?
            $product = new Product((int) $productWithoutCategory['id_product']);

            if (!$product->id) {
                continue;
            }

            if ($mode->shouldRemoveProducts()) {
                $product->delete();

                continue;
            }

            if ($mode->shouldDisableProducts()) {
                $product->active = false;
            }

            $this->addProductDefaultCategory($product, (int) $product->id_category_default, $deletedCategoryIdsByParent);
        }
    }

    /**
     * @param array<int, int[]> $deletedCategoryIdsByParent
     */
    private function updateProductsByDefaultCategories(array $deletedCategoryIdsByParent): void
    {
        $affectedProductsByDefaultCategory = $this->findProductsByDefaultCategories($deletedCategoryIdsByParent);

        foreach ($affectedProductsByDefaultCategory as $affectedProduct) {
            $product = new Product((int) $affectedProduct['id_product']);
            if (!$product->id) {
                continue;
            }

            $this->addProductDefaultCategory($product, (int) $product->id_category_default, $deletedCategoryIdsByParent);
        }
    }
}
