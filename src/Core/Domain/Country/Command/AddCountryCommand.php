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
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryZipCodeFormat;

/**
 * Creates country with provided data
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
    private $defaultCurrency = 0;

    /**
     * @var int|null
     */
    private $zone;

    /**
     * @var bool
     */
    private $needZipCode = false;

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
    private $enabled = false;

    /**
     * @var bool
     */
    private $containsStates = false;

    /**
     * @var bool
     */
    private $needIdNumber = false;

    /**
     * @var bool
     */
    private $displayTaxLabel = false;

    /**
     * @var array
     */
    private $shopAssociation = [];

    /**
     * @param string[] $localizedNames
     * @param string $isoCode
     * @param int $callPrefix
     * @param string $addressFormat
     */
    public function __construct(
        array $localizedNames,
        string $isoCode,
        int $callPrefix,
        string $addressFormat
    ) {
        $this->localizedNames = $localizedNames;
        $this->isoCode = $isoCode;
        $this->callPrefix = $callPrefix;
        $this->addressFormat = $addressFormat;
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
     * @return CountryZipCodeFormat|null
     */
    public function getZipCodeFormat(): ?CountryZipCodeFormat
    {
        return $this->zipCodeFormat;
    }

    /**
     * @param string $zipCodeFormat
     *
     * @return AddCountryCommand
     *
     * @throws CountryConstraintException
     */
    public function setZipCodeFormat(string $zipCodeFormat): AddCountryCommand
    {
        $this->zipCodeFormat = new CountryZipCodeFormat($zipCodeFormat);

        return $this;
    }

    /**
     * @return string
     */
    public function getAddressFormat(): string
    {
        return $this->addressFormat;
    }

    /**
     * @return int
     */
    public function getDefaultCurrency(): int
    {
        return $this->defaultCurrency;
    }

    /**
     * @param int $defaultCurrency
     *
     * @return AddCountryCommand
     */
    public function setDefaultCurrency(int $defaultCurrency): AddCountryCommand
    {
        $this->defaultCurrency = $defaultCurrency;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getZone(): ?int
    {
        return $this->zone;
    }

    /**
     * @param int $zone
     *
     * @return AddCountryCommand
     */
    public function setZone(int $zone): AddCountryCommand
    {
        $this->zone = $zone;

        return $this;
    }

    /**
     * @return bool
     */
    public function needZipCode(): bool
    {
        return $this->needZipCode;
    }

    /**
     * @param bool $needZipCode
     *
     * @return AddCountryCommand
     */
    public function setNeedZipCode(bool $needZipCode): AddCountryCommand
    {
        $this->needZipCode = $needZipCode;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     *
     * @return AddCountryCommand
     */
    public function setEnabled(bool $enabled): AddCountryCommand
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function containsStates(): bool
    {
        return $this->containsStates;
    }

    /**
     * @param bool $containsStates
     *
     * @return AddCountryCommand
     */
    public function setContainsStates(bool $containsStates): AddCountryCommand
    {
        $this->containsStates = $containsStates;

        return $this;
    }

    /**
     * @return bool
     */
    public function needIdNumber(): bool
    {
        return $this->needIdNumber;
    }

    /**
     * @param bool $needIdNumber
     *
     * @return AddCountryCommand
     */
    public function setNeedIdNumber(bool $needIdNumber): AddCountryCommand
    {
        $this->needIdNumber = $needIdNumber;

        return $this;
    }

    /**
     * @return bool
     */
    public function displayTaxLabel(): bool
    {
        return $this->displayTaxLabel;
    }

    /**
     * @param bool $displayTaxLabel
     *
     * @return AddCountryCommand
     */
    public function setDisplayTaxLabel(bool $displayTaxLabel): AddCountryCommand
    {
        $this->displayTaxLabel = $displayTaxLabel;

        return $this;
    }

    /**
     * @return array
     */
    public function getShopAssociation(): array
    {
        return $this->shopAssociation;
    }

    /**
     * @param array $shopAssociation
     *
     * @return AddCountryCommand
     */
    public function setShopAssociation(array $shopAssociation): AddCountryCommand
    {
        $this->shopAssociation = $shopAssociation;

        return $this;
    }
}
