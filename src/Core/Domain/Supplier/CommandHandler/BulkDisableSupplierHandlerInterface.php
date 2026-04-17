<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Supplier\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\BulkDisableSupplierCommand;

/**
 * Interface BulkDisableSupplierHandlerInterface defines contract for BulkDisableSupplierHandler.
 */
interface BulkDisableSupplierHandlerInterface
{
    /**
     * @param BulkDisableSupplierCommand $command
     */
    public function handle(BulkDisableSupplierCommand $command);
}
