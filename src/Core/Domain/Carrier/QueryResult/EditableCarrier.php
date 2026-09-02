<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult;

/**
 * Stores carrier data that's needed for editing.
 */
class EditableCarrier
{
    public function __construct(
        private int $carrierId,
        private string $name,
        private int $grade,
        private string $trackingUrl,
        private int $position,
        private bool $active,
        /** @var string[] $delay */
        private array $delay,
        private int $max_width,
        private int $max_height,
        private int $max_depth,
        private float $max_weight,
        private array $associatedGroupIds,
        private bool $hasAdditionalHandlingFee,
        private bool $isFree,
        private int $shippingMethod,
        private int $idTaxRuleGroup,
        private int $rangeBehavior,
        private array $associatedShopIds,
        private array $zones,
        private ?string $logoPath = null,
        private int $ordersCount = 0,
    ) {
    }

    public function getZones(): array
    {
        return $this->zones;
    }

    public function getCarrierId(): int
    {
        return $this->carrierId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getGrade(): int
    {
        return $this->grade;
    }

    public function getTrackingUrl(): string
    {
        return $this->trackingUrl;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @return string[]
     */
    public function getLocalizedDelay(): array
    {
        return $this->delay;
    }

    public function getLogoPath(): ?string
    {
        return $this->logoPath;
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

    public function hasAdditionalHandlingFee(): bool
    {
        return $this->hasAdditionalHandlingFee;
    }

    public function isFree(): bool
    {
        return $this->isFree;
    }

    public function getShippingMethod(): int
    {
        return $this->shippingMethod;
    }

    public function getIdTaxRuleGroup(): int
    {
        return $this->idTaxRuleGroup;
    }

    public function getRangeBehavior(): int
    {
        return $this->rangeBehavior;
    }

    /**
     * @return int[]
     */
    public function getAssociatedShopIds(): array
    {
        return $this->associatedShopIds;
    }

    public function getOrdersCount(): int
    {
        return $this->ordersCount;
    }
}
