<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\Command;

use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\AlphaIsoCode;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\ExchangeRate;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\Precision;

abstract class AbstractAddCurrencyCommand
{
    protected AlphaIsoCode $isoCode;

    protected ExchangeRate $exchangeRate;

    protected ?Precision $precision = null;

    /**
     * @var string[]
     */
    protected array $localizedNames = [];

    /**
     * @var string[]
     */
    protected array $localizedSymbols = [];

    protected bool $isEnabled;

    /**
     * @var int[]
     */
    protected array $shopIds = [];

    /**
     * @var string[]
     */
    protected array $localizedTransformations = [];

    /**
     * @throws CurrencyConstraintException
     */
    public function __construct(
        string $isoCode,
        float $exchangeRate,
        bool $isEnabled
    ) {
        $this->isoCode = new AlphaIsoCode($isoCode);
        $this->exchangeRate = new ExchangeRate($exchangeRate);
        $this->isEnabled = $isEnabled;
    }

    public function getIsoCode(): AlphaIsoCode
    {
        return $this->isoCode;
    }

    public function getPrecision(): ?Precision
    {
        return $this->precision;
    }

    /**
     * @throws CurrencyConstraintException
     */
    public function setPrecision(int|string $precision): self
    {
        $this->precision = new Precision($precision);

        return $this;
    }

    public function getExchangeRate(): ExchangeRate
    {
        return $this->exchangeRate;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    /**
     * @param string[] $localizedNames currency's localized names, indexed by language id
     *
     * @throws CurrencyConstraintException
     */
    public function setLocalizedNames(array $localizedNames): self
    {
        if (empty($localizedNames)) {
            throw new CurrencyConstraintException('Currency name cannot be empty', CurrencyConstraintException::EMPTY_NAME);
        }

        $this->localizedNames = $localizedNames;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalizedSymbols(): array
    {
        return $this->localizedSymbols;
    }

    /**
     * @param string[] $localizedSymbols currency's localized symbols, indexed by language id
     *
     * @throws CurrencyConstraintException
     */
    public function setLocalizedSymbols(array $localizedSymbols): self
    {
        if (empty($localizedSymbols)) {
            throw new CurrencyConstraintException('Currency symbol cannot be empty', CurrencyConstraintException::EMPTY_SYMBOL);
        }

        $this->localizedSymbols = $localizedSymbols;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * @return int[]
     */
    public function getShopIds(): array
    {
        return $this->shopIds;
    }

    /**
     * @param int[] $shopIds
     */
    public function setShopIds(array $shopIds): self
    {
        $this->shopIds = $shopIds;

        return $this;
    }

    /**
     * Returns the currency's localized transformations, indexed by language id
     *
     * @return string[]
     */
    public function getLocalizedTransformations(): array
    {
        return $this->localizedTransformations;
    }

    /**
     * @param string[] $localizedTransformations currency's localized transformations, indexed by language id
     */
    public function setLocalizedTransformations(array $localizedTransformations): self
    {
        $this->localizedTransformations = $localizedTransformations;

        return $this;
    }
}
