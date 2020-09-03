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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreContextCheckerInterface;

/**
 * Provides sql for attributes group list
 */
final class AttributeGroupQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @var MultistoreContextCheckerInterface
     */
    private $multistoreContextChecker;

    /**
     * @var int[]
     */
    private $contextShopIds;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param int $contextLangId
     * @param MultistoreContextCheckerInterface $multistoreContextChecker
     * @param int[] $contextShopIds
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        $contextLangId,
        MultistoreContextCheckerInterface $multistoreContextChecker,
        array $contextShopIds
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->contextLangId = $contextLangId;
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->multistoreContextChecker = $multistoreContextChecker;
        $this->contextShopIds = $contextShopIds;
    }

    /**
     * Get query that searches grid rows.
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return QueryBuilder
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters())
            ->select('DISTINCT ag.id_attribute_group, agl.name, ag.position, acount.values');

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb);

        return $qb;
    }

    /**
     * Get query that counts grid rows.
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return QueryBuilder
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb->select('COUNT(DISTINCT ag.id_attribute_group)');

        return $qb;
    }

    /**
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(array $filters)
    {
        $subQuery = $this->connection->createQueryBuilder()
            ->select('COUNT(DISTINCT a.id_attribute) AS `values`, a.id_attribute_group')
            ->from($this->dbPrefix . 'attribute', 'a')
            ->groupBy('a.id_attribute_group');

        if (!$this->multistoreContextChecker->isAllShopContext()) {
            $subQuery->andWhere('attrShop.id_shop IN (:contextShopIds)');
        }

        $subQuery->leftJoin(
            'a',
            $this->dbPrefix . 'attribute_shop',
            'attrShop',
            'a.id_attribute = attrShop.id_attribute'
        );

        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'attribute_group', 'ag')
            ->leftJoin(
                'ag',
                '(' . $subQuery->getSQL() . ')',
                'acount',
                'acount.id_attribute_group = ag.id_attribute_group'
            )
            ->setParameter('contextLangId', $this->contextLangId)
            ->setParameter('contextShopIds', $this->contextShopIds, Connection::PARAM_INT_ARRAY);

        $qb->leftJoin(
            'ag',
            $this->dbPrefix . 'attribute_group_lang',
            'agl',
            'agl.id_attribute_group = ag.id_attribute_group AND agl.id_lang = :contextLangId'
        );

        $qb->leftJoin(
            'ag',
            $this->dbPrefix . 'attribute_group_shop',
            'ags',
            'ag.id_attribute_group = ags.id_attribute_group'
        );
        $qb->andWhere('ags.id_shop IN (:contextShopIds)');

        $this->applyFilters($filters, $qb);

        return $qb;
    }

    /**
     * @param array $filters
     * @param QueryBuilder $qb
     */
    private function applyFilters(array $filters, QueryBuilder $qb)
    {
        $allowedFiltersMap = [
            'id_attribute_group' => 'ag.id_attribute_group',
            'name' => 'agl.name',
            'position' => 'ag.position',
        ];

        foreach ($filters as $filterName => $value) {
            if (!array_key_exists($filterName, $allowedFiltersMap)) {
                continue;
            }

            if ('name' === $filterName) {
                $qb->andWhere($allowedFiltersMap[$filterName] . ' LIKE :' . $filterName)
                    ->setParameter($filterName, '%' . $value . '%');
                continue;
            }

            if ('position' === $filterName) {
                // When filtering by position,
                // value must be decreased by 1,
                // since position value in database starts at 0,
                // but for user display positions are increased by 1.
                if (is_numeric($value)) {
                    --$value;
                } else {
                    $value = null;
                }
            }

            $qb->andWhere($allowedFiltersMap[$filterName] . ' = :' . $filterName)
                ->setParameter($filterName, $value);
        }
    }
}
