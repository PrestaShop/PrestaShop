<?php

use PrestaShop\PrestaShop\Core\Product\ProductPresenter;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;
use PrestaShop\PrestaShop\Adapter\Product\PricePresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Translator;
use PrestaShop\PrestaShop\Adapter\LegacyContext;

class ProductPresenterFactoryCore
{
    private $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function getPresentationSettings()
    {
        $settings = new ProductPresentationSettings;

        $settings->catalog_mode = Configuration::get('PS_CATALOG_MODE');
        // TODO StarterTheme : $settings->restricted_country_mode = "???";
        $settings->include_taxes = !Product::getTaxCalculationMethod((int)$this->context->cookie->id_customer);
        $settings->allow_add_variant_to_cart_from_listing =  (int)Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY');
        $settings->stock_management_enabled = Configuration::get('PS_STOCK_MANAGEMENT');

        return $settings;
    }

    public function getPresenter()
    {
        $imageRetriever = new ImageRetriever(
            $this->context->link
        );

        return new ProductPresenter(
            $imageRetriever,
            $this->context->link,
            new PricePresenter,
            new ProductColorsRetriever,
            new Translator(new LegacyContext)
        );
    }
}
