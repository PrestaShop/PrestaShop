<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shop\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopGroupRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\GetShopGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shop\QueryHandler\GetShopGroupForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\QueryResult\EditableShopGroup;

#[AsQueryHandler]
final class GetShopGroupForEditingHandler implements GetShopGroupForEditingHandlerInterface
{
    public function __construct(
        private readonly ShopGroupRepository $repository,
        private readonly ShopRepository $shopRepository,
    ) {
    }

    public function handle(GetShopGroupForEditing $query): EditableShopGroup
    {
        $shopGroupId = $query->getShopGroupId();
        $shopGroup = $this->repository->get($shopGroupId);

        return new EditableShopGroup(
            $shopGroupId->getValue(),
            (string) $shopGroup->name,
            (string) $shopGroup->color,
            (bool) $shopGroup->share_customer,
            (bool) $shopGroup->share_stock,
            (bool) $shopGroup->share_order,
            (bool) $shopGroup->active,
            $this->shopRepository->countActiveShops() > 1,
        );
    }
}
