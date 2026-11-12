<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\Command;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

final class DeleteShopCommand
{
    private readonly ShopId $shopId;

    public function __construct(int $shopId)
    {
        $this->shopId = new ShopId($shopId);
    }

    public function getShopId(): ShopId
    {
        return $this->shopId;
    }
}
