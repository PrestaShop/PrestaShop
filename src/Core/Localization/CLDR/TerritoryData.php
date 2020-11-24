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

namespace PrestaShop\PrestaShop\Core\Localization\CLDR;

/**
 * The TerritoryData class is the exact representation of Territory's data structure inside CLDR xml data files.
 */
class TerritoryData
{
    /**
     * Alphabetic ISO 4217 territory code.
     *
     * @var string|null
     */
    protected $isoCode;

    /**
     * Territory code.
     *
     * @var string|null
     */
    protected $name;

    /**
     * Override this object's data with another TerritoryData object.
     *
     * @param TerritoryData $territoryData Currency data to use for the override
     *
     * @return $this Fluent interface
     */
    public function overrideWith(TerritoryData $territoryData)
    {
        if (null !== $territoryData->getIsoCode()) {
            $this->setIsoCode($territoryData->getIsoCode());
        }

        if (null !== $territoryData->getName()) {
            $this->setName($territoryData->getName());
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIsoCode(): ?string
    {
        return $this->isoCode;
    }

    /**
     * @param string $isoCode
     *
     * @return TerritoryData
     */
    public function setIsoCode(string $isoCode): self
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return TerritoryData
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
