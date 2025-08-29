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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Context;

use PrestaShop\Decimal\DecimalNumber;

/**
 * This context service gives access to all contextual data related to currency.
 */
class CurrencyContext
{
    private DecimalNumber $conversionRate;

    public function __construct(
        protected int $id,
        protected string $name,
        protected array $localizedNames,
        protected string $isoCode,
        protected string $numericIsoCode,
        string $conversionRate,
        protected string $symbol,
        protected array $localizedSymbols,
        protected int $precision,
        protected string $pattern,
        protected array $localizedPatterns
    ) {
        $this->conversionRate = new DecimalNumber($conversionRate);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    public function getIsoCode(): string
    {
        return $this->isoCode;
    }

    public function getNumericIsoCode(): string
    {
        return $this->numericIsoCode;
    }

    public function getConversionRate(): DecimalNumber
    {
        return $this->conversionRate;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function getLocalizedSymbols(): array
    {
        return $this->localizedSymbols;
    }

    public function getPrecision(): int
    {
        return $this->precision;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function getLocalizedPatterns(): array
    {
        return $this->localizedPatterns;
    }
}
