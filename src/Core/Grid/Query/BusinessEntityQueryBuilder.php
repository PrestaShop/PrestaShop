<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Builds search and count queries for the business entity grid.
 *
 * Soft-deleted entities are excluded, rows are scoped to the associated shops outside
 * of an all-shop context, and customers_count is an aggregate alias rather than a column.
 */
final class BusinessEntityQueryBuilder extends AbstractDoctrineQueryBuilder
{
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        private readonly DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        private readonly ShopContext $shopContext,
    ) {
        parent::__construct($connection, $dbPrefix);
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getBaseQueryBuilder($searchCriteria->getFilters());

        $qb->select('be.id_business_entity')
            ->addSelect('be.name')
            ->addSelect('be.legal_name')
            ->addSelect('be.status')
            ->addSelect('s.name AS shop_name')
            ->addSelect('COUNT(DISTINCT becb.id_customer_b2b) AS customers_count')
            ->leftJoin(
                'be',
                $this->dbPrefix . 'business_entity_customer_b2b',
                'becb',
                'becb.id_business_entity = be.id_business_entity'
            )
            ->groupBy('be.id_business_entity')
        ;

        $this->applySorting($qb, $searchCriteria);

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applyDeterministicSorting($searchCriteria, $qb, 'be', 'id_business_entity')
        ;

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getBaseQueryBuilder($searchCriteria->getFilters());
        $qb->select('COUNT(DISTINCT be.id_business_entity)');

        return $qb;
    }

    private function applySorting(QueryBuilder $qb, SearchCriteriaInterface $searchCriteria): void
    {
        $sortableFields = [
            'id_business_entity' => 'be.id_business_entity',
            'name' => 'be.`name`',
            'legal_name' => 'be.`legal_name`',
            'status' => 'be.`status`',
            'shop_name' => 's.`name`',
            'customers_count' => 'customers_count',
        ];

        if (isset($sortableFields[$searchCriteria->getOrderBy()])) {
            $qb->orderBy(
                $sortableFields[$searchCriteria->getOrderBy()],
                $searchCriteria->getOrderWay()
            );
        }
    }

    /**
     * @param array<string, mixed> $filters
     */
    private function getBaseQueryBuilder(array $filters): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'business_entity', 'be')
            ->leftJoin(
                'be',
                $this->dbPrefix . 'shop',
                's',
                's.id_shop = be.id_shop'
            )
            ->andWhere('be.deleted = 0')
        ;

        if (!$this->shopContext->isAllShopContext()) {
            $qb->andWhere('be.id_shop IN (:beShopIds)')
                ->setParameter('beShopIds', $this->shopContext->getAssociatedShopIds(), Connection::PARAM_INT_ARRAY);
        }

        foreach ($filters as $filterName => $filterValue) {
            if ($filterValue === '' || $filterValue === null) {
                continue;
            }

            if ('id_business_entity' === $filterName) {
                $qb->andWhere("be.id_business_entity = :$filterName");
                $qb->setParameter($filterName, (int) $filterValue);
                continue;
            }

            if ('name' === $filterName) {
                $qb->andWhere("be.name LIKE :$filterName");
                $qb->setParameter($filterName, '%' . $this->escapePercent((string) $filterValue) . '%');
                continue;
            }

            if ('legal_name' === $filterName) {
                $qb->andWhere("be.legal_name LIKE :$filterName");
                $qb->setParameter($filterName, '%' . $this->escapePercent((string) $filterValue) . '%');
                continue;
            }

            if ('shop_name' === $filterName) {
                $qb->andWhere("s.name LIKE :$filterName");
                $qb->setParameter($filterName, '%' . $this->escapePercent((string) $filterValue) . '%');
                continue;
            }

            if ('status' === $filterName) {
                $qb->andWhere("be.status = :$filterName");
                $qb->setParameter($filterName, (string) $filterValue);
                continue;
            }
        }

        return $qb;
    }
}
