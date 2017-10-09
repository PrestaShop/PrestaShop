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

use PrestaShopBundle\Currency\Exception\Exception;
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
     * Display names data (names depending on context)
     * eg: [
     *     'default' => 'euro',
     *     'one' => 'euro',
     *     'other' => 'euros',
     * ]
     *
     * @var string[]
     */
    protected $displayNameData;

    /**
     * Currency id in case it is installed and present in DB
     *
     * @var int
     */
    protected $id;

    /**
     * Currency ISO 4217 code
     *
     * Example : EUR for euro
     *
     * @var string
     */
    protected $isoCode;

    /**
     * Currency ISO 4217 number
     *
     * Example : 978 for euro
     *
     * @var int
     */
    protected $numericIsoCode;

    /**
     * Currency's symbol
     *
     * @var Symbol
     */
    protected $symbol;

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
     * Get currency's display names
     *
     * @return string[]
     */
    public function getDisplayNameData()
    {
        return $this->displayNameData;
    }

    /**
     * Get currency internal id
     *
     * @return int
     *   The currency id
     */
    public function getId()
    {
        return $this->id;
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
     * Get currency's symbol
     *
     * @return Symbol
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * Get currency's numeric ISO 4217 code.
     *
     * eg :
     * - US Dollar ISO 4217 alphabetic code = USD
     * - US Dollar ISO 4217 numeric code    = 840
     *
     * @return int
     *   The numeric ISO code
     */
    public function getNumericIsoCode()
    {
        return $this->numericIsoCode;
    }

    /**
     * Set the number of digits to display for this currency
     *
     * @param int $decimalDigits
     *   The number of digits to use
     *
     * @return $this
     *   Fluent interface
     */
    public function setDecimalDigits($decimalDigits)
    {
        $this->decimalDigits = (int)$decimalDigits;

        return $this;
    }

    /**
     * Set all needed data to display this currency's name in any situation.
     * 3 types are commonly used :
     * - default : when talking about the currency itself ("the euro currency")
     * - one     : when talking about one unit of this currency ("one euro")
     * - other   : when talking about several units of this currency ("ten euros")
     *
     * @param string[] $displayNameData
     *   The contextualized display names
     *
     * @return $this
     *   Fluent interface
     */
    public function setDisplayNameData($displayNameData)
    {
        if (!is_array($displayNameData)) {
            throw new InvalidArgumentException('$displayNameData must be an array');
        }

        $this->displayNameData = $displayNameData;

        return $this;
    }

    /**
     * Set the currency's internal id
     *
     * @param int $id
     *   The currency's internal id
     *
     * @return $this
     *   Fluent interface
     */
    public function setId($id)
    {
        $this->id = (int)$id;

        return $this;
    }

    /**
     * Set the currency's ISO 4217 alphabetic code
     *
     * @param string $isoCode
     *   The ISO 4217 alphabetic code
     *
     * @return $this
     *   Fluent interface
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = (string)$isoCode;

        return $this;
    }

    /**
     * Set the currency's ISO 4217 numeric code
     *
     * @param int $numericIsoCode
     *   The ISO 4217 numeric code
     *
     * @return $this
     *   Fluent interface
     */
    public function setNumericIsoCode($numericIsoCode)
    {
        $this->numericIsoCode = (int)$numericIsoCode;

        return $this;
    }

    /**
     * Set the currency symbol
     * This object contains multiple symbol notations, depending on context
     * eg: default symbol, narrow symbol, etc
     *
     * @param Symbol $symbol
     *   The symbol object
     *
     * @return $this
     *   Fluent notation
     */
    public function setSymbol(Symbol $symbol)
    {
        $this->symbol = (string)$symbol;

        return $this;
    }

    /**
     * Validate all the Currency parameters.
     * If any of the properties is missing or invalid, an exception will be raised.
     *
     * @throws Exception
     *   When a Currency property is missing or invalid
     */
    public function validateProperties()
    {
        if (is_null($this->getDecimalDigits())) {
            throw new Exception('Decimal digits number must be set (' . $this->getIsoCode() . ')');
        }

        if (is_null($this->getDisplayNameData())) {
            throw new Exception('Display name data must be set (' . $this->getIsoCode() . ')');
        }

        if (is_null($this->getIsoCode())) {
            throw new Exception('Alphabetic ISO code must be set');
        }

        if (is_null($this->getNumericIsoCode())) {
            throw new Exception('Numeric ISO code must be set (' . $this->getIsoCode() . ')');
        }

        if (is_null($this->getSymbol())) {
            throw new Exception('Currency symbol must be set (' . $this->getIsoCode() . ')');
        }
    }
}
