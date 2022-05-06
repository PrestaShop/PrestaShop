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

/**
 * Adds new zone with provided data.
 */
class AddCountryCommand
{
    /** @var string */
    private $name;

    /** @var string */
    private $isoCode;

    /** @var string */
    private $callPrefix;

    /** @var int */
    private $currencyId;

    /** @var int */
    private $zoneId;

    /** @var bool */
    private $needZipCode;

    /** @var string */
    private $zipCodeFormat;

    /** @var string[] */
    private $addressLayout;

    /** @var bool */
    private $enabled;

    /** @var bool */
    private $containsStates;

    /** @var bool */
    private $needIdentificationNumber;

    /** @var bool */
    private $displayTaxLabel;

    /** @var array */
    private $shopAssociation;

    public function __construct(
        string $name,
        string $isoCode,
        string $callPrefix,
        int $currencyId,
        int $zoneId,
        bool $needZipCode,
        string $zipCodeFormat,
        array $addressLayout,
        bool $enabled, bool
        $containsStates,
        bool $needIdentificationNumber,
        bool $displayTaxLabel,
        array $shopAssociation
    )
    {
        $this->name = $name;
        $this->isoCode = $isoCode;
        $this->callPrefix = $callPrefix;
        $this->currencyId = $currencyId;
        $this->zoneId = $zoneId;
        $this->needZipCode = $needZipCode;
        $this->zipCodeFormat = $zipCodeFormat;
        $this->addressLayout = $addressLayout;
        $this->enabled = $enabled;
        $this->containsStates = $containsStates;
        $this->needIdentificationNumber = $needIdentificationNumber;
        $this->displayTaxLabel = $displayTaxLabel;
        $this->shopAssociation = $shopAssociation;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIsoCode(): string
    {
        return $this->isoCode;
    }

    public function getCallPrefix(): string
    {
        return $this->callPrefix;
    }

    public function getCurrencyId(): int
    {
        return $this->currencyId;
    }

    public function getZoneId(): int
    {
        return $this->zoneId;
    }

    public function isNeedZipCode(): bool
    {
        return $this->needZipCode;
    }

    public function getZipCodeFormat(): string
    {
        return $this->zipCodeFormat;
    }

    public function getAddressLayout(): array
    {
        return $this->addressLayout;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isContainsStates(): bool
    {
        return $this->containsStates;
    }

    public function isNeedIdentificationNumber(): bool
    {
        return $this->needIdentificationNumber;
    }

    public function isDisplayTaxLabel(): bool
    {
        return $this->displayTaxLabel;
    }

    public function getShopAssociation(): array
    {
        return $this->shopAssociation;
    }
}
