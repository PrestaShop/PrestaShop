<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shop\Validate;

use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopGroupRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotUpdateShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopGroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use Shop;

final class ShopValidator extends AbstractObjectModelValidator
{
    public function __construct(
        private readonly ShopRepository $shopRepository,
        private readonly ShopGroupRepository $shopGroupRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly ThemeRepository $themeRepository,
    ) {
    }

    public function validate(Shop $shop, ?ShopId $shopId = null): void
    {
        $this->validateObjectModelProperty($shop, 'name', ShopConstraintException::class, ShopConstraintException::INVALID_NAME);
        $this->validateObjectModelProperty($shop, 'color', ShopConstraintException::class, ShopConstraintException::INVALID_COLOR);

        if (!array_key_exists((string) $shop->theme_name, $this->themeRepository->getList())) {
            throw new ShopConstraintException(sprintf('Theme "%s" does not exist', $shop->theme_name), ShopConstraintException::INVALID_THEME);
        }

        $shopGroupId = new ShopGroupId((int) $shop->id_shop_group);
        $this->shopGroupRepository->assertShopGroupExists($shopGroupId);

        if ($this->shopRepository->isNameUsedInGroup((string) $shop->name, $shopGroupId, $shopId)) {
            throw new ShopConstraintException(
                sprintf('A shop named "%s" already exists in shop group %d', $shop->name, $shopGroupId->getValue()),
                ShopConstraintException::NAME_ALREADY_USED_IN_GROUP
            );
        }
    }

    /**
     * @param int[] $categoryIds
     */
    public function assertRootCategory(CategoryId $rootCategoryId, array $categoryIds): void
    {
        if (!$this->categoryRepository->get($rootCategoryId)->is_root_category) {
            throw new ShopConstraintException(
                sprintf('Category %d is not a root category', $rootCategoryId->getValue()),
                ShopConstraintException::INVALID_ROOT_CATEGORY
            );
        }

        if (!in_array($rootCategoryId->getValue(), $categoryIds, true)) {
            throw new ShopConstraintException(
                sprintf('Root category %d must be associated to the shop', $rootCategoryId->getValue()),
                ShopConstraintException::ROOT_CATEGORY_NOT_ASSOCIATED
            );
        }
    }

    public function assertShopGroupCanChange(ShopGroupId $currentShopGroupId, ShopGroupId $targetShopGroupId): void
    {
        if ($this->shopGroupRepository->isSharingData($currentShopGroupId)) {
            throw new CannotUpdateShopException(
                sprintf('Shop group %d shares data, its shops cannot be moved', $currentShopGroupId->getValue()),
                CannotUpdateShopException::SHOP_GROUP_LOCKED
            );
        }

        if ($this->shopGroupRepository->isSharingData($targetShopGroupId) && $this->shopGroupRepository->hasCustomersOrOrders($targetShopGroupId)) {
            throw new CannotUpdateShopException(
                sprintf('Shop group %d shares data and has customers or orders', $targetShopGroupId->getValue()),
                CannotUpdateShopException::INVALID_TARGET_SHOP_GROUP
            );
        }
    }
}
