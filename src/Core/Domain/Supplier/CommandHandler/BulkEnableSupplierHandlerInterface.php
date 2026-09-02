<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Supplier\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\BulkEnableSupplierCommand;

/**
 * Interface BulkEnableSupplierHandlerInterface defines contract for BulkEnableSupplierHandler.
 */
interface BulkEnableSupplierHandlerInterface
{
    /**
     * @param BulkEnableSupplierCommand $command
     */
    public function handle(BulkEnableSupplierCommand $command);
}
