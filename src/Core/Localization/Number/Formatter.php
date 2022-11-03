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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Localization\Number;

use InvalidArgumentException as SPLInvalidArgumentException;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\Decimal\Operation\Rounding;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberInterface as NumberSpecification;
use PrestaShop\PrestaShop\Core\Localization\Specification\Price as PriceSpecification;

/**
 * Formats a number (raw, price, percentage) according to passed specifications.
 */
class Formatter
{
    /**
     * These placeholders are used in CLDR number formatting templates.
     * They are meant to be replaced by the correct localized symbols in the number formatting process.
     */
    public const CURRENCY_SYMBOL_PLACEHOLDER = '¤';
    public const DECIMAL_SEPARATOR_PLACEHOLDER = '.';
    public const GROUP_SEPARATOR_PLACEHOLDER = ',';
    public const MINUS_SIGN_PLACEHOLDER = '-';
    public const PERCENT_SYMBOL_PLACEHOLDER = '%';
    public const PLUS_SIGN_PLACEHOLDER = '+';

    /**
     * @var string The wanted rounding mode when formatting numbers.
     *             Cf. PrestaShop\Decimal\Operation\Rounding::ROUND_* values
     */
    protected $roundingMode;

    /**
     * @var string Numbering system to use when formatting numbers
     *
     * @see http://cldr.unicode.org/translation/numbering-systems
     */
    protected $numberingSystem;

    /**
     * Number specification to be used when formatting a number.
     *
     * @var NumberSpecification
     */
    protected $numberSpecification;

    /**
     * Create a number formatter instance.
     *
     * @param string $roundingMode The wanted rounding mode when formatting numbers
     *                             Cf. PrestaShop\Decimal\Operation\Rounding::ROUND_* values
     * @param string $numberingSystem Numbering system to use when formatting numbers
     *
     *                             @see http://cldr.unicode.org/translation/numbering-systems
     */
    public function __construct($roundingMode, $numberingSystem)
    {
        $this->roundingMode = $roundingMode;
        $this->numberingSystem = $numberingSystem;
    }

    /**
     * Formats the passed number according to specifications.
     *
     * @param int|float|string $number
     *                                 The number to format
     * @param NumberSpecification $specification
     *                                           Number specification to be used (can be a number spec, a price spec, a percentage spec)
     *
     * @return string
     *                The formatted number
     *                You should use this this value for display, without modifying it
     *
     * @throws LocalizationException
     */
    public function format($number, NumberSpecification $specification)
    {
        $this->numberSpecification = $specification;

        try {
            $decimalNumber = $this->prepareNumber($number);
        } catch (SPLInvalidArgumentException $e) {
            throw new LocalizationException('Invalid $number parameter: ' . $e->getMessage(), 0, $e);
        }

        /*
         * We need to work on the absolute value first.
         * Then the CLDR pattern will add the sign if relevant (at the end).
         */
        $isNegative = $decimalNumber->isNegative();
        $decimalNumber = $decimalNumber->toPositive();

        list($majorDigits, $minorDigits) = $this->extractMajorMinorDigits($decimalNumber);
        $majorDigits = $this->splitMajorGroups($majorDigits);
        $minorDigits = $this->adjustMinorDigitsZeroes($minorDigits);

        // Assemble the final number
        $formattedNumber = $majorDigits;
        if (strlen($minorDigits)) {
            $formattedNumber .= self::DECIMAL_SEPARATOR_PLACEHOLDER . $minorDigits;
        }

        // Get the good CLDR formatting pattern. Sign is important here !
        $pattern = $this->getCldrPattern($isNegative);
        $formattedNumber = $this->addPlaceholders($formattedNumber, $pattern);
        $formattedNumber = $this->localizeNumber($formattedNumber);

        $formattedNumber = $this->performSpecificReplacements($formattedNumber);

        return $formattedNumber;
    }

