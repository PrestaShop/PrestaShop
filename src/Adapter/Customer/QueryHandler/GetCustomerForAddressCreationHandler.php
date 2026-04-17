<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Customer\QueryHandler;

use Customer;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerByEmailNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerForAddressCreation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryHandler\GetCustomerForAddressCreationHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\AddressCreationCustomerInformation;
use PrestaShopDatabaseException;

/**
 * Handles finding customer by email
 */
#[AsQueryHandler]
final class GetCustomerForAddressCreationHandler implements GetCustomerForAddressCreationHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @return AddressCreationCustomerInformation
     *
     * @throws CustomerByEmailNotFoundException
     * @throws CustomerException
     */
    public function handle(GetCustomerForAddressCreation $query): AddressCreationCustomerInformation
    {
        $email = $query->getCustomerEmail();

        try {
            $result = Customer::searchByName($email);
        } catch (PrestaShopDatabaseException) {
            throw new CustomerException(sprintf('Failed to fetch results for customers with email %s', $email));
        }

        if (empty($result)) {
            throw new CustomerByEmailNotFoundException(sprintf('Failed to find customer with email %s', $email));
        }

        $customer = reset($result);

        $customerInformation = new AddressCreationCustomerInformation(
            (int) $customer['id_customer'],
            $customer['firstname'],
            $customer['lastname']
        );

        if (null !== $customer['company']) {
            $customerInformation->setCompany($customer['company']);
        }

        return $customerInformation;
    }
}
