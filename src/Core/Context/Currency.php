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

/**
 * Immutable DTO Class representing the currency accessible via the CurrencyContext
 */
class Currency
{
    public function __construct(
        protected int $id,
        protected string $name,
        protected array $localizedNames,
        protected string $iso_code,
        protected string $iso_code_num,
        protected ?string $numeric_iso_code,
        protected float $conversion_rate,
        protected bool $deleted,
        protected bool $unofficial,
        protected bool $modified,
        protected bool $active,
        protected string $sign,
        protected string $symbol,
        protected array $localizedSymbols,
        protected ?string $format,
        protected int $blank,
        protected int $decimals,
        protected int $precision,
        protected array $pattern,
        protected array $localizedPatterns
    ) {
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
        return $this->iso_code;
    }

    public function getIsoCodeNum(): string
    {
        return $this->iso_code_num;
    }

    public function getNumericIsoCode(): ?string
    {
        return $this->numeric_iso_code;
    }

    public function getConversionRate(): float
    {
        return $this->conversion_rate;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function isUnofficial(): bool
    {
        return $this->unofficial;
    }

    public function isModified(): bool
    {
        return $this->modified;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getSign(): string
    {
        return $this->sign;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function getLocalizedSymbols(): array
    {
        return $this->localizedSymbols;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getBlank(): int
    {
        return $this->blank;
    }

    public function getDecimals(): int
    {
        return $this->decimals;
    }

    public function getPrecision(): int
    {
        return $this->precision;
    }

    public function getPattern(): array
    {
        return $this->pattern;
    }

    public function getLocalizedPatterns(): array
    {
        return $this->localizedPatterns;
    }
}
