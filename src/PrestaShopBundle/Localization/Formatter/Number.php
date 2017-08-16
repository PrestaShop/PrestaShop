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
    protected $currencyDisplay;

    /**
     * Create Number instance (NumberFormatter).
     *
     * @param Locale $locale
     */
    public function __construct(Locale $locale)
    {
        $this->setLocale($locale);
        $this->setCurrencyDisplay(self::CURRENCY_DISPLAY_SYMBOL);
    }

    public function format($number, $style = self::DECIMAL)
    {
        $availablePatterns = $this->getAvailablePatterns();
        if (!array_key_exists($style, $availablePatterns)) {
            throw new InvalidArgumentException('Unknown format style provided.');
        }

        $number = (string)$number;


        return 'TODO';
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
