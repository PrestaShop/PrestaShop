<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Product;

use Context;
use Currency;
use Tools;

/**
 * Format a price depending on locale and currency.
 */
class PriceFormatter
{
    /**
     * @param float $price
     * @param int|null $currency
     *
     * @return float
     */
    public function convertAmount($price, $currency = null)
    {
        return (float) Tools::convertPrice($price, $currency);
    }

    /**
     * @param float $price
     * @param int|Currency|array|null $currency
     *
     * @return string
     */
    public function format($price, $currency = null)
    {
        $context = Context::getContext();
        $priceCurrency = is_array($currency) ? $currency['iso_code'] : null;
        $priceCurrency = !$priceCurrency && $currency instanceof Currency ? $currency->iso_code : $priceCurrency;
        $priceCurrency = !$priceCurrency ? $context->currency->iso_code : $priceCurrency;

        return Tools::getContextLocale($context)->formatPrice($price, $priceCurrency);
    }

    /**
     * @param float $price
     *
     * @return string
     */
    public function convertAndFormat($price)
    {
        return $this->format($this->convertAmount($price));
    }
}
