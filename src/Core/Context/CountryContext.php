<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Context;

/**
 * This context service gives access to all contextual data related to country.
 */
class CountryContext
{
    public function __construct(
        protected int $id,
        protected int $zoneId,
        protected int $currencyId,
        protected string $isoCode,
        protected int $callPrefix,
        protected string $name,
        protected bool $containsStates,
        protected bool $identificationNumberNeeded,
        protected bool $zipCodeNeeded,
        protected string $zipCodeFormat,
        protected bool $taxLabelDisplayed,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getZoneId(): int
    {
        return $this->zoneId;
    }

    public function getCurrencyId(): int
    {
        return $this->currencyId;
    }

    public function getIsoCode(): string
    {
        return $this->isoCode;
    }

    public function getCallPrefix(): int
    {
        return $this->callPrefix;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function containsStates(): bool
    {
        return $this->containsStates;
    }

    public function isIdentificationNumberNeeded(): bool
    {
        return $this->identificationNumberNeeded;
    }

    public function isZipCodeNeeded(): bool
    {
        return $this->zipCodeNeeded;
    }

    public function getZipCodeFormat(): string
    {
        return $this->zipCodeFormat;
    }

    public function isTaxLabelDisplayed(): bool
    {
        return $this->taxLabelDisplayed;
    }
}
