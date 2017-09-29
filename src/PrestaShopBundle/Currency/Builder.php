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
     * All possible names depending on count
     *
     * @var array
     */
    protected $displayNameData;

    /**
     * All possible symbol notations depending on context
     *
     * @var array
     */
    protected $symbolData;

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
    public function getDisplayNameData()
    {
        return $this->displayNameData;
    }

    /**
     * @return array
     */
    public function getSymbolData()
    {
        return $this->symbolData;
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
     * @param array $displayNameData
     *
     * @return Builder
     */
    public function setDisplayNameData($displayNameData)
    {
        $this->displayNameData = $displayNameData;

        return $this;
    }

    /**
     * @param array $symbolData
     *
     * @return Builder
     */
    public function setSymbolData($symbolData)
    {
        $this->symbolData = $symbolData;

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

    public function setId($id)
    {
        $this->id = (int)$id;

        return $this;
    }

    /**
     * @return Currency
     */
    public function build()
    {
        $this->validateProperties();

        return new Currency($this);
    }

    protected function validateProperties()
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
            throw new Exception('Symbol data must be set (' . $this->getIsoCode() . ')');
        }

        if (is_null($this->getNumericIsoCode())) {
            throw new Exception('Numeric ISO code must be set (' . $this->getIsoCode() . ')');
        }
    }
}
