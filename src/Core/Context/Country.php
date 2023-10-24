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

namespace PrestaShop\PrestaShop\Core\Context;

/**
 * Immutable DTO Class representing the country accessible via the CountryContext
 */
class Country
{
    public function __construct(
        protected int $id,
        protected int $zoneId,
        protected int $currencyId,
        protected string $isoCode,
        protected int $callPrefix,
        protected string $name,
        protected bool $containsStates,
        protected bool $needIdentificationNumber,
        protected bool $needZipCode,
        protected string $zipCodeFormat,
        protected bool $displayTaxLabel = true,
        protected bool $active = true
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getIdZone(): int
    {
        return $this->zoneId;
    }

    public function getIdCurrency(): int
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

    public function getContainsStates(): bool
    {
        return $this->containsStates;
    }

    public function getNeedIdentificationNumber(): bool
    {
        return $this->needIdentificationNumber;
    }

    public function getNeedZipCode(): bool
    {
        return $this->needZipCode;
    }

    public function getZipCodeFormat(): string
    {
        return $this->zipCodeFormat;
    }

    public function getDisplayTaxLabel(): bool
    {
        return $this->displayTaxLabel;
    }

    public function getIsActive(): bool
    {
        return $this->active;
    }
}
