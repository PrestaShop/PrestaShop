<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Builds search and count queries for the withdrawal request grid, scoped to the shops of the context.
 */
final class WithdrawalRequestQueryBuilder extends AbstractDoctrineQueryBuilder
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
        $qb = $this->getBaseQueryBuilder($searchCriteria->getFilters())
            ->select('wr.id_withdrawal_request, wr.id_order, wr.order_reference, wr.email, wr.status, wr.submitted_at')
            ->addSelect('CONCAT(wr.firstname, " ", wr.lastname) AS customer')
            ->addSelect('s.name AS shop_name');

        $this->applySorting($qb, $searchCriteria);
        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applyDeterministicSorting($searchCriteria, $qb, 'wr', 'id_withdrawal_request');

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getBaseQueryBuilder($searchCriteria->getFilters())
            ->select('COUNT(wr.id_withdrawal_request)');
    }

    private function applySorting(QueryBuilder $qb, SearchCriteriaInterface $searchCriteria): void
    {
        $sortableFields = [
            'id_withdrawal_request' => 'wr.id_withdrawal_request',
            'order_reference' => 'wr.order_reference',
            'customer' => 'customer',
            'email' => 'wr.email',
            'submitted_at' => 'wr.submitted_at',
            'shop_name' => 's.name',
            'status' => 'wr.status',
        ];

        if (isset($sortableFields[$searchCriteria->getOrderBy()])) {
            $qb->orderBy($sortableFields[$searchCriteria->getOrderBy()], $searchCriteria->getOrderWay());
        }
    }

    /**
     * @param array<string, mixed> $filters
     */
    private function getBaseQueryBuilder(array $filters): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'withdrawal_request', 'wr')
            ->leftJoin('wr', $this->dbPrefix . 'shop', 's', 's.id_shop = wr.id_shop');

        if (!$this->shopContext->isAllShopContext()) {
            $qb->andWhere('wr.id_shop IN (:contextShopIds)')
                ->setParameter('contextShopIds', $this->shopContext->getAssociatedShopIds(), ArrayParameterType::INTEGER);
        }

        foreach ($filters as $filterName => $filterValue) {
            if ('' === $filterValue || null === $filterValue) {
                continue;
            }

            switch ($filterName) {
                case 'id_withdrawal_request':
                    $qb->andWhere('wr.id_withdrawal_request = :id_withdrawal_request')
                        ->setParameter('id_withdrawal_request', (int) $filterValue);
                    break;
                case 'status':
                    $qb->andWhere('wr.status = :status')
                        ->setParameter('status', (string) $filterValue);
                    break;
                case 'customer':
                    $qb->andWhere('CONCAT(wr.firstname, " ", wr.lastname) LIKE :customer')
                        ->setParameter('customer', '%' . $this->escapePercent((string) $filterValue) . '%');
                    break;
                case 'order_reference':
                case 'email':
                    $qb->andWhere(sprintf('wr.%1$s LIKE :%1$s', $filterName))
                        ->setParameter($filterName, '%' . $this->escapePercent((string) $filterValue) . '%');
                    break;
                case 'shop_name':
                    $qb->andWhere('s.name LIKE :shop_name')
                        ->setParameter('shop_name', '%' . $this->escapePercent((string) $filterValue) . '%');
                    break;
            }
        }

        return $qb;
    }
}
