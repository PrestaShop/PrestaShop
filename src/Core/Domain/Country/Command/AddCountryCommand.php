<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Country\Command;

use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryZipCodeFormat;
use PrestaShop\PrestaShop\Core\Domain\Zone\ValueObject\ZoneId;
use Tools;

/**
 * Adds new zone with provided data.
 */
class AddCountryCommand
{
    private string $isoCode;
    private ZoneId $zoneId;
    private ?CountryZipCodeFormat $zipCodeFormat;

    /**
     * @param string[] $localizedNames
     * @param int[] $shopAssociation
     */
    public function __construct(
        private array $localizedNames,
        string $isoCode,
        private int $callPrefix,
        private int $defaultCurrency,
        int $zoneId,
        private bool $needZipCode,
        ?string $zipCodeFormat,
        private string $addressFormat,
        private bool $enabled,
        private bool $containsStates,
        private bool $needIdNumber,
        private bool $displayTaxLabel,
        private array $shopAssociation,
    ) {
        $this->isoCode = Tools::strtoupper(Tools::substr($isoCode, 0, 2));
        $this->zoneId = new ZoneId($zoneId);
        $this->zipCodeFormat = $zipCodeFormat ? new CountryZipCodeFormat($zipCodeFormat) : null;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    public function getIsoCode(): string
    {
        return $this->isoCode;
    }

    public function getCallPrefix(): int
    {
        return $this->callPrefix;
    }

    public function getDefaultCurrency(): int
    {
        return $this->defaultCurrency;
    }

    public function getZoneId(): ZoneId
    {
        return $this->zoneId;
    }

    public function needZipCode(): bool
    {
        return $this->needZipCode;
    }

    public function getZipCodeFormat(): ?CountryZipCodeFormat
    {
        return $this->zipCodeFormat;
    }

    public function getAddressFormat(): string
    {
        return $this->addressFormat;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function containsStates(): bool
    {
        return $this->containsStates;
    }

    public function needIdNumber(): bool
    {
        return $this->needIdNumber;
    }

    public function displayTaxLabel(): bool
    {
        return $this->displayTaxLabel;
    }

    /**
     * @return int[]
     */
    public function getShopAssociation(): array
    {
        return $this->shopAssociation;
    }
}
