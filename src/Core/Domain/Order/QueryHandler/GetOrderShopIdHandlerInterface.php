<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderShopId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

interface GetOrderShopIdHandlerInterface
{
    public function handle(GetOrderShopId $query): ShopId;
}
