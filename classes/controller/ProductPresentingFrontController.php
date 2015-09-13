<?php

use PrestaShop\PrestaShop\Core\Business\Product\ProductPresenter;
use PrestaShop\PrestaShop\Core\Business\Product\ProductPresentationSettings;

class ProductPresentingFrontControllerCore extends FrontController
{
    protected function getProductPresentationSettings()
    {
        $settings = new ProductPresentationSettings;

        $settings->catalog_mode = Configuration::get('PS_CATALOG_MODE');
        $settings->restricted_country_mode = $this->restricted_country_mode;
        $settings->include_taxes = !Product::getTaxCalculationMethod((int)$this->context->cookie->id_customer);

        return $settings;
    }

    protected function getProductPresenter()
    {
        $imageRetriever = new Adapter_ImageRetriever($this->context->link);

        return new ProductPresenter(
            new Adapter_ProductPriceCalculator,
            $imageRetriever,
            $this->context->link,
            new Adapter_PricePresenter
        );
    }
}
