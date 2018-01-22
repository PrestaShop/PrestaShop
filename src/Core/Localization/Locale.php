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

use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\Number\Formatter as NumberFormatter;
use PrestaShop\PrestaShop\Core\Localization\Specification\Number as NumberSpecification;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberCollection as PriceSpecificationMap;

/**
 * Locale entity
 *
 * This is the main CLDR entry point. For example, Locale is used to format numbers, prices, percentages.
 * To build a Locale instance, use the Locale repository.
 */
class Locale implements LocaleInterface
{
    /**
     * Number formatter.
     * Used to format raw numbers in this locale context
     *
     * @var NumberFormatter
     */
    protected $numberFormatter;

    /**
     * Number formatting specification
     *
     * @var NumberSpecification
     */
    protected $numberSpecification;

    /**
     * Price formatting specifications collection (one spec per currency)
     *
     * @var PriceSpecificationMap
     */
    protected $priceSpecifications;

    /**
     * Locale constructor.
     *
     * @param NumberSpecification $numberSpecification
     *  Number specification used when formatting a number
     *
     * @param PriceSpecificationMap $priceSpecifications
     *  Collection of Price specifications (one per installed currency)
     *
     * @param NumberFormatter $formatter
     *  This number formatter will use stored number / price specs
     */
    public function __construct(
        NumberSpecification $numberSpecification,
        PriceSpecificationMap $priceSpecifications,
        NumberFormatter $formatter
    ) {
        $this->numberSpecification = $numberSpecification;
        $this->priceSpecifications = $priceSpecifications;
        $this->numberFormatter     = $formatter;
    }

    /**
     * Format a number according to locale rules
     *
     * @param int|float|string $number
     *  The number to be formatted
     *
     * @return string
     *  The formatted number
     *
     * @throws Exception\LocalizationException
     */
    public function formatNumber($number)
    {
        return $this->numberFormatter->format(
            $number,
            $this->numberSpecification
        );
    }

    /**
     * Format a number as a price
     *
     * @param int|float|string $number
     *  Number to be formatted as a price
     *
     * @param string $currencyCode
     *  Currency of the price
     *
     * @return string
     *  The formatted price
     *
     * @throws Exception\LocalizationException
     */
    public function formatPrice($number, $currencyCode)
    {
        $currencyCode = (string)$currencyCode;
        $priceSpec    = $this->priceSpecifications->get($currencyCode);
        if (null === $priceSpec) {
            throw new LocalizationException(
                'Price specification not found for currency "' . $currencyCode . '"'
            );
        }

        return $this->numberFormatter->format(
            $number,
            $priceSpec
        );
    }
}
