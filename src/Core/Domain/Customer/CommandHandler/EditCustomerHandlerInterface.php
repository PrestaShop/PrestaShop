<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Customer\Command\EditCustomerCommand;

/**
 * Interface for service that handles customer editing command
 */
interface EditCustomerHandlerInterface
{
    /**
     * @param EditCustomerCommand $command
     */
    public function handle(EditCustomerCommand $command);
}
