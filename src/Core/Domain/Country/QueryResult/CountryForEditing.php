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

namespace PrestaShop\PrestaShop\Core\Domain\Country\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryZipCodeFormat;

/**
 * Stores editable country data
 */
class CountryForEditing
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
     * @var int
     */
    private $zone;

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

    /**
     * @param CountryId $countryId
     * @param string[] $localisedNames
     * @param string $isoCode
     * @param int $callPrefix
     * @param int $defaultCurrency
     * @param int $zone
     * @param bool $needZipCode
     * @param ?string $zipCodeFormat
     * @param string $addressFormat
     * @param bool $enabled
     * @param bool $containsStates
     * @param bool $needIdNumber
     * @param bool $displayTaxLabel
     * @param int[] $shopAssociation
     */
    public function __construct(
        CountryId $countryId,
        array $localisedNames,
        string $isoCode,
        int $callPrefix,
        int $defaultCurrency,
        int $zone,
        bool $needZipCode,
        ?string $zipCodeFormat,
        string $addressFormat,
        bool $enabled,
        bool $containsStates,
        bool $needIdNumber,
        bool $displayTaxLabel,
        array $shopAssociation
    ) {
        $this->countryId = $countryId;
        $this->localizedNames = $localisedNames;
        $this->isoCode = $isoCode;
        $this->callPrefix = $callPrefix;
        $this->defaultCurrency = $defaultCurrency;
        $this->zone = $zone;
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
     * @return CountryId
     */
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
     * @return int
     */
    public function getDefaultCurrency(): int
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
    public function isNeedZipCode(): bool
    {
        return $this->needZipCode;
    }

    /**
     * @return ?CountryZipCodeFormat
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
    public function isContainsStates(): bool
    {
        return $this->containsStates;
    }

    /**
     * @return bool
     */
    public function isNeedIdNumber(): bool
    {
        return $this->needIdNumber;
    }

    /**
     * @return bool
     */
    public function isDisplayTaxLabel(): bool
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
