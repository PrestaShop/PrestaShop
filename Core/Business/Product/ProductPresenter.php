<?php

namespace PrestaShop\PrestaShop\Core\Business\Product;

use Adapter_ObjectSerializer;
use Adapter_ImageRetriever;
use Adapter_ProductPriceCalculator;
use PrestaShop\PrestaShop\Core\Business\Price\PricePresenterInterface;
use Product;
use Language;
use Link;

class ProductPresenter
{
    private $imageRetriever;
    private $link;
    private $pricePresenter;
    private $productPriceCalculator;

    public function __construct(
        Adapter_ProductPriceCalculator $productPriceCalculator,
        Adapter_ImageRetriever $imageRetriever,
        Link $link,
        PricePresenterInterface $pricePresenter
    ) {
        $this->productPriceCalculator = $productPriceCalculator;
        $this->imageRetriever = $imageRetriever;
        $this->link = $link;
        $this->pricePresenter = $pricePresenter;
    }

    public function present(
        ProductPresentationSettings $settings,
        Product $product,
        $id_product_attribute,
        Language $language
    ) {
        $presentedProduct = (new Adapter_ObjectSerializer)->toArray($product);

        if ($settings->catalog_mode) {
            $presentedProduct['show_price'] = false;
        }

        if ($settings->restricted_country_mode) {
            $presentedProduct['show_price'] = false;
        }

        if (!$product->available_for_order) {
            $presentedProduct['show_price'] = false;
        }

        $presentedProduct['images'] = $this->imageRetriever->getProductImages(
            $product,
            $language
        );

        if (!isset($presentedProduct['cover'])) {
            $presentedProduct['cover'] = $presentedProduct['images'][0];
        }

        $presentedProduct['url'] = $this->link->getProductLink(
            $product,
            $product->link_rewrite, null, null,
            $language->id
        );

        $specific_price = [];

        $regular_price = $price = $this->productPriceCalculator->getProductPrice(
            $product->id,
            $settings->include_taxes,
            $id_product_attribute,
            6,
            null,
            false,
            true,
            1,
            false,
            null,
            null,
            null,
            $specific_price
        );

        if ($specific_price) {
            $regular_price = $this->productPriceCalculator->getProductPrice(
                $product->id,
                $settings->include_taxes,
                $id_product_attribute,
                6,
                null,
                false,
                false,
                1,
                false,
                null,
                null,
                null,
                $specific_price
            );
            $presentedProduct['has_discount'] = true;
            $presentedProduct['discount_type'] = $specific_price['reduction_type'];
            // TODO: format according to locale preferences
            $presentedProduct['discount_percentage'] = -round(100 * $specific_price['reduction'])."%";
        } else {
            $presentedProduct['has_discount'] = false;
            $presentedProduct['discount_percentage'] = 0;
        }


        $presentedProduct['price'] = $this->pricePresenter->format(
            $this->pricePresenter->convertAmount($price)
        );

        $presentedProduct['regular_price'] = $this->pricePresenter->format(
            $this->pricePresenter->convertAmount($regular_price)
        );

        return $presentedProduct;
    }
}
