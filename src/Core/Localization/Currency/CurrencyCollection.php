<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\Currency;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use PrestaShop\PrestaShop\Core\Localization\Currency;
use Traversable;

class CurrencyCollection implements IteratorAggregate, Countable
{
    protected $currencies = [];

    /**
     * Gets the current CurrencyCollection as an Iterator that includes all currencies.
     *
     * @return Traversable<Currency>
     */
    public function getIterator(): Traversable
    {
        /** @var Traversable<Currency> $iterator (needed for auto-completion) */
        $iterator = new ArrayIterator($this->currencies);

        return $iterator;
    }

    /**
     * Gets the number of Currencies in this collection.
     *
     * @return int
     *             The number of currencies
     */
    public function count(): int
    {
        return count($this->currencies);
    }

    /**
     * Adds a currency at the end of the collection.
     *
     * @param Currency $currency
     *                           The currency to add
     *
     * @return CurrencyCollection
     *                            Fluent interface
     */
    public function add(Currency $currency)
    {
        $this->currencies[$currency->getIsoCode()] = $currency;

        return $this;
    }

    /**
     * Returns all currencies in this collection.
     *
     * @return Currency[]
     *                    An array of currencies
     */
    public function all()
    {
        return $this->currencies;
    }

    /**
     * Gets a currency by ISO code.
     *
     * @param string $isoCode
     *                        The currency code (alphabetic ISO 4217 code)
     *
     * @return Currency|null
     *                       A Currency instance or null when not found
     */
    public function get($isoCode)
    {
        return $this->currencies[$isoCode] ?? null;
    }

    /**
     * Removes a currency or an array of currencies by iso code from the collection.
     *
     * @param string|string[] $isoCode
     *                                 The currency ISO code or an array of currency ISO codes
     *
     * @return CurrencyCollection
     *                            Fluent interface
     */
    public function remove($isoCode)
    {
        foreach ((array) $isoCode as $c) {
            unset($this->currencies[$c]);
        }

        return $this;
    }

    /**
     * Adds a currency collection at the end of the current set by appending all
     * currencies of the added collection.
     *
     * @param CurrencyCollection $collection The CurrencyCollection to append at the end of the current one
     *
     * @return CurrencyCollection Fluent interface
     */
    public function addCollection(CurrencyCollection $collection)
    {
        // we need to remove all currencies with the same codes first because just replacing them
        // would not place the new currency at the end of the merged array
        foreach ($collection->all() as $isoCode => $currency) {
            unset($this->currencies[$isoCode]);
            $this->currencies[$isoCode] = $currency;
        }

        return $this;
    }

    /**
     * Clear the collection, removing all contained currencies.
     *
     * @return CurrencyCollection
     *                            Fluent interface
     */
    public function clear()
    {
        $this->currencies = [];

        return $this;
    }
}
