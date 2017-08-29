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

use Configuration;
use InvalidArgumentException;
use PrestaShop\Decimal\Number as DecimalNumber;
use PrestaShopBundle\Currency\Currency;
use PrestaShopBundle\Localization\Locale;

class Number
{
    const DECIMAL  = 'decimal';
    const PERCENT  = 'percent';
    const CURRENCY = 'currency';

    const MAXIMUM_FRACTION_DIGITS = 3; // Used for decimal and percent styles only
    const MINIMUM_FRACTION_DIGITS = 0; // Used for decimal and percent styles only

    const CURRENCY_DISPLAY_SYMBOL = 'symbol';
    const CURRENCY_DISPLAY_CODE   = 'code';

    protected $locale;
    protected $groupingUsed;
    protected $primaryGroupSize;
    protected $secondaryGroupSize;
    protected $positiveDecimalPattern;
    protected $negativeDecimalPattern;
    protected $positivePercentPattern;
    protected $negativePercentPattern;
    protected $positiveCurrencyPattern;
    protected $negativeCurrencyPattern;
    protected $minimumFractionDigits;
    protected $maximumFractionDigits;
    protected $currencyDisplay;

    /**
     * Create Number instance (NumberFormatter).
     *
     * @param Locale $locale
     */
    public function __construct(Locale $locale)
    {
        $this->setLocale($locale);
        $this->setMinimumFractionDigits(0)
            ->setMaximumFractionDigits(3)
            ->setCurrencyDisplay(self::CURRENCY_DISPLAY_SYMBOL);
    }

