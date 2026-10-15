<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartOrderMessageCommand;

/**
 * Interface for service that stores the order message of a cart.
 */
interface UpdateCartOrderMessageHandlerInterface
{
    public function handle(UpdateCartOrderMessageCommand $command): void;
}
