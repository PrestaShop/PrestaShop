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

namespace PrestaShop\PrestaShop\Core\Grid\Query\Monitoring;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicator;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreContextCheckerInterface;

/**
 * Builds queries for empty category list data
 */
final class EmptyCategoryQueryBuilder extends AbstractDoctrineQueryBuilder
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
     * @var DoctrineSearchCriteriaApplicator
     */
    private $searchCriteriaApplicator;

    /**
     * @var MultistoreContextCheckerInterface
     */
    private $multistoreContextChecker;

    /**
     * @var int
     */
    private $rootCategoryId;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicator $searchCriteriaApplicator
     * @param int $contextLangId
     * @param int $contextShopId
     * @param MultistoreContextCheckerInterface $multistoreContextChecker
     * @param int $rootCategoryId
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        $contextLangId,
        $contextShopId,
        DoctrineSearchCriteriaApplicator $searchCriteriaApplicator,
        MultistoreContextCheckerInterface $multistoreContextChecker,
        $rootCategoryId
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->contextLangId = $contextLangId;
        $this->contextShopId = $contextShopId;
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->multistoreContextChecker = $multistoreContextChecker;
        $this->rootCategoryId = $rootCategoryId;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb->select('c.id_category, c.active, cl.name, cl.description');

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
        $isSingleShopContext = $this->multistoreContextChecker->isSingleShopContext();

        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'category', 'c')
            ->setParameter('context_lang_id', $this->contextLangId)
            ->setParameter('context_shop_id', $this->contextShopId)
            ->setParameter('root_category_id', $this->rootCategoryId);

        $qb->leftJoin(
            'c',
            $this->dbPrefix . 'category_lang',
            'cl',
            $isSingleShopContext ?
                'c.id_category = cl.id_category AND cl.id_lang = :context_lang_id AND cl.id_shop = :context_shop_id' :
                'c.id_category = cl.id_category AND cl.id_lang = :context_lang_id AND cl.id_shop = c.id_shop_default'
        );

        $qb->leftJoin(
            'c',
            $this->dbPrefix . 'category_shop',
            'cs',
            $isSingleShopContext ?
                'c.id_category = cs.id_category AND cs.id_shop = :context_shop_id' :
                'c.id_category = cs.id_category AND cs.id_shop = c.id_shop_default'
        );

        $qb->leftJoin(
            'c',
            $this->dbPrefix . 'category_product',
            'cp',
            'c.`id_category` = cp.id_category'
        );

        $subSelect = $this->connection->createQueryBuilder()
            ->select('1')
            ->from($this->dbPrefix . 'category_product', 'cp')
            ->andWhere('c.id_category = cp.id_category')
        ;

        $qb->andWhere('NOT EXISTS(' . $subSelect->getSQL() . ')');
        $qb->andWhere('c.id_category != :root_category_id');

        if ($isSingleShopContext) {
            $qb->andWhere('cs.id_shop = :context_shop_id');
        }

        $allowedFiltersAliasMap = [
            'id_category' => 'c.id_category',
            'active' => 'c.active',
            'name' => 'cl.name',
            'description' => 'cl.description',
        ];

        foreach ($filters as $filterName => $filterValue) {
            if (!array_key_exists($filterName, $allowedFiltersAliasMap)) {
                continue;
            }

            if ('active' === $filterName || 'id_category' === $filterName) {
                $qb->andWhere($allowedFiltersAliasMap[$filterName] . " = :$filterName");
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            $qb->andWhere($allowedFiltersAliasMap[$filterName] . " LIKE :$filterName");
            $qb->setParameter($filterName, '%' . $filterValue . '%');
        }

        return $qb;
    }
}
