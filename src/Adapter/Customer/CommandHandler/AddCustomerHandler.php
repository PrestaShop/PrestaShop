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
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\AddCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler\AddCustomerHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;

/**
 * Handles command that adds new customer
 *
 * @internal
 */
final class AddCustomerHandler implements AddCustomerHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddCustomerCommand $command)
    {
        $customer = new Customer();

        $this->fillCustomerWithCommandData($customer, $command);

        if (false === $customer->validateFields()) {
            throw new CustomerException('Customer contains invalid field values');
        }

        $this->assertCustomerWithGivenEmailDoesNotExist($command->getEmail());

        $customer->add();

        return new CustomerId((int) $customer->id);
    }

    /**
     * @param string $email
     */
    private function assertCustomerWithGivenEmailDoesNotExist($email)
    {
        $customer = new Customer();
        $customer->getByEmail($email);

        if ($customer->id) {
            throw new CustomerConstraintException(
                sprintf('Customer with email "%s" already exists', $email),
                CustomerConstraintException::DUPLICATE_EMAIL
            );
        }
    }

    /**
     * @param Customer $customer
     * @param AddCustomerCommand $command
     */
    private function fillCustomerWithCommandData(Customer $customer, AddCustomerCommand $command)
    {
        $customer->firstname = $command->getFirstName();
        $customer->lastname = $command->getLastName();
        $customer->email = $command->getEmail();
        $customer->passwd = $command->getPassword();
        $customer->id_default_group = $command->getDefaultGroupId();
        $customer->groupBox = $command->getGroupIds();
        $customer->id_gender = $command->getGenderId();
        $customer->active = $command->isEnabled();
        $customer->optin = $command->isPartnerOffersSubscribed();
        $customer->birthday = $command->getBirthday();
        $customer->id_shop = $command->getShopId();

        // fill b2b customer fields
        $customer->company = $command->getCompanyName();
        $customer->siret = $command->getSiretCode();
        $customer->ape = $command->getApeCode();
        $customer->website = $command->getWebsite();
        $customer->outstanding_allow_amount = $command->getAllowedOutstandingAmount();
        $customer->max_payment_days = $command->getMaxPaymentDays();
        $customer->id_risk = $command->getRiskId();
    }
}
