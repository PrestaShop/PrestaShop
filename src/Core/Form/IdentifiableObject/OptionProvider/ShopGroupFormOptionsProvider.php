<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\OptionProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\GetShopGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shop\QueryResult\EditableShopGroup;

final class ShopGroupFormOptionsProvider implements FormOptionsProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $queryBus,
    ) {
    }

    public function getOptions(int $id, array $data): array
    {
        /** @var EditableShopGroup $shopGroup */
        $shopGroup = $this->queryBus->handle(new GetShopGroupForEditing($id));

        return ['sharing_options_locked' => $shopGroup->areSharingOptionsLocked()];
    }

    public function getDefaultOptions(array $data): array
    {
        return [];
    }
}
