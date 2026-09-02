<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Customer\CommandHandler;

use Customer;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
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
#[AsCommandHandler]
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
     * Checks if a customer with the same email already exists in database.
     *
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
