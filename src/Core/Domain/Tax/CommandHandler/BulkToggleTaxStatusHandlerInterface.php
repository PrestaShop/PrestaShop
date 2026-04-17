<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Tax\Command\BulkToggleTaxStatusCommand;

/**
 * Defines contract for BulkToggleTaxStatus
 */
interface BulkToggleTaxStatusHandlerInterface
{
    /**
     * @param BulkToggleTaxStatusCommand $command
     */
    public function handle(BulkToggleTaxStatusCommand $command);
}
