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

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Statement;
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
    protected $languageId;

    /**
     * @var int
     */
    protected $shopId;

    /**
     * @var \Context
     */
    protected $context;

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

        $this->context = $contextAdapter->getContext();

        if (!$this->context->employee instanceof Employee) {
            throw new RuntimeException('Determining the active language requires a contextual employee instance.');
        }

        $languageId = $this->context->employee->id_lang;
        $this->languageId = (int) $languageId;

        if (!$this->context->shop instanceof Shop) {
            throw new RuntimeException('Determining the active shop requires a contextual shop instance.');
        }

        $shop = $this->context->shop;
        if ($shop->getContextType() !== $shop::CONTEXT_SHOP) {
            throw new NotImplementedException('Shop context types other than "single shop" are not supported');
        }

        $this->shopId = $shop->getContextualShopId();
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
        $query = $this->selectSql(
                $this->andWhere($queryParams),
                $this->having($queryParams),
                $this->orderBy($queryParams)
            ) . $this->paginate();

        $statement = $this->connection->prepare($query);
        $this->bindStockManagementValues($statement, $queryParams);

        $statement->execute();
        $rows = $statement->fetchAll();
        $statement->closeCursor();
        $this->foundRows = $this->getFoundRows();

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
        throw new RuntimeException('You need to implement your own `selectSql` function.');
    }

    /**
     * @param QueryParamsCollection $queryParams
     *
     * @return int
     */
    public function countPages(QueryParamsCollection $queryParams)
    {
        $query = sprintf(
            'SELECT CEIL(%d / :%s) as total_pages',
            $this->foundRows,
            QueryParamsCollection::SQL_PARAM_MAX_RESULTS
        );

        $statement = $this->connection->prepare($query);
        $this->bindMaxResultsValue($statement, $queryParams);

        $statement->execute();

        $count = (int) $statement->fetchColumn();
        $statement->closeCursor();

        return $count;
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

        $descendingOrder = false !== strpos($orderByClause, ' DESC');

        $productColumns = 'product_id, combination_id';
        if ($descendingOrder) {
            $productColumns = 'product_id DESC, combination_id DESC';
        }

        return strtr($orderByClause, [
            '{product} DESC' => $productColumns,
            '{product}' => $productColumns,
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
            'LIMIT :%s,:%s',
            QueryParamsCollection::SQL_PARAM_FIRST_RESULT,
            QueryParamsCollection::SQL_PARAM_MAX_RESULTS
        );
    }

    /**
     * @param Statement $statement
     * @param QueryParamsCollection|null $queryParams
     * @param ProductIdentity|null $productIdentity
     */
    protected function bindStockManagementValues(
        Statement $statement,
        QueryParamsCollection $queryParams = null,
        ProductIdentity $productIdentity = null
    ) {
        $statement->bindValue('shop_id', $this->shopId, PDO::PARAM_INT);
        $statement->bindValue('language_id', $this->languageId, PDO::PARAM_INT);
        $statement->bindValue('state', Product::STATE_SAVED, PDO::PARAM_INT);

        // if quantities are shared between shops of the group
        $shop = $this->context->shop;
        $shopGroup = $shop->getGroup();
        if ($shopGroup->share_stock) {
            $stockShopId = 0;
            $stockGroupId = $shopGroup->id;
        } else {
            $stockShopId = $shop->getContextualShopId();
            $stockGroupId = 0;
        }

        $statement->bindValue('stock_shop_id', $stockShopId, PDO::PARAM_INT);
        $statement->bindValue('stock_group_id', $stockGroupId, PDO::PARAM_INT);

        if ($queryParams) {
            $this->bindValuesInStatement($statement, $queryParams);
        }

        if ($productIdentity) {
            $statement->bindValue('product_id', $productIdentity->getProductId(), PDO::PARAM_INT);
            $statement->bindValue('combination_id', $productIdentity->getCombinationId(), PDO::PARAM_INT);
        }
    }

    /**
     * @param Statement $statement
     * @param QueryParamsCollection $queryParams
     */
    protected function bindValuesInStatement(Statement $statement, QueryParamsCollection $queryParams)
    {
        $sqlParams = $queryParams->getSqlParams();

        foreach ($sqlParams as $name => $value) {
            if (is_int($value)) {
                $statement->bindValue($name, $value, PDO::PARAM_INT);
            } else {
                $statement->bindValue($name, $value, PDO::PARAM_STR);
            }
        }
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
     * Store the number of rows found in a previous query executed with SQL_CALC_FOUND_ROWS.
     */
    protected function getFoundRows()
    {
        $statement = $this->connection->prepare('SELECT FOUND_ROWS()');
        $statement->execute();
        $rowCount = (int) $statement->fetchColumn();
        $statement->closeCursor();

        return $rowCount;
    }

    /**
     * Get the attribute name subquery to be used in the select field of the main query.
     *
     * @return string
     */
    protected function getAttributeNameSubquery()
    {
        return '(SELECT GROUP_CONCAT(
                        DISTINCT CONCAT(agl.name)
                        SEPARATOR ", "
                    )
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
        return '(SELECT GROUP_CONCAT(
                        DISTINCT CONCAT(agl.name, " - ", al.name)
                        SEPARATOR ", "
                    )
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
            $query = 'SELECT GROUP_CONCAT(
                      CONCAT(fp.id_feature, ":", fp.id_feature_value)
                      ORDER BY fp.id_feature_value
                    ) AS features
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
            $statement->bindValue('id_product', (int) $row['product_id'], \PDO::PARAM_INT);
            $statement->bindValue('shop_id', $this->shopId, \PDO::PARAM_INT);
            $statement->execute();
            $this->productFeatures[$row['product_id']] = $statement->fetchColumn(0);
            $statement->closeCursor();
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
        $statement->bindValue('id_product_attribute', (int) $row['combination_id'], \PDO::PARAM_INT);
        $statement->execute();
        $combinationCoverId = (int) $statement->fetchColumn(0);
        $statement->closeCursor();

        return $combinationCoverId;
    }

    /**
     * @param array $row
     *
     * @return string
     */
    protected function getProductAttributes(array $row)
    {
        $query = 'SELECT GROUP_CONCAT(
                    CONCAT(ag.id_attribute_group, ":", a.id_attribute)
                    ORDER BY ag.id_attribute_group, a.id_attribute
                ) AS attributes
                    FROM ' . $this->tablePrefix . 'product_attribute_combination pac
                        JOIN ' . $this->tablePrefix . 'attribute a ON (
                            pac.id_attribute = a.id_attribute
                        )
                        JOIN ' . $this->tablePrefix . 'attribute_group ag ON (
                            ag.id_attribute_group = a.id_attribute_group
                        )
                    WHERE pac.id_product_attribute=:id_product_attribute';
        $statement = $this->connection->prepare($query);
        $statement->bindValue('id_product_attribute', (int) $row['combination_id'], \PDO::PARAM_INT);
        $statement->execute();
        $productAttributes = $statement->fetchColumn(0);
        $statement->closeCursor();

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
