<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Zone\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Zone\Command\EditZoneCommand;

/**
 * Defines contract for EditZoneHandler
 */
interface EditZoneHandlerInterface
{
    /**
     * @param EditZoneCommand $command
     */
    public function handle(EditZoneCommand $command): void;
}
