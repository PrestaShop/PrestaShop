<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace PrestaShop\PrestaShop\Core\Domain\Country\Command;

use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryZipCodeFormat;

/**
 * Edit country with provided data
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
    private $localisedNames;

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
     * @var int
     */
    private $zone;

    /**
     * @var bool
     */
    private $needZipCode;

    /**
     * @var CountryZipCodeFormat|null
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
     * @var int[]|null
     */
    private $shopAssociation;

    /**
     * @param string $countryId
     * @param array $localisedNames
     * @param string $isoCode
     * @param int $callPrefix
     * @param int $zone
     * @param bool $needZipCode
     * @param string $addressFormat
     * @param bool $enabled
     * @param bool $containsStates
     * @param bool $needIdNumber
     * @param bool $displayTaxLabel
     * @param int $defaultCurrency
     *
     * @throws CountryConstraintException
     */
    public function __construct(
        string $countryId,
        array $localisedNames,
        string $isoCode,
        int $callPrefix,
        int $zone,
        bool $needZipCode,
        string $addressFormat,
        bool $enabled,
        bool $containsStates,
        bool $needIdNumber,
        bool $displayTaxLabel,
        int $defaultCurrency
    ) {
        $this->countryId = new CountryId($countryId);
        $this->localisedNames = $localisedNames;
        $this->isoCode = $isoCode;
        $this->callPrefix = $callPrefix;
        $this->zone = $zone;
        $this->needZipCode = $needZipCode;
        $this->addressFormat = $addressFormat;
        $this->enabled = $enabled;
        $this->containsStates = $containsStates;
        $this->needIdNumber = $needIdNumber;
        $this->displayTaxLabel = $displayTaxLabel;
        $this->defaultCurrency = $defaultCurrency;
    }

    /**
     * @return CountryId
     */
    public function getCountryId(): CountryId
    {
        return $this->countryId;
    }

    /**
     * @return string[]
     */
    public function getLocalisedNames(): array
    {
        return $this->localisedNames;
    }

    /**
     * @return string
     */
    public function getIsoCode(): string
    {
        return $this->isoCode;
    }

    /**
     * @return int
     */
    public function getCallPrefix(): int
    {
        return $this->callPrefix;
    }

    /**
     * @return int|null
     */
    public function getDefaultCurrency(): ?int
    {
        return $this->defaultCurrency;
    }

    /**
     * @return int
     */
    public function getZone(): int
    {
        return $this->zone;
    }

    /**
     * @return bool
     */
    public function needZipCode(): bool
    {
        return $this->needZipCode;
    }

    /**
     * @return CountryZipCodeFormat|null
     */
    public function getZipCodeFormat(): ?CountryZipCodeFormat
    {
        return $this->zipCodeFormat;
    }

    /**
     * @return string
     */
    public function getAddressFormat(): string
    {
        return $this->addressFormat;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return bool
     */
    public function containsStates(): bool
    {
        return $this->containsStates;
    }

    /**
     * @return bool
     */
    public function needIdNumber(): bool
    {
        return $this->needIdNumber;
    }

    /**
     * @return bool
     */
    public function displayTaxLabel(): bool
    {
        return $this->displayTaxLabel;
    }

    /**
     * @return int[]|null
     */
    public function getShopAssociation(): ?array
    {
        return $this->shopAssociation;
    }

    /**
     * @param string $zipCodeFormat
     *
     * @return EditCountryCommand
     *
     * @throws CountryConstraintException
     */
    public function setZipCodeFormat(string $zipCodeFormat): EditCountryCommand
    {
        $this->zipCodeFormat = new CountryZipCodeFormat($zipCodeFormat);

        return $this;
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
