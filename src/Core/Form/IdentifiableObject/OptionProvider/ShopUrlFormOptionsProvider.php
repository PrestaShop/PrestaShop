<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\OptionProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\GetShopTree;

final class ShopUrlFormOptionsProvider implements FormOptionsProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $queryBus,
    ) {
    }

    public function getOptions(int $id, array $data): array
    {
        return [
            'shop_ids_with_url' => $this->getShopIdsWithUrl(),
            'main_url_shop_id' => $data['main'] ? (int) $data['shop_id'] : null,
        ];
    }

    public function getDefaultOptions(array $data): array
    {
        return [
            'shop_ids_with_url' => $this->getShopIdsWithUrl(),
        ];
    }

    /**
     * @return int[]
     */
    private function getShopIdsWithUrl(): array
    {
        $shopIds = [];
        foreach ($this->queryBus->handle(new GetShopTree()) as $group) {
            foreach ($group['shops'] as $shop) {
                if ([] !== $shop['urls']) {
                    $shopIds[] = $shop['id'];
                }
            }
        }

        return $shopIds;
    }
}
