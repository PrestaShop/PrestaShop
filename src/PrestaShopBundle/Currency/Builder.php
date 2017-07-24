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

namespace PrestaShopBundle\Currency;

class Builder
{
    /**
     * Number of digits needed to display the decimal part of the price.
     *
     * @var int
     */
    protected $decimalDigits;

    /**
     * Currency ISO 4217 code
     *
     * Example : EUR for euro
     *
     * @var string
     */
    protected $isoCode;

    /**
     * All possible names depending on locale
     *
     * @var array
     */
    protected $localizedNames;

    /**
     * All possible symbols depending on locale
     *
     * @var array
     */
    protected $localizedSymbols;

    /**
     * Currency ISO 4217 number
     *
     * Example : 978 for euro
     *
     * @var int
     */
    protected $numericIsoCode;

    /**
     * @return int
     */
    public function getDecimalDigits()
    {
        return $this->decimalDigits;
    }

    /**
     * @return string
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * @return array
     */
    public function getLocalizedNames()
    {
        return $this->localizedNames;
    }

    /**
     * @return array
     */
    public function getLocalizedSymbols()
    {
        return $this->localizedSymbols;
    }

    /**
     * @return int
     */
    public function getNumericIsoCode()
    {
        return $this->numericIsoCode;
    }

    /**
     * @param int $decimalDigits
     *
     * @return Builder
     */
    public function setDecimalDigits($decimalDigits)
    {
        $this->decimalDigits = $decimalDigits;

        return $this;
    }

    /**
     * @param string $isoCode
     *
     * @return Builder
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    /**
     * @param array $localizedNames
     *
     * @return Builder
     */
    public function setLocalizedNames($localizedNames)
    {
        $this->localizedNames = $localizedNames;

        return $this;
    }

    /**
     * @param array $localizedSymbols
     *
     * @return Builder
     */
    public function setLocalizedSymbols($localizedSymbols)
    {
        $this->localizedSymbols = $localizedSymbols;

        return $this;
    }

    /**
     * @param int $numericIsoCode
     *
     * @return Builder
     */
    public function setNumericIsoCode($numericIsoCode)
    {
        $this->numericIsoCode = $numericIsoCode;

        return $this;
    }

    public function build()
    {
        $this->validateProperties();

        return new Currency($this);
    }

    protected function validateProperties()
    {
        if (is_null($this->getDecimalDigits())) {
            throw new Exception('Decimal digits number must be set');
        }

        if (is_null($this->getIsoCode())) {
            throw new Exception('Alphabetic ISO code must be set');
        }

        if (is_null($this->getLocalizedNames())) {
            throw new Exception('Localized names must be set');
        }

        if (is_null($this->getLocalizedSymbols())) {
            throw new Exception('Localized symbols must be set');
        }

        if (is_null($this->getNumericIsoCode())) {
            throw new Exception('Numeric ISO code must be set');
        }
    }
}
