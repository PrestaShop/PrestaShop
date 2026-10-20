<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shop;

use Category;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

final class ShopCategoriesUpdater
{
    public function __construct(
        private readonly ShopConfigurationInterface $configuration,
    ) {
    }

    /**
     * @param int[] $categoryIds
     */
    public function associateCategories(ShopId $shopId, array $categoryIds): bool
    {
        $rootCategoryId = (int) $this->configuration->get('PS_ROOT_CATEGORY', null, ShopConstraint::allShops());

        return Category::updateFromShop(array_unique(array_merge([$rootCategoryId], $categoryIds)), $shopId->getValue());
    }

    public function setHomeCategory(ShopId $shopId, CategoryId $rootCategoryId): void
    {
        $shopConstraint = ShopConstraint::shop($shopId->getValue());

        if ((int) $this->configuration->get('PS_HOME_CATEGORY', null, $shopConstraint) !== $rootCategoryId->getValue()) {
            $this->configuration->set('PS_HOME_CATEGORY', $rootCategoryId->getValue(), $shopConstraint);
        }
    }
}
