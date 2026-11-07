<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\Command;

final class AddShopGroupCommand
{
    public function __construct(
        private readonly string $name,
        private readonly string $color,
        private readonly bool $shareCustomer,
        private readonly bool $shareStock,
        private readonly bool $shareOrder,
        private readonly bool $active,
    ) {
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
}
