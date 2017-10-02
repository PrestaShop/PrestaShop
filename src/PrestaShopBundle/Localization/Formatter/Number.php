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

namespace PrestaShopBundle\Localization\Formatter;

use InvalidArgumentException as SplInvalidArgumentException;
use PrestaShop\Decimal\Number as DecimalNumber;
use PrestaShop\PrestaShop\Adapter\RoundingMapper;
use PrestaShopBundle\Currency\Currency;
use PrestaShopBundle\Localization\Exception\InvalidArgumentException;
use PrestaShopBundle\Currency\Exception\InvalidArgumentException as CurrencyInvalidArgumentException;
use PrestaShopBundle\Localization\Locale;

/**
 * Class Number
 *
 * Number formatter.
 * Formats numbers, percentages and prices for a given locale (and currency, when relevant)
 *
 * @package PrestaShopBundle\Localization\Formatter
 */
class Number
{
    /**
     * Decimal number format name
     */
    const DECIMAL = 'decimal';

    /**
     * Percent number format name
     */
    const PERCENT = 'percent';

    /**
     * Decimal number format name
     */
    const CURRENCY = 'currency';

    /**
     * Max number of digits for fraction parts.
     * For decimal and percent formats only (currency digits are handled somewhere else)
     */
    const MAXIMUM_FRACTION_DIGITS = 3;

    /**
     * Min number of digits for fraction parts.
     * For decimal and percent formats only (currency digits are handled somewhere else)
     */
    const MINIMUM_FRACTION_DIGITS = 0;

    /**
     * Currency display option : symbol notation
     * eg: €
     */
    const CURRENCY_DISPLAY_SYMBOL = 'symbol';

    /**
     * Currency display option : ISO code notation
     * eg: EUR
     */
    const CURRENCY_DISPLAY_CODE = 'code';

    /**
     * Locale used for formatting numbers
     *
     * @var Locale
     */
    protected $locale;

    /**
     * Is digits grouping used ?
     * eg: if yes -> "9 999 999". If no => "9999999"
     *
     * @var bool
     */
    protected $groupingUsed;

    /**
     * Size of primary group in the number
     * eg: primary group in this number is 999 : 1 234 999.567
     *
     * @var int
     */
    protected $primaryGroupSize;

    /**
     * Size of secondary group in the number
     * eg: secondary group in this number is 999 : 123 999 456.789
     * eg: another secondary group (still 999 in this example) : 999 123 456.789
     *
     * @var int
     */
    protected $secondaryGroupSize;

    /**
     * CLDR syntax pattern to use when formatting positive decimal numbers
     * eg: #,##0.###
     *
     * @var string
     */
    protected $positiveDecimalPattern;

    /**
     * CLDR syntax pattern to use when formatting negative decimal numbers
     * eg: -#,##0.###
     *
     * @var string
     */
    protected $negativeDecimalPattern;

    /**
     * CLDR syntax pattern to use when formatting positive percentage numbers
     * eg: #,##0 %
     *
     * @var string
     */
    protected $positivePercentPattern;

    /**
     * CLDR syntax pattern to use when formatting negative percentage numbers
     * eg: -#,##0 %
     *
     * @var string
     */
    protected $negativePercentPattern;

    /**
     * CLDR syntax pattern to use when formatting positive prices
     * eg: #,##0.00 ¤
     *
     * @var string
     */
    protected $positiveCurrencyPattern;

    /**
     * CLDR syntax pattern to use when formatting negative prices
     * eg: -#,##0.00 ¤
     *
     * @var string
     */
    protected $negativeCurrencyPattern;

    /**
     * Min number of decimal digits to use when formatting a number.
     * eg: If $minimumFractionDigits is 2, the number 123.4 would be formatted as "123.40"
     *
     * @var int
     */
    protected $minimumFractionDigits;

    /**
     * Max number of decimal digits to use when formatting a number (a rounding is applied when relevant).
     * eg: If $maximumFractionDigits is 3, the number 123.4000 would be formatted as "123.400"
     *
     * @var int
     */
    protected $maximumFractionDigits;

    /**
     * Type of display for currency symbol
     * cf. self::CURRENCY_DISPLAY_SYMBOL and self::CURRENCY_DISPLAY_CODE constants
     *
     * @var string
     */
    protected $currencyDisplay;

    /**
     * Create Number instance (NumberFormatter).
     *
     * @param Locale $locale
     */
    public function __construct(Locale $locale)
    {
        $this->setLocale($locale);
        $this->setMinimumFractionDigits(self::MINIMUM_FRACTION_DIGITS)
            ->setMaximumFractionDigits(self::MAXIMUM_FRACTION_DIGITS)
            ->setCurrencyDisplay(self::CURRENCY_DISPLAY_SYMBOL);
    }

