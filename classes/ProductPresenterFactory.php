<?php

use PrestaShop\PrestaShop\Core\Product\ProductPresenter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;

class ProductPresenterFactoryCore
{
    private $context;
    private $taxConfiguration;

    public function __construct(Context $context, TaxConfiguration $taxConfiguration = null)
    {
        $this->context = $context;
        $this->taxConfiguration = (is_null($taxConfiguration)) ? new TaxConfiguration() : $taxConfiguration;
    }

    public function getPresentationSettings()
    {
        $settings = new ProductPresentationSettings();

        $settings->catalog_mode = Configuration::get('PS_CATALOG_MODE');
        // TODO StarterTheme : $settings->restricted_country_mode = "???";
        $settings->include_taxes = $this->taxConfiguration->includeTaxes();
        $settings->allow_add_variant_to_cart_from_listing = (int) Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY');
        $settings->stock_management_enabled = Configuration::get('PS_STOCK_MANAGEMENT');

        return $settings;
    }

    public function getPresenter()
    {
        $imageRetriever = new ImageRetriever(
            $this->context->link
        );

        if (is_a($this->context->controller, 'ProductListingFrontControllerCore')) {
            return new ProductListingPresenter(
                $imageRetriever,
                $this->context->link,
                new PriceFormatter(),
                new ProductColorsRetriever(),
                $this->context->getTranslator()
            );
        }

        return new ProductPresenter(
            $imageRetriever,
            $this->context->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            $this->context->getTranslator()
        );
    }
}
