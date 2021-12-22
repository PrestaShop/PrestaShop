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

/**
 * Class CmsPageCategoryQueryBuilder builds search & count queries for cms page categories grid.
 */
final class CmsPageCategoryQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @var array
     */
    private $contextShopIds;

    /**
     * @var int
     */
    private $contextIdLang;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param array $contextShopIds
     * @param int $contextIdLang
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        array $contextShopIds,
        $contextIdLang
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->contextShopIds = $contextShopIds;
        $this->contextIdLang = $contextIdLang;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb
            ->select('cc.`id_cms_category`, cc.`id_parent`, cc.`active`, cc.`position`, ccl.`name`, ccl.`description`')
            ->groupBy('cc.`id_cms_category`');

        $orderBy = $this->getModifiedOrderBy($searchCriteria->getOrderBy());
        if (!empty($orderBy)) {
            $qb->orderBy(
                $orderBy,
                $searchCriteria->getOrderWay()
            );
        }

        $this->searchCriteriaApplicator->applyPagination($searchCriteria, $qb);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters())
            ->select('COUNT(DISTINCT cc.`id_cms_category`)')
        ;

        return $qb;
    }

    /**
     * Gets query builder.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(array $filters)
    {
        $availableFilters = [
            'id_cms_category_parent',
            'id_cms_category',
            'active',
            'position',
            'name',
            'description',
        ];

        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'cms_category', 'cc')
            ->leftJoin(
                'cc',
                $this->dbPrefix . 'cms_category_lang',
                'ccl',
                'ccl.`id_cms_category` = cc.`id_cms_category`'
            )
            ->innerJoin(
                'cc',
                $this->dbPrefix . 'cms_category_shop',
                'ccs',
                'ccs.`id_cms_category` = cc.`id_cms_category`'
            )
        ;

        $qb->andWhere('ccl.`id_lang` = :contextLangId');
        $qb->andWhere('ccl.`id_shop` IN (:contextShopIds)');
        $qb->andWhere('ccs.`id_shop` IN (:contextShopIds)');

        $qb->setParameter('contextLangId', $this->contextIdLang);
        $qb->setParameter('contextShopIds', $this->contextShopIds, Connection::PARAM_INT_ARRAY);

        foreach ($filters as $filterName => $value) {
            if (!in_array($filterName, $availableFilters, true)) {
                continue;
            }

            if ('id_cms_category_parent' === $filterName) {
                $qb->andWhere('cc.`id_parent` = :id_cms_category_parent');
                $qb->setParameter('id_cms_category_parent', $value);

                continue;
            }

            if (in_array($filterName, ['id_cms_category', 'active'], true)) {
                $qb->andWhere('cc.`' . $filterName . '` = :' . $filterName);
                $qb->setParameter($filterName, $value);

                continue;
            }

            if ('position' === $filterName) {
                $modifiedPositionFilter = $this->getModifiedPositionFilter($value);
                $qb->andWhere('cc.`' . $filterName . '` = :' . $filterName);
                $qb->setParameter($filterName, $modifiedPositionFilter);

                continue;
            }

            $qb->andWhere('ccl.`' . $filterName . '` LIKE :' . $filterName);
            $qb->setParameter($filterName, '%' . $value . '%');
        }

        return $qb;
    }

    /**
     * Gets modified order by by having prefix appended.
     *
     * @param string $orderBy
     *
     * @return string
     */
    private function getModifiedOrderBy($orderBy)
    {
        if ('id_cms_category' === $orderBy) {
            $orderBy = 'cc.id_cms_category';
        }

        return $orderBy;
    }

    /**
     * Gets modified position filter value. This is required due to in database position filter index starts from 0 and
     * for the customer which wants to filter results the value starts from 1 instead.
     *
     * @param string|int $positionFilterValue
     *
     * @return int|null - if null is returned then no results are found since position field does not hold null values
     */
    private function getModifiedPositionFilter($positionFilterValue)
    {
        if (!is_numeric($positionFilterValue)) {
            return null;
        }

        $reducedByOneFilterValue = $positionFilterValue - 1;

        if (0 > $reducedByOneFilterValue) {
            return null;
        }

        return $reducedByOneFilterValue;
    }
}
