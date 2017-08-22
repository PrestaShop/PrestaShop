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
    protected $positiveNumberPattern;
    protected $negativeNumberPattern;
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
        $number = $decNumber->dividedBy($signMultiplier, 12);
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

        if ($this->getMinimumFractionDigits() < $this->getMaximumFractionDigits()) {
            // Strip any trailing zeroes.
            $minorDigits = rtrim($minorDigits, '0');
            // Re-add needed zeroes
            $minorDigits = str_pad($minorDigits, $this->getMinimumFractionDigits(), '0');
        }

        // Assemble the final number and insert it into the pattern.
        $number = $majorDigits;
        if ($minorDigits) {
            $number .= '.' . $minorDigits;
        }
        $pattern = $negative ? $this->getNegativeNumberPattern() : $this->getPositiveNumberPattern();
        $number = preg_replace('/#(?:[\.,]#+)*0(?:[,\.][0#]+)*/', $number, $pattern);
        // Localize the number.
        $number = $this->replaceDigits($number);
        $number = $this->replaceSymbols($number);


        return $number;
    }

    protected function replaceDigits($number)
    {
        // TODO use digits set for the locale (cf. /localization/CLDR/core/common/supplemental/numberingSystems.xml)
        return $number;
    }

    protected function replaceSymbols($number)
    {
        $replacements = $this->getNumberSymbols();

        return strtr($number, $replacements);
    }

    public function formatCurrency($number, Currency $currency)
    {
        return 'TODO';
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
        if (!isset($this->positiveNumberPattern) || !isset($this->negativeNumberPattern)) {
            $patterns = explode(';', $this->getDecimalPattern());
            if (!isset($patterns[1])) {
                // No explicit negative pattern was provided, construct it according to CLDR documentation.
                $patterns[1] = '-' . $patterns[0];
            }

            $this->positiveNumberPattern = $patterns[0];
            $this->negativeNumberPattern = $patterns[1];
        }

        return $this;
    }

    protected function getPositiveNumberPattern()
    {
        $this->initDecimalPatterns();

        return $this->positiveNumberPattern;
    }

    protected function getNegativeNumberPattern()
    {
        $this->initDecimalPatterns();

        return $this->negativeNumberPattern;
    }

    /**
     * Determine if grouping should be used to format numbers
     *
     * @return bool true if grouping is used
     */
    protected function groupingUsed()
    {
        if (!isset($this->groupingUsed)) {
            $this->groupingUsed = (strpos($this->getPositiveNumberPattern(), ',') !== false);
        }

        return $this->groupingUsed;
    }

    protected function initGroupsSizes()
    {
        if (!isset($this->primaryGroupSize) || !isset($this->secondaryGroupSize)) {
            preg_match('/#+0/', $this->getPositiveNumberPattern(), $primaryGroupMatches);
            $this->primaryGroupSize = $this->secondaryGroupSize = strlen($primaryGroupMatches[0]);
            $numberGroups           = explode(',', $this->getPositiveNumberPattern());
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
}
