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
    /**
     * @var string[]
     */
    private $localizedNames;

    /**
     * @var string
     */
    private $isoCode;

    /**
     * @var int
     */
    private $callPrefix;

    /**
     * @var int
     */
    private $defaultCurrency;

    /**
     * @var ZoneId
     */
    private $zoneId;

    /**
     * @var bool
     */
    private $needZipCode;

    /**
     * @var ?CountryZipCodeFormat
     */
    private $zipCodeFormat;

    /**
     * @var string
     */
    private $addressFormat;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var bool
     */
    private $containsStates;

    /**
     * @var bool
     */
    private $needIdNumber;

    /**
     * @var bool
     */
    private $displayTaxLabel;

    /**
     * @var int[]
     */
    private $shopAssociation;

    public function __construct(
        array $localizedNames,
        string $isoCode,
        int $callPrefix,
        int $defaultCurrency,
        int $zoneId,
        bool $needZipCode,
        ?string $zipCodeFormat,
        string $addressFormat,
        bool $enabled,
        bool $containsStates,
        bool $needIdNumber,
        bool $displayTaxLabel,
        array $shopAssociation
    ) {
        $this->localizedNames = $localizedNames;
        $this->isoCode = Tools::strtoupper(Tools::substr($isoCode, 0, 2));
        $this->callPrefix = $callPrefix;
        $this->defaultCurrency = $defaultCurrency;
        $this->zoneId = new ZoneId($zoneId);
        $this->needZipCode = $needZipCode;
        $this->zipCodeFormat = $zipCodeFormat ? new CountryZipCodeFormat($zipCodeFormat) : null;
        $this->addressFormat = $addressFormat;
        $this->enabled = $enabled;
        $this->containsStates = $containsStates;
        $this->needIdNumber = $needIdNumber;
        $this->displayTaxLabel = $displayTaxLabel;
        $this->shopAssociation = $shopAssociation;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    public function getIsoCode()
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
