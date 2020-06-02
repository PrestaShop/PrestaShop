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
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetEditableProduct;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler\GetEditableProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\EditableProduct;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductType;
use Product;

/**
 * Handles the query GetEditableProduct using legacy ObjectModel
 */
class GetEditableProductHandler extends AbstractProductHandler implements GetEditableProductHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws ProductConstraintException
     * @throws ProductException
     * @throws ProductNotFoundException
     */
    public function handle(GetEditableProduct $query): EditableProduct
    {
        $product = $this->getProduct($query->getProductId());

        return new EditableProduct(
            $product->id,
            $product->name,
            $this->getProductType($product)
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