    /**
     * Prepares a basic number (either a string, an integer or a float) to be formatted.
     *
     * @param string|float|int $number The number to be prepared
     *
     * @return DecimalNumber The prepared number
     */
    protected function prepareNumber($number)
    {
        $decimalNumber = new DecimalNumber((string) $number);
        $precision = $this->numberSpecification->getMaxFractionDigits();

        return (new Rounding())->compute(
            $decimalNumber,
            $precision,
            $this->roundingMode
        );
    }

    /**
     * Get $number's major and minor digits.
     *
     * Major digits are the "integer" part (before decimal separator), minor digits are the fractional part
     * Result will be an array of exactly 2 items: [$majorDigits, $minorDigits]
     *
     * Usage example:
     *  list($majorDigits, $minorDigits) = $this->getMajorMinorDigits($decimalNumber);
     *
     * @param DecimalNumber $number
     *
     * @return string[]
     */
    protected function extractMajorMinorDigits(DecimalNumber $number)
    {
        // Get the number's major and minor digits.
        $majorDigits = $number->getIntegerPart();
        $minorDigits = $number->getFractionalPart();
        $minorDigits = ('0' === $minorDigits) ? '' : $minorDigits;

        return [$majorDigits, $minorDigits];
    }

    /**
     * Splits major digits into groups.
     *
     * e.g.: Given the major digits "1234567", and major group size
     *  configured to 3 digits, the result would be "1 234 567"
     *
     * @param string $majorDigits The major digits to be grouped
     *
     * @return string The grouped major digits
     */
    protected function splitMajorGroups($majorDigits)
    {
        if ($this->numberSpecification->isGroupingUsed()) {
            // Reverse the major digits, since they are grouped from the right.
            $majorDigits = array_reverse(str_split($majorDigits));
            // Group the major digits.
            $groups = $groupsDigits = [];
            $groups[] = array_splice($majorDigits, 0, $this->numberSpecification->getPrimaryGroupSize());
            while (!empty($majorDigits)) {
                $groups[] = array_splice($majorDigits, 0, $this->numberSpecification->getSecondaryGroupSize());
            }
            // Reverse back the digits and the groups
            $groups = array_reverse($groups);
            foreach ($groups as $group) {
                $groupsDigits[] = implode('', array_reverse($group));
            }
            // Reconstruct the major digits.
            $majorDigits = implode(self::GROUP_SEPARATOR_PLACEHOLDER, $groupsDigits);
        }

        return $majorDigits;
    }

    /**
     * Adds or remove trailing zeroes, depending on specified min and max fraction digits numbers.
     *
     * @param string $minorDigits Digits to be adjusted with (trimmed or padded) zeroes
     *
     * @return string The adjusted minor digits
     */
    protected function adjustMinorDigitsZeroes($minorDigits)
    {
        if (strlen($minorDigits) < $this->numberSpecification->getMinFractionDigits()) {
            // Re-add needed zeroes
            $minorDigits = str_pad(
                $minorDigits,
                $this->numberSpecification->getMinFractionDigits(),
                '0'
            );
        }

        if (strlen($minorDigits) > $this->numberSpecification->getMaxFractionDigits()) {
            // Strip any trailing zeroes.
            $minorDigits = rtrim($minorDigits, '0');
        }

        return $minorDigits;
    }

    /**
     * Get the CLDR formatting pattern.
     *
     * @see http://cldr.unicode.org/translation/number-patterns
     *
     * @param bool $isNegative
     *                         If true, the negative pattern will be returned instead of the positive one
     *
     * @return string
     *                The CLDR formatting pattern
     */
    protected function getCldrPattern($isNegative)
    {
        if ((bool) $isNegative) {
            return $this->numberSpecification->getNegativePattern();
        }

        return $this->numberSpecification->getPositivePattern();
    }

