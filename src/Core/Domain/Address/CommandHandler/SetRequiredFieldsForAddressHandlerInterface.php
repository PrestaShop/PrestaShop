<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Address\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Address\Command\SetRequiredFieldsForAddressCommand;

/**
 * Defines interface for services that handles setting required fields for address command.
 */
interface SetRequiredFieldsForAddressHandlerInterface
{
    /**
     * @param SetRequiredFieldsForAddressCommand $command
     */
    public function handle(SetRequiredFieldsForAddressCommand $command);
}
