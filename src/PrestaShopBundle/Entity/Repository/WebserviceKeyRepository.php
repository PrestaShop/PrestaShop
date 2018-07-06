<?php
/**
 * 2007-2018 PrestaShop
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
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineQueryBuilderInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Repository\RepositoryInterface;

/**
 * Retrieve Webservice accounts data from database.
 */
class WebserviceKeyRepository implements RepositoryInterface, DoctrineQueryBuilderInterface
{
    /**
     * @var \Doctrine\DBAL\Driver\Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $databasePrefix;

    /**
     * @var string
     */
    private $webserviceKeyTable;

    /**
     * @param Connection $connection
     * @param string $databasePrefix
     */
    public function __construct(Connection $connection, $databasePrefix)
    {
        $this->connection = $connection;
        $this->databasePrefix = $databasePrefix;

        $this->webserviceKeyTable = $this->databasePrefix."webservice_account";
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        $statement = $this->connection->query("SELECT l.* FROM $this->webserviceKeyTable l");

        return $statement->fetchAll();
    }

    /**
     * Delete all webservice keys.
     *
     * @return integer The number of affected rows.
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function deleteAll()
    {
        $platform   = $this->connection->getDatabasePlatform();

        return $this->connection->executeUpdate($platform->getTruncateTableSQL($this->webserviceKeyTable, true));
    }

    /**
     * Get query that searches grid rows
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return QueryBuilder
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria = null)
    {
        $qb = $this->buildGridQuery($searchCriteria);
        $qb->select('*')
            ->orderBy(
                $searchCriteria->getOrderBy(),
                $searchCriteria->getOrderWay()
            )
            ->setFirstResult($searchCriteria->getOffset())
            ->setMaxResults($searchCriteria->getLimit());

        return $qb;
    }

    /**
     * Get query that counts grid rows
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return QueryBuilder
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria = null)
    {
        $qb = $this->buildGridQuery($searchCriteria);
        $qb->select('COUNT(*)');

        return $qb;
    }

    /**
     * Build query body without select, sorting & limiting
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return QueryBuilder
     */
    private function buildGridQuery(SearchCriteriaInterface $searchCriteria = null)
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->webserviceKeyTable, 'wk');

        if (null === $searchCriteria) {
            return $qb;
        }

        $filters = $searchCriteria->getFilters();
        foreach ($filters as $filterName => $filterValue) {
            if (empty($filterValue)) {
                continue;
            }
            $qb->andWhere("$filterName LIKE :$filterName");
            $qb->setParameter($filterName, '%'.$filterValue.'%');
        }

        return $qb;
    }
}
