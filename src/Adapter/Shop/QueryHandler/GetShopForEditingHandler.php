<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shop\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopGroupRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\GetShopForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shop\QueryHandler\GetShopForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\QueryResult\EditableShop;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopGroupId;

#[AsQueryHandler]
final class GetShopForEditingHandler implements GetShopForEditingHandlerInterface
{
    public function __construct(
        private readonly ShopRepository $repository,
        private readonly ShopGroupRepository $shopGroupRepository,
        private readonly CategoryRepository $categoryRepository,
    ) {
    }

    public function handle(GetShopForEditing $query): EditableShop
    {
        $shopId = $query->getShopId();
        $shop = $this->repository->get($shopId);
        $shopGroupId = (int) $shop->id_shop_group;

        return new EditableShop(
            $shopId->getValue(),
            (string) $shop->name,
            (string) $shop->color,
            $shopGroupId,
            (int) $shop->id_category,
            $this->categoryRepository->getShopCategoryIds($shopId),
            (string) $shop->theme_name,
            $this->shopGroupRepository->isSharingData(new ShopGroupId($shopGroupId)),
        );
    }
}
