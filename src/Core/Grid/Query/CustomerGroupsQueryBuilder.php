<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

class CustomerGroupsQueryBuilder extends AbstractDoctrineQueryBuilder
{
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        private readonly DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        private readonly LanguageContext $languageContext,
        private readonly ShopContext $shopContext,
    ) {
        parent::__construct($connection, $dbPrefix);
    }

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $builder = $this->getCustomerGroupsQueryBuilder($searchCriteria);
        $builder->select(sprintf(
            'g.id_group, gl.name, g.reduction, %s AS members, g.show_prices, g.date_add',
            $this->getMembersSubQuery($builder)
        ));

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $builder)
            ->applyPagination($searchCriteria, $builder);

        return $builder;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getCustomerGroupsQueryBuilder($searchCriteria)->select('COUNT(g.id_group)');
    }

    private function getCustomerGroupsQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $builder = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'group', 'g')
            ->innerJoin(
                'g',
                $this->dbPrefix . 'group_lang',
                'gl',
                'gl.id_group = g.id_group AND gl.id_lang = :languageId'
            )
            ->setParameter('languageId', $this->languageContext->getId())
        ;

        if (!$this->shopContext->isAllShopContext()) {
            $shopAssociationQuery = $this->connection->createQueryBuilder()
                ->select('1')
                ->from($this->dbPrefix . 'group_shop', 'gs')
                ->where('gs.id_group = g.id_group')
                ->andWhere('gs.id_shop IN (:contextShopIds)')
            ;

            $builder
                ->andWhere('EXISTS (' . $shopAssociationQuery->getSQL() . ')')
                ->setParameter('contextShopIds', $this->shopContext->getAssociatedShopIds(), Connection::PARAM_INT_ARRAY)
            ;
        }

        $this->applyFilters($builder, $searchCriteria);

        return $builder;
    }

    /**
     * Counts the non deleted customers of the group, restricted like the legacy Shop::SHARE_CUSTOMER restriction.
     */
    private function getMembersSubQuery(QueryBuilder $builder): string
    {
        $membersQuery = $this->connection->createQueryBuilder()
            ->select('COUNT(cg.id_customer)')
            ->from($this->dbPrefix . 'customer_group', 'cg')
            ->innerJoin('cg', $this->dbPrefix . 'customer', 'c', 'c.id_customer = cg.id_customer AND c.deleted = 0')
            ->where('cg.id_group = g.id_group')
        ;

        if (!$this->shopContext->isAllShopContext()) {
            if ($this->shopContext->hasGroupSharingCustomers()) {
                $membersQuery->andWhere('c.id_shop_group = :membersShopGroupId');
                $builder->setParameter('membersShopGroupId', $this->shopContext->getShopGroupId());
            } else {
                $membersQuery->andWhere('c.id_shop IN (:membersShopIds)');
                $builder->setParameter('membersShopIds', $this->shopContext->getAssociatedShopIds(), Connection::PARAM_INT_ARRAY);
            }
        }

        return '(' . $membersQuery->getSQL() . ')';
    }

    private function applyFilters(QueryBuilder $builder, SearchCriteriaInterface $searchCriteria): void
    {
        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            switch ($filterName) {
                case 'id_group':
                case 'reduction':
                case 'show_prices':
                    $builder
                        ->andWhere(sprintf('g.%s = :%s', $filterName, $filterName))
                        ->setParameter($filterName, $filterValue);
                    break;
                case 'members':
                    $builder
                        ->andWhere($this->getMembersSubQuery($builder) . ' = :members')
                        ->setParameter('members', (int) $filterValue);
                    break;
                case 'name':
                    $builder
                        ->andWhere('gl.name LIKE :name')
                        ->setParameter('name', '%' . $filterValue . '%');
                    break;
                case 'date_add':
                    if (!empty($filterValue['from'])) {
                        $builder
                            ->andWhere('g.date_add >= :dateAddFrom')
                            ->setParameter('dateAddFrom', sprintf('%s 00:00:00', $filterValue['from']));
                    }
                    if (!empty($filterValue['to'])) {
                        $builder
                            ->andWhere('g.date_add <= :dateAddTo')
                            ->setParameter('dateAddTo', sprintf('%s 23:59:59', $filterValue['to']));
                    }
                    break;
            }
        }
    }
}