    /**
     * Format a number according to locale rules
     *
     * @param float|string|DecimalNumber $number
     *   The number to format
     *
     * @param string                     $style
     *   The format style (decimal, percent, currency)
     *
     * @return string
     */
    public function format($number, $style = self::DECIMAL)
    {
        $availablePatterns = $this->getAvailablePatterns();
        if (!array_key_exists($style, $availablePatterns)) {
            $message = sprintf('The provided format style "%s" is invalid.', $style);
            throw new InvalidArgumentException($message);
        }

        // Ensure that the value is positive and has the right number of digits.
        try {
            $decNumber = new DecimalNumber((string)$number);
        } catch (SplInvalidArgumentException $e) {
            throw new InvalidArgumentException($e->getMessage());
        }

        $isNegative = $decNumber->isNegative();
        $number     = $decNumber->round(
            $this->getMaximumFractionDigits(),
            RoundingMapper::mapRounding($this->getLocale()->getRoundMode())
        );
        $decNumber  = new DecimalNumber($number);
        $decNumber  = $decNumber->toPositive();
        // Get the number's major and minor digits.
        $majorDigits = $decNumber->getIntegerPart();
        $minorDigits = $decNumber->getFractionalPart();
        $minorDigits = ('0' === $minorDigits) ? '' : $minorDigits;

        if ($this->isGroupingUsed()) {
            // Reverse the major digits, since they are grouped from the right.
            $majorDigits = array_reverse(str_split($majorDigits));
            // Group the major digits.
            $groups   = array();
            $groups[] = array_splice($majorDigits, 0, $this->getPrimaryGroupSize());
            while (!empty($majorDigits)) {
                $groups[] = array_splice($majorDigits, 0, $this->getSecondaryGroupSize());
            }
            // Reverse the groups and the digits inside of them.
            $groups = array_reverse($groups);
            foreach ($groups as &$group) {
                $group = implode(array_reverse($group));
            }
            // Reconstruct the major digits.
            $majorDigits = implode(',', $groups);
        }

        if (strlen($minorDigits) > $this->getMaximumFractionDigits()) {
            // Strip any trailing zeroes.
            $minorDigits = rtrim($minorDigits, '0');
        }

        if (strlen($minorDigits) < $this->getMinimumFractionDigits()) {
            // Re-add needed zeroes
            $minorDigits = str_pad($minorDigits, $this->getMinimumFractionDigits(), '0');
        }

        // Assemble the final number and insert it into the pattern.
        $formattedNumber = $majorDigits;
        if ($minorDigits) {
            $formattedNumber .= '.' . $minorDigits;
        }
        $pattern = $this->getPattern($style, $isNegative);

        /*
         * Use $pattern to add any new character (like "-", "%" or "¤") around our raw number.
         * Regex groups explanation :
         * #          : literal "#" character. Once.
         * (,#+)*     : any other "#" characters group, separated by ",". Zero to infinity times.
         * 0          : literal "0" character. Once.
         * (\.[0#]+)* : any combination of "0" and "#" characters groups, separated by '.'. Zero to infinity times.
         */
        $formattedNumber = preg_replace('/#(,#+)*0(\.[0#]+)*/', $formattedNumber, $pattern);

        // Localize the number.
        $formattedNumber = $this->replaceDigits($formattedNumber);
        $formattedNumber = $this->replaceSymbols($formattedNumber);

        return $formattedNumber;
    }

    /**
     * Replace latn digits with relevant numbering system's digits
     *
     * @param string $number The number to process
     *
     * @return string The number with replaced digits
     */
    protected function replaceDigits($number)
    {
        // TODO use digits set for the locale (cf. /localization/CLDR/core/common/supplemental/numberingSystems.xml)
        return $number;
    }

    /**
     * Replace placeholder number symbols with relevant numbering system's symbols
     *
     * @param string $number The number to process
     *
     * @return string The number with replaced symbols
     */
    protected function replaceSymbols($number)
    {
        $locale = $this->getLocale();
        $replacements = $locale->getSpecification()->getNumberSymbols($locale->getNumberingSystem());

        return strtr($number, $replacements);
    }

