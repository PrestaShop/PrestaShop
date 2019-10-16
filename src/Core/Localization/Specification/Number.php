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

namespace PrestaShop\PrestaShop\Core\Localization\Specification;

use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;

/**
 * Number specification class.
 *
 * Regroups rules and data used when formatting a decimal number in a given locale and a given numbering system
 * (latin, arab, ...).
 */
class Number implements NumberInterface
{
    /**
     * Number specification constructor.
     *
     * @param string $positivePattern
     *                                CLDR formatting pattern for positive amounts
     * @param string $negativePattern
     *                                CLDR formatting pattern for negative amounts
     * @param NumberSymbolList[] $symbols
     *                                    List of available number symbols lists (NumberSymbolList objects)
     *                                    Each list is indexed by numbering system
     * @param int $maxFractionDigits
     *                               Maximum number of digits after decimal separator
     * @param int $minFractionDigits
     *                               Minimum number of digits after decimal separator
     * @param bool $groupingUsed
     *                           Is digits grouping used ?
     * @param int $primaryGroupSize
     *                              Size of primary digits group in the number
     * @param int $secondaryGroupSize
     *                                Size of secondary digits group in the number
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
        $secondaryGroupSize
    ) {
        $this->positivePattern = $positivePattern;
        $this->negativePattern = $negativePattern;
        $this->symbols = $symbols;

        if ($maxFractionDigits < $minFractionDigits) {
            $minFractionDigits = $maxFractionDigits;
        }
        $this->maxFractionDigits = $maxFractionDigits;
        $this->minFractionDigits = $minFractionDigits;

        $this->groupingUsed = $groupingUsed;
        $this->primaryGroupSize = $primaryGroupSize;
        $this->secondaryGroupSize = $secondaryGroupSize;

        $this->validateData();
    }

    /**
     * Positive number pattern.
     *
     * Unicode's CLDR specific syntax. Describes how to format a positive number.
     * eg: #,##0.###     (decimal)
     * eg: #,##0.##0 %   (percentage)
     * eg: #,##0.00 ¤    (price)
     *
     * @var string
     */
    protected $positivePattern;

    /**
     * Negative number pattern.
     *
     * Unicode's CLDR specific syntax. Describes how to format a negative number.
     * eg: -#,##0.###     (decimal)
     * eg: -#,##0.##0 %   (percentage)
     * eg: -#,##0.00 ¤    (price)
     *
     * @var string
     */
    protected $negativePattern;

    /**
     * List of available number symbols lists (NumberSymbolList objects)
     * Each list is indexed by numbering system.
     *
     * @var NumberSymbolList[]
     */
    protected $symbols;

    /**
     * Maximum number of digits after decimal separator (rounding if needed).
     *
     * @var int
     */
    protected $maxFractionDigits;

    /**
     * Minimum number of digits after decimal separator (fill with "0" if needed).
     *
     * @var int
     */
    protected $minFractionDigits;

    /**
     * Is digits grouping used ?
     * eg: if yes -> "9 999 999". If no => "9999999".
     *
     * @var bool
     */
    protected $groupingUsed;

    /**
     * Size of primary digits group in the number
     * e.g.: 999 is the primary group in this number: 1 234 999.567.
     *
     * @var int
     */
    protected $primaryGroupSize;

    /**
     * Size of secondary digits groups in the number
     * eg: 999 is a secondary group in this number: 123 999 456.789
     * eg: another secondary group (still 999): 999 123 456.789.
     *
     * @var int
     */
    protected $secondaryGroupSize;

    /**
     * Get all specified symbols lists, indexed by available numbering system.
     *
     * Each item of the result is a NumberSymbolList
     *
     * @return NumberSymbolList[]
     */
    public function getAllSymbols()
    {
        return $this->symbols;
    }

