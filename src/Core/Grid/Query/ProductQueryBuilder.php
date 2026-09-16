<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopGroupRepository;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopGroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Grid\Query\Filter\DoctrineFilterApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\Filter\SqlFilters;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\ShopSearchCriteriaInterface;

/**
 * Defines all required sql statements to render products list.
 */
class ProductQueryBuilder extends AbstractDoctrineQueryBuilder
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
     * @var DoctrineFilterApplicatorInterface
     */
    private $filterApplicator;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ShopGroupRepository
     */
    private $shopGroupRepository;

    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        int $contextLanguageId,
        DoctrineFilterApplicatorInterface $filterApplicator,
        Configuration $configuration,
        ShopGroupRepository $shopGroupRepository
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->contextLanguageId = $contextLanguageId;
        $this->filterApplicator = $filterApplicator;
        $this->configuration = $configuration;
        $this->shopGroupRepository = $shopGroupRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        /*
         * WHY id-first pagination: the display query joins seven tables, and the database drives it from
         * product_shop's id_shop index rather than from the product primary key, so rows do not arrive in
         * id_product order. That makes the ORDER BY a sort of the whole matching set, and every row is
         * joined through all seven tables before LIMIT discards all but twenty of them. Measured on 60,020
         * products: 5.9 s for one page.
         *
         * Narrowing the SELECT does not help - the joins are the cost, not the projection (measured at
         * 5.2 s with id_product alone). So the page of ids is resolved first, against product and
         * product_shop plus only the joins a filter or the sort actually needs, which lets the database
         * walk the primary key backwards and stop after twenty rows; the display columns are then fetched
         * for those twenty. 2.5 ms for the same page.
         */
        $paginatedIdsQb = $this
            ->getQueryBuilder($searchCriteria, $this->getAliasesNeededToSelectAPage($searchCriteria))
            ->select('p.`id_product`')
        ;
        $this->applySortingToPaginatedIds($paginatedIdsQb, $searchCriteria);
        $this->searchCriteriaApplicator->applyPagination($searchCriteria, $paginatedIdsQb);

        $qb = $this->getQueryBuilder($searchCriteria);
        $qb
            ->addSelect('p.`id_product`, p.`reference`, p.`id_shop_default`, p.`product_type`')
            ->addSelect('ps.`price` AS `price_tax_excluded`, ps.`ecotax` AS `ecotax_tax_excluded`, ps.`id_tax_rules_group`, ps.`active`')
            ->addSelect('pl.`name`, pl.`link_rewrite`')
            ->addSelect('cl.`name` AS `category`')
            ->addSelect('img_shop.`id_image`')
            ->addSelect('img_lang.legend')
            ->addSelect('p.`id_tax_rules_group`')
            ->addSelect('(ps.`price` + ps.`ecotax`) AS `final_price_tax_excluded`')
        ;

        if ($this->configuration->getBoolean('PS_STOCK_MANAGEMENT')) {
            $qb->addSelect('IF(sa.`quantity` IS NULL OR sa.`quantity` = \'\', 0, sa.`quantity`) AS quantity');
        }

        /*
         * WHY the page is joined in rather than passed as a list of ids: it keeps this a single statement,
         * so the grid still receives one query builder, and the filters stay written once - the ids query
         * carries them and the display query inherits the result.
         */
        $qb->innerJoin('p', '(' . $paginatedIdsQb->getSQL() . ')', 'paginated', 'paginated.`id_product` = p.`id_product`');
        $qb->setParameters($paginatedIdsQb->getParameters(), $paginatedIdsQb->getParameterTypes());

        // Any sorting that is not based on position can be applied
        if ($searchCriteria->getOrderBy() !== 'position') {
            $this->searchCriteriaApplicator->applySorting($searchCriteria, $qb);
        } elseif (array_key_exists('id_category', $searchCriteria->getFilters())) {
            // Sort by position only works when we filter by category, so we need to be cautious and apply it only when the filter is present
            $this->searchCriteriaApplicator->applySorting($searchCriteria, $qb);
        }

        $this->searchCriteriaApplicator->applyDeterministicSorting($searchCriteria, $qb, 'p', 'id_product');

        return $qb;
    }

    /**
     * The ids query has no SELECT aliases to order by, so the grid's column name is resolved to the
     * expression behind it - the same reason OrderQueryBuilder sorts its ids query by the customer
     * expression rather than by the `customer` alias.
     *
     * @return array<string, string>
     */
    private function getSortableColumnExpressions(): array
    {
        return [
            'id_product' => 'p.`id_product`',
            'reference' => 'p.`reference`',
            'name' => 'pl.`name`',
            'category' => 'cl.`name`',
            'active' => 'ps.`active`',
            'price_tax_excluded' => 'ps.`price`',
            'final_price_tax_excluded' => '(ps.`price` + ps.`ecotax`)',
            'quantity' => 'sa.`quantity`',
            'position' => 'pc.`position`',
        ];
    }

    /**
     * Everything the ids query needs beyond product and product_shop: the table behind the sorted
     * column, and the table behind every column being filtered. The rest of the joins only carry
     * display columns and cannot change which products match, so they are left to the second query.
     *
     * @return array<int, string>
     */
    private function getAliasesNeededToSelectAPage(SearchCriteriaInterface $searchCriteria): array
    {
        $aliasOfColumn = [
            'name' => 'pl',
            'category' => 'cl',
            'quantity' => 'sa',
            'position' => 'pc',
        ];

        $needed = [];
        foreach (array_keys($searchCriteria->getFilters()) as $filteredColumn) {
            if (isset($aliasOfColumn[$filteredColumn])) {
                $needed[] = $aliasOfColumn[$filteredColumn];
            }
        }

        $orderBy = $searchCriteria->getOrderBy();
        if (null !== $orderBy && isset($aliasOfColumn[$orderBy])) {
            $needed[] = $aliasOfColumn[$orderBy];
        }

        return array_values(array_unique($needed));
    }

    private function applySortingToPaginatedIds(QueryBuilder $qb, SearchCriteriaInterface $searchCriteria): void
    {
        $orderBy = $searchCriteria->getOrderBy();
        $orderWay = $searchCriteria->getOrderWay();
        $expressions = $this->getSortableColumnExpressions();

        // Sort by position only works when we filter by category, exactly as in the display query.
        $positionWithoutCategory = 'position' === $orderBy && !array_key_exists('id_category', $searchCriteria->getFilters());

        if (null !== $orderBy && null !== $orderWay && isset($expressions[$orderBy]) && !$positionWithoutCategory) {
            $qb->orderBy($expressions[$orderBy], $orderWay);
        }

        if ('id_product' !== $orderBy) {
            $qb->addOrderBy('p.`id_product`', $orderWay ?? 'ASC');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        /*
         * The count inherited every display join, and each of them is a single-row lookup on a primary or
         * unique key, so none can change the number of rows counted - they were pure cost. Counting through
         * product and product_shop plus the filtered columns only took 5.2 s to 1.3 s on 60,020 products.
         */
        $qb = $this->getQueryBuilder($searchCriteria, $this->getAliasesNeededToCount($searchCriteria));
        $qb->select('COUNT(p.`id_product`)');

        return $qb;
    }

    /**
     * Like getAliasesNeededToSelectAPage(), without the sort: ordering cannot change a count.
     *
     * @return array<int, string>
     */
    private function getAliasesNeededToCount(SearchCriteriaInterface $searchCriteria): array
    {
        $aliasOfColumn = [
            'name' => 'pl',
            'category' => 'cl',
            'quantity' => 'sa',
            'position' => 'pc',
        ];

        $needed = [];
        foreach (array_keys($searchCriteria->getFilters()) as $filteredColumn) {
            if (isset($aliasOfColumn[$filteredColumn])) {
                $needed[] = $aliasOfColumn[$filteredColumn];
            }
        }

        return array_values(array_unique($needed));
    }

    /**
     * Gets query builder.
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    /**
     * @param array<int, string>|null $onlyJoins when given, the joins that only carry display columns are
     *                                           left out and just these aliases are added to product and
     *                                           product_shop - see getAliasesNeededToSelectAPage()
     */
    private function getQueryBuilder(SearchCriteriaInterface $searchCriteria, ?array $onlyJoins = null): QueryBuilder
    {
        $joins = static function (string $alias) use ($onlyJoins): bool {
            return null === $onlyJoins || in_array($alias, $onlyJoins, true);
        };

        if (!$searchCriteria instanceof ShopSearchCriteriaInterface) {
            throw new InvalidArgumentException(sprintf('Invalid search criteria, expected a %s', ShopSearchCriteriaInterface::class));
        }

        $shopId = null;
        $filteredShopGroupId = null;
        $sharedStockGroupId = null;
        if ($searchCriteria->getShopConstraint()->getShopId()) {
            $shopId = $searchCriteria->getShopConstraint()->getShopId()->getValue();
            $shopGroup = $this->shopGroupRepository->getByShop($searchCriteria->getShopConstraint()->getShopId());
            $sharedStockGroupId = (bool) $shopGroup->share_stock ? (int) $shopGroup->id : null;
        } elseif ($searchCriteria->getShopConstraint()->getShopGroupId()) {
            $filteredShopGroupId = $searchCriteria->getShopConstraint()->getShopGroupId()->getValue();
            $shopGroup = $this->shopGroupRepository->get(new ShopGroupId($filteredShopGroupId));
            $sharedStockGroupId = (bool) $shopGroup->share_stock ? $filteredShopGroupId : null;
        }

        $filterValues = $searchCriteria->getFilters();
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'product', 'p')
            ->innerJoin(
                'p',
                $this->dbPrefix . 'product_shop',
                'ps',
                $this->addShopCondition('ps.`id_product` = p.`id_product`', 'ps', $shopId, $filteredShopGroupId)
            )
            ->andWhere('p.`state` = 1')
        ;

        // This join is only useful in multishop mode, but it's too complicated to handle this in ProductShopsQueryBuilder since
        // it must be called precisely here
        if ($joins('s')) {
            $qb->leftJoin('p', $this->dbPrefix . 'shop', 's', 's.`id_shop` = ps.`id_shop`');
        }
        if ($joins('pl')) {
            $qb->leftJoin(
                'p',
                $this->dbPrefix . 'product_lang',
                'pl',
                $this->addShopCondition('pl.`id_product` = p.`id_product` AND pl.`id_lang` = :langId', 'pl', $shopId, $filteredShopGroupId)
            );
        }
        if ($joins('cl')) {
            $qb->leftJoin(
                'ps',
                $this->dbPrefix . 'category_lang',
                'cl',
                $this->addShopCondition('cl.`id_category` = ps.`id_category_default` AND cl.`id_lang` = :langId', 'cl', $shopId, $filteredShopGroupId)
            );
        }
        if ($joins('img_shop')) {
            $qb->leftJoin(
                'ps',
                $this->dbPrefix . 'image_shop',
                'img_shop',
                $this->addShopCondition('img_shop.`id_product` = ps.`id_product` AND img_shop.`cover` = 1', 'img_shop', $shopId, $filteredShopGroupId)
            );
        }
        if ($joins('img_lang')) {
            $qb->leftJoin(
                'img_shop',
                $this->dbPrefix . 'image_lang',
                'img_lang',
                'img_shop.`id_image` = img_lang.`id_image` AND img_lang.`id_lang` = :langId'
            );
        }

        $filteredCategoryId = $this->getFilteredCategoryId($filterValues);
        if (null !== $filteredCategoryId) {
            $qb
                ->rightJoin(
                    'p',
                    $this->dbPrefix . 'category_product',
                    'pc',
                    'p.`id_product` = pc.`id_product` AND pc.id_category = :categoryId'
                )
                ->setParameter('categoryId', $filteredCategoryId)
                ->addSelect('pc.`position`, pc.`id_category`')
            ;
        }

        $isStockManagementEnabled = $this->configuration->getBoolean('PS_STOCK_MANAGEMENT');

        if ($isStockManagementEnabled) {
            $stockOnCondition = $this->getStockOnCondition($sharedStockGroupId, $shopId, $filteredShopGroupId);
            if ($sharedStockGroupId) {
                $qb->setParameter('sharedShopGroupId', $sharedStockGroupId);
            }

            if ($joins('sa')) {
                $qb->leftJoin(
                    'p',
                    $this->dbPrefix . 'stock_available',
                    'sa',
                    $stockOnCondition
                );
            }
        }

        // Prepare filters
        $sqlFilters = new SqlFilters();
        $sqlFilters
            ->addFilter(
                'id_product',
                'p.`id_product`',
                SqlFilters::MIN_MAX
            )
        ;

        // When ecotax is enabled the real final price is the sum of price and ecotax so the filters must be setup accordingly
        if ($this->configuration->getBoolean('PS_USE_ECOTAX')) {
            $sqlFilters->addFilter(
                'final_price_tax_excluded',
                '(ps.`price` + ps.`ecotax`)',
                SqlFilters::MIN_MAX
            );
        } else {
            $sqlFilters->addFilter(
                'final_price_tax_excluded',
                'ps.`price`',
                SqlFilters::MIN_MAX
            );
        }

        if ($isStockManagementEnabled) {
            $sqlFilters
                ->addFilter(
                    'quantity',
                    'sa.`quantity`',
                    SqlFilters::MIN_MAX
                )
            ;
        }

        $this->filterApplicator->apply($qb, $sqlFilters, $filterValues);

        // If shop is specified we use it as the reference, if not we use the product's default shop (for each product)
        $qb->setParameter('langId', $this->contextLanguageId);
        if ($shopId) {
            $qb->setParameter('shopId', $shopId);
        }
        if ($filteredShopGroupId) {
            $qb->setParameter('filteredShopGroupId', $filteredShopGroupId);
        }

        foreach ($filterValues as $filterName => $filter) {
            if ('active' === $filterName) {
                $qb->andWhere('ps.`active` = :active');
                $qb->setParameter('active', $filter);
            }

            if ('name' === $filterName) {
                $qb->andWhere('pl.`name` LIKE :name');
                $qb->setParameter('name', '%' . $filter . '%');
            }

            if ('reference' === $filterName) {
                $qb->andWhere('p.`reference` LIKE :reference');
                $qb->setParameter('reference', '%' . $filter . '%');
            }

            if ('category' === $filterName) {
                $qb->andWhere('cl.`name` LIKE :category');
                $qb->setParameter('category', '%' . $filter . '%');
            }

            // Filter by position is only relevant when a category has been selected
            if (array_key_exists('id_category', $filterValues) && 'position' === $filterName) {
                $qb->andWhere('pc.`position` = :position');
                $qb->setParameter('position', $filter);
            }
        }

        return $qb;
    }

    protected function addShopCondition(string $sql, string $tableAlias, ?int $shopId, ?int $filteredShopGroupId): string
    {
        if ($shopId) {
            // Single shop context simple left join on a single shopId
            return $sql . ' AND ' . $tableAlias . '.`id_shop` = :shopId';
        } elseif ($filteredShopGroupId) {
            // Group shop context, we add a condition on the left join that the id_shop must be part of the group shop AND be associated with the product
            // And we only select the MIN because we only need the first id_shop which will be used as the default display thus we don't need to use a group by on id_product
            $groupSubQuery = '
                SELECT MIN(s2.id_shop)
                FROM ' . $this->dbPrefix . 'shop s2
                INNER JOIN ' . $this->dbPrefix . 'product_shop ps2
                ON ps2.id_shop = s2.id_shop
                WHERE s2.id_shop_group = :filteredShopGroupId
                AND ps2.id_product = ps.id_product';

            return $sql . ' AND ' . $tableAlias . '.`id_shop` IN (' . $groupSubQuery . ')';
        }

        // All shops context left join on the product's default shop
        return $sql . ' AND ' . $tableAlias . '.`id_shop` = p.id_shop_default';
    }

    protected function getStockOnCondition(?int $sharedStockGroupId, ?int $shopId, ?int $filteredShopGroupId): string
    {
        $stockOnCondition =
            'sa.`id_product` = p.`id_product`
            AND sa.`id_product_attribute` = 0
        ';

        if ($sharedStockGroupId) {
            $stockOnCondition .= 'AND sa.`id_shop` = 0 AND sa.`id_shop_group` = :sharedShopGroupId';
        } else {
            $stockOnCondition = $this->addShopCondition($stockOnCondition, 'sa', $shopId, $filteredShopGroupId);
        }

        return $stockOnCondition;
    }

    private function getFilteredCategoryId(array $filterValues): ?int
    {
        foreach ($filterValues as $filterName => $filter) {
            if ('id_category' === $filterName) {
                return (int) $filter;
            }
        }

        return null;
    }
}
