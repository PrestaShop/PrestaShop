<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\Command;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopGroupId;

final class DeleteShopGroupCommand
{
    private readonly ShopGroupId $shopGroupId;

    public function __construct(int $shopGroupId)
    {
        $this->shopGroupId = new ShopGroupId($shopGroupId);
    }

    public function getShopGroupId(): ShopGroupId
    {
        return $this->shopGroupId;
    }
}
