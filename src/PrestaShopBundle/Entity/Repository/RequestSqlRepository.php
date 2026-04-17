<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Repository\RepositoryInterface;

/**
 * Class RequestSqlRepository is responsible for retrieving RequestSql data from database.
 */
class RequestSqlRepository implements RepositoryInterface
{
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var string
     */
    private $requestSqlTable;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(Connection $connection, $dbPrefix)
    {
        $this->connection = $connection;
        $this->requestSqlTable = $dbPrefix . 'request_sql';
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        $statement = $this->connection->query("SELECT rs.* FROM $this->requestSqlTable rs");

        return $statement->fetchAll();
    }

    /**
     * Get count of all request sql's.
     *
     * @return int Number of request sql rows
     */
    public function getCount()
    {
        $statement = $this->connection->query("SELECT COUNT(rs.id_request_sql) AS c FROM $this->requestSqlTable rs");
        $row = $statement->fetch();

        return (int) $row['c'];
    }
}