    /**
     * Format a number as a price (with correct currency symbol and symbol positioning)
     *
     * @param float|string $number   The number to be formatted as a price
     * @param Currency     $currency The price currency
     *
     * @return string
     */
    public function formatCurrency($number, Currency $currency)
    {
        $this->setMinimumFractionDigits($currency->getDecimalDigits())
            ->setMaximumFractionDigits($currency->getDecimalDigits());

        // Format the numeric part using the currency pattern
        $formattedNumber = $this->format($number, self::CURRENCY);

        $this->setMinimumFractionDigits(self::MINIMUM_FRACTION_DIGITS)
            ->setMaximumFractionDigits(self::MAXIMUM_FRACTION_DIGITS);

        // Determine the symbol to use
        if (self::CURRENCY_DISPLAY_CODE === $this->getCurrencyDisplay()) {
            $symbol = $currency->getIsoCode();
        } else {
            try {
                // TODO : change this when symbol type is configurable
                $symbol = $currency->getSymbol()->getNarrow();
            } catch (CurrencyInvalidArgumentException $e) {
                $symbol = $currency->getSymbol()->getDefault();
            }
        }

        // Replace the currency symbol placeholder
        return str_replace('¤', $symbol, $formattedNumber);
    }

    /**
     * Get all available patterns to format a number (decimal, percent, currency).
     * Each patterns may contain both positive and negative version, separated by ";".
     * If there is no negative version, default is positive version preceded by "-" character.
     *
     * eg : [
     *     'decimal'  => '#,##0.###;-#,##0.###',
     *     'percent'  => '#,##0.### %;-#,##0.### %',
     *     'currency' => '#,##0.00 ¤;-#,##0.00 ¤',
     * ]
     *
     * @return string[]
     */
    protected function getAvailablePatterns()
    {
        return array(
            self::DECIMAL  => $this->getLocale()->getDecimalPattern(),
            self::PERCENT  => $this->getLocale()->getPercentPattern(),
            self::CURRENCY => $this->getLocale()->getCurrencyPattern(),
        );
    }

    /**
     * Init positive and negative decimal patterns from locale data
     *
     * @return $this
     */
    protected function initDecimalPatterns()
    {
        if (!isset($this->positiveDecimalPattern) || !isset($this->negativeDecimalPattern)) {
            $patterns = explode(';', $this->getLocale()->getDecimalPattern());
            if (!isset($patterns[1])) {
                // No explicit negative pattern was provided, construct it according to CLDR documentation.
                $patterns[1] = '-' . $patterns[0];
            }

            $this->positiveDecimalPattern = $patterns[0];
            $this->negativeDecimalPattern = $patterns[1];
        }

        return $this;
    }

    /**
     * Get the pattern to use when formatting positive decimal numbers
     * eg: #,##0.###
     *
     * @return string
     */
    protected function getPositiveDecimalPattern()
    {
        $this->initDecimalPatterns();

        return $this->positiveDecimalPattern;
    }

    /**
     * Get the pattern to use when formatting negative decimal numbers
     * eg: -#,##0.###
     *
     * @return string
     */
    protected function getNegativeDecimalPattern()
    {
        $this->initDecimalPatterns();

        return $this->negativeDecimalPattern;
    }

    /**
     * Init positive and negative percent patterns from locale data
     *
     * @return $this
     */
    protected function initPercentPatterns()
    {
        if (!isset(
            $this->positivePercentPattern,
            $this->negativePercentPattern
        )) {
            $patterns = explode(';', $this->getLocale()->getPercentPattern());
            if (!isset($patterns[1])) {
                // No explicit negative pattern was provided, construct it according to CLDR documentation.
                $patterns[1] = '-' . $patterns[0];
            }

            $this->positivePercentPattern = $patterns[0];
            $this->negativePercentPattern = $patterns[1];
        }

        return $this;
    }

    /**
     * Get the pattern to use when formatting positive percentage numbers
     * eg: #,##0.### %
     *
     * @return string
     */
    protected function getPositivePercentPattern()
    {
        $this->initPercentPatterns();

        return $this->positivePercentPattern;
    }

    /**
     * Get the pattern to use when formatting negative percentage numbers
     * eg: -#,##0.### %
     *
     * @return string
     */
    protected function getNegativePercentPattern()
    {
        $this->initPercentPatterns();

        return $this->negativePercentPattern;
    }

    /**
     * Init positive and negative currency patterns from locale data
     *
     * @return $this
     */
    protected function initCurrencyPatterns()
    {
        if (!isset(
            $this->positiveCurrencyPattern,
            $this->negativeCurrencyPattern
        )) {
            $patterns = explode(';', $this->getLocale()->getCurrencyPattern());
            if (!isset($patterns[1])) {
                // No explicit negative pattern was provided, construct it according to CLDR documentation.
                $patterns[1] = '-' . $patterns[0];
            }

            $this->positiveCurrencyPattern = $patterns[0];
            $this->negativeCurrencyPattern = $patterns[1];
        }

        return $this;
    }

