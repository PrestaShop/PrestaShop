<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\Command;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopUrlId;

final class EditShopUrlCommand
{
    private readonly ShopUrlId $shopUrlId;

    private ?ShopId $shopId = null;

    private ?string $domain = null;

    private ?string $domainSsl = null;

    private ?string $physicalUri = null;

    private ?string $virtualUri = null;

    private ?bool $main = null;

    private ?bool $active = null;

    public function __construct(int $shopUrlId)
    {
        $this->shopUrlId = new ShopUrlId($shopUrlId);
    }

    public function getShopUrlId(): ShopUrlId
    {
        return $this->shopUrlId;
    }

    public function getShopId(): ?ShopId
    {
        return $this->shopId;
    }

    public function setShopId(int $shopId): self
    {
        $this->shopId = new ShopId($shopId);

        return $this;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    public function getDomainSsl(): ?string
    {
        return $this->domainSsl;
    }

    public function setDomainSsl(string $domainSsl): self
    {
        $this->domainSsl = $domainSsl;

        return $this;
    }

    public function getPhysicalUri(): ?string
    {
        return $this->physicalUri;
    }

    public function setPhysicalUri(string $physicalUri): self
    {
        $this->physicalUri = $physicalUri;

        return $this;
    }

    public function getVirtualUri(): ?string
    {
        return $this->virtualUri;
    }

    public function setVirtualUri(string $virtualUri): self
    {
        $this->virtualUri = $virtualUri;

        return $this;
    }

    public function isMain(): ?bool
    {
        return $this->main;
    }

    public function setMain(bool $main): self
    {
        $this->main = $main;

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
