<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Supplier\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\DeleteSupplierCommand;

/**
 * Interface DeleteSupplierHandlerInterface defines contract for DeleteSupplierHandler.
 */
interface DeleteSupplierHandlerInterface
{
    /**
     * @param DeleteSupplierCommand $command
     */
    public function handle(DeleteSupplierCommand $command);
}
