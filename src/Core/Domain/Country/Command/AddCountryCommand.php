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

namespace PrestaShop\PrestaShop\Core\Domain\Country\Command;

use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryZipCodeFormat;
use Tools;

/**
 * Adds new zone with provided data.
 */
class AddCountryCommand
{
    /** @var string[] */
    private $localizedNames;

    /** @var string */
    private $isoCode;

    /** @var int */
    private $callPrefix;

    /** @var int */
    private $defaultCurrency = 0;

    /**@var int|null */
    private $zoneId;

    /** @var bool */
    private $needZipCode = false;

    /** @var CountryZipCodeFormat|null */
    private $zipCodeFormat;

    /** @var string */
    private $addressFormat;

    /** @var bool */
    private $enabled = false;

    /** @var bool */
    private $containsStates = false;

    /** @var bool */
    private $needIdNumber = false;

    /** @var bool */
    private $displayTaxLabel = false;

    /** @var array */
    private $shopAssociation = [];

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

    public function getIsoCode(): string
    {
        return Tools::strtoupper(Tools::substr($this->isoCode, 0, 2));
    }

    public function getCallPrefix(): int
    {
        return $this->callPrefix;
    }

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

    public function getAddressFormat(): string
    {
        return $this->addressFormat;
    }

    public function getDefaultCurrency(): int
    {
        return $this->defaultCurrency;
    }

    public function setDefaultCurrency(int $defaultCurrency): self
    {
        $this->defaultCurrency = $defaultCurrency;

        return $this;
    }

    public function getZoneId(): ?int
    {
        return $this->zoneId;
    }

    public function setZoneId(int $zoneId): self
    {
        $this->zoneId = $zoneId;

        return $this;
    }

    public function needZipCode(): bool
    {
        return $this->needZipCode;
    }

    public function setNeedZipCode(bool $needZipCode): self
    {
        $this->needZipCode = $needZipCode;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function containsStates(): bool
    {
        return $this->containsStates;
    }

    public function setContainsStates(bool $containsStates): self
    {
        $this->containsStates = $containsStates;

        return $this;
    }

    public function needIdNumber(): bool
    {
        return $this->needIdNumber;
    }

    public function setNeedIdNumber(bool $needIdNumber): self
    {
        $this->needIdNumber = $needIdNumber;

        return $this;
    }

    public function displayTaxLabel(): bool
    {
        return $this->displayTaxLabel;
    }

    public function setDisplayTaxLabel(bool $displayTaxLabel): self
    {
        $this->displayTaxLabel = $displayTaxLabel;

        return $this;
    }

    public function getShopAssociation(): array
    {
        return $this->shopAssociation;
    }

    public function setShopAssociation(array $shopAssociation): self
    {
        $this->shopAssociation = $shopAssociation;

        return $this;
    }
}
