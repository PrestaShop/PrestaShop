<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\GetShopGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shop\QueryResult\EditableShopGroup;

final class ShopGroupFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $queryBus,
    ) {
    }

    public function getData($id): array
    {
        /** @var EditableShopGroup $shopGroup */
        $shopGroup = $this->queryBus->handle(new GetShopGroupForEditing((int) $id));

        return [
            'name' => $shopGroup->getName(),
            'color' => $shopGroup->getColor(),
            'share_customer' => $shopGroup->isShareCustomer(),
            'share_stock' => $shopGroup->isShareStock(),
            'share_order' => $shopGroup->isShareOrder(),
            'active' => $shopGroup->isActive(),
        ];
    }

    public function getDefaultData(): array
    {
        return [
            'share_customer' => false,
            'share_stock' => false,
            'share_order' => false,
            'active' => true,
        ];
    }
}
