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
     * @var string
     */
    private $isoCode;

    /**
     * @var string
     */
    private $numericIsoCode;

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
     * @param string $numericIsoCode
     * @param array $names
     * @param array $symbols
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
        $numericIsoCode,
        $names,
        $symbols,
        $exchangeRate,
        $precision,
        $isEnabled,
        $isUnofficial,
        array $associatedShopIds
    ) {
        $this->currencyId = new CurrencyId($currencyId);
        $this->isoCode = $isoCode;
        $this->numericIsoCode = $numericIsoCode;
        $this->names = $names;
        $this->symbols = $symbols;
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
     * Currency numeric ISO code
     *
     * @return string
     */
    public function getNumericIsoCode(): string
    {
        return $this->numericIsoCode;
    }

    /**
     * Currency's names, indexed by language id.
     *
     * @return array
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * Currency's names, indexed by language id.
     *
     * @return array
     */
    public function getSymbols()
    {
        return $this->symbols;
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
    public function getPrecision()
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
    public function isUnofficial()
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
