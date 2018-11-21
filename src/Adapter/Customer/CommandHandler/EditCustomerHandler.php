<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Customer\CommandHandler;

use Customer;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\EditCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler\EditCustomerHandlerInterface;

/**
 * Handles commands which edits given customer with provided data.
 *
 * @internal
 */
final class EditCustomerHandler extends AbstractCustomerHandler implements EditCustomerHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(EditCustomerCommand $command)
    {
        $customerId = $command->getCustomerId();
        $customer = new Customer($customerId->getValue());

        $this->assertCustomerWasFound($customerId, $customer);

        $this->updateCustomerWithCommandData($customer, $command);

        $customer->update();
    }

    /**
     * @param Customer $customer
     * @param EditCustomerCommand $command
     */
    private function updateCustomerWithCommandData(Customer $customer, EditCustomerCommand $command)
    {
        if (is_bool($command->isEnabled())) {
            $customer->active = $command->isEnabled();
        }

        if (is_bool($command->isNewsletterSubscribed())) {
            $customer->newsletter = $command->isNewsletterSubscribed();
        }

        if (is_bool($command->isPartnerOfferSubscribed())) {
            $customer->optin = $command->isPartnerOfferSubscribed();
        }
    }
}
