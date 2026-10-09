<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\Command;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopUrlId;

final class DeleteShopUrlCommand
{
    private readonly ShopUrlId $shopUrlId;

    public function __construct(int $shopUrlId)
    {
        $this->shopUrlId = new ShopUrlId($shopUrlId);
    }

    public function getShopUrlId(): ShopUrlId
    {
        return $this->shopUrlId;
    }
}
