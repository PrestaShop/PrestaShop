<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Context;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * This context service gives access to all contextual data related to shop.
 */
class ShopContext
{
    public function __construct(
        protected readonly ShopConstraint $shopConstraint,
        protected int $id,
        protected string $name,
        protected int $shopGroupId,
        protected int $categoryId,
        protected string $themeName,
        protected string $color,
        protected string $physicalUri,
        protected string $virtualUri,
        protected string $domain,
        protected string $domainSSL,
        protected bool $active,
        protected bool $secured,
        protected array $associatedShopIds,
        protected bool $isMultiShopEnabled,
        protected bool $isMultiShopUsed,
        protected bool $groupSharingStocks,
        protected bool $groupSharingCustomers,
        protected bool $groupSharingOrders,
    ) {
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }

    public function isAllShopContext(): bool
    {
        return $this->shopConstraint->forAllShops();
    }

    public function isShopGroupContext(): bool
    {
        return $this->shopConstraint->isShopGroupContext();
    }

    public function isSingleShopContext(): bool
    {
        return $this->shopConstraint->isSingleShopContext();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShopGroupId(): int
    {
        return $this->shopGroupId;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function getThemeName(): string
    {
        return $this->themeName;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getPhysicalUri(): string
    {
        return $this->physicalUri;
    }

    public function getVirtualUri(): string
    {
        return $this->virtualUri;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getDomainSSL(): string
    {
        return $this->domainSSL;
    }

    public function getBaseURI(): string
    {
        return $this->physicalUri . $this->virtualUri;
    }

    public function getBaseURL(): string
    {
        if ($this->secured) {
            $url = 'https://' . $this->domainSSL;
        } else {
            $url = 'http://' . $this->domain;
        }

        return $url . $this->getBaseURI();
    }

    public function hasGroupSharingStocks(): bool
    {
        return $this->groupSharingStocks;
    }

    public function hasGroupSharingCustomers(): bool
    {
        return $this->groupSharingCustomers;
    }

    public function hasGroupSharingOrders(): bool
    {
        return $this->groupSharingOrders;
    }

    /**
     * @return int[]
     */
    public function getAssociatedShopIds(): array
    {
        return $this->associatedShopIds;
    }

    public function isMultiShopEnabled(): bool
    {
        return $this->isMultiShopEnabled;
    }

    public function isMultiShopUsed(): bool
    {
        return $this->isMultiShopUsed;
    }
}
