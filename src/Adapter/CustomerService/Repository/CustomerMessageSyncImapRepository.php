<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\CustomerService\Repository;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;

/**
 * Tracks which IMAP messages have already been processed by the customer
 * service mailbox synchronization, so a message already imported (or
 * discarded) is not re-processed on the next sync run.
 *
 * @internal
 */
final class CustomerMessageSyncImapRepository
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * @param string[] $md5Headers
     *
     * @return string[]
     */
    public function getAlreadyProcessedHashes(array $md5Headers): array
    {
        return $this->connection->createQueryBuilder()
            ->select('md5_header')
            ->from($this->dbPrefix . 'customer_message_sync_imap')
            ->where('md5_header IN (:hashes)')
            ->setParameter('hashes', $md5Headers, ArrayParameterType::STRING)
            ->executeQuery()
            ->fetchFirstColumn();
    }

    public function markAsProcessed(string $md5Header): void
    {
        $this->connection->insert($this->dbPrefix . 'customer_message_sync_imap', [
            'md5_header' => $md5Header,
        ]);
    }
}
