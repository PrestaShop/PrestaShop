<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Shop\Query\GetShopTree;

interface GetShopTreeHandlerInterface
{
    /**
     * @return array<int, array{id: int, name: string, shops: array<int, array{id: int, name: string, urls: list<array{id: int, url: string}>}>}>
     */
    public function handle(GetShopTree $query): array;
}
