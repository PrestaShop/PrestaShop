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

namespace PrestaShopBundle\Localization\Number;

use InvalidArgumentException as SPLInvalidArgumentException;
use PrestaShop\Decimal\Number as DecimalNumber;
use PrestaShop\Decimal\Operation\Rounding;
use PrestaShop\PrestaShop\Adapter\RoundingMapper;
use PrestaShopBundle\Localization\InvalidArgumentException;
use PrestaShopBundle\Localization\Locale;
use PrestaShopBundle\Localization\Specification\NumberInterface;

class Formatter
{
    const GROUP_SEPARATOR_PLACEHOLDER   = ',';
    const DECIMAL_SEPARATOR_PLACEHOLDER = ',';

    /**
     * Locale to be used when formatting a number
     *
     * @var Locale
     */
    protected $locale;

    /**
     * Number specification to be used when formatting a number
     *
     * @var NumberInterface
     */
    protected $numberSpecification;

    /**
     * Create a number formatter instance whose behavior depends on the passed locale and number specification
     *
     * @param Locale          $locale
     *   Locale used when formatting a number
     *
     * @param NumberInterface $numberSpecification
     *   Number specification used when formatting a number
     */
    public function __construct(Locale $locale, NumberInterface $numberSpecification)
    {
        $this
            ->setLocale($locale)
            ->setNumberSpecification($numberSpecification);
    }

    /**
     * @param Locale $locale
     *
     * @return Formatter
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @param NumberInterface $numberSpecification
     *
     * @return Formatter
     */
    public function setNumberSpecification($numberSpecification)
    {
        $this->numberSpecification = $numberSpecification;

        return $this;
    }

    public function format($number)
    {
        try {
            $decimalNumber = $this->prepareNumber($number);
        } catch (SPLInvalidArgumentException $e) {
            throw new InvalidArgumentException('Invalid $number parameter : ' . $e->getMessage());
        }

        /*
         * We need to work on the absolute value first.
         * Then the CLDR pattern will add the sign if relevant (at the end).
         */
        $isNegative    = $decimalNumber->isNegative();
        $decimalNumber = $decimalNumber->toPositive();

        list($majorDigits, $minorDigits) = $this->extractMajorMinorDigits($decimalNumber);
        $majorDigits = $this->splitMajorGroups($majorDigits);
        $minorDigits = $this->adjustMinorDigitsZeroes($minorDigits);

        // Assemble the final number
        $formattedNumber = $majorDigits;
        if ($minorDigits) {
            $formattedNumber .= self::DECIMAL_SEPARATOR_PLACEHOLDER . $minorDigits;
        }

        // Get the good CLDR formatting pattern. Sign is important here !
        $pattern         = $this->getCldrPattern($isNegative);
        $formattedNumber = $this->addPlaceholders($formattedNumber, $pattern);
        $formattedNumber = $this->localizeNumber($formattedNumber);

        return $formattedNumber;
    }

    /**
     * Prepares a basic number (either a string, an integer or a float) to be formatted.
     *
     * @param $number
     *  The number to be prepared
     *
     * @return DecimalNumber
     *  The prepared number
     */
    protected function prepareNumber($number)
    {
        $decimalNumber = new DecimalNumber((string)$number);
        $precision     = $this->numberSpecification->getMaxFractionDigits();
        $roundingMode  = RoundingMapper::mapRounding($this->locale->getRoundMode());

        $roundedNumber = (new Rounding())->compute(
            $decimalNumber,
            $precision,
            $roundingMode
        );

        return $roundedNumber;
    }