    /**
     * Format a number according to locale rules
     *
     * @param float|string $number The number to format
     * @param string $style The format style (decimal, percent, currency)
     *
     * @return string
     */
    public function format($number, $style = self::DECIMAL)
    {
        if (!is_numeric($number)) {
            $message = sprintf('The provided value "%s" must be a valid number or numeric string.', $number);
            throw new InvalidArgumentException($message);
        }

        $availablePatterns = $this->getAvailablePatterns();
        if (!array_key_exists($style, $availablePatterns)) {
            $message = sprintf('The provided format style "%s" is invalid.', $style);
            throw new InvalidArgumentException($message);
        }

        // Ensure that the value is positive and has the right number of digits.
        $decNumber = new DecimalNumber($number);
        $negative = $decNumber->isNegative();
        $signMultiplier = $negative ? new DecimalNumber('-1') : new DecimalNumber('1');
        $number = $decNumber->dividedBy($signMultiplier, $this->getMaximumFractionDigits())
            ->round($this->getMaximumFractionDigits(), $this->getLocale()->getPrestaShopDecimalRoundMode());
        // Split the number into major and minor digits.
        $numberParts = explode('.', $number);
        $majorDigits = $numberParts[0];
        // Account for maximumFractionDigits = 0, where the number won't
        // have a decimal point, and $numberParts[1] won't be set.
        $minorDigits = isset($numberParts[1]) ? $numberParts[1] : '';

        if ($this->groupingUsed()) {
            // Reverse the major digits, since they are grouped from the right.
            $majorDigits = array_reverse(str_split($majorDigits));
            // Group the major digits.
            $groups = [];
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
        $pattern = $this->getPattern($style, $negative);
        $formattedNumber = preg_replace('/#(?:[\.,]#+)*0(?:[,\.][0#]+)*/', $formattedNumber, $pattern);
        // Localize the number.
        $formattedNumber = $this->replaceDigits($formattedNumber);
        $formattedNumber = $this->replaceSymbols($formattedNumber);


        return $formattedNumber;
    }

    /**
     * Replace latn digits with relevant numbering system's digits
     *
     * @param string $number The number process
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
        $replacements = $this->getNumberSymbols();

        return strtr($number, $replacements);
    }

    /**
     * Format a number as a price (with correct currency symbol and symbol positioning)
     *
     * @param float|string $number The number to be formatted as a price
     * @param Currency $currency The price currency
     *
     * @return mixed
     */
    public function formatCurrency($number, Currency $currency)
    {
        $this->setMinimumFractionDigits(2)
            ->setMaximumFractionDigits(2);

        // Format the numeric part using the currency pattern
        $formattedNumber = $this->format($number, self::CURRENCY);

        $this->setMinimumFractionDigits(0)
            ->setMaximumFractionDigits(3);

        // Determine the symbole to use
        if ($this->getCurrencyDisplay() == self::CURRENCY_DISPLAY_CODE) {
            $symbol = $currency->getIsoCode();
        } else {
            try {
                $symbol = $currency->getSymbol('narrow'); // To be changed when symbol type is configurable
            } catch (InvalidArgumentException $e) {
                $symbol = $currency->getSymbol('default');
            }
        }

        // Replace the currency symbol placeholder
        return str_replace('¤', $symbol, $formattedNumber);
    }

    /**
     * @return array
     */
    protected function getAvailablePatterns()
    {
        return array(
            self::DECIMAL  => $this->getDecimalPattern(),
            self::PERCENT  => $this->getPercentPattern(),
            self::CURRENCY => $this->getCurrencyPattern(),
        );
    }

    protected function getDecimalPattern()
    {
        return $this->getLocale()->getDecimalPattern();
    }

    protected function getNumberSymbols()
    {
        return $this->getLocale()->getNumberSymbols();
    }

    /**
     * Init positive and negative decimal patterns from locale data
     */
    protected function initDecimalPatterns()
    {
        if (!isset($this->positiveDecimalPattern) || !isset($this->negativeDecimalPattern)) {
            $patterns = explode(';', $this->getDecimalPattern());
            if (!isset($patterns[1])) {
                // No explicit negative pattern was provided, construct it according to CLDR documentation.
                $patterns[1] = '-' . $patterns[0];
            }

            $this->positiveDecimalPattern = $patterns[0];
            $this->negativeDecimalPattern = $patterns[1];
        }

        return $this;
    }

    protected function getPositiveDecimalPattern()
    {
        $this->initDecimalPatterns();

        return $this->positiveDecimalPattern;
    }

    protected function getNegativeDecimalPattern()
    {
        $this->initDecimalPatterns();

        return $this->negativeDecimalPattern;
    }

    /**
     * Init positive and negative percent patterns from locale data
     */
    protected function initPercentPatterns()
    {
        if (!isset($this->positivePercentPattern) || !isset($this->negativePercentPattern)) {
            $patterns = explode(';', $this->getPercentPattern());
            if (!isset($patterns[1])) {
                // No explicit negative pattern was provided, construct it according to CLDR documentation.
                $patterns[1] = '-' . $patterns[0];
            }

            $this->positivePercentPattern = $patterns[0];
            $this->negativePercentPattern = $patterns[1];
        }

        return $this;
    }

    protected function getPositivePercentPattern()
    {
        $this->initPercentPatterns();

        return $this->positivePercentPattern;
    }

    protected function getNegativePercentPattern()
    {
        $this->initPercentPatterns();

        return $this->negativePercentPattern;
    }

    /**
     * Init positive and negative decimal patterns from locale data
     */
    protected function initCurrencyPatterns()
    {
        if (!isset($this->positiveCurrencyPattern) || !isset($this->negativeCurrencyPattern)) {
            $patterns = explode(';', $this->getCurrencyPattern());
            if (!isset($patterns[1])) {
                // No explicit negative pattern was provided, construct it according to CLDR documentation.
                $patterns[1] = '-' . $patterns[0];
            }

            $this->positiveCurrencyPattern = $patterns[0];
            $this->negativeCurrencyPattern = $patterns[1];
        }

        return $this;
    }

    protected function getPositiveCurrencyPattern()
    {
        $this->initCurrencyPatterns();

        return $this->positiveCurrencyPattern;
    }

    protected function getNegativeCurrencyPattern()
    {
        $this->initCurrencyPatterns();

        return $this->negativeCurrencyPattern;
    }

    /**
     * Determine if grouping should be used to format numbers
     *
     * @return bool true if grouping is used
     */
    protected function groupingUsed()
    {
        if (!isset($this->groupingUsed)) {
            $this->groupingUsed = (strpos($this->getPositiveDecimalPattern(), ',') !== false);
        }

        return $this->groupingUsed;
    }

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

    protected function getPrimaryGroupSize()
    {
        if (!$this->groupingUsed()) {
            return null;
        }

        $this->initGroupsSizes();

        return $this->primaryGroupSize;
    }

    protected function getSecondaryGroupSize()
    {
        if (!$this->groupingUsed()) {
            return null;
        }

        $this->initGroupsSizes();

        return $this->secondaryGroupSize;
    }

    protected function getPercentPattern()
    {
        return $this->getLocale()->getPercentPattern();
    }

    protected function getCurrencyPattern()
    {
        return $this->getLocale()->getCurrencyPattern();
    }

    /**
     * @return Locale
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param $locale
     *
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMinimumFractionDigits()
    {
        return $this->minimumFractionDigits;
    }

    /**
     * @param $minimumFractionDigits
     *
     * @return $this
     */
    public function setMinimumFractionDigits($minimumFractionDigits)
    {
        $this->minimumFractionDigits = $minimumFractionDigits;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaximumFractionDigits()
    {
        return $this->maximumFractionDigits;
    }

    /**
     * @param $maximumFractionDigits
     *
     * @return $this
     */
    public function setMaximumFractionDigits($maximumFractionDigits)
    {
        $this->maximumFractionDigits = $maximumFractionDigits;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrencyDisplay()
    {
        return $this->currencyDisplay;
    }

    /**
     * @param $currencyDisplay
     *
     * @return $this
     */
    public function setCurrencyDisplay($currencyDisplay)
    {
        $this->currencyDisplay = $currencyDisplay;

        return $this;
    }

    protected function getPattern($style, $negative = false)
    {
        $style = ucfirst($style);
        $sign = $negative ? 'Negative' : 'Positive';
        $method = 'get' . $sign . $style . 'Pattern';

        return $this->$method();
    }
}
