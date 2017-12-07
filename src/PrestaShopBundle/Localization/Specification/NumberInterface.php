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

/**
 * Number specification class
 *
 * Regroups rules and data used when formatting a decimal number in a given locale and a given numbering system
 * (latin, arab, ...).
 */
interface NumberInterface
{
    /**
     * Add a new symbols list in the specification (one list by numbering system)
     *
     * @param string                    $numberingSystem
     *  The numbering system
     *
     * @param NumberSymbolListInterface $symbolList
     *  The symbols list to use when formatting in this numbering system
     */
    public function addSymbols($numberingSystem, NumberSymbolListInterface $symbolList);

    /**
     * Fill missing symbols with "fallback" data
     *
     * For the given symbols lists, if one or several symbols are missing, they will be filled with fallback symbols
     *
     * @param string                    $numberingSystem
     *  The concerned numbering system
     *
     * @param NumberSymbolListInterface $fallbackSymbolList
     *  The fallback symbols list
     */
    public function hydrateSymbols($numberingSystem, NumberSymbolListInterface $fallbackSymbolList);

    /**
     * Get all specified symbols lists, indexed by available numbering system.
     *
     * Each item of the result is a NumberSymbolList
     *
     * @return NumberSymbolListInterface[]
     */
    public function getAllSymbols();

    /**
     * Get the specified symbols list for a given numbering system
     *
     * @param $numberingSystem
     *
     * @return NumberSymbolListInterface
     */
    public function getSymbolsByNumberingSystem($numberingSystem = null);

    /**
     * Get the formatting rules for this number (when positive)
     *
     * This pattern uses the Unicode CLDR number pattern syntax
     *
     * @return string
     */
    public function getPositivePattern();

    /**
     * Get the formatting rules for this number (when negative)
     *
     * This pattern uses the Unicode CLDR number pattern syntax
     *
     * @return string
     */
    public function getNegativePattern();

    /**
     * Get the maximum number of digits after decimal separator (rounding if needed)
     *
     * @return int
     */
    public function getMaxFractionDigits();

    /**
     * Get the minimum number of digits after decimal separator (fill with "0" if needed)
     *
     * @return int
     */
    public function getMinFractionDigits();

    /**
     * Get the "grouping" flag. This flag defines if digits grouping should be used when formatting this number.
     *
     * @return bool
     */
    public function isGroupingUsed();

    /**
     * Get the size of primary digits group in the number
     *
     * @return int
     */
    public function getPrimaryGroupSize();

    /**
     * Get the size of secondary digits groups in the number
     *
     * @return int
     */
    public function getSecondaryGroupSize();
}
