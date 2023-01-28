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

namespace PrestaShop\PrestaShop\Core\Localization\Specification;

use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\Specification\Number as NumberSpecification;

/**
 * Price number specification class
 * Regroups specific rules and data used when formatting a price in a given locale and a given numbering system
 * (latin, arab, ...).
 */
class Price extends NumberSpecification
{
    /**
     * Currency display option: symbol notation.
     */
    public const CURRENCY_DISPLAY_SYMBOL = 'symbol';

    /**
     * Currency display option: ISO code notation.
     */
    public const CURRENCY_DISPLAY_CODE = 'code';

    /**
     * Price value placeholder without symbols
     */
    public const PATTERN_BASE_PLACEHOLDER = '#,##0.00';

    /**
     * Type of display for currency symbol
     * cf. self::CURRENCY_DISPLAY_SYMBOL and self::CURRENCY_DISPLAY_CODE constants.
     *
     * @var string
     */
    protected $currencyDisplay;

    /**
     * @var string The currency symbol
     *             eg : €
     */
    protected $currencySymbol;

    /**
     * @var string The currency code
     *             eg : EUR
     */
    protected $currencyCode;

    /**
     * Price specification constructor.
     *
     * @param string $positivePattern CLDR formatting pattern for positive amounts
     * @param string $negativePattern CLDR formatting pattern for negative amounts
     * @param NumberSymbolList[] $symbols List of available number symbols lists (NumberSymbolList objects)
     *                                    Each list is indexed by numbering system
     * @param int $maxFractionDigits Maximum number of digits after decimal separator
     * @param int $minFractionDigits Minimum number of digits after decimal separator
     * @param bool $groupingUsed Is digits grouping used ?
     * @param int $primaryGroupSize Size of primary digits group in the number
     * @param int $secondaryGroupSize Size of secondary digits group in the number
     * @param string $currencyDisplay Type of display for currency symbol
     * @param string $currencySymbol Currency symbol of this price (eg. : €)
     * @param string $currencyCode Currency code of this price (e.g.: EUR)
     *
     * @throws LocalizationException
     */
    public function __construct(
        $positivePattern,
        $negativePattern,
        $symbols,
        $maxFractionDigits,
        $minFractionDigits,
        $groupingUsed,
        $primaryGroupSize,
        $secondaryGroupSize,
        $currencyDisplay,
        $currencySymbol,
        $currencyCode
    ) {
        $this->currencyDisplay = $currencyDisplay;
        $this->currencySymbol = $currencySymbol;
        $this->currencyCode = $currencyCode;

        parent::__construct(
            $positivePattern,
            $negativePattern,
            $symbols,
            $maxFractionDigits,
            $minFractionDigits,
            $groupingUsed,
            $primaryGroupSize,
            $secondaryGroupSize
        );
    }

    /**
     * Get type of display for currency symbol.
     *
     * @return string
     */
    public function getCurrencyDisplay()
    {
        return $this->currencyDisplay;
    }

    /**
     * Get the currency symbol
     * e.g.: €.
     *
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->currencySymbol;
    }

    /**
     * Get the currency ISO code
     * e.g.: EUR.
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * Data (attributes) validation.
     *
     * @throws LocalizationException
     */
    protected function validateData()
    {
        parent::validateData();

        if (!in_array($this->currencyDisplay, [self::CURRENCY_DISPLAY_CODE, self::CURRENCY_DISPLAY_SYMBOL])) {
            throw new LocalizationException('Invalid currencyDisplay');
        }
    }

    /**
     * To array function
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_merge(
            [
                'currencyCode' => $this->getCurrencyCode(),
                'currencySymbol' => $this->getCurrencySymbol(),
            ],
            parent::toArray()
        );
    }
}
