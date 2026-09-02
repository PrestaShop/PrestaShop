<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Customer\CommandHandler;

use Customer;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\MissingCustomerRequiredFieldsException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;

/**
 * Provides reusable methods for customer command handlers.
 *
 * @internal
 */
abstract class AbstractCustomerHandler
{
    /**
     * @param CustomerId $customerId
     * @param Customer $customer
     *
     * @throws CustomerNotFoundException
     */
    protected function assertCustomerWasFound(CustomerId $customerId, Customer $customer)
    {
        if ($customer->id !== $customerId->getValue()) {
            throw new CustomerNotFoundException(sprintf('Customer with id "%d" was not found.', $customerId->getValue()));
        }
    }

    /**
     * @param Customer $customer
     *
     * @throws MissingCustomerRequiredFieldsException
     */
    protected function assertRequiredFieldsAreNotMissing(Customer $customer)
    {
        $errors = $customer->validateFieldsRequiredDatabase();

        if (!empty($errors)) {
            $missingFields = array_keys($errors);

            throw new MissingCustomerRequiredFieldsException($missingFields, sprintf('One or more required fields for customer are missing. Missing fields are: %s', implode(',', $missingFields)));
        }
    }
}
