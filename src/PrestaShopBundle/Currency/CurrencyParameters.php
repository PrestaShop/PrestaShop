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

use PrestaShopBundle\Currency\Exception\InvalidArgumentException;

/**
 * Class CurrencyParameters
 *
 * Parameters bag used for building Currency objects
 *
 * @package PrestaShopBundle\Currency
 */
class CurrencyParameters
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
     * All possible names depending on count
     *
     * @var array
     */
    protected $displayNameData;

    /**
     * Currency's symbol
     *
     * @var Symbol
     */
    protected $symbol;

    /**
     * Currency ISO 4217 number
     *
     * Example : 978 for euro
     *
     * @var int
     */
    protected $numericIsoCode;

    /**
     * Currency id in case it is installed and present in DB
     *
     * @var int
     */
    protected $id;

    /**
     * Get the number of decimal digits for this currency
     *
     * @return int
     */
    public function getDecimalDigits()
    {
        return $this->decimalDigits;
    }

    /**
     * Get currency's ISO code
     *
     * @return string
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * Get currency's display names
     *
     * @return string[]
     */
    public function getDisplayNameData()
    {
        return $this->displayNameData;
    }

    /**
     * Get currency's symbol
     *
     * @return Symbol
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * @return int
     */
    public function getNumericIsoCode()
    {
        return $this->numericIsoCode;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $decimalDigits
     *
     * @return static
     */
    public function setDecimalDigits($decimalDigits)
    {
        $this->decimalDigits = (int)$decimalDigits;

        return $this;
    }

    /**
     * @param string $isoCode
     *
     * @return static
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = (string)$isoCode;

        return $this;
    }

    /**
     * @param array $displayNameData
     *
     * @return static
     */
    public function setDisplayNameData($displayNameData)
    {
        if (!is_array($displayNameData)) {
            throw new InvalidArgumentException('$displayNameData must be an array');
        }

        $this->displayNameData = $displayNameData;

        return $this;
    }

    public function setSymbol($symbol)
    {
        $this->symbol = (string)$symbol;

        return $this;
    }

    /**
     * @param int $numericIsoCode
     *
     * @return static
     */
    public function setNumericIsoCode($numericIsoCode)
    {
        $this->numericIsoCode = (int)$numericIsoCode;

        return $this;
    }

    public function setId($id)
    {
        $this->id = (int)$id;

        return $this;
    }

    public function validateProperties()
    {
        if (is_null($this->getIsoCode())) {
            throw new Exception('Alphabetic ISO code must be set');
        }

        if (is_null($this->getDecimalDigits())) {
            throw new Exception('Decimal digits number must be set (' . $this->getIsoCode() . ')');
        }

        if (is_null($this->getDisplayNameData())) {
            throw new Exception('Display name data must be set (' . $this->getIsoCode() . ')');
        }

        if (is_null($this->getSymbolData())) {
            throw new Exception('Symbols must be set (' . $this->getIsoCode() . ')');
        }

        if (is_null($this->getNumericIsoCode())) {
            throw new Exception('Numeric ISO code must be set (' . $this->getIsoCode() . ')');
        }
    }
}
