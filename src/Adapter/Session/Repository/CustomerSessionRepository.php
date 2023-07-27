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
            } catch (CannotDeleteCustomerSessionException $e) {
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
        } catch (CoreException $e) {
            throw new CannotClearCustomerSessionException();
        }
    }
}
