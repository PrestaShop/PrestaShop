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
 * This context service gives access to all contextual data related to country.
 */
class CountryContext
{
    public function __construct(
        protected int $id,
        protected int $zoneId,
        protected int $currencyId,
        protected string $isoCode,
        protected int $callPrefix,
        protected string $name,
        protected bool $containsStates,
        protected bool $identificationNumberNeeded,
        protected bool $zipCodeNeeded,
        protected string $zipCodeFormat,
        protected bool $taxLabelDisplayed,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getZoneId(): int
    {
        return $this->zoneId;
    }

    public function getCurrencyId(): int
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

    public function containsStates(): bool
    {
        return $this->containsStates;
    }

    public function isIdentificationNumberNeeded(): bool
    {
        return $this->identificationNumberNeeded;
    }

    public function isZipCodeNeeded(): bool
    {
        return $this->zipCodeNeeded;
    }

    public function getZipCodeFormat(): string
    {
        return $this->zipCodeFormat;
    }

    public function isTaxLabelDisplayed(): bool
    {
        return $this->taxLabelDisplayed;
    }
}
