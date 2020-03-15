<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Customer\CommandHandler;

use Customer;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\BulkDeleteCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler\BulkDeleteCustomerHandlerInterface;

/**
 * Handles command that deletes customers in bulk action.
 *
 * @internal
 */
final class BulkDeleteCustomerHandler extends AbstractCustomerHandler implements BulkDeleteCustomerHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteCustomerCommand $command)
    {
        foreach ($command->getCustomerIds() as $customerId) {
            $customer = new Customer($customerId->getValue());

            $this->assertCustomerWasFound($customerId, $customer);

            if ($command->getDeleteMethod()->isAllowedToRegisterAfterDelete()) {
                $customer->delete();

                continue;
            }

            $customer->deleted = 1;
            $customer->update();
        }
    }
}
