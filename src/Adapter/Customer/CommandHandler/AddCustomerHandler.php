<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Customer\CommandHandler;

use Customer;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Crypto\Hashing;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\AddCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler\AddCustomerHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerDefaultGroupAccessException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\DuplicateCustomerEmailException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\RequiredField;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

/**
 * Handles command that adds new customer
 *
 * @internal
 */
#[AsCommandHandler]
final class AddCustomerHandler extends AbstractCustomerHandler implements AddCustomerHandlerInterface
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
    public function handle(AddCustomerCommand $command)
    {
        // Prepare new legacy customer object
        $customer = new Customer();

        // Load it up with the data from the command
        $this->fillCustomerWithCommandData($customer, $command);

        // validateFieldsRequiredDatabase() below is using $_POST to check if required fields are set
        $_POST[RequiredField::PARTNER_OFFERS] = $command->isPartnerOffersSubscribed();

        $this->assertRequiredFieldsAreNotMissing($customer);

        if (false === $customer->validateFields(false)) {
            throw new CustomerException('Customer contains invalid field values');
        }

        // If dealing with a registered customer, we need to check if the email does not exist.
        // Two guests with the same email can co-exist, two registered customers can not.
        if (!$command->isGuest()) {
            $this->assertCustomerWithGivenEmailDoesNotExist($command->getEmail());
        }

        // Check if provided groups contain the correct data (the default group must be in group list)
        $this->assertCustomerCanAccessDefaultGroup($command);

        $customer->add();

        return new CustomerId((int) $customer->id);
    }

    /**
     * Checks if a registered customer (is_guest = false) doesn't already exist in the database.
     * In that case, we refuse the creation, we cannot have two registered customers with the same email.
     *
     * @param Email $email
     */
    private function assertCustomerWithGivenEmailDoesNotExist(Email $email)
    {
        $customer = new Customer();
        $customer->getByEmail($email->getValue());

        if ($customer->id) {
            throw new DuplicateCustomerEmailException(
                $email, sprintf('Registered customer with email "%s" already exists', $email->getValue()),
                DuplicateCustomerEmailException::ADD
            );
        }
    }

    /**
     * @param Customer $customer
     * @param AddCustomerCommand $command
     */
    private function fillCustomerWithCommandData(Customer $customer, AddCustomerCommand $command)
    {
        $apeCode = null !== $command->getApeCode() ?
            $command->getApeCode()->getValue() :
            null;

        $hashedPassword = $this->hashing->hash(
            $command->getPassword()->getValue(),
            $this->legacyCookieKey
        );

        $customer->firstname = $command->getFirstName()->getValue();
        $customer->lastname = $command->getLastName()->getValue();
        $customer->email = $command->getEmail()->getValue();
        $customer->passwd = $hashedPassword;
        $customer->id_default_group = $command->getDefaultGroupId();
        $customer->groupBox = $command->getGroupIds();
        $customer->id_gender = $command->getGenderId();
        $customer->active = $command->isEnabled();
        $customer->optin = $command->isPartnerOffersSubscribed();
        $customer->birthday = $command->getBirthday()->getValue();
        $customer->id_shop = $command->getShopId();
        $customer->is_guest = $command->isGuest();

        // Fill B2B customer fields
        $customer->company = $command->getCompanyName();
        $customer->siret = $command->getSiretCode();
        $customer->ape = $apeCode;
        $customer->website = $command->getWebsite();
        $customer->outstanding_allow_amount = $command->getAllowedOutstandingAmount();
        $customer->max_payment_days = $command->getMaxPaymentDays();
        $customer->id_risk = $command->getRiskId();
    }

    /**
     * Checks if the default group of the customer was provided in the group list
     *
     * @param AddCustomerCommand $command
     */
    private function assertCustomerCanAccessDefaultGroup(AddCustomerCommand $command)
    {
        if (!in_array($command->getDefaultGroupId(), $command->getGroupIds())) {
            throw new CustomerDefaultGroupAccessException(sprintf('Customer default group with id "%s" must be in access groups', $command->getDefaultGroupId()));
        }
    }
}
