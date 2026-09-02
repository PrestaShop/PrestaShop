<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Presenter\Product;

use PrestaShop\PrestaShop\Adapter\Presenter\LazyArrayAttribute;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductCustomizabilitySettings;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;

class ProductListingLazyArray extends ProductLazyArray
{
    /**
     * Custom implementation of add to cart URL for product listing. In product listing, we have a bit stricter
     * rules to allow adding to cart a product. Specifically, we do not want to allow adding to cart of product
     * combinations if the setting is disabled. Also, we do not want to allow adding to cart of products that
     * require customization, because it's not possible to do so from the listing page.
     *
     * @return string|null
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getAddToCartUrl()
    {
        if ($this->product['id_product_attribute'] != 0 && !$this->settings->allow_add_variant_to_cart_from_listing) {
            return null;
        }

        if ($this->product['customizable'] == ProductCustomizabilitySettings::REQUIRES_CUSTOMIZATION || $this->getCustomizationRequired()) {
            return null;
        }

        return parent::getAddToCartUrl();
    }

    /**
     * @param array $product
     * @param ProductPresentationSettings $settings
     *
     * @return bool
     */
    protected function shouldEnableAddToCartButton(array $product, ProductPresentationSettings $settings)
    {
        if (isset($product['attributes'])
            && !empty($product['attributes'])
            && !$settings->allow_add_variant_to_cart_from_listing) {
            return false;
        }

        return parent::shouldEnableAddToCartButton($product, $settings);
    }

    /**
     * Returns the quantity wanted value for products in listings. We have this specific implementation
     * because in listings, the quantity wanted is not to be taken from the request directly.
     * If a specific value was already provided, we use it. For example, in cart context.
     *
     * @return int Quantity wanted, usually 1, altered if needed, always a positive integer
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getQuantityWanted()
    {
        if (empty($this->product['quantity_wanted'])) {
            $this->product['quantity_wanted'] = $this->getQuantityRequired();
        }

        return $this->product['quantity_wanted'];
    }
}
