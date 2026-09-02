<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\Command;

use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\OutOfRangeBehavior;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingMethod;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * Command aim to add carrier
 */
class AddCarrierCommand
{
    private ShippingMethod $shippingMethod;
    private OutOfRangeBehavior $rangeBehavior;

    /**
     * @var ShopId[]
     */
    private array $associatedShopIds;

    private ?int $position = null;

    /**
     * @throws CarrierConstraintException
     */
    public function __construct(
        private string $name,
        /** @var string[] $localizedDelay */
        private array $localizedDelay,
        private int $grade,
        private string $trackingUrl,
        private bool $active,
        private array $associatedGroupIds,
        private bool $hasAdditionalHandlingFee,
        private bool $isFree,
        int $shippingMethod,
        int $rangeBehavior,
        /** @var int[] $zones */
        private array $zones,
        array $associatedShopIds,
        private int $max_width = 0,
        private int $max_height = 0,
        private int $max_depth = 0,
        private float $max_weight = 0,
        private ?string $logoPathName = null
    ) {
        $this->assertCarrierHasAtLeastOneZone($zones);
        $this->shippingMethod = new ShippingMethod($shippingMethod);
        $this->rangeBehavior = new OutOfRangeBehavior($rangeBehavior);
        $this->associatedShopIds = array_map(fn (int $shopId) => new ShopId($shopId), $associatedShopIds);
    }

    /**
     * @param int[] $zones
     */
    private function assertCarrierHasAtLeastOneZone(array $zones): void
    {
        if (count($zones) === 0) {
            throw new CarrierConstraintException(
                'Carrier need to have at least one zone',
                CarrierConstraintException::INVALID_ZONE_MISSING
            );
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    /** @return string[] */
    public function getLocalizedDelay(): array
    {
        return $this->localizedDelay;
    }

    public function getGrade(): int
    {
        return $this->grade;
    }

    public function getTrackingUrl(): string
    {
        return $this->trackingUrl;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function getMaxWidth(): int
    {
        return $this->max_width;
    }

    public function getMaxHeight(): int
    {
        return $this->max_height;
    }

    public function getMaxDepth(): int
    {
        return $this->max_depth;
    }

    public function getMaxWeight(): float
    {
        return $this->max_weight;
    }

    public function getAssociatedGroupIds(): array
    {
        return $this->associatedGroupIds;
    }

    public function getLogoPathName(): ?string
    {
        return $this->logoPathName;
    }

    public function hasAdditionalHandlingFee(): bool
    {
        return $this->hasAdditionalHandlingFee;
    }

    public function isFree(): bool
    {
        return $this->isFree;
    }

    public function getShippingMethod(): ShippingMethod
    {
        return $this->shippingMethod;
    }

    public function getRangeBehavior(): OutOfRangeBehavior
    {
        return $this->rangeBehavior;
    }

    /**
     * @return ShopId[]
     */
    public function getAssociatedShopIds(): array
    {
        return $this->associatedShopIds;
    }

    /**
     * @return int[]
     */
    public function getZones(): array
    {
        return $this->zones;
    }
}
