<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Session\Repository;

use CustomerSession;
use DateInterval;
use DateTime;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\Security\Exception\CannotBulkDeleteCustomerSessionException;
use PrestaShop\PrestaShop\Core\Domain\Security\Exception\CannotClearCustomerSessionException;
use PrestaShop\PrestaShop\Core\Domain\Security\Exception\CannotDeleteCustomerSessionException;
use PrestaShop\PrestaShop\Core\Domain\Security\Exception\SessionNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Security\ValueObject\CustomerSessionId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

/**
 * Methods to access data storage for Customer session
 */
class CustomerSessionRepository extends AbstractObjectModelRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @var int
     */
    private $cookieLifetime;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param int $cookieLifetime
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        int $cookieLifetime
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->cookieLifetime = $cookieLifetime;
    }

    /**
     * @param CustomerSessionId $sessionId
     *
     * @return CustomerSession
     */
    public function get(CustomerSessionId $sessionId): CustomerSession
    {
        /** @var CustomerSession $session */
        $session = $this->getObjectModel(
            $sessionId->getValue(),
            CustomerSession::class,
            SessionNotFoundException::class
        );

        return $session;
    }

    /**
     * @param CustomerSessionId $customerSessionId
     */
    public function delete(CustomerSessionId $customerSessionId): void
    {
        $this->deleteObjectModel($this->get($customerSessionId), CannotDeleteCustomerSessionException::class);
    }

    /**
     * @param array $customerSessionIds
     *
     * @throws CannotBulkDeleteCustomerSessionException
     */
    public function bulkDelete(array $customerSessionIds): void
    {
        $failedIds = [];
        foreach ($customerSessionIds as $customerSessionId) {
            try {
                $this->delete($customerSessionId);
            } catch (CannotDeleteCustomerSessionException) {
                $failedIds[] = $customerSessionId->getValue();
            }
        }

        if (empty($failedIds)) {
            return;
        }

        throw new CannotBulkDeleteCustomerSessionException(
            $failedIds,
            sprintf('Failed to delete following customers sessions: "%s"', implode(', ', $failedIds))
        );
    }

    /**
     * Clear outdated customer sessions
     *
     * @return void
     *
     * @throws CannotClearCustomerSessionException
     */
    public function clearOutdatedSessions(): void
    {
        try {
            $date = new DateTime();
            $date->sub(new DateInterval('PT' . $this->cookieLifetime . 'H'));

            $qb = $this->connection->createQueryBuilder();
            $qb->delete($this->dbPrefix . 'customer_session')
                ->where('date_upd <= :dateUpdated')
                ->setParameter('dateUpdated', $date->format('Y-m-d H:i:s'));

            $qb->executeStatement();
        } catch (CoreException) {
            throw new CannotClearCustomerSessionException();
        }
    }
}
