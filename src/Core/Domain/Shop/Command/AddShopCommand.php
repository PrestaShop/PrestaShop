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

final class AddShopCommand
{
    private readonly ShopGroupId $shopGroupId;

    private readonly CategoryId $rootCategoryId;

    /**
     * @var CategoryId[]
     */
    private readonly array $categoryIds;

    private readonly ?ShopId $sourceShopId;

    /**
     * @param int[] $categoryIds
     * @param string[] $dataToImport Shop association tables and Module<Name> keys copied from the source shop
     */
    public function __construct(
        private readonly string $name,
        private readonly string $color,
        int $shopGroupId,
        int $rootCategoryId,
        array $categoryIds,
        private readonly string $themeName,
        ?int $sourceShopId = null,
        private readonly array $dataToImport = [],
    ) {
        $this->shopGroupId = new ShopGroupId($shopGroupId);
        $this->rootCategoryId = new CategoryId($rootCategoryId);
        $this->categoryIds = array_map(static fn (int $categoryId): CategoryId => new CategoryId($categoryId), $categoryIds);
        $this->sourceShopId = null !== $sourceShopId ? new ShopId($sourceShopId) : null;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getShopGroupId(): ShopGroupId
    {
        return $this->shopGroupId;
    }

    public function getRootCategoryId(): CategoryId
    {
        return $this->rootCategoryId;
    }

    /**
     * @return CategoryId[]
     */
    public function getCategoryIds(): array
    {
        return $this->categoryIds;
    }

    public function getThemeName(): string
    {
        return $this->themeName;
    }

    public function getSourceShopId(): ?ShopId
    {
        return $this->sourceShopId;
    }

    /**
     * @return string[]
     */
    public function getDataToImport(): array
    {
        return $this->dataToImport;
    }
}
