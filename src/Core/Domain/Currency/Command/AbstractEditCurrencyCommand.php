<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\Command;

use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\ExchangeRate;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\Precision;

abstract class AbstractEditCurrencyCommand
{
    protected CurrencyId $currencyId;

    protected ?ExchangeRate $exchangeRate = null;

    protected ?Precision $precision = null;

    /**
     * @var string[]
     */
    protected array $localizedNames = [];

    /**
     * @var string[]
     */
    protected array $localizedSymbols = [];

    protected bool $isEnabled = false;

    /**
     * @var int[]
     */
    protected array $shopIds = [];

    /**
     * @var string[]
     */
    protected array $localizedTransformations = [];

    /**
     * @throws CurrencyException
     */
    public function __construct(int $currencyId)
    {
        $this->currencyId = new CurrencyId($currencyId);
    }

    public function getCurrencyId(): CurrencyId
    {
        return $this->currencyId;
    }

    public function getExchangeRate(): ?ExchangeRate
    {
        return $this->exchangeRate;
    }

    /**
     * @throws CurrencyConstraintException
     */
    public function setExchangeRate(float $exchangeRate): self
    {
        $this->exchangeRate = new ExchangeRate($exchangeRate);

        return $this;
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

    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

        return $this;
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
