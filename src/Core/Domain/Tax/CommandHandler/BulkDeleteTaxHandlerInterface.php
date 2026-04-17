<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Tax\Command\BulkDeleteTaxCommand;

/**
 * Defines contract for BulkDeleteTaxHandler
 */
interface BulkDeleteTaxHandlerInterface
{
    /**
     * @param BulkDeleteTaxCommand $command
     */
    public function handle(BulkDeleteTaxCommand $command);
}
