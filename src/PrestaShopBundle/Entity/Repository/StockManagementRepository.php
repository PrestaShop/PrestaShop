<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity\Repository;

use Context;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Statement;
use Doctrine\ORM\EntityManager;
use Employee;
use PDO;
use PrestaShop\PrestaShop\Adapter\ImageManager;
use PrestaShop\PrestaShop\Adapter\LegacyContext as ContextAdapter;
use PrestaShopBundle\Api\QueryParamsCollection;
use PrestaShopBundle\Entity\ProductIdentity;
use PrestaShopBundle\Exception\NotImplementedException;
use Product;
use RuntimeException;
use Shop;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class StockManagementRepository
{
    use NormalizeFieldTrait;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var ContextAdapter
     */
    protected $contextAdapter;

    /**
     * @var ImageManager
     */
    protected $imageManager;

    /**
     * @var string
     */
    protected $tablePrefix;

    /**
     * @var int
     */
    protected $foundRows = 0;

    /**
     * @var array
     */
    protected $productFeatures = [];

    /**
     * @param ContainerInterface $container
     * @param Connection $connection
     * @param ContextAdapter $contextAdapter
     * @param ImageManager $imageManager
     * @param string $tablePrefix
     *
     * @throws NotImplementedException
     */
    public function __construct(
        ContainerInterface $container,
        Connection $connection,
        EntityManager $entityManager,
        ContextAdapter $contextAdapter,
        ImageManager $imageManager,
        $tablePrefix
    ) {
        $this->container = $container;
        $this->connection = $connection;
        $this->em = $entityManager;
        $this->contextAdapter = $contextAdapter;
        $this->imageManager = $imageManager;
        $this->tablePrefix = $tablePrefix;
    }

    /**
     * Returns the current context
     */
    protected function getCurrentContext(): Context
    {
        return $this->contextAdapter->getContext();
    }

    /**
     * Returns the employee set in current context
     */
    protected function getCurrentEmployee(): Employee
    {
        $employee = $this->getCurrentContext()->employee;

        if (!$employee instanceof Employee) {
            throw new RuntimeException('Determining the active language requires a contextual employee instance.');
        }

        return $employee;
    }

    /**
     * Returns the language ID of the employee in current context
     */
    protected function getCurrentLanguageId(): int
    {
        return (int) $this->getCurrentEmployee()->id_lang;
    }

    /**
     * Returns the shop set in current context
     *
     * @throws NotImplementedException
     */
    protected function getCurrentShop(): Shop
    {
        $shop = $this->getCurrentContext()->shop;

        if (!$shop instanceof Shop) {
            throw new RuntimeException('Determining the active shop requires a contextual shop instance.');
        }
        if ($shop->getContextType() !== Shop::CONTEXT_SHOP) {
            throw new NotImplementedException('Shop context types other than "single shop" are not supported');
        }

        return $shop;
    }

    /**
     * Returns the contextual ID of the shop in current context
     *
     * @throws NotImplementedException
     */
    protected function getContextualShopId(): int
    {
        return $this->getCurrentShop()->getContextualShopId();
    }

    /**
     * @param array $rows
     *
     * @return array
     */
    protected function addAdditionalData(array $rows)
    {
        $rows = $this->addCombinationsAndFeatures($rows);
        $rows = $this->addImageThumbnailPaths($rows);
        $rows = $this->addProductLink($rows);

        return $rows;
    }

    /**
     * @param array $rows
     *
     * @return array
     */
    protected function addProductLink(array $rows)
    {
        $router = $this->container->get('router');

        foreach ($rows as &$row) {
            $row['product_url'] = $router->generate('admin_products_edit', [
                'productId' => $row['product_id'],
            ]);
        }

        return $rows;
    }

    /**
     * @param array $rows
     *
     * @return array
     */
    protected function addImageThumbnailPaths(array $rows)
    {
        foreach ($rows as &$row) {
            $row['product_thumbnail'] = 'N/A';
            $row['combination_thumbnail'] = 'N/A';

            if ((int) $row['product_cover_id'] > 0) {
                $row['product_thumbnail'] = $this->imageManager->getThumbnailPath(
                    $row['product_cover_id']
                );
            }

            if ((int) $row['combination_cover_id'] > 0) {
                $row['combination_thumbnail'] = $this->imageManager->getThumbnailPath(
                    $row['combination_cover_id']
                );
            }
        }

        return $rows;
    }

    /**
     * @param QueryParamsCollection $queryParams
     *
     * @return mixed
     */
    public function getData(QueryParamsCollection $queryParams)
    {
        $andWhere = $this->andWhere($queryParams);
        $having = $this->having($queryParams);
        $query = $this->selectSql($andWhere, $having, $this->orderBy($queryParams)) . $this->paginate();

        $statement = $this->connection->prepare($query);
        $this->bindStockManagementValues($statement, $queryParams, null, $query);

        $result = $statement->executeQuery();
        $rows = $result->fetchAllAssociative();
        $result->free();
        $this->foundRows = $this->countFoundRows($this->selectSql($andWhere, $having, ''), $queryParams);

        $rows = $this->addAdditionalData($rows);

        return $this->castNumericToInt($rows);
    }

    /**
     * @param string $andWhereClause
     * @param string $having
     * @param string|null $orderByClause
     *
     * @return mixed
     */
    protected function selectSql(
        $andWhereClause = '',
        $having = '',
        $orderByClause = null
    ) {
        throw new RuntimeException('You need to implement your own selectSql function.');
    }

    /**
     * @param QueryParamsCollection $queryParams
     *
     * @return int
     */
    public function countPages(QueryParamsCollection $queryParams)
    {
        $paginationParams = $queryParams->getSqlPaginationParams();

        return (int) ceil($this->foundRows / $paginationParams[QueryParamsCollection::SQL_PARAM_MAX_RESULTS]);
    }

    /**
     * @param QueryParamsCollection $queryParams
     *
     * @return string
     */
    protected function andWhere(QueryParamsCollection $queryParams)
    {
        $filters = $queryParams->getSqlFilters();
        $filters = strtr($filters[$queryParams::SQL_CLAUSE_WHERE], [
            '{product_id}' => 'p.id_product',
            '{supplier_id}' => 'p.id_supplier',
            '{id_employee}' => 'sm.id_employee',
            '{date_add}' => 'sm.date_add',
            '{id_stock_mvt_reason}' => 'sm.id_stock_mvt_reason',
            '{active}' => 'p.active',
        ]);

        return $filters;
    }

    /**
     * @param QueryParamsCollection $queryParams
     *
     * @return string
     */
    protected function having(QueryParamsCollection $queryParams)
    {
        $filters = $queryParams->getSqlFilters();

        if (!array_key_exists($queryParams::SQL_CLAUSE_HAVING, $filters)) {
            return '';
        }

        return strtr($filters['having'], [
            '{combination_name}' => 'combination_name',
            '{product_reference}' => 'product_reference',
            '{product_ean13}' => 'product_ean13',
            '{product_isbn}' => 'product_isbn',
            '{product_upc}' => 'product_upc',
            '{product_mpn}' => 'product_mpn',
            '{combination_ean13}' => 'combination_ean13',
            '{combination_isbn}' => 'combination_isbn',
            '{combination_upc}' => 'combination_upc',
            '{combination_mpn}' => 'combination_mpn',
            '{supplier_name}' => 'supplier_name',
            '{product_name}' => 'product_name',
        ]);
    }

    /**
     * @param QueryParamsCollection $queryParams
     *
     * @return string
     */
    protected function orderBy(QueryParamsCollection $queryParams)
    {
        $orderByClause = $queryParams->getSqlOrder();

        $descendingOrder = str_contains($orderByClause, ' DESC');

        $productColumns = 'product_id, combination_id';
        if ($descendingOrder) {
            $productColumns = 'product_id DESC, combination_id DESC';
        }

        return strtr($orderByClause, [
            '{product} DESC' => $productColumns,
            '{product}' => $productColumns,
            '{product_id}' => 'product_id',
            '{product_name}' => 'product_name',
            '{combination_id}' => 'combination_id',
            '{reference}' => 'product_reference',
            '{supplier}' => 'supplier_name',
            '{available_quantity}' => 'product_available_quantity',
            '{physical_quantity}' => 'product_physical_quantity',
            '{id_stock_mvt}' => 'id_stock_mvt',
            '{date_add}' => 'date_add',
            '{product_low_stock_alert}' => 'product_low_stock_alert',
        ]);
    }

    /**
     * @return string
     */
    protected function paginate()
    {
        return sprintf(
            ' LIMIT :%s OFFSET :%s',
            QueryParamsCollection::SQL_PARAM_MAX_RESULTS,
            QueryParamsCollection::SQL_PARAM_FIRST_RESULT
        );
    }

    /**
     * @param Statement $statement
     * @param QueryParamsCollection|null $queryParams
     * @param ProductIdentity|null $productIdentity
     * @param string|null $query when given, the values that this query does not use are not bound
     */
    protected function bindStockManagementValues(
        Statement $statement,
        ?QueryParamsCollection $queryParams = null,
        ?ProductIdentity $productIdentity = null,
        ?string $query = null
    ) {
        $shop = $this->getCurrentShop();
        $shopId = $shop->getContextualShopId();
        $shopGroup = $shop->getGroup();
        $languageId = $this->getCurrentLanguageId();

        $this->bindValueUsedBy($statement, $query, 'shop_id', $shopId, PDO::PARAM_INT);
        $this->bindValueUsedBy($statement, $query, 'language_id', $languageId, PDO::PARAM_INT);
        $this->bindValueUsedBy($statement, $query, 'state', Product::STATE_SAVED, PDO::PARAM_INT);

        // if quantities are shared between shops of the group
        if ($shopGroup->share_stock) {
            $stockShopId = 0;
            $stockGroupId = $shopGroup->id;
        } else {
            $stockShopId = $shopId;
            $stockGroupId = 0;
        }

        $this->bindValueUsedBy($statement, $query, 'stock_shop_id', $stockShopId, PDO::PARAM_INT);
        $this->bindValueUsedBy($statement, $query, 'stock_group_id', $stockGroupId, PDO::PARAM_INT);

        if ($queryParams) {
            $this->bindValuesInStatement($statement, $queryParams, $query);
        }

        if ($productIdentity) {
            $this->bindValueUsedBy($statement, $query, 'product_id', $productIdentity->getProductId(), PDO::PARAM_INT);
            $this->bindValueUsedBy($statement, $query, 'combination_id', $productIdentity->getCombinationId(), PDO::PARAM_INT);
        }
    }

    /**
     * @param Statement $statement
     * @param QueryParamsCollection $queryParams
     * @param string|null $query when given, the values that this query does not use are not bound
     */
    protected function bindValuesInStatement(Statement $statement, QueryParamsCollection $queryParams, ?string $query = null)
    {
        $sqlParams = $queryParams->getSqlParams();

        foreach ($sqlParams as $name => $value) {
            $this->bindValueUsedBy($statement, $query, (string) $name, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
    }

    /**
     * PostgreSQL rejects a value bound to a parameter that the query does not contain.
     */
    private function bindValueUsedBy(Statement $statement, ?string $query, string $name, mixed $value, int $type): void
    {
        if (null !== $query && !preg_match('/:' . preg_quote(ltrim($name, ':'), '/') . '\b/', $query)) {
            return;
        }

        $statement->bindValue($name, $value, $type);
    }

    /**
     * @param Statement $statement
     * @param QueryParamsCollection $queryParams
     */
    protected function bindMaxResultsValue(Statement $statement, QueryParamsCollection $queryParams)
    {
        $paginationParams = $queryParams->getSqlPaginationParams();
        $statement->bindValue(
            QueryParamsCollection::SQL_PARAM_MAX_RESULTS,
            $paginationParams[QueryParamsCollection::SQL_PARAM_MAX_RESULTS],
            PDO::PARAM_INT
        );
    }

    /**
     * Counts the rows of the query without pagination.
     */
    private function countFoundRows(string $query, QueryParamsCollection $queryParams): int
    {
        $countQuery = 'SELECT COUNT(*) FROM (' . $query . ') found_rows';
        $statement = $this->connection->prepare($countQuery);
        $this->bindStockManagementValues($statement, $queryParams, null, $countQuery);

        $result = $statement->executeQuery();
        $rowCount = (int) $result->fetchOne();
        $result->free();

        return $rowCount;
    }

    /**
     * Aggregates the values of the expression into a string, like MySQL GROUP_CONCAT.
     */
    protected function groupConcat(string $expression, string $separator = ',', string $orderBy = '', bool $distinct = false): string
    {
        $distinctKeyword = $distinct ? 'DISTINCT ' : '';
        $orderByClause = $orderBy !== '' ? ' ORDER BY ' . $orderBy : '';
        $quotedSeparator = $this->connection->quote($separator);

        if ($this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            return 'STRING_AGG(' . $distinctKeyword . 'CAST(' . $expression . ' AS TEXT), ' . $quotedSeparator . $orderByClause . ')';
        }

        return 'GROUP_CONCAT(' . $distinctKeyword . $expression . $orderByClause . ' SEPARATOR ' . $quotedSeparator . ')';
    }

    /**
     * Get the attribute name subquery to be used in the select field of the main query.
     *
     * @return string
     */
    protected function getAttributeNameSubquery()
    {
        return '(SELECT ' . $this->groupConcat('agl.name', ', ', '', true) . '
                    FROM ' . $this->tablePrefix . 'product_attribute pa2
                    JOIN ' . $this->tablePrefix . 'product_attribute_combination pac ON (
                        pac.id_product_attribute = pa2.id_product_attribute
                    )
                    JOIN ' . $this->tablePrefix . 'attribute a ON (
                        a.id_attribute = pac.id_attribute
                    )
                    JOIN ' . $this->tablePrefix . 'attribute_group ag ON (
                        ag.id_attribute_group = a.id_attribute_group
                    )
                    JOIN ' . $this->tablePrefix . 'attribute_group_lang agl ON (
                        ag.id_attribute_group = agl.id_attribute_group
                        AND agl.id_lang = :language_id
                    )
                    WHERE pa2.id_product=p.id_product AND pa2.id_product_attribute=pa.id_product_attribute)
                    AS attribute_name';
    }

    /**
     * Get the combination name subquery to be used in the select field of the main query.
     *
     * @return string
     */
    protected function getCombinationNameSubquery()
    {
        return '(SELECT ' . $this->groupConcat('CONCAT(agl.name, \' - \', al.name)', ', ', '', true) . '
                    FROM ' . $this->tablePrefix . 'product_attribute pa2
                    JOIN ' . $this->tablePrefix . 'product_attribute_combination pac ON (
                        pac.id_product_attribute = pa2.id_product_attribute
                    )
                    JOIN ' . $this->tablePrefix . 'attribute a ON (
                        a.id_attribute = pac.id_attribute
                    )
                    JOIN ' . $this->tablePrefix . 'attribute_lang al ON (
                        a.id_attribute = al.id_attribute
                        AND al.id_lang = :language_id
                    )
                    JOIN ' . $this->tablePrefix . 'attribute_group ag ON (
                        ag.id_attribute_group = a.id_attribute_group
                    )
                    JOIN ' . $this->tablePrefix . 'attribute_group_lang agl ON (
                        ag.id_attribute_group = agl.id_attribute_group
                        AND agl.id_lang = :language_id
                    )
                    WHERE pa2.id_product=p.id_product AND pa2.id_product_attribute=pa.id_product_attribute)
                    AS combination_name';
    }

    /**
     * @param array $row
     *
     * @return string
     */
    protected function getProductFeatures(array $row)
    {
        if (!isset($this->productFeatures[$row['product_id']])) {
            $query = 'SELECT ' . $this->groupConcat('CONCAT(fp.id_feature, \':\', fp.id_feature_value)', ',', 'fp.id_feature_value') . ' AS features
                        FROM ' . $this->tablePrefix . 'feature_product fp
                            JOIN  ' . $this->tablePrefix . 'feature f ON (
                                fp.id_feature = f.id_feature
                            )
                            JOIN ' . $this->tablePrefix . 'feature_shop fs ON (
                                fs.id_shop = :shop_id AND
                                fs.id_feature = f.id_feature
                            )
                            JOIN ' . $this->tablePrefix . 'feature_value fv ON (
                                f.id_feature = fv.id_feature AND
                                fp.id_feature_value = fv.id_feature_value
                            )
                        WHERE fv.custom = 0 AND fp.id_product=:id_product';
            $statement = $this->connection->prepare($query);
            $statement->bindValue('id_product', (int) $row['product_id'], PDO::PARAM_INT);
            $statement->bindValue('shop_id', $this->getContextualShopId(), PDO::PARAM_INT);
            $result = $statement->executeQuery();
            $this->productFeatures[$row['product_id']] = $result->fetchOne();
            $result->free();
        }

        return (string) $this->productFeatures[$row['product_id']];
    }

    /**
     * @param array $row
     *
     * @return int
     */
    protected function getCombinationCoverId(array $row)
    {
        $query = 'SELECT id_image
                  FROM ' . $this->tablePrefix . 'product_attribute_image pai
                  WHERE id_product_attribute=:id_product_attribute
                  LIMIT 1';
        $statement = $this->connection->prepare($query);
        $statement->bindValue('id_product_attribute', (int) $row['combination_id'], PDO::PARAM_INT);
        $result = $statement->executeQuery();
        $combinationCoverId = (int) $result->fetchOne();
        $result->free();

        return $combinationCoverId;
    }

    /**
     * @param array $row
     *
     * @return string
     */
    protected function getProductAttributes(array $row)
    {
        $query = 'SELECT ' . $this->groupConcat('CONCAT(ag.id_attribute_group, \':\', a.id_attribute)', ',', 'ag.id_attribute_group, a.id_attribute') . ' AS attributes
                    FROM ' . $this->tablePrefix . 'product_attribute_combination pac
                        JOIN ' . $this->tablePrefix . 'attribute a ON (
                            pac.id_attribute = a.id_attribute
                        )
                        JOIN ' . $this->tablePrefix . 'attribute_group ag ON (
                            ag.id_attribute_group = a.id_attribute_group
                        )
                    WHERE pac.id_product_attribute=:id_product_attribute';
        $statement = $this->connection->prepare($query);
        $statement->bindValue('id_product_attribute', (int) $row['combination_id'], PDO::PARAM_INT);
        $result = $statement->executeQuery();
        $productAttributes = $result->fetchOne();
        $result->free();

        return (string) $productAttributes;
    }

    /**
     * @param array $rows
     *
     * @return array
     */
    protected function addCombinationsAndFeatures(array $rows)
    {
        foreach ($rows as &$row) {
            $row['product_features'] = $this->getProductFeatures($row);
            if ($row['combination_id'] != 0) {
                $row['combination_cover_id'] = $this->getCombinationCoverId($row);
                $row['product_attributes'] = $this->getProductAttributes($row);
            } else {
                $row['combination_name'] = 'N/A';
                $row['combination_cover_id'] = 0;
                $row['product_attributes'] = '';
            }
        }

        return $rows;
    }
}
