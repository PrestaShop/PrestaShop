<?php

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\Connection;

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
     * Count number of orders grouped by order state
     *
     * @param array $shopIds
     *
     * @return array
     */
    public function countByOrderState(array $shopIds)
    {
        $sql = '
            SELECT COUNT(o.id_order) AS nbOrders, o.current_state as id_order_state
			FROM `{table_prefix}order_invoice` oi
			LEFT JOIN `{table_prefix}orders` o ON oi.id_order = o.id_order
			WHERE o.id_shop IN(:shopIds)
			AND oi.number > 0
			GROUP BY o.current_state'
        ;

        $sql = str_replace('{table_prefix}', $this->tablePrefix, $sql);

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('shopIds', implode(',', array_map('intval', $shopIds)));
        $statement->execute();

        $result = [];

        while ($row = $statement->fetch()) {
            $result[$row['id_order_state']] = $row['nbOrders'];
        }

        return $result;
    }
}
