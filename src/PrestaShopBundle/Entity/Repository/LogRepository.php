<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Repository\RepositoryInterface;

class LogRepository implements RepositoryInterface
{
    private $connection;
    private $databasePrefix;
    private $logTable;

    public function __construct(Connection $connection, $databasePrefix)
    {
        $this->connection = $connection;
        $this->databasePrefix = $databasePrefix;
        $this->logTable = $this->databasePrefix."log";
    }

    /**
     * @{inheritdoc}
     */
    public function findAll()
    {
        $statement = $this->connection->query("SELECT l.* FROM $this->logTable l");

        return $statement->fetchAll();
    }

    /**
     * Get all logs with employee name and avatar information
     * @param array $filters
     */
    public function findAllWithEmployeeInformation($filters)
    {
        $employeeTable = $this->databasePrefix.'employee';
        $queryBuilder = $this->connection->createQueryBuilder();

        $statement = $queryBuilder
            ->select('l.*', 'e.firstname', 'e.lastname', 'e.email')
            ->from($this->logTable, 'l')
            ->innerJoin('l', $employeeTable, 'e', 'l.id_employee = e.id_employee')
            ->orderBy(':orderBy', ':sortOrder')
            ->setFirstResult($filters['offset'])
            ->setMaxResults($filters['limit'])
            ->setParameters(array(
                ':orderBy' => $filters['orderBy'],
                ':sortOrder' => $filters['sortOrder'],
            ))
            ->execute()
        ;

        return $statement->fetchAll();
    }

    /**
     * Delete all logs.
     */
    public function deleteAll()
    {
        return  $this->connection->delete("$this->tableName");
    }
}
