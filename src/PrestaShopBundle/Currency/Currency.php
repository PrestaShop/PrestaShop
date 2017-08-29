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

use InvalidArgumentException;

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
     * All possible symbols depending on context
     *
     * @var array
     */
    protected $symbols;

    /**
     * Currency ISO 4217 number
     *
     * Example : 978 for euro
     *
     * @var int
     */
    protected $numericIsoCode;

    public function __construct(Builder $builder)
    {
        $this->setIsoCode($builder->getIsoCode())
            ->setNumericIsoCode($builder->getNumericIsoCode())
            ->setDecimalDigits($builder->getDecimalDigits())
            ->setDisplayNames($builder->getDisplayName())
            ->setSymbols($builder->getSymbols())
            ->setId($builder->getId());
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Currency
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * @param string $isoCode
     *
     * @return Currency
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    public function getNumericIsoCode()
    {
        return $this->numericIsoCode;
    }

    /**
     * @param int $numericIsoCode
     *
     * @return Currency
     */
    public function setNumericIsoCode($numericIsoCode)
    {
        $this->numericIsoCode = (int)$numericIsoCode;

        return $this;
    }

    public function getName($localeCode)
    {
        return $this->displayNames[$localeCode];
    }

    public function getSymbol($type)
    {
        if (!isset($this->symbols[$type])) {
            throw new InvalidArgumentException("$type symbol not found for");
        }

        return $this->symbols[$type];
    }

    /**
     * @return int
     */
    public function getDecimalDigits()
    {
        return $this->decimalDigits;
    }

    /**
     * @param int $decimalDigits
     *
     * @return Currency
     */
    public function setDecimalDigits($decimalDigits)
    {
        $this->decimalDigits = $decimalDigits;

        return $this;
    }

    /**
     * @return array
     */
    public function getDisplayNames()
    {
        return $this->displayNames;
    }

    /**
     * @param $displayNames
     *
     * @return $this
     */
    public function setDisplayNames($displayNames)
    {
        $this->displayNames = $displayNames;

        return $this;
    }

    /**
     * @return array
     */
    public function getSymbols()
    {
        return $this->symbols;
    }

    /**
     * @param $symbols
     *
     * @return $this
     */
    public function setSymbols($symbols)
    {
        $this->symbols = $symbols;

        return $this;
    }
}
