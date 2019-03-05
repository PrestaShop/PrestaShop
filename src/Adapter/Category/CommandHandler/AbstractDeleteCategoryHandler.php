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

namespace PrestaShop\PrestaShop\Adapter\Category\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryDeleteMode;
use Product;
use Shop;

/**
 * Class AbstractDeleteCategoryHandler.
 */
abstract class AbstractDeleteCategoryHandler
{
    /**
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
                    $product->active = 0;
                }

                $product->id_category_default = $parentCategoryId;
                $product->addToCategories($parentCategoryId);
                $product->save();
            }
        }
    }
}
