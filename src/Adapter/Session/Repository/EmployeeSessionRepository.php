<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Session\Repository;

use DateInterval;
use DateTime;
use Doctrine\DBAL\Connection;
use EmployeeSession;
use PrestaShop\PrestaShop\Core\Domain\Security\Exception\CannotBulkDeleteEmployeeSessionException;
use PrestaShop\PrestaShop\Core\Domain\Security\Exception\CannotClearEmployeeSessionException;
use PrestaShop\PrestaShop\Core\Domain\Security\Exception\CannotDeleteEmployeeSessionException;
use PrestaShop\PrestaShop\Core\Domain\Security\Exception\SessionNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Security\ValueObject\EmployeeSessionId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

/**
 * Methods to access data storage for Employee session
 */
class EmployeeSessionRepository extends AbstractObjectModelRepository
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
     * @param EmployeeSessionId $sessionId
     *
     * @return EmployeeSession
     */
    public function get(EmployeeSessionId $sessionId): EmployeeSession
    {
        /** @var EmployeeSession $session */
        $session = $this->getObjectModel(
            $sessionId->getValue(),
            EmployeeSession::class,
            SessionNotFoundException::class
        );

        return $session;
    }

    /**
     * @param EmployeeSessionId $employeeSessionId
     */
    public function delete(EmployeeSessionId $employeeSessionId): void
    {
        $this->deleteObjectModel($this->get($employeeSessionId), CannotDeleteEmployeeSessionException::class);
    }

    /**
     * @param array $employeeSessionIds
     *
     * @throws CannotBulkDeleteEmployeeSessionException
     */
    public function bulkDelete(array $employeeSessionIds): void
    {
        $failedIds = [];
        foreach ($employeeSessionIds as $employeeSessionId) {
            try {
                $this->delete($employeeSessionId);
            } catch (CannotDeleteEmployeeSessionException) {
                $failedIds[] = $employeeSessionId->getValue();
            }
        }

        if (empty($failedIds)) {
            return;
        }

        throw new CannotBulkDeleteEmployeeSessionException(
            $failedIds,
            sprintf('Failed to delete following employees sessions: "%s"', implode(', ', $failedIds))
        );
    }

    /**
     * Clear outdated employee sessions
     *
     * @return void
     *
     * @throws CannotClearEmployeeSessionException
     */
    public function clearOutdatedSessions(): void
    {
        try {
            $date = new DateTime();
            $date->sub(new DateInterval('PT' . $this->cookieLifetime . 'H'));

            $qb = $this->connection->createQueryBuilder();
            $qb->delete($this->dbPrefix . 'employee_session')
                ->where('date_upd <= :dateUpdated')
                ->setParameter('dateUpdated', $date->format('Y-m-d H:i:s'));

            $qb->executeStatement();
        } catch (CoreException) {
            throw new CannotClearEmployeeSessionException();
        }
    }
}
