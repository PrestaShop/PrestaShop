<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Product\QueryHandler;

use Pack;
use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler\GetProductForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductBasicInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductCategoriesInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductPricesInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductType;
use Product;

/**
 * Handles the query GetEditableProduct using legacy ObjectModel
 */
class GetProductForEditingHandler extends AbstractProductHandler implements GetProductForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetProductForEditing $query): ProductForEditing
    {
        $product = $this->getProduct($query->getProductId());

        return new ProductForEditing(
            (int) $product->id,
            (bool) $product->active,
            $this->getBasicInformation($product),
            $this->getCategoriesInformation($product),
            $this->getPricesInformation($product)
        );
    }

    /**
     * @param Product $product
     *
     * @return ProductBasicInformation
     */
    private function getBasicInformation(Product $product): ProductBasicInformation
    {
        return new ProductBasicInformation(
            $this->getProductType($product),
            $product->name,
            $product->description,
            $product->description_short
        );
    }

    /**
     * @param Product $product
     *
     * @return ProductCategoriesInformation
     */
    private function getCategoriesInformation(Product $product): ProductCategoriesInformation
    {
        $categoryIds = array_map('intval', $product->getCategories());
        $defaultCategoryId = (int) $product->id_category_default;

        return new ProductCategoriesInformation($categoryIds, $defaultCategoryId);
    }

    /**
     * @param Product $product
     *
     * @return ProductPricesInformation
     */
    private function getPricesInformation(Product $product): ProductPricesInformation
    {
        return new ProductPricesInformation(
            $this->getPropertyAsNumber($product, 'price'),
            $this->getPropertyAsNumber($product, 'ecotax'),
            (int) $product->id_tax_rules_group,
            (bool) $product->on_sale,
            $this->getPropertyAsNumber($product, 'wholesale_price'),
            $this->getPropertyAsNumber($product, 'unit_price'),
            (string) $product->unity,
            $this->getPropertyAsNumber($product, 'unit_price_ratio')
        );
    }

    /**
     * @param Product $product
     *
     * @return ProductType
     *
     * @throws ProductConstraintException
     */
    private function getProductType(Product $product): ProductType
    {
        if ($product->is_virtual) {
            $productTypeValue = ProductType::TYPE_VIRTUAL;
        } elseif (Pack::isPack($product->id)) {
            $productTypeValue = ProductType::TYPE_PACK;
        } elseif (!empty($product->getAttributeCombinations())) {
            $productTypeValue = ProductType::TYPE_COMBINATION;
        } else {
            $productTypeValue = ProductType::TYPE_STANDARD;
        }

        return new ProductType($productTypeValue);
    }
}
