<?php

/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Localization\Currency;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use PrestaShop\PrestaShop\Core\Localization\Currency;

class CurrencyCollection implements IteratorAggregate, Countable
{
    protected $currencies = [];

    /**
     * Gets the current CurrencyCollection as an Iterator that includes all currencies.
     *
     * It implements \IteratorAggregate.
     *
     * @return ArrayIterator|Currency[]
     *  An ArrayIterator object for iterating over currencies
     */
    public function getIterator()
    {
        return new ArrayIterator($this->currencies);
    }

    /**
     * Gets the number of Currencies in this collection.
     *
     * @return int
     *  The number of currencies
     */
    public function count()
    {
        return count($this->currencies);
    }

    /**
     * Adds a currency at the end of the collection.
     *
     * @param Currency $currency
     *  The currency to add.
     */
    public function add(Currency $currency)
    {
        $this->currencies[$currency->getIsoCode()] = $currency;
    }

    /**
     * Returns all currencies in this collection.
     *
     * @return Currency[]
     *  An array of currencies
     */
    public function all()
    {
        return $this->currencies;
    }

    /**
     * Gets a currency by ISO code.
     *
     * @param string $isoCode
     *  The currency code (alphabetic ISO 4217 code)
     *
     * @return Currency|null
     *  A Currency instance or null when not found
     */
    public function get($isoCode)
    {
        return isset($this->currencies[$isoCode])
            ? $this->currencies[$isoCode]
            : null;
    }

    /**
     * Removes a currency or an array of currencies by iso code from the collection.
     *
     * @param string|string[] $isoCode
     *  The currency ISO code or an array of currency ISO codes
     */
    public function remove($isoCode)
    {
        foreach ((array)$isoCode as $c) {
            unset($this->currencies[$c]);
        }
    }

    /**
     * Adds a currency collection at the end of the current set by appending all
     * currencies of the added collection.
     *
     * @param CurrencyCollection $collection
     *  The CurrencyCollection to append at the end of the current one
     */
    public function addCollection(CurrencyCollection $collection)
    {
        // we need to remove all currencies with the same codes first because just replacing them
        // would not place the new currency at the end of the merged array
        foreach ($collection->all() as $isoCode => $currency) {
            unset($this->currencies[$isoCode]);
            $this->currencies[$isoCode] = $currency;
        }
    }

    /**
     * Clear the collection, removing all contained currencies.
     */
    public function clear()
    {
        $this->currencies = [];
    }
}
