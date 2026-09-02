<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Supplier\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\AddSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;

/**
 * Defines contract for AddSupplierHandler
 */
interface AddSupplierHandlerInterface
{
    /**
     * @param AddSupplierCommand $command
     *
     * @return SupplierId
     */
    public function handle(AddSupplierCommand $command);
}
