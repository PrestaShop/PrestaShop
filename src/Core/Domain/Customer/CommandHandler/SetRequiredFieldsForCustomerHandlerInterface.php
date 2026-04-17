<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Customer\Command\SetRequiredFieldsForCustomerCommand;

/**
 * Defines interface for services that handles setting required fields for customer command.
 */
interface SetRequiredFieldsForCustomerHandlerInterface
{
    /**
     * @param SetRequiredFieldsForCustomerCommand $command
     */
    public function handle(SetRequiredFieldsForCustomerCommand $command);
}
