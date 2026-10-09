<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Shop\Command\AddShopUrlCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopUrlId;

interface AddShopUrlHandlerInterface
{
    public function handle(AddShopUrlCommand $command): ShopUrlId;
}
