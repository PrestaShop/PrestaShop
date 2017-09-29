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

/**
 * Class CurrencyFactory
 *
 * Builds Currency object from a given parameters set.
 *
 * @package PrestaShopBundle\Currency
 */
class CurrencyFactory
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
    protected $displayName;

    /**
     * All possible symbol data depending on context
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
     * @param int $decimalDigits
     *
     * @return CurrencyFactory
     */
    public function setDecimalDigits($decimalDigits)
    {
        $this->decimalDigits = $decimalDigits;

        return $this;
    }

    /**
     * @param string $isoCode
     *
     * @return CurrencyFactory
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    /**
     * @param array $displayName
     *
     * @return CurrencyFactory
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * @param array $symbols
     *
     * @return CurrencyFactory
     */
    public function setSymbolData($symbols)
    {
        $this->symbols = $symbols;

        return $this;
    }

    /**
     * @param int $numericIsoCode
     *
     * @return CurrencyFactory
     */
    public function setNumericIsoCode($numericIsoCode)
    {
        $this->numericIsoCode = $numericIsoCode;

        return $this;
    }

    public function setId($id)
    {
        $this->id = (int) $id;

        return $this;
    }

    /**
     * @return Currency
     */
    public function build()
    {
        $currencyParameters = new CurrencyParameters();
        $currencyParameters->setIsoCode($this->isoCode)
                           ->setNumericIsoCode($this->numericIsoCode)
                           ->setDecimalDigits($this->decimalDigits)
                           ->setDisplayName($this->displayName)
                           ->setSymbolData($this->symbolData);

        $symbolBuilder = new Symbol\Builder();
        foreach ($this->symbolData as $type => $symbol) {
            $methodName = 'set' . ucfirst($type);
            $symbolBuilder->$methodName($symbol);
        }
        $currencyParameters->setSymbol($symbolBuilder->build());

        return new Currency($currencyParameters);
    }
}
