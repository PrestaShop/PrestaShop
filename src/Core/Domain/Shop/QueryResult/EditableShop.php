<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\QueryResult;

final class EditableShop
{
    /**
     * @param int[] $categoryIds
     */
    public function __construct(
        private readonly int $shopId,
        private readonly string $name,
        private readonly string $color,
        private readonly int $shopGroupId,
        private readonly int $rootCategoryId,
        private readonly array $categoryIds,
        private readonly string $themeName,
        private readonly bool $shopGroupLocked,
    ) {
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getShopGroupId(): int
    {
        return $this->shopGroupId;
    }

    public function getRootCategoryId(): int
    {
        return $this->rootCategoryId;
    }

    /**
     * @return int[]
     */
    public function getCategoryIds(): array
    {
        return $this->categoryIds;
    }

    public function getThemeName(): string
    {
        return $this->themeName;
    }

    public function isShopGroupLocked(): bool
    {
        return $this->shopGroupLocked;
    }
}
