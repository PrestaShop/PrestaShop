<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Address\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Address\Command\DeleteAddressCommand;

/**
 * Defines contract for DeleteAddressHandler
 */
interface DeleteAddressHandlerInterface
{
    /**
     * @param DeleteAddressCommand $command
     */
    public function handle(DeleteAddressCommand $command);
}