    /**
     * Get pattern used to format a positive currency (price) number.
     * eg : #,##0.00 ¤
     *
     * @return string
     */
    protected function getPositiveCurrencyPattern()
    {
        $this->initCurrencyPatterns();

        return $this->positiveCurrencyPattern;
    }

    /**
     * Get pattern used to format a negative currency (price) number.
     * eg : -#,##0.00 ¤
     *
     * @return string
     */
    protected function getNegativeCurrencyPattern()
    {
        $this->initCurrencyPatterns();

        return $this->negativeCurrencyPattern;
    }

    /**
     * Check if digits grouping should be used to format numbers
     *
     * @return bool true if grouping is used
     */
    protected function isGroupingUsed()
    {
        if (!isset($this->groupingUsed)) {
            $this->groupingUsed = (strpos($this->getPositiveDecimalPattern(), ',') !== false);
        }

        return $this->groupingUsed;
    }

    /**
     * Initializes primary and secondary digits group sizes.
     * If they were already initialized, nothing will happen.
     *
     * @return $this
     */
    protected function initGroupsSizes()
    {
        if (!isset($this->primaryGroupSize) || !isset($this->secondaryGroupSize)) {
            preg_match('/#+0/', $this->getPositiveDecimalPattern(), $primaryGroupMatches);
            $this->primaryGroupSize = $this->secondaryGroupSize = strlen($primaryGroupMatches[0]);
            $numberGroups           = explode(',', $this->getPositiveDecimalPattern());
            if (count($numberGroups) > 2) {
                // This pattern has a distinct secondary group size.
                $this->secondaryGroupSize = strlen($numberGroups[1]);
            }
        }

        return $this;
    }

    /**
     * Get primary digits group size.
     *
     * @return int|null
     */
    protected function getPrimaryGroupSize()
    {
        if (!$this->isGroupingUsed()) {
            return null;
        }

        $this->initGroupsSizes();

        return $this->primaryGroupSize;
    }

    /**
     * Get secondary digits group size.
     *
     * @return int|null
     */
    protected function getSecondaryGroupSize()
    {
        if (!$this->isGroupingUsed()) {
            return null;
        }

        $this->initGroupsSizes();

        return $this->secondaryGroupSize;
    }

    /**
     * Get locale used to format numbers.
     *
     * @return Locale
     */
    protected function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set locale used to format numbers.
     *
     * @param $locale
     *
     * @return $this
     */
    protected function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get the min number of decimal digits to use when formatting a number.
     *
     * @return int
     */
    protected function getMinimumFractionDigits()
    {
        return $this->minimumFractionDigits;
    }

    /**
     * Set the min number of decimal digits to use when formatting a number.
     *
     * @param $minimumFractionDigits
     *
     * @return $this
     */
    protected function setMinimumFractionDigits($minimumFractionDigits)
    {
        $this->minimumFractionDigits = $minimumFractionDigits;

        return $this;
    }

    /**
     * Get the max number of decimal digits to use when formatting a number.
     *
     * @return int
     */
    protected function getMaximumFractionDigits()
    {
        return $this->maximumFractionDigits;
    }

    /**
     * Set the max number of decimal digits to use when formatting a number.
     *
     * @param $maximumFractionDigits
     *
     * @return $this
     */
    protected function setMaximumFractionDigits($maximumFractionDigits)
    {
        $this->maximumFractionDigits = $maximumFractionDigits;

        return $this;
    }

    /**
     * Get the type of display for currency symbol (either symbol or ISO code).
     *
     * @return string
     */
    protected function getCurrencyDisplay()
    {
        return $this->currencyDisplay;
    }

    /**
     * Set the type of display for currency symbol (either symbol or ISO code).
     *
     * @param string $currencyDisplay
     *
     * @return $this
     */
    protected function setCurrencyDisplay($currencyDisplay)
    {
        $this->currencyDisplay = $currencyDisplay;

        return $this;
    }

    /**
     * Get a number formatting pattern.
     *
     * @param string $type
     *   The formatting type (decimal, percent, currency).
     *
     * @param bool   $isNegative
     *   Set to true if you want the negative version of this pattern.
     *
     * @return string
     *   The wanted format.
     *
     * @throws InvalidArgumentException
     *   When passed $type is unknown.
     */
    protected function getPattern($type, $isNegative = false)
    {
        $type   = ucfirst($type);
        $sign   = $isNegative ? 'Negative' : 'Positive';
        $method = 'get' . $sign . $type . 'Pattern';

        if (!method_exists($this, $method)) {
            throw new InvalidArgumentException(strtolower($type) . ' is not a valid format type');
        }

        return $this->$method();
    }
}
