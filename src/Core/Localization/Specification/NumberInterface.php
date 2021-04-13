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

/**
 * Number specification interface.
 *
 * Regroups rules and data used when formatting a decimal number in a given locale and a given numbering system
 * (latin, arab, ...).
 */
interface NumberInterface
{
    /**
     * Latin numbering system is the "occidental" numbering system. Number digits are 0123456789.
     * This is the default numbering system in PrestaShop, even for arabian or asian languages, until we
     * provide a way to configure this in admin.
     */
    public const NUMBERING_SYSTEM_LATIN = 'latn';

    /**
     * Get all specified symbols lists, indexed by available numbering system.
     *
     * Each item of the result is a NumberSymbolList
     *
     * @return NumberSymbolList[]
     */
    public function getAllSymbols();

    /**
     * Get the specified symbols list for a given numbering system.
     *
     * @param string $numberingSystem
     *                                Numbering system to use when formatting numbers. @see http://cldr.unicode.org/translation/numbering-systems
     *
     * @return NumberSymbolList
     */
    public function getSymbolsByNumberingSystem($numberingSystem = NumberInterface::NUMBERING_SYSTEM_LATIN);

    /**
     * Get the formatting rules for this number (when positive).
     *
     * This pattern uses the Unicode CLDR number pattern syntax
     *
     * @return string
     */
    public function getPositivePattern();

    /**
     * Get the formatting rules for this number (when negative).
     *
     * This pattern uses the Unicode CLDR number pattern syntax
     *
     * @return string
     */
    public function getNegativePattern();

    /**
     * Get the maximum number of digits after decimal separator (rounding if needed).
     *
     * @return int
     */
    public function getMaxFractionDigits();

    /**
     * Get the minimum number of digits after decimal separator (fill with "0" if needed).
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
     * Get the size of primary digits group in the number.
     *
     * @return int
     */
    public function getPrimaryGroupSize();

    /**
     * Get the size of secondary digits groups in the number.
     *
     * @return int
     */
    public function getSecondaryGroupSize();

    /**
     * To array function.
     *
     * @return array
     */
    public function toArray(): array;
}
