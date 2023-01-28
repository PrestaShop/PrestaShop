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
    protected $localizedNames;

    /**
     * @var string
     */
    protected $isoCode;

    /**
     * @var int
     */
    protected $callPrefix;

    /**
     * @var int
     */
    protected $defaultCurrency = 0;

    /**
     * @var ZoneId
     */
    protected $zoneId;

    /**
     * @var bool
     */
    protected $needZipCode = false;

    /**
     * @var ?CountryZipCodeFormat
     */
    protected $zipCodeFormat;

    /**
     * @var string
     */
    protected $addressFormat = '';

    /**
     * @var bool
     */
    protected $enabled = false;

    /**
     * @var bool
     */
    protected $containsStates = false;

    /**
     * @var bool
     */
    protected $needIdNumber = false;

    /**
     * @var bool
     */
    protected $displayTaxLabel = false;

    /**
     * @var int[]
     */
    protected $shopAssociation = [];

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
