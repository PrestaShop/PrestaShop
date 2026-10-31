<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shop\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Adapter\Shop\ShopCategoriesUpdater;
use PrestaShop\PrestaShop\Adapter\Shop\Validate\ShopValidator;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\EditShopCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\CommandHandler\EditShopHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotUpdateShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopGroupId;
use Shop;

#[AsCommandHandler]
final class EditShopHandler implements EditShopHandlerInterface
{
    public function __construct(
        private readonly ShopRepository $repository,
        private readonly CategoryRepository $categoryRepository,
        private readonly ShopValidator $validator,
        private readonly ShopCategoriesUpdater $categoriesUpdater,
    ) {
    }

    public function handle(EditShopCommand $command): void
    {
        $shopId = $command->getShopId();
        $shop = $this->repository->get($shopId);
        $propertiesToUpdate = [];

        if (null !== $command->getName()) {
            $shop->name = $command->getName();
            $propertiesToUpdate[] = 'name';
        }
        if (null !== $command->getColor()) {
            $shop->color = $command->getColor();
            $propertiesToUpdate[] = 'color';
        }
        if (null !== $command->getThemeName()) {
            $shop->theme_name = $command->getThemeName();
            $propertiesToUpdate[] = 'theme_name';
        }
        if (null !== $command->getShopGroupId() && $command->getShopGroupId()->getValue() !== (int) $shop->id_shop_group) {
            $this->validator->assertShopGroupCanChange(new ShopGroupId((int) $shop->id_shop_group), $command->getShopGroupId());
            $shop->id_shop_group = $command->getShopGroupId()->getValue();
            $propertiesToUpdate[] = 'id_shop_group';
        }
        if (null !== $command->getRootCategoryId()) {
            $shop->id_category = $command->getRootCategoryId()->getValue();
            $propertiesToUpdate[] = 'id_category';
        }

        $categoryIds = null !== $command->getCategoryIds()
            ? array_map(static fn (CategoryId $categoryId): int => $categoryId->getValue(), $command->getCategoryIds())
            : null;
        if (null !== $command->getRootCategoryId() || null !== $categoryIds) {
            $this->validator->assertRootCategory(
                new CategoryId((int) $shop->id_category),
                $categoryIds ?? $this->categoryRepository->getShopCategoryIds($shopId)
            );
        }

        $this->validator->validate($shop, $shopId);
        $this->repository->partialUpdate($shop, $propertiesToUpdate);
        Shop::resetStaticCache();

        if (null !== $categoryIds && !$this->categoriesUpdater->associateCategories($shopId, $categoryIds)) {
            throw new CannotUpdateShopException(
                sprintf('Cannot associate categories to shop %d', $shopId->getValue()),
                CannotUpdateShopException::FAILED_UPDATE
            );
        }
        if (null !== $command->getRootCategoryId()) {
            $this->categoriesUpdater->setHomeCategory($shopId, $command->getRootCategoryId());
        }
    }
}
