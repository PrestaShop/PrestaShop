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

use PrestaShopBundle\Currency\Builder as CurrencyBuilder;
use PrestaShopBundle\Currency\Symbol\Builder as SymbolBuilder;

class Currency
{
    /**
     * Number of digits needed to display the decimal part of the price.
     *
     * @var int
     */
    protected $decimalDigits;

    /**
     * Currency id when installed in shop
     *
     * This id might be empty if currency was built from external data
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
     * All possible names depending on context
     *
     * @var array
     */
    protected $displayNames;

    /**
     * The currency symbol (has multiple available notations depending on context)
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

    public function __construct(CurrencyBuilder $currencyBuilder)
    {
        $this->isoCode        = $currencyBuilder->getIsoCode();
        $this->numericIsoCode = $currencyBuilder->getNumericIsoCode();
        $this->decimalDigits  = $currencyBuilder->getDecimalDigits();
        $this->displayNames   = $currencyBuilder->getDisplayNameData();
        $this->id             = $currencyBuilder->getId();

        $symbolData    = $currencyBuilder->getSymbolData();
        $symbolBuilder = new SymbolBuilder();
        foreach ($symbolData as $type => $symbol) {
            $methodName = 'set' . ucfirst($type);
            $symbolBuilder->$methodName($symbol);
        }
        $this->symbol = $symbolBuilder->build();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    public function getNumericIsoCode()
    {
        return $this->numericIsoCode;
    }

    public function getName($context)
    {
        return $this->displayNames[$context];
    }

    /**
     * @return Symbol
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * @return int
     */
    public function getDecimalDigits()
    {
        return $this->decimalDigits;
    }

    /**
     * @return array
     */
    public function getDisplayNames()
    {
        return $this->displayNames;
    }
}
