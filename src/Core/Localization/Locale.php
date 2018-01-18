<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Localization;

use PrestaShop\PrestaShop\Core\Localization\Currency\CurrencyCollection;
use PrestaShop\PrestaShop\Core\Localization\Specification\Number as NumberSpecification;
use PrestaShop\PrestaShop\Core\Localization\Specification\Price as PriceSpecification;

class Locale implements LocaleInterface
{
    /**
     * @var NumberSpecification
     */
    protected $numberSpecification;

    /**
     * @var CurrencyCollection
     */
    protected $currencies;

    /**
     * Get price specification for a given currency
     *
     * @param string $currencyCode
     *  The currency's ISO 4217 code
     *
     * @return PriceSpecification
     *  A Price specification
     */
    protected function getPriceSpecification($currencyCode)
    {
        $currency = $this->currencies->get($currencyCode);

        // $priceSpecification = new Price(
        //     $positivePattern,
        //     $negativePattern,
        //     $symbols,
        //     $maxFractionDigits,
        //     $minFractionDigits,
        //     $groupingUsed,
        //     $primaryGroupSize,
        //     $secondaryGroupSize,
        //     $currencyDisplay,
        //     $currency->getSymbol($this->code),
        //     $currency->getIsoCode()
        // );
    }
}