    /**
     * Get the specified symbols list for a given numbering system.
     *
     * @param string $numberingSystem
     *                                Numbering system to use when formatting numbers. @see http://cldr.unicode.org/translation/numbering-systems
     *
     * @return NumberSymbolList
     *
     * @throws LocalizationException
     */
    public function getSymbolsByNumberingSystem($numberingSystem = NumberInterface::NUMBERING_SYSTEM_LATIN)
    {
        if (!isset($this->symbols[$numberingSystem])) {
            throw new LocalizationException('Unknown or invalid numbering system');
        }

        return $this->symbols[$numberingSystem];
    }

    /**
     * Get the formatting rules for this number (when positive).
     *
     * This pattern uses the Unicode CLDR number pattern syntax
     *
     * @return string
     */
    public function getPositivePattern()
    {
        return $this->positivePattern;
    }

    /**
     * Get the formatting rules for this number (when negative).
     *
     * This pattern uses the Unicode CLDR number pattern syntax
     *
     * @return string
     */
    public function getNegativePattern()
    {
        return $this->negativePattern;
    }

    /**
     * Get the maximum number of digits after decimal separator (rounding if needed).
     *
     * @return int
     */
    public function getMaxFractionDigits()
    {
        return $this->maxFractionDigits;
    }

    /**
     * Get the minimum number of digits after decimal separator (fill with "0" if needed).
     *
     * @return int
     */
    public function getMinFractionDigits()
    {
        return $this->minFractionDigits;
    }

    /**
     * Get the "grouping" flag. This flag defines if digits grouping should be used when formatting this number.
     *
     * @return bool
     */
    public function isGroupingUsed()
    {
        return $this->groupingUsed;
    }

    /**
     * Get the size of primary digits group in the number.
     *
     * @return int
     */
    public function getPrimaryGroupSize()
    {
        return $this->primaryGroupSize;
    }

    /**
     * Get the size of secondary digits groups in the number.
     *
     * @return int
     */
    public function getSecondaryGroupSize()
    {
        return $this->secondaryGroupSize;
    }

    /**
     * Data (attributes) validation.
     *
     * @throws LocalizationException
     */
    protected function validateData()
    {
        if (!isset($this->positivePattern)
            || !is_string($this->positivePattern)
        ) {
            throw new LocalizationException('Invalid positivePattern');
        }

        if (!isset($this->negativePattern)
            || !is_string($this->negativePattern)
        ) {
            throw new LocalizationException('Invalid negativePattern');
        }

        if (!isset($this->symbols)
            || !(is_array($this->symbols))
        ) {
            throw new LocalizationException('Invalid symbols');
        }

        foreach ($this->symbols as $symbolList) {
            if (!$symbolList instanceof NumberSymbolList) {
                throw new LocalizationException('Symbol lists must be instances of NumberSymbolList');
            }
        }

        if (!isset($this->maxFractionDigits)
            || !is_int($this->maxFractionDigits)
        ) {
            throw new LocalizationException('Invalid maxFractionDigits');
        }

        if (!isset($this->minFractionDigits)
            || !is_int($this->minFractionDigits)
        ) {
            throw new LocalizationException('Invalid minFractionDigits');
        }

        if (!isset($this->groupingUsed)
            || !is_bool($this->groupingUsed)
        ) {
            throw new LocalizationException('Invalid groupingUsed');
        }

        if (!isset($this->primaryGroupSize)
            || !is_int($this->primaryGroupSize)
        ) {
            throw new LocalizationException('Invalid primaryGroupSize');
        }

        if (!isset($this->secondaryGroupSize)
            || !is_int($this->secondaryGroupSize)
        ) {
            throw new LocalizationException('Invalid secondaryGroupSize');
        }
    }

    /**
     * To array function
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'numberSymbols' => $this->getSymbolsByNumberingSystem()->toArray(),
            'positivePattern' => $this->getPositivePattern(),
            'negativePattern' => $this->getNegativePattern(),
            'maxFractionDigits' => $this->getMaxFractionDigits(),
            'minFractionDigits' => $this->getMinFractionDigits(),
            'groupingUsed' => $this->isGroupingUsed(),
            'primaryGroupSize' => $this->getPrimaryGroupSize(),
            'secondaryGroupSize' => $this->getSecondaryGroupSize(),
        ];
    }
}
