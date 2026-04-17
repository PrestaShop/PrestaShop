<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Supplier\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\EditSupplierCommand;

/**
 * Defines contract for EditSupplierHandler
 */
interface EditSupplierHandlerInterface
{
    /**
     * @param EditSupplierCommand $command
     */
    public function handle(EditSupplierCommand $command);
}
