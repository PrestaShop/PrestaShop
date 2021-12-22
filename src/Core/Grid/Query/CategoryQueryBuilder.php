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
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreContextCheckerInterface;

/**
 * Class CategoryQueryBuilder builds search & count queries for categories grid.
 */
final class CategoryQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @var int|null
     *
     * Can be null for backward-compatibility
     */
    private $rootCategoryId;

    /**
     * @var DoctrineSearchCriteriaApplicator
     */
    private $searchCriteriaApplicator;

    /**
     * @var MultistoreContextCheckerInterface
     */
    private $multistoreContextChecker;

    /**
     * @var FeatureInterface
     */
    private $multistoreFeature;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicator $searchCriteriaApplicator
     * @param int $contextLangId
     * @param int $contextShopId
     * @param MultistoreContextCheckerInterface $multistoreContextChecker
     * @param FeatureInterface $multistoreFeature
     * @param int|null $rootCategoryId
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicator $searchCriteriaApplicator,
        $contextLangId,
        $contextShopId,
        MultistoreContextCheckerInterface $multistoreContextChecker,
        FeatureInterface $multistoreFeature,
        $rootCategoryId = null
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->contextLangId = $contextLangId;
        $this->contextShopId = $contextShopId;
        $this->rootCategoryId = $rootCategoryId;
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->multistoreContextChecker = $multistoreContextChecker;
        $this->multistoreFeature = $multistoreFeature;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb->select('COUNT(cp.`id_product`) AS `products_count`, c.id_category, c.id_parent, c.active, cl.name, cl.description, cs.position');
        $qb->leftJoin(
            'c',
            $this->dbPrefix . 'category_product',
            'cp',
            'c.`id_category` = cp.`id_category`'
        );
        $qb->groupBy('c.`id_category`');

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb->select('COUNT(c.id_category)');

        return $qb;
    }

    /**
     * Get generic query builder.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(array $filters)
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'category', 'c')
            ->setParameter('context_lang_id', $this->contextLangId)
            ->setParameter('context_shop_id', $this->contextShopId);

        $qb->leftJoin(
            'c',
            $this->dbPrefix . 'category_lang',
            'cl',
            $this->multistoreFeature->isUsed() && $this->multistoreContextChecker->isSingleShopContext() ?
                'c.id_category = cl.id_category AND cl.id_lang = :context_lang_id AND cl.id_shop = :context_shop_id' :
                'c.id_category = cl.id_category AND cl.id_lang = :context_lang_id AND cl.id_shop = c.id_shop_default'
        );

        $qb->leftJoin(
            'c',
            $this->dbPrefix . 'category_shop',
            'cs',
            $this->multistoreContextChecker->isSingleShopContext() ?
                'c.id_category = cs.id_category AND cs.id_shop = :context_shop_id' :
                'c.id_category = cs.id_category AND cs.id_shop = c.id_shop_default'
        );

        foreach ($filters as $filterName => $filterValue) {
            if ('id_category' === $filterName) {
                $qb->andWhere("c.id_category = :$filterName");
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            // exclude root category from search results
            if ($this->rootCategoryId !== null) {
                $qb->andWhere('c.id_category != :root_category_id');
                $qb->setParameter('root_category_id', $this->rootCategoryId);
            }

            if ('name' === $filterName) {
                $qb->andWhere("cl.name LIKE :$filterName");
                $qb->setParameter($filterName, '%' . $filterValue . '%');

                continue;
            }

            if ('description' === $filterName) {
                $qb->andWhere("cl.description LIKE :$filterName");
                $qb->setParameter($filterName, '%' . $filterValue . '%');

                continue;
            }

            if ('position' === $filterName) {
                // When filtering by position,
                // value must be decreased by 1,
                // since position value in database starts at 0,
                // but for user display positions are increased by 1.
                if (is_numeric($filterValue)) {
                    --$filterValue;
                } else {
                    $filterValue = null;
                }

                $qb->andWhere("cs.position = :$filterName");
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            if ('active' === $filterName) {
                $qb->andWhere("c.active = :$filterName");
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            if ('id_category_parent' === $filterName) {
                if ($this->isSearchRequestOnHomeCategory($filters)) {
                    continue;
                }

                $qb->andWhere("c.id_parent = :$filterName");
                $qb->setParameter($filterName, $filterValue);

                continue;
            }
        }

        if ($this->multistoreFeature->isUsed() && $this->multistoreContextChecker->isSingleShopContext()) {
            $qb->andWhere('cs.id_shop = :context_shop_id');
        }

        return $qb;
    }

    /**
     * @param array $filters
     *
     * @return bool
     */
    private function isSearchRequestOnHomeCategory(array $filters)
    {
        return isset($filters['is_home_category'], $filters['is_search_request'])
            && $filters['is_home_category'] === true && $filters['is_search_request'] === true;
    }
}
