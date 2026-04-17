<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
