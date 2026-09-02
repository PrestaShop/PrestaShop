<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\Connection;

/**
 * Class OrderInvoiceRepository.
 */
class OrderInvoiceRepository
{
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var string
     */
    private $tablePrefix;

    /**
     * @param Connection $connection
     * @param string $tablePrefix
     */
    public function __construct(Connection $connection, $tablePrefix)
    {
        $this->connection = $connection;
        $this->tablePrefix = $tablePrefix;
    }

    /**
     * Count number of orders grouped by order state.
     *
     * @param array $shopIds
     *
     * @return array
     */
    public function countByOrderState(array $shopIds)
    {
        $sql = 'SELECT COUNT(o.id_order) AS nbOrders, o.current_state as id_order_state
            FROM `{table_prefix}order_invoice` oi
            INNER JOIN `{table_prefix}orders` o ON oi.id_order = o.id_order
            WHERE o.id_shop IN(' . implode(',', array_map('intval', $shopIds)) . ')
            AND oi.number > 0
            GROUP BY o.current_state';
        $sql = str_replace('{table_prefix}', $this->tablePrefix, $sql);

        $statement = $this->connection->prepare($sql);
        $statementResult = $statement->executeQuery();

        $result = [];

        while ($row = $statementResult->fetchAssociative()) {
            $result[$row['id_order_state']] = $row['nbOrders'];
        }

        return $result;
    }
}
