<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\Command;

use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\AlphaIsoCode;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\ExchangeRate;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\Precision;

/**
 * Class AddUnofficialCurrencyCommand used to add an alternative currency
 */
class AddUnofficialCurrencyCommand
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
     *
     * @return $this
     */
    public function setLocalizedTransformations(array $localizedTransformations): self
    {
        $this->localizedTransformations = $localizedTransformations;

        return $this;
    }
}
