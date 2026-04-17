<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Order\Command\SendProcessOrderEmailCommand;

/**
 * Interface for handling SendProcessOrderEmail command
 */
interface SendProcessOrderEmailHandlerInterface
{
    /**
     * @param SendProcessOrderEmailCommand $command
     */
    public function handle(SendProcessOrderEmailCommand $command): void;
}
