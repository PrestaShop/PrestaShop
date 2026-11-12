<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\CustomerService\Repository;

use CustomerThread;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CannotDeleteCustomerThreadException;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerThreadNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadId;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadStatus;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

final class CustomerThreadRepository extends AbstractObjectModelRepository
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

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

    /**
     * @return array{email: string, message: string|null, customer_name: string|null}
     *
     * @throws CustomerThreadNotFoundException
     */
    public function getLatestMessage(CustomerThreadId $customerThreadId): array
    {
        $message = $this->connection->createQueryBuilder()
            ->select('ct.email', 'cm.message')
            ->addSelect('CONCAT(c.firstname, \' \', c.lastname) AS customer_name')
            ->from($this->dbPrefix . 'customer_thread', 'ct')
            ->leftJoin('ct', $this->dbPrefix . 'customer_message', 'cm', 'cm.id_customer_thread = ct.id_customer_thread')
            ->leftJoin('ct', $this->dbPrefix . 'customer', 'c', 'c.email = ct.email')
            ->where('ct.id_customer_thread = :customerThreadId')
            ->orderBy('cm.date_add', 'DESC')
            ->addOrderBy('cm.id_customer_message', 'DESC')
            ->setParameter('customerThreadId', $customerThreadId->getValue())
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative()
        ;

        if (false === $message) {
            throw new CustomerThreadNotFoundException(sprintf('Customer thread with id %d was not found', $customerThreadId->getValue()));
        }

        return $message;
    }

    /**
     * @return bool false when no row was changed
     */
    public function updateStatus(CustomerThreadId $customerThreadId, CustomerThreadStatus $status): bool
    {
        return 0 !== $this->connection->createQueryBuilder()
            ->update($this->dbPrefix . 'customer_thread')
            ->set('status', ':status')
            ->where('id_customer_thread = :customerThreadId')
            ->setParameter('status', $status->getValue())
            ->setParameter('customerThreadId', $customerThreadId->getValue())
            ->executeStatement()
        ;
    }
}
