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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Localization;

use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\Number\Formatter as NumberFormatter;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberCollection;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberInterface;

/**
 * Locale entity.
 *
 * This is the main CLDR entry point. For example, Locale is used to format numbers, prices, percentages.
 * To build a Locale instance, use the Locale repository.
 */
class Locale implements LocaleInterface
{
    /**
     * Latin numbering system is the "occidental" numbering system. Number digits are 0123456789.
     * This is the default numbering system in PrestaShop, even for arabian or asian languages, until we
     * provide a way to configure this in admin.
     */
    public const NUMBERING_SYSTEM_LATIN = 'latn';

    /**
     * The locale code (simplified IETF tag syntax)
     * Combination of ISO 639-1 (2-letters language code) and ISO 3166-2 (2-letters region code)
     * eg: fr-FR, en-US.
     *
     * @var string
     */
    protected string $code;

    /**
     * Number formatter.
     * Used to format raw numbers in this locale context.
     *
     * @var NumberFormatter
     */
    protected NumberFormatter $numberFormatter;

    /**
     * Number formatting specification.
     */
    protected NumberInterface $numberSpecification;

    /**
     * Price formatting specifications collection (one spec per currency).
     *
     * @var NumberCollection
     */
    protected $priceSpecifications;

    /**
     * Locale constructor.
     *
     * @param string $localeCode
     *                           The locale code (simplified IETF tag syntax)
     *                           Combination of ISO 639-1 (2-letters language code) and ISO 3166-2 (2-letters region code)
     *                           eg: fr-FR, en-US
     * @param NumberInterface $numberSpecification
     *                                             Number specification used when formatting a number
     * @param NumberCollection $priceSpecifications
     *                                              Collection of Price specifications (one per installed currency)
     * @param NumberFormatter $formatter
     *                                   This number formatter will use stored number / price specs
     */
    public function __construct(
        string $localeCode,
        NumberInterface $numberSpecification,
        NumberCollection $priceSpecifications,
        NumberFormatter $formatter
    ) {
        $this->code = $localeCode;
        $this->numberSpecification = $numberSpecification;
        $this->priceSpecifications = $priceSpecifications;
        $this->numberFormatter = $formatter;
    }

    /**
     * Get this locale's code (simplified IETF tag syntax)
     * Combination of ISO 639-1 (2-letters language code) and ISO 3166-2 (2-letters region code)
     * eg: fr-FR, en-US.
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Format a number according to locale rules.
     *
     * @param int|float|string $number
     *                                 The number to be formatted
     *
     * @return string
     *                The formatted number
     *
     * @throws Exception\LocalizationException
     */
    public function formatNumber(int|float|string $number): string
    {
        return $this->numberFormatter->format(
            $number,
            $this->numberSpecification
        );
    }

    /**
     * Format a number as a price.
     *
     * @param int|float|string $number
     *                                 Number to be formatted as a price
     * @param string $currencyCode
     *                             Currency of the price
     *
     * @return string The formatted price
     *
     * @throws Exception\LocalizationException
     */
    public function formatPrice(int|float|string $number, string $currencyCode): string
    {
        return $this->numberFormatter->format(
            $number,
            $this->getPriceSpecification($currencyCode)
        );
    }

    /**
     * Get price specification
     *
     * @param string $currencyCode Currency of the price
     *
     * @return NumberInterface
     */
    public function getPriceSpecification(string $currencyCode): NumberInterface
    {
        $currencyCode = (string) $currencyCode;
        $priceSpec = $this->priceSpecifications->get($currencyCode);
        if (null === $priceSpec) {
            throw new LocalizationException('Price specification not found for currency: "' . $currencyCode . '"');
        }

        return $priceSpec;
    }

    /**
     * Get number specification
     *
     * @return NumberInterface
     */
    public function getNumberSpecification(): NumberInterface
    {
        return $this->numberSpecification;
    }
}
