<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
     * @param TaxConfiguration|null $taxConfiguration
     */
    public function __construct(Context $context, ?TaxConfiguration $taxConfiguration = null)
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
