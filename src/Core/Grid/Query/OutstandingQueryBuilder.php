<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PDO;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class OutstandingQueryBuilder implements DoctrineQueryBuilderInterface
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $dbPrefix;

    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $criteriaApplicator;

    /**
     * @var array
     */
    private $contextShopIds;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $criteriaApplicator
     * @param int $contextLangId
     * @param array $contextShopIds
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $criteriaApplicator,
        int $contextLangId,
        array $contextShopIds
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->contextLangId = $contextLangId;
        $this->criteriaApplicator = $criteriaApplicator;
        $this->contextShopIds = $contextShopIds;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getBaseQueryBuilder($searchCriteria)
            ->addSelect('oi.id_order_invoice AS id_invoice, oi.date_add')
            ->addSelect('CONCAT(LEFT(c.`firstname`, 1), \'. \' , c.`lastname`) AS customer')
            ->addSelect('c.company, rl.name AS risk, r.color')
            ->addSelect('c.outstanding_allow_amount')
            ->addSelect('c.id_customer, o.id_order')
            ->addSelect('cur.iso_code');
        $this->applySorting($qb, $searchCriteria);
        $this->criteriaApplicator->applyPagination($searchCriteria, $qb);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        return $this->getBaseQueryBuilder($searchCriteria)->select('COUNT(oi.id_order_invoice)');
    }

    /**
     * @param SearchCriteriaInterface $criteria
     *
     * @return QueryBuilder
     */
    private function getBaseQueryBuilder(SearchCriteriaInterface $criteria): QueryBuilder
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'order_invoice', 'oi')
            ->leftJoin('oi', $this->dbPrefix . 'orders', 'o', 'oi.id_order = o.id_order')
            ->leftJoin('o', $this->dbPrefix . 'customer', 'c', 'o.id_customer = c.id_customer')
            ->leftJoin('c', $this->dbPrefix . 'risk', 'r', 'c.id_risk = r.id_risk')
            ->leftJoin('r', $this->dbPrefix . 'risk_lang', 'rl', 'r.id_risk = rl.id_risk AND rl.id_lang = :context_lang_id')
            ->leftJoin('o', $this->dbPrefix . 'currency', 'cur', 'o.id_currency = cur.id_currency')
            ->andWhere('number > 0')
            ->andWhere('o.id_shop IN (:context_shop_ids)')
            ->setParameter('context_lang_id', $this->contextLangId, PDO::PARAM_INT)
            ->setParameter('context_shop_ids', $this->contextShopIds, Connection::PARAM_INT_ARRAY);

        $this->applyFilters($qb, $criteria->getFilters());

        return $qb;
    }

    /**
     * Apply filters for query builder.
     *
     * @param QueryBuilder $qb
     * @param array $filters
     */
    private function applyFilters(QueryBuilder $qb, array $filters): void
    {
        $strictComparisonFilters = [
            'id_invoice' => 'oi.id_order_invoice',
            'risk' => 'r.id_risk',
            'outstanding_allow_amount' => 'c.outstanding_allow_amount',
        ];

        $likeComparisonFilters = [
            'customer' => 'CONCAT(LEFT(c.firstname, 1), \'. \' , c.lastname)',
            'company' => 'c.company',
        ];

        $dateComparisonFilters = [
            'date_add' => 'oi.date_add',
        ];

        foreach ($filters as $filterName => $filterValue) {
            if (isset($strictComparisonFilters[$filterName])) {
                $alias = $strictComparisonFilters[$filterName];

                $qb->andWhere("$alias = :$filterName");
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            if (isset($likeComparisonFilters[$filterName])) {
                $alias = $likeComparisonFilters[$filterName];

                $qb->andWhere("$alias LIKE :$filterName");
                $qb->setParameter($filterName, '%' . $filterValue . '%');

                continue;
            }

            if (isset($dateComparisonFilters[$filterName])) {
                $alias = $dateComparisonFilters[$filterName];

                if (isset($filterValue['from'])) {
                    $name = sprintf('%s_from', $filterName);

                    $qb->andWhere("$alias >= :$name");
                    $qb->setParameter($name, sprintf('%s %s', $filterValue['from'], '0:0:0'));
                }

                if (isset($filterValue['to'])) {
                    $name = sprintf('%s_to', $filterName);

                    $qb->andWhere("$alias <= :$name");
                    $qb->setParameter($name, sprintf('%s %s', $filterValue['to'], '23:59:59'));
                }

                continue;
            }
        }
    }

    /**
     * @param QueryBuilder $qb
     * @param SearchCriteriaInterface $criteria
     */
    private function applySorting(QueryBuilder $qb, SearchCriteriaInterface $criteria): void
    {
        $sortableFields = [
            'id_invoice' => 'oi.id_order_invoice',
            'date_add' => 'oi.date_add',
            'customer' => 'CONCAT(LEFT(c.firstname, 1), \'. \' , c.lastname)',
            'company' => 'c.company',
            'risk' => 'r.id_risk',
            'outstanding_allow_amount' => 'c.outstanding_allow_amount',
        ];

        if (isset($sortableFields[$criteria->getOrderBy()])) {
            $qb->orderBy($sortableFields[$criteria->getOrderBy()], $criteria->getOrderWay());
        }
    }
}
