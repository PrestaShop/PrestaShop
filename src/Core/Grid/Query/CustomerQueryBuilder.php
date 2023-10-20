<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class CustomerQueryBuilder builds queries to fetch data for customers grid.
 */
final class CustomerQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @var int[]
     */
    private $contextShopIds;

    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $criteriaApplicator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $criteriaApplicator
     * @param int $contextLangId
     * @param int[] $contextShopIds
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $criteriaApplicator,
        $contextLangId,
        array $contextShopIds
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->contextLangId = $contextLangId;
        $this->contextShopIds = $contextShopIds;
        $this->criteriaApplicator = $criteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $searchQueryBuilder = $this->getCustomerQueryBuilder($searchCriteria)
            ->select('c.id_customer, c.firstname, c.lastname, c.email, c.active, c.newsletter, c.optin')
            ->addSelect('c.date_add, gl.name as social_title, grl.name as default_group, s.name as shop_name, c.company');

        $this->appendTotalSpentQuery($searchQueryBuilder);
        $this->appendLastVisitQuery($searchQueryBuilder);
        $this->applySorting($searchQueryBuilder, $searchCriteria);

        $this->criteriaApplicator->applyPagination(
            $searchCriteria,
            $searchQueryBuilder
        );

        return $searchQueryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $countQueryBuilder = $this->getCustomerQueryBuilder($searchCriteria)
            ->select('COUNT(*)');

        return $countQueryBuilder;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    private function getCustomerQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'customer', 'c')
            ->leftJoin(
                'c',
                $this->dbPrefix . 'gender_lang',
                'gl',
                'c.id_gender = gl.id_gender AND gl.id_lang = :context_lang_id'
            )
            ->leftJoin(
                'c',
                $this->dbPrefix . 'group_lang',
                'grl',
                'c.id_default_group = grl.id_group AND grl.id_lang = :context_lang_id'
            )
            ->leftJoin(
                'c',
                $this->dbPrefix . 'shop',
                's',
                'c.id_shop = s.id_shop'
            )
            ->where('c.deleted = 0')
            ->andWhere('c.id_shop IN (:context_shop_ids)')
            ->setParameter('context_shop_ids', $this->contextShopIds, Connection::PARAM_INT_ARRAY)
            ->setParameter('context_lang_id', $this->contextLangId);

        $this->applyFilters($searchCriteria->getFilters(), $queryBuilder);

        return $queryBuilder;
    }

    /**
     * @param QueryBuilder $queryBuilder
     */
    private function appendTotalSpentQuery(QueryBuilder $queryBuilder)
    {
        $totalSpentQueryBuilder = $this->connection->createQueryBuilder()
            ->select('SUM(total_paid_real / conversion_rate)')
            ->from($this->dbPrefix . 'orders', 'o')
            ->where('o.id_customer = c.id_customer')
            ->andWhere('o.id_shop IN (:context_shop_ids)')
            ->andWhere('o.valid = 1')
            ->setParameter('context_shop_ids', $this->contextShopIds, Connection::PARAM_INT_ARRAY);

        $queryBuilder->addSelect('(' . $totalSpentQueryBuilder->getSQL() . ') as total_spent');
    }

    /**
     * Append "last visit" column to customers query builder.
     *
     * @param QueryBuilder $queryBuilder
     */
    private function appendLastVisitQuery(QueryBuilder $queryBuilder)
    {
        $lastVisitQueryBuilder = $this->connection->createQueryBuilder()
            ->select('con.date_add')
            ->from($this->dbPrefix . 'guest', 'g')
            ->leftJoin('g', $this->dbPrefix . 'connections', 'con', 'con.id_guest = g.id_guest')
            ->where('g.id_customer = c.id_customer')
            ->orderBy('con.date_add', 'DESC')
            ->setMaxResults(1);

        $queryBuilder->addSelect('(' . $lastVisitQueryBuilder->getSQL() . ') as connect');
    }

    /**
     * Apply filters to customers query builder.
     *
     * @param array $filters
     * @param QueryBuilder $qb
     */
    private function applyFilters(array $filters, QueryBuilder $qb)
    {
        $allowedFilters = [
            'id_customer',
            'social_title',
            'firstname',
            'lastname',
            'email',
            'default_group',
            'active',
            'newsletter',
            'optin',
            'date_add',
            'company',
        ];

        foreach ($filters as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if (in_array($filterName, ['active', 'newsletter', 'optin', 'id_customer'])) {
                $qb->andWhere('c.`' . $filterName . '` = :' . $filterName);
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            if ('social_title' === $filterName) {
                $qb->andWhere('gl.id_gender = :' . $filterName);
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            if ('default_group' === $filterName) {
                $qb->andWhere('grl.id_group = :' . $filterName);
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            if ('date_add' === $filterName) {
                if (isset($filterValue['from'])) {
                    $qb->andWhere('c.date_add >= :date_from');
                    $qb->setParameter('date_from', sprintf('%s 0:0:0', $filterValue['from']));
                }

                if (isset($filterValue['to'])) {
                    $qb->andWhere('c.date_add <= :date_to');
                    $qb->setParameter('date_to', sprintf('%s 23:59:59', $filterValue['to']));
                }

                continue;
            }

            $qb->andWhere('c.`' . $filterName . '` LIKE :' . $filterName);
            $qb->setParameter($filterName, '%' . $filterValue . '%');
        }
    }

    /**
     * Apply sorting so search query builder for customers.
     *
     * @param QueryBuilder $searchQueryBuilder
     * @param SearchCriteriaInterface $searchCriteria
     */
    private function applySorting(QueryBuilder $searchQueryBuilder, SearchCriteriaInterface $searchCriteria)
    {
        switch ($searchCriteria->getOrderBy()) {
            case 'id_customer':
            case 'firstname':
            case 'lastname':
            case 'email':
            case 'date_add':
            case 'company':
            case 'active':
            case 'newsletter':
            case 'optin':
                $orderBy = 'c.' . $searchCriteria->getOrderBy();

                break;
            case 'social_title':
                $orderBy = 'gl.name';

                break;
            case 'default_group':
                $orderBy = 'grl.name';

                break;
            case 'connect':
            case 'total_spent':
                $orderBy = $searchCriteria->getOrderBy();

                break;
            default:
                return;
        }

        $searchQueryBuilder->orderBy($orderBy, $searchCriteria->getOrderWay());
    }
}
