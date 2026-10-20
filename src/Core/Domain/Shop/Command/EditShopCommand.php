<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\Command;

use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopGroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

final class EditShopCommand
{
    private readonly ShopId $shopId;

    private ?string $name = null;

    private ?string $color = null;

    private ?ShopGroupId $shopGroupId = null;

    private ?CategoryId $rootCategoryId = null;

    /**
     * @var CategoryId[]|null
     */
    private ?array $categoryIds = null;

    private ?string $themeName = null;

    public function __construct(int $shopId)
    {
        $this->shopId = new ShopId($shopId);
    }

    public function getShopId(): ShopId
    {
        return $this->shopId;
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

    public function getShopGroupId(): ?ShopGroupId
    {
        return $this->shopGroupId;
    }

    public function setShopGroupId(int $shopGroupId): self
    {
        $this->shopGroupId = new ShopGroupId($shopGroupId);

        return $this;
    }

    public function getRootCategoryId(): ?CategoryId
    {
        return $this->rootCategoryId;
    }

    public function setRootCategoryId(int $rootCategoryId): self
    {
        $this->rootCategoryId = new CategoryId($rootCategoryId);

        return $this;
    }

    /**
     * @return CategoryId[]|null
     */
    public function getCategoryIds(): ?array
    {
        return $this->categoryIds;
    }

    /**
     * @param int[] $categoryIds
     */
    public function setCategoryIds(array $categoryIds): self
    {
        $this->categoryIds = array_map(static fn (int $categoryId): CategoryId => new CategoryId($categoryId), $categoryIds);

        return $this;
    }

    public function getThemeName(): ?string
    {
        return $this->themeName;
    }

    public function setThemeName(string $themeName): self
    {
        $this->themeName = $themeName;

        return $this;
    }
}
