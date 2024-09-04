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

use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\OutOfRangeBehavior;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingMethod;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * Command aim to edit carrier
 */
class EditCarrierCommand
{
    private CarrierId $carrierId;
    private ?string $name;
    /** @var string[] */
    private ?array $localizedDelay;
    private ?int $grade;
    private ?string $trackingUrl;
    private ?int $position;
    private ?bool $active;
    private ?int $max_width;
    private ?int $max_height;
    private ?int $max_depth;
    private ?float $max_weight;
    private ?array $associatedGroupIds;
    private ?string $logoPathName;
    private ?bool $hasAdditionalHandlingFee;
    private ?bool $isFree;
    private ?ShippingMethod $shippingMethod;
    private ?int $idTaxRuleGroup;
    private ?OutOfRangeBehavior $rangeBehavior;
    /** @var int[] */
    private ?array $zones;

    private ?array $associatedShopIds;

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

    public function getMaxWidth(): ?int
    {
        return $this->max_width ?? null;
    }

    public function setMaxWidth(?int $max_width): self
    {
        $this->max_width = $max_width;

        return $this;
    }

    public function getMaxHeight(): ?int
    {
        return $this->max_height ?? null;
    }

    public function setMaxHeight(?int $max_height): self
    {
        $this->max_height = $max_height;

        return $this;
    }

    public function getMaxDepth(): ?int
    {
        return $this->max_depth ?? null;
    }

    public function setMaxDepth(?int $max_depth): self
    {
        $this->max_depth = $max_depth;

        return $this;
    }

    public function getMaxWeight(): ?float
    {
        return $this->max_weight ?? null;
    }

    public function setMaxWeight(?float $max_weight): self
    {
        $this->max_weight = $max_weight;

        return $this;
    }

    public function getAssociatedGroupIds(): ?array
    {
        return $this->associatedGroupIds ?? null;
    }

    public function setAssociatedGroupIds(?array $associatedGroupIds): self
    {
        $this->associatedGroupIds = $associatedGroupIds;

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

    public function hasAdditionalHandlingFee(): ?bool
    {
        return $this->hasAdditionalHandlingFee ?? null;
    }

    public function setAdditionalHandlingFee(bool $hasAdditionalHandlingFee): self
    {
        $this->hasAdditionalHandlingFee = $hasAdditionalHandlingFee;

        return $this;
    }

    public function isFree(): ?bool
    {
        return $this->isFree ?? null;
    }

    public function setIsFree(bool $isFree): self
    {
        $this->isFree = $isFree;

        return $this;
    }

    public function getShippingMethod(): ?ShippingMethod
    {
        return $this->shippingMethod ?? null;
    }

    public function setShippingMethod(int $shippingMethod): self
    {
        $this->shippingMethod = new ShippingMethod($shippingMethod);

        return $this;
    }

    public function getTaxRuleGroupId(): ?int
    {
        return $this->idTaxRuleGroup ?? null;
    }

    public function setIdTaxRuleGroup(int $idTaxRuleGroup): self
    {
        $this->idTaxRuleGroup = $idTaxRuleGroup;

        return $this;
    }

    public function getRangeBehavior(): ?OutOfRangeBehavior
    {
        return $this->rangeBehavior ?? null;
    }

    public function setRangeBehavior(int $rangeBehavior): self
    {
        $this->rangeBehavior = new OutOfRangeBehavior($rangeBehavior);

        return $this;
    }

    public function getAssociatedShopIds(): ?array
    {
        return $this->associatedShopIds ?? null;
    }

    /**
     * @param int[] $associatedShopIds
     *
     * @return void
     *
     * @throws ShopException
     */
    public function setAssociatedShopIds(array $associatedShopIds): void
    {
        $this->associatedShopIds = array_map(fn (int $shopId) => new ShopId($shopId), $associatedShopIds);
    }

    public function getZones(): ?array
    {
        return $this->zones ?? null;
    }

    public function setZones(array $zones): self
    {
        if (count($zones) === 0) {
            throw new CarrierConstraintException(
                'Carrier need to have at least one zone',
                CarrierConstraintException::INVALID_ZONE_MISSING
            );
        }

        $this->zones = $zones;

        return $this;
    }
}
