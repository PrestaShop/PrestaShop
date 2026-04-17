<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Product;

class ProductPresentationSettings
{
    public $catalog_mode;
    public $catalog_mode_with_prices;
    public $restricted_country_mode;
    public $include_taxes;
    public $allow_add_variant_to_cart_from_listing;
    public $stock_management_enabled;
    public $showPrices;
    public $lastRemainingItems;
    /**
     * @var bool|null
     */
    public $showLabelOOSListingPages;

    public function shouldShowPrice()
    {
        return $this->showPrices && (!$this->catalog_mode || $this->catalog_mode_with_prices);
    }
}