    /**
     * Localize the passed number.
     *
     * If needed, occidental ("latn") digits are replaced with the relevant
     * ones (for instance with arab digits).
     * Symbol placeholders will also be replaced by the real symbols (configured
     * in number specification)
     *
     * @param string $number
     *                       The number to be processed
     *
     * @return string
     *                The number after digits and symbols replacement
     */
    protected function localizeNumber($number)
    {
        // If locale uses non-latin digits
        $number = $this->replaceDigits($number);

        // Placeholders become real localized symbols
        $number = $this->replaceSymbols($number);

        return $number;
    }

    /**
     * Replace latin digits with relevant numbering system's digits.
     *
     * @param string $number
     *                       The number to process
     *
     * @return string
     *                The number with replaced digits
     */
    protected function replaceDigits($number)
    {
        // TODO use digits set from the locale (cf. /localization/CLDR/core/common/supplemental/numberingSystems.xml)
        return $number;
    }

    /**
     * Replace placeholder number symbols with relevant numbering system's symbols.
     *
     * @param string $number
     *                       The number to process
     *
     * @return string
     *                The number with replaced symbols
     */
    protected function replaceSymbols($number)
    {
        $symbols = $this->numberSpecification->getSymbolsByNumberingSystem($this->numberingSystem);
        $replacements = [
            self::DECIMAL_SEPARATOR_PLACEHOLDER => $symbols->getDecimal(),
            self::GROUP_SEPARATOR_PLACEHOLDER => $symbols->getGroup(),
            self::MINUS_SIGN_PLACEHOLDER => $symbols->getMinusSign(),
            self::PERCENT_SYMBOL_PLACEHOLDER => $symbols->getPercentSign(),
            self::PLUS_SIGN_PLACEHOLDER => $symbols->getPlusSign(),
        ];

        return strtr($number, $replacements);
    }

    /**
     * Add missing placeholders to the number using the passed CLDR pattern.
     *
     * Missing placeholders can be the percent sign, currency symbol, etc.
     *
     * e.g. with a currency CLDR pattern:
     *  - Passed number (partially formatted): 1,234.567
     *  - Returned number: 1,234.567 ¤
     *  ("¤" symbol is the currency symbol placeholder)
     *
     * @see http://cldr.unicode.org/translation/number-patterns
     *
     * @param string $formattedNumber Number to process
     * @param string $pattern CLDR formatting pattern to use
     *
     * @return string
     */
    protected function addPlaceholders($formattedNumber, $pattern)
    {
        /*
         * Regex groups explanation:
         * #          : literal "#" character. Once.
         * (,#+)*     : any other "#" characters group, separated by ",". Zero to infinity times.
         * 0          : literal "0" character. Once.
         * (\.[0#]+)* : any combination of "0" and "#" characters groups, separated by '.'. Zero to infinity times.
         */
        $formattedNumber = preg_replace('/#?(,#+)*0(\.[0#]+)*/', $formattedNumber, $pattern);

        return $formattedNumber;
    }

    /**
     * Perform some more specific replacements.
     *
     * Specific replacements are needed when number specification is extended.
     * For instance, prices have an extended number specification in order to
     * add currency symbol to the formatted number.
     *
     * @param string $formattedNumber
     *
     * @return mixed
     */
    public function performSpecificReplacements($formattedNumber)
    {
        $formattedNumber = $this->tryCurrencyReplacement($formattedNumber);

        return $formattedNumber;
    }

    /**
     * Try to replace currency placeholder by actual currency.
     *
     * Placeholder will be replaced either by the symbol or the ISO code, depending on price specification
     *
     * @param string $formattedNumber The number to format
     *
     * @return string The number after currency replacement
     */
    protected function tryCurrencyReplacement($formattedNumber)
    {
        if ($this->numberSpecification instanceof PriceSpecification) {
            $currency = PriceSpecification::CURRENCY_DISPLAY_CODE == $this->numberSpecification->getCurrencyDisplay()
                ? $this->numberSpecification->getCurrencyCode()
                : $this->numberSpecification->getCurrencySymbol();

            $formattedNumber = str_replace(self::CURRENCY_SYMBOL_PLACEHOLDER, $currency, $formattedNumber);
        }

        return $formattedNumber;
    }
}
