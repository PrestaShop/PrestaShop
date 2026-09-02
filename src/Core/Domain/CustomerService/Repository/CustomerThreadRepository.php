<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\Repository;

use CustomerThread;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CannotDeleteCustomerThreadException;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerThreadNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadId;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

/**
 * Methods to access data storage for customerThread
 */
class CustomerThreadRepository extends AbstractObjectModelRepository
{
    /**
     * @throws CustomerThreadNotFoundException
     */
    public function get(CustomerThreadId $customerThreadId): CustomerThread
    {
        /** @var CustomerThread $customerThread */
        $customerThread = $this->getObjectModel(
            $customerThreadId->getValue(),
            CustomerThread::class,
            CustomerThreadNotFoundException::class
        );

        return $customerThread;
    }

    /**
     * @throws CannotDeleteCustomerThreadException
     */
    public function delete(CustomerThreadId $customerThreadId): void
    {
        $this->deleteObjectModel($this->get($customerThreadId), CannotDeleteCustomerThreadException::class);
    }
}
