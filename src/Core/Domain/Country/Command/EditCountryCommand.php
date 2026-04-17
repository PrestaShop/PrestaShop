<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Country\Command;

use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryZipCodeFormat;

/**
 * Adds new zone with provided data.
 */
class EditCountryCommand
{
    /**
     * @var CountryId
     */
    private $countryId;

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
     * @var int|null
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
        int $countryId
    ) {
        $this->countryId = new CountryId($countryId);
    }

    public function getCountryId(): CountryId
    {
        return $this->countryId;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    /**
     * @param string[] $localizedNames
     *
     * @return EditCountryCommand
     */
    public function setLocalizedNames(array $localizedNames): EditCountryCommand
    {
        $this->localizedNames = $localizedNames;

        return $this;
    }

    public function getIsoCode(): ?string
    {
        return $this->isoCode;
    }

    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    public function getCallPrefix(): ?int
    {
        return $this->callPrefix;
    }

    public function setCallPrefix(int $callPrefix): EditCountryCommand
    {
        $this->callPrefix = $callPrefix;

        return $this;
    }

    public function getDefaultCurrency(): ?int
    {
        return $this->defaultCurrency;
    }

    public function setDefaultCurrency(int $defaultCurrency): EditCountryCommand
    {
        $this->defaultCurrency = $defaultCurrency;

        return $this;
    }

    public function getZoneId(): ?int
    {
        return $this->zoneId;
    }

    public function setZoneId(?int $zoneId): EditCountryCommand
    {
        $this->zoneId = $zoneId;

        return $this;
    }

    public function needZipCode(): ?bool
    {
        return $this->needZipCode;
    }

    public function setNeedZipCode(bool $needZipCode): EditCountryCommand
    {
        $this->needZipCode = $needZipCode;

        return $this;
    }

    public function getZipCodeFormat(): ?CountryZipCodeFormat
    {
        return $this->zipCodeFormat;
    }

    public function setZipCodeFormat(?string $zipCodeFormat): EditCountryCommand
    {
        $this->zipCodeFormat = $zipCodeFormat ? new CountryZipCodeFormat($zipCodeFormat) : null;

        return $this;
    }

    public function getAddressFormat(): ?string
    {
        return $this->addressFormat;
    }

    public function setAddressFormat(string $addressFormat): EditCountryCommand
    {
        $this->addressFormat = $addressFormat;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): EditCountryCommand
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function containsStates(): ?bool
    {
        return $this->containsStates;
    }

    public function setContainsStates(bool $containsStates): EditCountryCommand
    {
        $this->containsStates = $containsStates;

        return $this;
    }

    public function needIdNumber(): ?bool
    {
        return $this->needIdNumber;
    }

    public function setNeedIdNumber(bool $needIdNumber): EditCountryCommand
    {
        $this->needIdNumber = $needIdNumber;

        return $this;
    }

    public function displayTaxLabel(): ?bool
    {
        return $this->displayTaxLabel;
    }

    /**
     * @param bool $displayTaxLabel
     *
     * @return EditCountryCommand
     */
    public function setDisplayTaxLabel(bool $displayTaxLabel): EditCountryCommand
    {
        $this->displayTaxLabel = $displayTaxLabel;

        return $this;
    }

    /**
     * @return ?int[]
     */
    public function getShopAssociation(): ?array
    {
        return $this->shopAssociation;
    }

    /**
     * @param int[] $shopAssociation
     *
     * @return EditCountryCommand
     */
    public function setShopAssociation(array $shopAssociation): EditCountryCommand
    {
        $this->shopAssociation = $shopAssociation;

        return $this;
    }
}
