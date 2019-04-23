<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
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
     * @var FeatureInterface
     */
    private $multistoreFeature;

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
     * @param FeatureInterface $multistoreFeature
     * @param $rootCategoryId
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicator $searchCriteriaApplicator,
        $contextLangId,
        $contextShopId,
        MultistoreContextCheckerInterface $multistoreContextChecker,
        FeatureInterface $multistoreFeature,
        $rootCategoryId
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->contextLangId = $contextLangId;
        $this->contextShopId = $contextShopId;
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->multistoreContextChecker = $multistoreContextChecker;
        $this->multistoreFeature = $multistoreFeature;
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

        $qb->andWhere('NOT EXISTS(' . $subSelect->getSQL() .')');
        $qb->andWhere('c.id_category != ' . $this->rootCategoryId);

        foreach ($filters as $filterName => $filterValue) {
            if ('id_category' === $filterName) {
                $qb->andWhere("c.id_category = :$filterName");
                $qb->setParameter($filterName, $filterValue);

                continue;
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

            if ('active' === $filterName) {
                $qb->andWhere("c.active = :$filterName");
                $qb->setParameter($filterName, $filterValue);

                continue;
            }
        }

        if ($this->multistoreFeature->isUsed() && $this->multistoreContextChecker->isSingleShopContext()) {
            $qb->andWhere('cs.id_shop = :context_shop_id');
        }

        return $qb;
    }
}
