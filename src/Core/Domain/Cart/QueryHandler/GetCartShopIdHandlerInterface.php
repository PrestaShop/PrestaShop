<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartShopId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

interface GetCartShopIdHandlerInterface
{
    public function handle(GetCartShopId $query): ShopId;
}
