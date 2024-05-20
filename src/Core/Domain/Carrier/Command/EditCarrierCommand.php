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

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\Command;

use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;

/**
 * Command aim to edit carrier
 */
class EditCarrierCommand
{
    private CarrierId $carrierId;
    private ?string $name;
    private ?array $localizedDelay;
    private ?int $grade;
    private ?string $trackingUrl;
    private ?int $position;
    private ?bool $active;
    private ?string $logoPathName;

    public function __construct(int $carrierId)
    {
        $this->carrierId = new CarrierId($carrierId);
    }

    public function getCarrierId(): CarrierId
    {
        return $this->carrierId;
    }

    public function getName(): ?string
    {
        return $this->name ?? null;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedDelay(): ?array
    {
        return $this->localizedDelay ?? null;
    }

    /**
     * @param string[] $localizedDelay
     */
    public function setLocalizedDelay(array $localizedDelay): self
    {
        $this->localizedDelay = $localizedDelay;

        return $this;
    }

    public function getGrade(): ?int
    {
        return $this->grade ?? null;
    }

    public function setGrade(int $grade): self
    {
        $this->grade = $grade;

        return $this;
    }

    public function getTrackingUrl(): ?string
    {
        return $this->trackingUrl ?? null;
    }

    public function setTrackingUrl(string $trackingUrl): self
    {
        $this->trackingUrl = $trackingUrl;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position ?? null;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active ?? null;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getLogoPathName(): ?string
    {
        return $this->logoPathName ?? null;
    }

    public function setLogoPathName(?string $logoPathName): self
    {
        $this->logoPathName = $logoPathName;

        return $this;
    }
}
