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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use PrestaShop\PrestaShop\Adapter\Configuration as AdapterConfiguration;
use PrestaShop\PrestaShop\Adapter\HookManager;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;

/**
 * Class ProductPresenterFactoryCore.
 */
class ProductPresenterFactoryCore
{
    private $context;
    private $taxConfiguration;

    /**
     * ProductPresenterFactoryCore constructor.
     *
     * @param Context $context
     * @param \TaxConfiguration|null $taxConfiguration
     */
    public function __construct(Context $context, TaxConfiguration $taxConfiguration = null)
    {
        $this->context = $context;
        $this->taxConfiguration = (null === $taxConfiguration) ? new TaxConfiguration() : $taxConfiguration;
    }

    /**
     * Get presentation settings.
     *
     * @return ProductPresentationSettings
     */
    public function getPresentationSettings()
    {
        $settings = new ProductPresentationSettings();

        $settings->catalog_mode = Configuration::isCatalogMode();
        $settings->catalog_mode_with_prices = (int) Configuration::get('PS_CATALOG_MODE_WITH_PRICES');
        $settings->include_taxes = $this->taxConfiguration->includeTaxes();
        $settings->allow_add_variant_to_cart_from_listing = (int) Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY');
        $settings->stock_management_enabled = Configuration::get('PS_STOCK_MANAGEMENT');
        $settings->showPrices = Configuration::showPrices();
        $settings->lastRemainingItems = Configuration::get('PS_LAST_QTIES');
        $settings->showLabelOOSListingPages = (bool) Configuration::get('PS_SHOW_LABEL_OOS_LISTING_PAGES');

        return $settings;
    }

    /**
     * Get presenter.
     *
     * @return ProductListingPresenter|ProductPresenter
     */
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
            $this->context->getTranslator(),
            new HookManager(),
            new AdapterConfiguration()
        );
    }
}
