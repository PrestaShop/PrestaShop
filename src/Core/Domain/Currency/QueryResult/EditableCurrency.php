<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;

/**
 * Class EditableCurrency
 */
class EditableCurrency
{
    /**
     * @var CurrencyId
     */
    private $currencyId;

    /**
     * @var array
     */
    private $names;

    /**
     * @var array
     */
    private $symbols;

    /**
     * @var array
     */
    private $transformations;

    /**
     * @var string
     */
    private $isoCode;

    /**
     * @var float
     */
    private $exchangeRate;

    /**
     * @var int
     */
    private $precision;

    /**
     * @var bool
     */
    private $isEnabled;

    /**
     * @var bool
     */
    private $isUnofficial;

    /**
     * @var int[]
     */
    private $associatedShopIds;

    /**
     * @param int $currencyId
     * @param string $isoCode
     * @param array $names
     * @param array $symbols
     * @param array $transformations
     * @param float $exchangeRate
     * @param int $precision
     * @param bool $isEnabled
     * @param bool $isUnofficial
     * @param int[] $associatedShopIds
     *
     * @throws CurrencyException
     */
    public function __construct(
        $currencyId,
        $isoCode,
        array $names,
        array $symbols,
        array $transformations,
        $exchangeRate,
        int $precision,
        $isEnabled,
        bool $isUnofficial,
        array $associatedShopIds
    ) {
        $this->currencyId = new CurrencyId($currencyId);
        $this->isoCode = $isoCode;
        $this->names = $names;
        $this->symbols = $symbols;
        $this->transformations = $transformations;
        $this->exchangeRate = $exchangeRate;
        $this->precision = $precision;
        $this->isEnabled = $isEnabled;
        $this->isUnofficial = $isUnofficial;
        $this->associatedShopIds = $associatedShopIds;
    }

    /**
     * @return CurrencyId
     */
    public function getCurrencyId()
    {
        return $this->currencyId;
    }

    /**
     * Currency ISO code
     *
     * @return string
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * Currency's names, indexed by language id.
     *
     * @return array
     */
    public function getNames(): array
    {
        return $this->names;
    }

    /**
     * Currency's names, indexed by language id.
     *
     * @return array
     */
    public function getSymbols(): array
    {
        return $this->symbols;
    }

    /**
     * Currency's transformations, indexed by language id.
     *
     * @return array
     */
    public function getTransformations(): array
    {
        return $this->transformations;
    }

    /**
     * Exchange rate of the currency compared to the shop's default one
     *
     * @return float
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    /**
     * Currency decimal precision
     *
     * @return int
     */
    public function getPrecision(): int
    {
        return $this->precision;
    }

    /**
     * Whether the currency is enabled on the front
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Whether the currency is an unofficial one (custom created)
     *
     * @return bool
     */
    public function isUnofficial(): bool
    {
        return $this->isUnofficial;
    }

    /**
     * List of shops that use this currency (shop IDs)
     *
     * @return int[]
     */
    public function getAssociatedShopIds()
    {
        return $this->associatedShopIds;
    }
}
