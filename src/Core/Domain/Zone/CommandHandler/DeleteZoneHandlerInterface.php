<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Zone\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Zone\Command\DeleteZoneCommand;

/**
 * Defines contract for DeleteZoneHandler
 */
interface DeleteZoneHandlerInterface
{
    /**
     * @param DeleteZoneCommand $command
     */
    public function handle(DeleteZoneCommand $command): void;
}
