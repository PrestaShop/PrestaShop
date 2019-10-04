<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\Command;

use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\ExchangeRate;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\AlphaIsoCode;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\NumericIsoCode;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\Precision;

/**
 * Class EditCurrencyCommand
 */
class EditCurrencyCommand implements CurrencyCommandInterface
{
    /**
     * @var CurrencyId
     */
    private $currencyId;

    /**
     * @var AlphaIsoCode
     */
    private $isoCode;

    /**
     * @var NumericIsoCode
     */
    private $numericIsoCode;

    /**
     * @var ExchangeRate
     */
    private $exchangeRate;

    /**
     * @var Precision
     */
    private $precision;

    /**
     * @var string[]
     */
    private $localizedNames;

    /**
     * @var string[]
     */
    private $localizedSymbols;

    /**
     * @var bool
     */
    private $isEnabled;

    /**
     * @var int[]
     */
    private $shopIds;

    /**
     * @param int $currencyId
     *
     * @throws CurrencyException
     */
    public function __construct($currencyId)
    {
        $this->currencyId = new CurrencyId($currencyId);
    }

    /**
     * @return CurrencyId
     */
    public function getCurrencyId()
    {
        return $this->currencyId;
    }

    /**
     * @return AlphaIsoCode
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * @param string $isoCode
     *
     * @return self
     *
     * @throws CurrencyConstraintException
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = new AlphaIsoCode($isoCode);

        return $this;
    }

    /**
     * @return NumericIsoCode
     */
    public function getNumericIsoCode()
    {
        return $this->numericIsoCode;
    }

    /**
     * @param int $numericIsoCode
     *
     * @return self
     *
     * @throws CurrencyConstraintException
     */
    public function setNumericIsoCode($numericIsoCode)
    {
        $this->numericIsoCode = new NumericIsoCode($numericIsoCode);

        return $this;
    }

    /**
     * @return ExchangeRate
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    /**
     * @param float $exchangeRate
     *
     * @return self
     *
     * @throws CurrencyConstraintException
     */
    public function setExchangeRate($exchangeRate)
    {
        $this->exchangeRate = new ExchangeRate($exchangeRate);

        return $this;
    }

    /**
     * @return Precision
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * @param int|string $precision
     *
     * @return self
     *
     * @throws CurrencyConstraintException
     */
    public function setPrecision($precision)
    {
        $this->precision = new Precision($precision);

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames()
    {
        return $this->localizedNames;
    }

    /**
     * @param string[] $localizedNames
     *
     * @return $this
     *
     * @throws CurrencyConstraintException
     */
    public function setLocalizedNames(array $localizedNames)
    {
        if (empty($localizedNames)) {
            throw new CurrencyConstraintException(
                'Currency name cannot be empty',
                CurrencyConstraintException::EMPTY_NAME
            );
        }

        $this->localizedNames = $localizedNames;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalizedSymbols()
    {
        return $this->localizedSymbols;
    }

    /**
     * @param string[] $localizedSymbols
     *
     * @return $this
     *
     * @throws CurrencyConstraintException
     */
    public function setLocalizedSymbols(array $localizedSymbols)
    {
        if (empty($localizedSymbols)) {
            throw new CurrencyConstraintException(
                'Currency symbol cannot be empty',
                CurrencyConstraintException::EMPTY_SYMBOL
            );
        }

        $this->localizedSymbols = $localizedSymbols;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @param bool $isEnabled
     *
     * @return self
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * @return int[]
     */
    public function getShopIds()
    {
        return $this->shopIds;
    }

    /**
     * @param int[] $shopIds
     *
     * @return self
     */
    public function setShopIds(array $shopIds)
    {
        $this->shopIds = $shopIds;

        return $this;
    }
}
