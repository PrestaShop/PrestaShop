<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\State\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\State\Command\BulkUpdateStateZoneCommand;

/**
 * Defines contract for bulk update of states zone
 */
interface BulkUpdateStateZoneHandlerInterface
{
    /**
     * Handles command which updates zone for multiple states
     *
     * @param BulkUpdateStateZoneCommand $command
     */
    public function handle(BulkUpdateStateZoneCommand $command): void;
}
