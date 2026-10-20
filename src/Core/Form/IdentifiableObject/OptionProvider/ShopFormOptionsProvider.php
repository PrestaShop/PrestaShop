<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\OptionProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\GetShopForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\GetShopTree;
use PrestaShop\PrestaShop\Core\Domain\Shop\QueryResult\EditableShop;

final class ShopFormOptionsProvider implements FormOptionsProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $queryBus,
    ) {
    }

    public function getOptions(int $id, array $data): array
    {
        /** @var EditableShop $shop */
        $shop = $this->queryBus->handle(new GetShopForEditing($id));

        return [
            'is_edition' => true,
            'shop_group_locked' => $shop->isShopGroupLocked(),
        ];
    }

    public function getDefaultOptions(array $data): array
    {
        $rootCategoryIds = [];
        foreach ($this->queryBus->handle(new GetShopTree()) as $group) {
            foreach ($group['shops'] as $shop) {
                $rootCategoryIds[$shop['id']] = $shop['root_category_id'];
            }
        }

        return ['source_shop_root_category_ids' => $rootCategoryIds];
    }
}
