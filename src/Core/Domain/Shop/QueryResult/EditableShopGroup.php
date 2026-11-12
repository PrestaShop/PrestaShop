<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\QueryResult;

final class EditableShopGroup
{
    public function __construct(
        private readonly int $shopGroupId,
        private readonly string $name,
        private readonly string $color,
        private readonly bool $shareCustomer,
        private readonly bool $shareStock,
        private readonly bool $shareOrder,
        private readonly bool $active,
        private readonly bool $sharingOptionsLocked,
    ) {
    }

    public function getShopGroupId(): int
    {
        return $this->shopGroupId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function isShareCustomer(): bool
    {
        return $this->shareCustomer;
    }

    public function isShareStock(): bool
    {
        return $this->shareStock;
    }

    public function isShareOrder(): bool
    {
        return $this->shareOrder;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function areSharingOptionsLocked(): bool
    {
        return $this->sharingOptionsLocked;
    }
}
