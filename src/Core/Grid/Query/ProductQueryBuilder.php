<?php

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Defines all required sql statements to render products list.
 */
final class ProductQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @var int
     */
    private $contextLanguageId;

    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @var bool
     */
    private $isStockManagementEnabled;

    /**
     * @var bool
     */
    private $isStockSharingBetweenShopGroupEnabled;

    /**
     * @var int
     */
    private $contextShopGroupId;

    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        $contextLanguageId,
        $contextShopId,
        $contextShopGroupId,
        $isStockManagementEnabled,
        $isStockSharingBetweenShopGroupEnabled
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->contextLanguageId = $contextLanguageId;
        $this->contextShopId = $contextShopId;
        $this->isStockManagementEnabled = $isStockManagementEnabled;
        $this->isStockSharingBetweenShopGroupEnabled = $isStockSharingBetweenShopGroupEnabled;
        $this->contextShopGroupId = $contextShopGroupId;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb
            ->select('p.`id_product`, p.`reference`')
            ->addSelect('ps.`price` AS `price_tax_excluded`, ps.`active`')
            ->addSelect('pl.`name`')
            ->addSelect('cl.`name` AS `category`')
        ;

        if ($this->isStockManagementEnabled) {
            $qb->addSelect('sa.`quantity`');
        }

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb)
        ;

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb->select('COUNT(p.`id_product`)');

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
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'product', 'p')
            ->innerJoin(
                'p',
                $this->dbPrefix . 'product_shop',
                'ps',
                'ps.`id_product` = p.`id_product` AND ps.`id_shop` = :id_shop'
            )
            ->leftJoin(
                'p',
                $this->dbPrefix . 'product_lang',
                'pl',
                'pl.`id_product` = p.`id_product` AND pl.`id_lang` = :id_lang AND pl.`id_shop` = :id_shop'
            )
            ->leftJoin(
                'ps',
                $this->dbPrefix . 'category_lang',
                'cl',
                'cl.`id_category` = ps.`id_category_default` AND cl.`id_lang` = :id_lang AND cl.`id_shop` = :id_shop'
            )
        ;

        if ($this->isStockManagementEnabled) {
            // todo: test
            $stockOnCondition =
                'sa.`id_product` = p.`id_product` 
                    AND sa.`id_product_attribute` = 0
                ';

            if ($this->isStockSharingBetweenShopGroupEnabled) {
                $stockOnCondition .= '
                     AND sa.`id_shop` = 0 AND sa.`id_shop_group` = :id_shop_group
                ';
            } else {
                $stockOnCondition .= '
                     AND sa.`id_shop` = :id_shop AND sa.`id_shop_group` = 0
                ';
            }

            $qb->leftJoin(
                'p',
                $this->dbPrefix . 'stock_available',
                'sa',
                $stockOnCondition
            );

            $qb->setParameter('id_shop_group', $this->contextShopGroupId);
        }

        /** todo: raise a discussion here with shop association. maybe we should pass an array of shop ids when we are
         *   n all shop context we will see all products from all shops. Although we need shop association form in such case
        */
        $qb->setParameter('id_shop', $this->contextShopId);
        $qb->setParameter('id_lang', $this->contextLanguageId);

        return $qb;
    }
}
