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

namespace PrestaShop\PrestaShop\Adapter\Product;

use Category;
use GroupReduction;
use Image;
use Pack;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use Product;

/**
 * Duplicates product
 */
class ProductDuplicator
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param Product $product
     *
     * @return Product
     */
    public function duplicate(Product $product): Product
    {
        $oldProductId = (int) $product->id;
        unset($product->id, $product->id_product);
        $product->indexed = 0;
        $product->active = 0;
        $newProductId = $this->productRepository->add($product)->getValue();

        if (Category::duplicateProductCategories($oldProductId, $newProductId)
            && Product::duplicateSuppliers($oldProductId, $newProductId)
            && ($combinationImages = Product::duplicateAttributes($oldProductId, $newProductId)) !== false
            && GroupReduction::duplicateReduction($oldProductId, $newProductId)
            && Product::duplicateAccessories($oldProductId, $newProductId)
            && Product::duplicateFeatures($oldProductId, $newProductId)
            && Product::duplicateSpecificPrices($oldProductId, $newProductId)
            && Pack::duplicate($oldProductId, $newProductId)
            && Product::duplicateCustomizationFields($oldProductId, $newProductId)
            && Product::duplicateTags($oldProductId, $newProductId)
            && Product::duplicateDownload($oldProductId, $newProductId)
        ) {
            if ($product->hasAttributes()) {
                Product::updateDefaultAttribute($newProductId);
            }
            Image::duplicateProductImages($oldProductId, $newProductId, $combinationImages);
        }
        //@todo: clean up and dont forget hooks in AdminProductsController L571 & PrestaShopBundle\Controller\Admin\ProductController L1063
    }
}
