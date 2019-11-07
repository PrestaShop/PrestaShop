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

use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Core\Localization\CLDR\ComputingPrecision;

class CurrencyAPIData
{
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
     * @var string|null
     */
    private $numericIsoCode;

    /**
     * @var Number
     */
    private $exchangeRate;

    /**
     * @var int
     */
    private $precision;

    /**
     * @param string $isoCode
     * @param string|null $numericIsoCode
     * @param array $names
     * @param array $symbols
     * @param Number $exchangeRate
     * @param int $precision
     */
    public function __construct(
        $isoCode,
        $numericIsoCode,
        $names,
        $symbols,
        Number $exchangeRate,
        $precision
    ) {
        $this->isoCode = $isoCode;
        $this->numericIsoCode = $numericIsoCode;
        $this->names = $names;
        $this->symbols = $symbols;
        $this->exchangeRate = $exchangeRate;
        $this->precision = $precision;
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
     * @return string|null
     */
    public function getNumericIsoCode(): ?string
    {
        return $this->numericIsoCode;
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
     * Exchange rate of the currency compared to the shop's default one
     *
     * @return Number
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

    public function toArray(): array
    {
        $computingPrecision = new ComputingPrecision();

        return [
            'iso_code' => $this->getIsoCode(),
            'numeric_iso_code' => $this->getNumericIsoCode(),
            'precision' => $this->getPrecision(),
            'names' => $this->getNames(),
            'symbols' => $this->getSymbols(),
            'exchange_rate' => $this->exchangeRate->round($computingPrecision->getPrecision($this->getPrecision())),
        ];
    }
}
