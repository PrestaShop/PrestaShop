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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Customer\CommandHandler;

use Customer;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\TransformGuestToCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler\TransformGuestToCustomerHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerTransformationException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;

/**
 * Handles guest to customer transformation command
 *
 * @internal
 */
final class TransformGuestToCustomerHandler implements TransformGuestToCustomerHandlerInterface
{
    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @param int $contextLangId
     */
    public function __construct($contextLangId)
    {
        $this->contextLangId = $contextLangId;
    }

    /**
     * @param TransformGuestToCustomerCommand $command
     */
    public function handle(TransformGuestToCustomerCommand $command)
    {
        $customerId = $command->getCustomerId();
        $customer = new Customer($customerId->getValue());

        $this->assertCustomerExists($customerId, $customer);
        $this->assertCustomerIsGuest($customer);

        if (!$customer->transformToCustomer($this->contextLangId)) {
            throw new CustomerTransformationException(sprintf('Failed to transform guest into customer'), CustomerTransformationException::TRANSFORMATION_FAILED);
        }
    }

    /**
     * @param CustomerId $customerId
     * @param Customer $customer
     *
     * @throws CustomerNotFoundException
     */
    private function assertCustomerExists(CustomerId $customerId, Customer $customer)
    {
        if ($customer->id !== $customerId->getValue()) {
            throw new CustomerNotFoundException(sprintf('Customer with id "%d" was not found', $customerId->getValue()));
        }
    }

    /**
     * @param Customer $customer
     *
     * @throws CustomerTransformationException
     */
    private function assertCustomerIsGuest(Customer $customer)
    {
        if (Customer::customerExists($customer->email)) {
            throw new CustomerTransformationException(sprintf('Customer with id "%s" already exists as non-guest', $customer->id), CustomerTransformationException::CUSTOMER_IS_NOT_GUEST);
        }
    }
}
