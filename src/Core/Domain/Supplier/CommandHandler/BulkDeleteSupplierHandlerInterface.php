<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Supplier\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\BulkDeleteSupplierCommand;

/**
 * Interface BulkDeleteSupplierHandlerInterface defines contract for BulkDeleteSupplierHandler.
 */
interface BulkDeleteSupplierHandlerInterface
{
    /**
     * @param BulkDeleteSupplierCommand $command
     */
    public function handle(BulkDeleteSupplierCommand $command);
}
