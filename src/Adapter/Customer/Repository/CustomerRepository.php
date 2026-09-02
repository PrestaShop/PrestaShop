<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Customer\Repository;

use Customer;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

/**
 * Provides methods to access Customer data storage
 */
class CustomerRepository extends AbstractObjectModelRepository
{
    /**
     * @param CustomerId $customerId
     *
     * @return Customer
     *
     * @throws CustomerNotFoundException
     */
    public function get(CustomerId $customerId): Customer
    {
        /** @var Customer $customer */
        $customer = $this->getObjectModel(
            $customerId->getValue(),
            Customer::class,
            CustomerNotFoundException::class
        );

        return $customer;
    }

    /**
     * @param CustomerId $customerId
     *
     * @throws CustomerNotFoundException
     */
    public function assertCustomerExists(CustomerId $customerId): void
    {
        $this->assertObjectModelExists(
            $customerId->getValue(),
            'customer',
            CustomerNotFoundException::class
        );
    }
}
