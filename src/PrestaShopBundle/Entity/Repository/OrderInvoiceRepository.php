<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
        $sql = <<<'SQL'
SELECT COUNT(o.id_order) AS nbOrders, o.current_state as id_order_state
FROM `{table_prefix}order_invoice` oi
LEFT JOIN `{table_prefix}orders` o ON oi.id_order = o.id_order
WHERE o.id_shop IN(:shopIds)
AND oi.number > 0
GROUP BY o.current_state
SQL;
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
