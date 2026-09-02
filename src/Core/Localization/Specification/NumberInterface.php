<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
