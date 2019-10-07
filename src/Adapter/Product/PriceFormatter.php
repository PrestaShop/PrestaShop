<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Product;

use Context;
use Currency;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository as LocaleRepository;
use Tools;

/**
 * Format a price depending on locale and currency.
 */
class PriceFormatter
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var Locale
     */
    private $locale;

    /**
     * @param Context $context
     * @param LocaleRepository $repository
     */
    public function __construct(
        Context $context,
        LocaleRepository $repository
    ) {
        $this->context = $context;
        $this->locale = $repository->getLocale(
            $this->context->getContext()->language->getLocale()
        );
    }

    /**
     * @param float $price
     * @param string|null $currency
     *
     * @return float
     */
    public function convertAmount($price, $currency = null)
    {
        return (float) Tools::convertPrice($price, $currency);
    }

    /**
     * @param float $price
     * @param string|null $currency
     *
     * @return string
     */
    public function format($price, $currency = null)
    {
        $currency = $currency ?: $this->context->currency;
        $currency = is_array($currency) ? Currency::getCurrencyInstance($currency['id_currency']) : $currency;
        return $this->locale->formatPrice($price, $currency->iso_code);
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
