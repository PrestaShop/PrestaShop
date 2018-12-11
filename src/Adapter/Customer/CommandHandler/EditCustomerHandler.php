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
use PrestaShop\PrestaShop\Core\Crypto\Hashing;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\EditCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler\EditCustomerHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Email;

/**
 * Handles command which edits customer's data
 */
final class EditCustomerHandler implements EditCustomerHandlerInterface
{
    /**
     * @var Hashing
     */
    private $hashing;

    /**
     * @var string Value of legacy _COOKIE_KEY_
     */
    private $legacyCookieKey;

    /**
     * @param Hashing $hashing
     * @param string $legacyCookieKey
     */
    public function __construct(Hashing $hashing, $legacyCookieKey)
    {
        $this->hashing = $hashing;
        $this->legacyCookieKey = $legacyCookieKey;
    }

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

        $this->assertCustomerWithUpdatedEmailDoesNotExist($customer, $command);

        $this->updateCustomerWithCommandData($customer, $command);

        if (false === $customer->validateFields(false)) {
            throw new CustomerException('Customer contains invalid field values');
        }

        $customer->update();

        return $customerId;
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
            $customer->firstname = $command->getFirstName()->getValue();
        }

        if (null !== $command->getLastName()) {
            $customer->lastname = $command->getLastName()->getValue();
        }

        if (null !== $command->getEmail()) {
            $customer->email = $command->getEmail()->getValue();
        }

        if (null !== $command->getPassword()) {
            $hashedPassword = $this->hashing->hash(
                $command->getPassword()->getValue(),
                $this->legacyCookieKey
            );

            $customer->passwd = $hashedPassword;
        }

        if (null !== $command->getBirthday()) {
            $customer->birthday = $command->getBirthday()->format('Y-m-d');
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

        $this->updateCustomerB2bData($customer, $command);
    }

    /**
     * @param Customer $customer
     * @param EditCustomerCommand $command
     */
    private function updateCustomerB2bData(Customer $customer, EditCustomerCommand $command)
    {
        if (null !== $command->getCompanyName()) {
            $customer->company = $command->getCompanyName();
        }

        if (null !== $command->getSiretCode()) {
            $customer->siret = $command->getSiretCode();
        }

        if (null !== $command->getApeCode()) {
            $customer->ape = $command->getApeCode();
        }

        if (null !== $command->getWebsite()) {
            $customer->website = $command->getWebsite();
        }

        if (null !== $command->getAllowedOutstandingAmount()) {
            $customer->outstanding_allow_amount = $command->getAllowedOutstandingAmount();
        }

        if (null !== $command->getMaxPaymentDays()) {
            $customer->max_payment_days = $command->getMaxPaymentDays();
        }

        if (null !== $command->getRiskId()) {
            $customer->id_risk = $command->getRiskId();
        }
    }

    /**
     * @param Customer $customer
     * @param EditCustomerCommand $command
     */
    private function assertCustomerWithUpdatedEmailDoesNotExist(Customer $customer, EditCustomerCommand $command)
    {
        // if email is not being updated
        // then assertion is not needed
        if (null === $command->getEmail()) {
            return;
        }

        if ($command->getEmail()->isEqualTo(new Email($customer->email))) {
            return;
        }

        $customerByEmail = new Customer();
        $customerByEmail->getByEmail($command->getEmail()->getValue());

        if ($customerByEmail->id) {
            throw new CustomerConstraintException(
                sprintf('Customer with email "%s" already exists', $command->getEmail()->getValue()),
                CustomerConstraintException::DUPLICATE_EMAIL
            );
        }
    }
}
