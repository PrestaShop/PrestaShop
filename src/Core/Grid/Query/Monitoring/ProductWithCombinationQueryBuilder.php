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

namespace PrestaShop\PrestaShop\Core\Grid\Query\Monitoring;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicator;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreContextCheckerInterface;

/**
 * Builds query for product with combination but without quantities list data
 */
final class ProductWithCombinationQueryBuilder extends AbstractDoctrineQueryBuilder
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
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicator $searchCriteriaApplicator
     * @param int $contextLangId
     * @param int $contextShopId
     * @param MultistoreContextCheckerInterface $multistoreContextChecker
     * @param FeatureInterface $multistoreFeature
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        $contextLangId,
        $contextShopId,
        DoctrineSearchCriteriaApplicator $searchCriteriaApplicator,
        MultistoreContextCheckerInterface $multistoreContextChecker,
        FeatureInterface $multistoreFeature
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->contextLangId = $contextLangId;
        $this->contextShopId = $contextShopId;
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
        $qb->select('p.id_product, p.reference, p.active, pl.name');

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
        $qb->select('COUNT(p.id_product)');

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
        $allowedFilters = ['id_product', 'reference', 'name', 'active'];

        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'product', 'p')
            ->setParameter('context_lang_id', $this->contextLangId)
            ->setParameter('context_shop_id', $this->contextShopId);

        $qb->leftJoin(
            'p',
            $this->dbPrefix . 'product_lang',
            'pl',
            $this->multistoreFeature->isUsed() && $this->multistoreContextChecker->isSingleShopContext() ?
                'p.id_product = pl.id_product AND pl.id_lang = :context_lang_id AND pl.id_shop = :context_shop_id' :
                'p.id_product = pl.id_product AND pl.id_lang = :context_lang_id AND pl.id_shop = p.id_shop_default'
        );

        $qb->leftJoin(
            'p',
            $this->dbPrefix . 'product_shop',
            'ps',
            $this->multistoreFeature->isUsed() && $this->multistoreContextChecker->isSingleShopContext() ?
                'p.id_product = ps.id_product AND ps.id_shop = :context_shop_id' :
                'p.id_product = ps.id_product AND ps.id_shop = p.id_shop_default'
        );

        $attrSubQuery = $this->connection->createQueryBuilder()
            ->select(1)
            ->from($this->dbPrefix . 'product_attribute', 'pa')
            ->andWhere('pa.id_product = p.id_product')
        ;

        $subQuery = $this->connection->createQueryBuilder();
        $subQuery->select('1')
            ->from($this->dbPrefix . 'stock_available', 'stock')
            ->andWhere('p.id_product = stock.id_product')
            ->andWhere('EXISTS(' . $attrSubQuery->getSQL() . ')')
            ->andWhere('IFNULL(stock.quantity, 0) <= 0')
        ;

        $qb->andWhere('EXISTS(' . $subQuery->getSQL() . ')');

        foreach ($filters as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters, true)) {
                continue;
            }

            if ('id_product' === $filterName) {
                $qb->andWhere("p.id_product = :$filterName");
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            if ('reference' === $filterName) {
                $qb->andWhere("p.reference LIKE :$filterName");
                $qb->setParameter($filterName, '%' . $filterValue . '%');

                continue;
            }

            if ('name' === $filterName) {
                $qb->andWhere("pl.name LIKE :$filterName");
                $qb->setParameter($filterName, '%' . $filterValue . '%');

                continue;
            }

            if ('active' === $filterName) {
                $qb->andWhere("p.active = :$filterName");
                $qb->setParameter($filterName, $filterValue);

                continue;
            }
        }

        if ($this->multistoreFeature->isUsed() && $this->multistoreContextChecker->isSingleShopContext()) {
            $qb->andWhere('ps.id_shop = :context_shop_id');
        }

        return $qb;
    }
}
