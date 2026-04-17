<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Order\Command\ResendOrderEmailCommand;

interface ResendOrderEmailHandlerInterface
{
    /**
     * @param ResendOrderEmailCommand $command
     */
    public function handle(ResendOrderEmailCommand $command): void;
}
