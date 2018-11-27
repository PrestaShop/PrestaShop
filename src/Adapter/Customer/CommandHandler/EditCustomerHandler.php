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
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;

/**
 * Handles command which edits customer's data
 */
final class EditCustomerHandler implements EditCustomerHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(EditCustomerCommand $command)
    {
        $customerId = $command->getCustomerId();
        $customer = new Customer($customerId->getValue());

        if ($customer->id !== $customerId->getValue()) {
            throw new CustomerNotFoundException(
                $customerId,
                sprintf('Customer with id "%s" was not found', $customerId->getValue())
            );
        }

        $this->updateCustomerWithCommandData($customer, $command);

        if (false === $customer->validateFields(false)) {
            throw new CustomerException('Customer contains invalid field values');
        }

        $customer->update();
    }

    /**
     * @param Customer $customer
     * @param EditCustomerCommand $command
     */
    private function updateCustomerWithCommandData(Customer $customer, EditCustomerCommand $command)
    {
        if (null !== $command->getGenderId()) {
            $customer->id_gender = $command->getGenderId();
        }

        if (null !== $command->getFirstName()) {
            $customer->firstname = $command->getFirstName();
        }

        if (null !== $command->getLastName()) {
            $customer->lastname = $command->getLastName();
        }

        if (null !== $command->getEmail()) {
            $customer->email = $command->getEmail();
        }

        if (null !== $command->getPassword()) {
            $customer->passwd = $command->getPassword();
        }

        if (null !== $command->getBirthday()) {
            $customer->birthday = $command->getBirthday();
        }

        if (null !== $command->isEnabled()) {
            $customer->active = $command->isEnabled();
        }

        if (null !== $command->isPartnerOffersSubscribed()) {
            $customer->optin = $command->isPartnerOffersSubscribed();
        }

        if (null !== $command->getGroupIds()) {
            $customer->groupBox = $command->getGroupIds();
        }

        if (null !== $command->getDefaultGroupId()) {
            $customer->id_default_group = $command->getDefaultGroupId();
        }
    }
}
