<?php
/**
 * 2007-2019 PrestaShop.
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

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class FeatureQueryBuilder builds queries for features grid data.
 */
final class FeatureQueryBuilder extends AbstractDoctrineQueryBuilder
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
        $searchQueryBuilder = $this->getFeatureQueryBuilder($searchCriteria);

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
        $queryBuilder = $this->getFeatureQueryBuilder($searchCriteria);

        $countQueryBuilder = $this->connection->createQueryBuilder()
            ->select('COUNT(*)')
            // Need to do a subquery because we have a HAVING filter.
            ->from('(' . $queryBuilder . ')', 'fc')
            ->setParameters($queryBuilder->getParameters(), $queryBuilder->getParameterTypes());

        return $countQueryBuilder;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    private function getFeatureQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $subQuery = $this->connection->createQueryBuilder()
            ->select(1)
            ->from($this->dbPrefix . 'feature_shop', 'fs')
            ->where('f.id_feature = fs.id_feature')
            ->andWhere('fs.id_shop IN (:context_shop_ids)');

        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('f.id_feature, fl.name, COUNT(fv.id_feature_value) values_count, f.position')
            ->from($this->dbPrefix . 'feature', 'f')
            ->leftJoin(
                'f',
                $this->dbPrefix . 'feature_value',
                'fv',
                'f.id_feature = fv.id_feature AND (fv.custom=0 OR fv.custom IS NULL)'
            )
            ->leftJoin(
                'f',
                $this->dbPrefix . 'feature_lang',
                'fl',
                'f.id_feature = fl.id_feature AND fl.id_lang = :context_lang_id'
            )
            ->where('EXISTS(' . $subQuery->getSQL() . ')')
            ->groupBy('f.id_feature')
            ->setParameter('context_shop_ids', $this->contextShopIds, Connection::PARAM_INT_ARRAY)
            ->setParameter('context_lang_id', $this->contextLangId);

        $this->applyFilters($searchCriteria->getFilters(), $queryBuilder);

        return $queryBuilder;
    }

    /**
     * Apply filters to features query builder.
     *
     * @param array $filters
     * @param QueryBuilder $qb
     */
    private function applyFilters(array $filters, QueryBuilder $qb)
    {
        $allowedFilters = [
            'id_feature',
            'name',
            'values_count',
            'position',
        ];

        foreach ($filters as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if ('id_feature' === $filterName) {
                $qb->andWhere('f.`id_feature` LIKE :' . $filterName);
                $qb->setParameter($filterName, '%' . $filterValue . '%');

                continue;
            }

            if ('values_count' === $filterName) {
                $qb->andHaving('values_count LIKE :values_count');
                $qb->setParameter('values_count', '%' . $filterValue . '%');

                continue;
            }

            if ('position' === $filterName) {
                // Position values needs to be reduced by one because presented values
                // are different from position values stored in the database.
                $filterValue = is_numeric($filterValue) ? $filterValue - 1 : null;
                $qb->andWhere('`' . $filterName . '` = :' . $filterName);
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            $qb->andWhere('`' . $filterName . '` LIKE :' . $filterName);
            $qb->setParameter($filterName, '%' . $filterValue . '%');
        }
    }

    /**
     * @param QueryBuilder $searchQueryBuilder
     * @param SearchCriteriaInterface $searchCriteria
     */
    private function applySorting(QueryBuilder $searchQueryBuilder, SearchCriteriaInterface $searchCriteria): void
    {
        switch ($searchCriteria->getOrderBy()) {
            case 'id_feature':
            case 'position':
                $orderBy = 'f.' . $searchCriteria->getOrderBy();

                break;
            case 'name':
                $orderBy = 'fl.name';

                break;
            case 'values_count':
                $orderBy = 'values_count';

                break;
            default:
                return;
        }

        $searchQueryBuilder->orderBy($orderBy, $searchCriteria->getOrderWay());
    }
}