    /**
     * Get $number's major and minor digits.
     *
     * Major digits are the "integer" part (before decimal separator), minor digits are the fractional part
     * Result will be an array of exactly 2 items : [$majorDigits, $minorDigits]
     *
     * Usage example :
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
     * @param $majorDigits
     *
     * @return string
     */
    protected function splitMajorGroups($majorDigits)
    {
        if ($this->numberSpecification->isGroupingUsed()) {
            // Reverse the major digits, since they are grouped from the right.
            $majorDigits = array_reverse(str_split($majorDigits));
            // Group the major digits.
            $groups   = array();
            $groups[] = array_splice($majorDigits, 0, $this->numberSpecification->getPrimaryGroupSize());
            while (!empty($majorDigits)) {
                $groups[] = array_splice($majorDigits, 0, $this->numberSpecification->getSecondaryGroupSize());
            }
            // Reverse back the digits and the groups
            $groups = array_reverse($groups);
            foreach ($groups as &$group) {
                $group = implode(array_reverse($group));
            }
            // Reconstruct the major digits.
            $majorDigits = implode(self::GROUP_SEPARATOR_PLACEHOLDER, $groups);
        }

        return $majorDigits;
    }

    protected function adjustMinorDigitsZeroes($minorDigits)
    {
        if (strlen($minorDigits) > $this->numberSpecification->getMaxFractionDigits()) {
            // Strip any trailing zeroes.
            $minorDigits = rtrim($minorDigits, '0');
        }

        if (strlen($minorDigits) < $this->numberSpecification->getMinFractionDigits()) {
            // Re-add needed zeroes
            $minorDigits = str_pad(
                $minorDigits,
                $this->numberSpecification->getMinFractionDigits(),
                '0'
            );
        }

        return $minorDigits;
    }

    /**
     * @param bool $isNegative
     *
     * @return string
     */
    protected function getCldrPattern($isNegative)
    {
        if ((bool)$isNegative) {
            return $this->numberSpecification->getNegativePattern();
        }

        return $this->numberSpecification->getPositivePattern();
    }

    /**
     * @param $formattedNumber
     *
     * @return mixed
     */
    protected function localizeNumber($formattedNumber)
    {
        // If locale uses non-latin digits
        $formattedNumber = $this->replaceDigits($formattedNumber);

        // Placeholders become real localized symbols
        $formattedNumber = $this->replaceSymbols($formattedNumber);

        return $formattedNumber;
    }

    /**
     * Replace latin digits with relevant numbering system's digits
     *
     * @param string $number
     *  The number to process
     *
     * @return string
     *  The number with replaced digits
     */
    protected function replaceDigits($number)
    {
        // TODO use digits set from the locale (cf. /localization/CLDR/core/common/supplemental/numberingSystems.xml)
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
        $numberingSystem = $this->locale->getNumberingSystem();
        $symbols         = $this->numberSpecification->getSymbolsByNumberingSystem($numberingSystem);
        $replacements    = [
            '.' => $symbols->getDecimal(),
            ',' => $symbols->getGroup(),
            '+' => $symbols->getPlusSign(),
            '-' => $symbols->getMinusSign(),
            '%' => $symbols->getPercentSign(),
        ];

        return strtr($number, $replacements);
    }

    /**
     * Add missing placeholders to the number using the passed CLDR pattern.
     *
     * Missing placeholders can be the percent sign, currency symbol, etc.
     *
     * e.g. with a currency CLDR pattern :
     *  - Passed number (partially formatted) : 1,234.567
     *  - Returned number : 1,234.567 ¤
     *  ("¤" symbol is the currency symbol placeholder)
     *
     * @param $formattedNumber
     *  Number to process
     *
     * @param $pattern
     *  CLDR formatting pattern to use
     *
     * @return string
     */
    protected function addPlaceholders($formattedNumber, $pattern)
    {
        /*
         * Regex groups explanation :
         * #          : literal "#" character. Once.
         * (,#+)*     : any other "#" characters group, separated by ",". Zero to infinity times.
         * 0          : literal "0" character. Once.
         * (\.[0#]+)* : any combination of "0" and "#" characters groups, separated by '.'. Zero to infinity times.
         */
        $formattedNumber = preg_replace('/#(,#+)*0(\.[0#]+)*/', $formattedNumber, $pattern);

        return $formattedNumber;
    }
}
