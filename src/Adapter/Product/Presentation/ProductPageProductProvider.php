<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Presentation;

use Configuration;
use Context;
use Group;
use GroupReduction;
use Hook;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductLazyArray;
use PrestaShop\PrestaShop\Core\Product\ProductExtraContentFinder;
use Product;
use Tools;

final class ProductPageProductProvider
{
    public function __construct(
        private readonly ProductPageProductPreparationInterface $preparation,
    ) {
    }

    public function getProduct(
        Product $product,
        Context $context,
        ?int $idProductAttribute,
        int $quantityWanted = 1,
    ): ProductLazyArray {
        return $this->getProductWithPreparation($product, $context, $idProductAttribute, $quantityWanted, $this->preparation);
    }

    /**
     * @internal
     */
    public function getProductWithPreparation(
        Product $product,
        Context $context,
        ?int $idProductAttribute,
        int $quantityWanted,
        ProductPageProductPreparationInterface $preparation,
        ?callable $idProductAttributeResolver = null,
    ): ProductLazyArray {
        $productForPresentation = $preparation->presentObject($product);
        $productForPresentation['description'] = $preparation->transformDescriptionWithImg((string) $product->description, $product, $context);
        $productForPresentation['out_of_stock'] = (int) $product->out_of_stock;

        if ($idProductAttributeResolver !== null) {
            $idProductAttribute = $idProductAttributeResolver();
        }

        $productForPresentation['id_product_attribute'] = $idProductAttribute;
        $productForPresentation['minimal_quantity'] = $preparation->getProductMinimalQuantity($productForPresentation, $product, $context, $idProductAttribute);
        $productForPresentation['cart_quantity'] = $context->cart->getProductQuantity((int) $product->id, $idProductAttribute)['quantity'];
        $productForPresentation['quantity_wanted'] = $preparation->getWantedQuantity($quantityWanted, $productForPresentation);
        $productForPresentation['quantity_required'] = $preparation->getRequiredQuantity($productForPresentation);
        $productForPresentation['extraContent'] = (new ProductExtraContentFinder())->addParams(['product' => $product])->present();
        $productForPresentation['ecotax_tax_inc'] = $product->getEcotax(null, true, true);
        $productForPresentation['ecotax'] = Tools::convertPrice($preparation->getProductEcotax($productForPresentation, $product, $context), $context->currency, true, $context);

        $productFull = Product::getProductProperties($context->language->id, $productForPresentation, $context);
        $productFull = $preparation->addProductCustomizationData($productFull, $product, $context);
        $productFull['show_quantities'] = (bool) (
            Configuration::get('PS_DISPLAY_QTIES')
            && Configuration::get('PS_STOCK_MANAGEMENT')
            && $productFull['quantity'] > 0
            && $product->available_for_order
            && !Configuration::isCatalogMode()
        );
        $productFull['quantity_label'] = $preparation->getQuantityLabel((int) $productFull['quantity'], $context);
        $productFull['quantity_discounts'] = $preparation->getQuantityDiscounts($product, $context, $idProductAttribute);
        $groupReduction = GroupReduction::getValueForProduct($product->id, (int) Group::getCurrent()->id);
        $productFull['customer_group_discount'] = $groupReduction === false ? Group::getReduction((int) $context->cookie->id_customer) / 100 : $groupReduction;
        $productFull['title'] = $preparation->appendAttributesToTitle($product->name, $product, $context, $idProductAttribute);

        return $preparation->presentProduct($productFull, $context);
    }

    /**
     * The legacy hook does not guarantee the type of the replacement object.
     *
     * @return mixed
     */
    public function filterProductContent(ProductLazyArray $product)
    {
        $filteredProduct = Hook::exec('filterProductContent', ['object' => $product], null, false, true, false, null, true);

        return !empty($filteredProduct['object']) ? $filteredProduct['object'] : $product;
    }
}
