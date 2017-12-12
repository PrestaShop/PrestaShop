<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Localization\Specification;

use PrestaShopBundle\Localization\Exception\LocalizationException;

/**
 * Number specification class
 *
 * Regroups rules and data used when formatting a decimal number in a given locale and a given numbering system
 * (latin, arab, ...).
 */
class Number
{
    public function __construct(
        $positivePattern = null,
        $negativePattern = null,
        $symbols = null,
        $maxFractionDigits = null,
        $minFractionDigits = null,
        $groupingUsed = null,
        $primaryGroupSize = null,
        $secondaryGroupSize = null
    ) {
        $this->positivePattern    = $positivePattern;
        $this->negativePattern    = $negativePattern;
        $this->symbols            = $symbols;
        $this->maxFractionDigits  = $maxFractionDigits;
        $this->minFractionDigits  = $minFractionDigits;
        $this->groupingUsed       = $groupingUsed;
        $this->primaryGroupSize   = $primaryGroupSize;
        $this->secondaryGroupSize = $secondaryGroupSize;
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
     * Each list is indexed by numbering system
     *
     * @var NumberSymbolList[]
     */
    protected $symbols;

    /**
     * Maximum number of digits after decimal separator (rounding if needed)
     *
     * @var int
     */
    protected $maxFractionDigits;

    /**
     * Minimum number of digits after decimal separator (fill with "0" if needed)
     *
     * @var int
     */
    protected $minFractionDigits;

    /**
     * Is digits grouping used ?
     * eg: if yes -> "9 999 999". If no => "9999999"
     *
     * @var bool
     */
    protected $groupingUsed;

    /**
     * Size of primary digits group in the number
     * eg: 999 is the primary group in this number : 1 234 999.567
     *
     * @var int
     */
    protected $primaryGroupSize;

    /**
     * Size of secondary digits groups in the number
     * eg: 999 is a secondary group in this number : 123 999 456.789
     * eg: another secondary group (still 999) : 999 123 456.789
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
     * Get the specified symbols list for a given numbering system
     *
     * @param $numberingSystem
     *
     * @return NumberSymbolList
     * @throws LocalizationException
     */
    public function getSymbolsByNumberingSystem($numberingSystem = null)
    {
        if (!isset($this->symbols[$numberingSystem])) {
            throw new LocalizationException('Unknown or invalid numbering system');
        }

        return $this->symbols[$numberingSystem];
    }

    /**
     * Get the formatting rules for this number (when positive)
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
     * Get the formatting rules for this number (when negative)
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
     * Get the maximum number of digits after decimal separator (rounding if needed)
     *
     * @return int
     */
    public function getMaxFractionDigits()
    {
        return $this->maxFractionDigits;
    }

    /**
     * Get the minimum number of digits after decimal separator (fill with "0" if needed)
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
     * Get the size of primary digits group in the number
     *
     * @return int
     */
    public function getPrimaryGroupSize()
    {
        return $this->primaryGroupSize;
    }

    /**
     * Get the size of secondary digits groups in the number
     *
     * @return int
     */
    public function getSecondaryGroupSize()
    {
        return $this->secondaryGroupSize;
    }
}
