<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Query\Security\Session;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class CustomerQueryBuilder is responsible for building queries for profiles grid data.
 */
class CustomerQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters())
            ->select('cs.id_customer_session, c.id_customer, c.firstname, c.lastname, c.email, cs.date_upd')
        ;

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $qb)
            ->applyPagination($searchCriteria, $qb);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getQueryBuilder($searchCriteria->getFilters())
            ->select('COUNT(cs.id_customer_session)')
        ;
    }

    /**
     * Get generic query builder.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(array $filters): QueryBuilder
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'customer_session', 'cs')
            ->join('cs', $this->dbPrefix . 'customer', 'c', 'c.id_customer = cs.id_customer')
        ;

        $allowedFilters = [
            'date_upd',
            'email',
            'firstname',
            'id_customer',
            'id_customer_session',
            'lastname',
        ];

        foreach ($filters as $name => $value) {
            if (!in_array($name, $allowedFilters, true)) {
                continue;
            }

            if ('id_customer_session' === $name) {
                $qb->andWhere('cs.id_customer_session = :' . $name);
                $qb->setParameter($name, $value);

                continue;
            }

            if ('id_customer' === $name) {
                $qb->andWhere('c.id_customer = :' . $name);
                $qb->setParameter($name, $value);

                continue;
            }

            if ('date_upd' === $name) {
                if (isset($value['from'])) {
                    $qb->andWhere('cs.date_upd >= :date_from');
                    $qb->setParameter('date_from', sprintf('%s 0:0:0', $value['from']));
                }

                if (isset($value['to'])) {
                    $qb->andWhere('cs.date_upd <= :date_to');
                    $qb->setParameter('date_to', sprintf('%s 23:59:59', $value['to']));
                }

                continue;
            }

            $qb->andWhere(
                sprintf(
                    'c.%s LIKE :%s',
                    $name,
                    $name
                )
            );

            $qb->setParameter($name, '%' . $value . '%');
        }

        return $qb;
    }
}
