<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Address\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Address\Command\BulkDeleteAddressCommand;

/**
 * Defines contract for BulkDeleteAddressHandler
 */
interface BulkDeleteAddressHandlerInterface
{
    /**
     * @param BulkDeleteAddressCommand $command
     */
    public function handle(BulkDeleteAddressCommand $command);
}
