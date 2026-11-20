<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\Command;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopGroupId;

final class EditShopGroupCommand
{
    private readonly ShopGroupId $shopGroupId;

    private ?string $name = null;

    private ?string $color = null;

    private ?bool $shareCustomer = null;

    private ?bool $shareStock = null;

    private ?bool $shareOrder = null;

    private ?bool $active = null;

    public function __construct(int $shopGroupId)
    {
        $this->shopGroupId = new ShopGroupId($shopGroupId);
    }

    public function getShopGroupId(): ShopGroupId
    {
        return $this->shopGroupId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function isShareCustomer(): ?bool
    {
        return $this->shareCustomer;
    }

    public function setShareCustomer(bool $shareCustomer): self
    {
        $this->shareCustomer = $shareCustomer;

        return $this;
    }

    public function isShareStock(): ?bool
    {
        return $this->shareStock;
    }

    public function setShareStock(bool $shareStock): self
    {
        $this->shareStock = $shareStock;

        return $this;
    }

    public function isShareOrder(): ?bool
    {
        return $this->shareOrder;
    }

    public function setShareOrder(bool $shareOrder): self
    {
        $this->shareOrder = $shareOrder;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}
