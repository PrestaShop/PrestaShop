<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Entity\Repository;

use Configuration;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Statement;
use Employee;
use PDO;
use PrestaShop\PrestaShop\Adapter\ImageManager;
use PrestaShop\PrestaShop\Adapter\LegacyContext as ContextAdapter;
use PrestaShop\PrestaShop\Adapter\StockManager;
use PrestaShopBundle\Api\QueryParamsCollection;
use PrestaShopBundle\Api\Stock\Movement;
use PrestaShopBundle\Api\Stock\MovementsCollection;
use PrestaShopBundle\Entity\ProductIdentity;
use PrestaShopBundle\Exception\NotImplementedException;
use PrestaShopBundle\Exception\ProductNotFoundException;
use Product;
use RuntimeException;
use Shop;

class StockRepository
{
    use NormalizeFieldTrait;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var ImageManager
     */
    private $imageManager;

    /**
     * @var string
     */
    private $tablePrefix;

    /**
     * @var int
     */
    private $languageId;

    /**
     * @var int
     */
    private $shopId;

    /**
     * @var StockManager
     */
    private $stockManager;

    /**
     * @var array
     */
    private $orderStates = array();

    /**
     * @param Connection $connection
     * @param ContextAdapter $contextAdapter
     * @param ImageManager $imageManager
     * @param StockManager $stockManager
     * @param $tablePrefix
     * @throws NotImplementedException
     */
    public function __construct(
        Connection $connection,
        ContextAdapter $contextAdapter,
        ImageManager $imageManager,
        StockManager $stockManager,
        $tablePrefix
    )
    {
        $this->connection = $connection;
        $this->imageManager = $imageManager;
        $this->stockManager = $stockManager;

        $this->tablePrefix = $tablePrefix;

        $context = $contextAdapter->getContext();

        if (!$context->employee instanceof Employee) {
            throw new RuntimeException('Determining the active language requires a contextual employee instance.');
        }

        $languageId = $context->employee->id_lang;
        $this->languageId = (int)$languageId;

        if (!$context->shop instanceof Shop) {
            throw new RuntimeException('Determining the active shop requires a contextual shop instance.');
        }

        $shop = $context->shop;
        if ($shop->getContextType() !== $shop::CONTEXT_SHOP) {
            throw new NotImplementedException('Shop context types other than "single shop" are not supported');
        }

        $this->orderStates['error'] = (int)Configuration::get('PS_OS_ERROR');
        $this->orderStates['cancellation'] = (int)Configuration::get('PS_OS_CANCELED');

        $this->shopId = $shop->getContextualShopId();
    }

    /**
     * @param MovementsCollection $movements
     * @return array
     */
    public function bulkUpdateStock(MovementsCollection $movements)
    {
        return $movements->map(function (Movement $movement) {
            return $this->updateStock($movement);
        });
    }

    /**
     * @param Movement $movement
     * @return mixed
     */
    public function updateStock(Movement $movement)
    {
        $query = '
            UPDATE {table_prefix}stock_available
            SET quantity = quantity + :delta,
            physical_quantity = reserved_quantity + quantity
            WHERE id_product = :product_id
            AND id_product_attribute = :combination_id
        ';

        $query = str_replace('{table_prefix}', $this->tablePrefix, $query);

        $statement = $this->connection->prepare($query);

        $productIdentity = $movement->getProductIdentity();
        $delta = $movement->getDelta();

        $statement->bindValue('product_id', $productIdentity->getProductId(), PDO::PARAM_INT);
        $statement->bindValue('combination_id', $productIdentity->getCombinationId(), PDO::PARAM_INT);
        $statement->bindValue('delta', $delta, PDO::PARAM_INT);

        $statement->execute();

        return $this->selectStockBy($productIdentity);
    }

    /**
     * @param ProductIdentity $productIdentity
     * @return mixed
     */
    private function selectStockBy(ProductIdentity $productIdentity)
    {
        $andWhereClause = '
            AND p.id_product = :product_id
            AND COALESCE(pa.id_product_attribute, 0) = :combination_id';
        $query = $this->selectStock($andWhereClause);

        $statement = $this->connection->prepare($query);
        $this->bindStockValues($statement, null, $productIdentity);

        $statement->execute();
        $rows = $statement->fetchAll();

        if (count($rows) === 0) {
            throw new ProductNotFoundException(
                sprintf(
                    'Product with id %d and combination id %d can not be found',
                    $productIdentity->getProductId(),
                    $productIdentity->getCombinationId()
                )
            );
        }

        $rows = $this->addImageThumbnailPaths($rows);

        return $this->castNumericToInt($rows)[0];
    }

    /**
     * @param QueryParamsCollection $queryParams
     * @return mixed
     */
    public function getStock(QueryParamsCollection $queryParams)
    {
        $this->stockManager->updatePhysicalProductQuantity(
            $this->shopId,
            $this->orderStates['error'],
            $this->orderStates['cancellation']
        );

        $query = $this->selectStock(
                $this->andWhere($queryParams),
                $this->having($queryParams),
                $this->orderBy($queryParams)
            ) . $this->paginate();

        $statement = $this->connection->prepare($query);
        $this->bindStockValues($statement, $queryParams);

        $statement->execute();
        $rows = $statement->fetchAll();

        $rows = $this->addImageThumbnailPaths($rows);

        return $this->castNumericToInt($rows);
    }

    /**
     * @param QueryParamsCollection $queryParams
     * @return bool|string
     */
    public function countStockPages(QueryParamsCollection $queryParams)
    {
        $query = sprintf(
            'SELECT CEIL(FOUND_ROWS() / :%s) as total_pages',
            QueryParamsCollection::SQL_PARAM_MAX_RESULTS
        );

        $statement = $this->connection->prepare($query);
        $this->bindMaxResultsValue($statement, $queryParams);

        $statement->execute();

        return (int)$statement->fetchColumn();
    }

    /**
     * @param array $rows
     * @return array
     */
    private function addImageThumbnailPaths(array $rows)
    {
        array_walk($rows, function (&$row) {
            $row['product_thumbnail'] = 'N/A';
            $row['combination_thumbnail'] = 'N/A';

            if ((int)$row['product_cover_id'] > 0) {
                $row['product_thumbnail'] = $this->imageManager->getThumbnailPath(
                    $row['product_cover_id']
                );
            }

            if ((int)$row['combination_cover_id'] > 0) {
                $row['combination_thumbnail'] = $this->imageManager->getThumbnailPath(
                    $row['combination_cover_id']
                );
            }
        });

        return $rows;
    }

    /**
     * @param string $andWhereClause
     * @param string $having
     * @param null $orderByClause
     * @return mixed
     */
    private function selectStock(
        $andWhereClause = '',
        $having = '',
        $orderByClause = null
    )
    {
        if (is_null($orderByClause)) {
            $orderByClause = $this->orderByProductIds();
        }

        return str_replace(
            array(
                '{left_join}',
                '{and_where}',
                '{having}',
                '{order_by}',
                '{table_prefix}',
            ),
            array(
                $this->joinLimitingCombinationsPerProduct(),
                $andWhereClause,
                $having,
                $orderByClause,
                $this->tablePrefix,
            ),
            'SELECT SQL_CALC_FOUND_ROWS
            p.id_product AS product_id,
            COALESCE(pa.id_product_attribute, 0) AS combination_id,
            IF (
              COALESCE(pa.id_product_attribute, 0) = 0,
              "N/A",
              total_combinations
            ) as total_combinations,
            IF (
                LENGTH(COALESCE(pa.reference, "")) = 0,
                IF (LENGTH(TRIM(p.reference)) > 0, p.reference, "N/A"),
                CONCAT(p.reference, " ", pa.reference)
            ) AS product_reference,
            IF (
                COALESCE(pa.id_product_attribute, 0) > 0,
                GROUP_CONCAT(
                    DISTINCT CONCAT(agl.name, " - ", al.name)
                    SEPARATOR ", "
                ),
                "N/A"
            ) AS combination_name,
            p.id_supplier AS supplier_id,
            COALESCE(ic.id_image, 0) AS product_cover_id,
            COALESCE(i.id_image, 0) as combination_cover_id,
            COALESCE(s.name, "N/A") AS supplier_name,
            pl.name AS product_name,
            sa.quantity as product_available_quantity,
            sa.physical_quantity as product_physical_quantity,
            sa.reserved_quantity as product_reserved_quantity,
            COALESCE(product_attributes.attributes, "") AS product_attributes,
            COALESCE(product_features.features, "") AS product_features
            FROM {table_prefix}product p
            LEFT JOIN {table_prefix}product_attribute pa ON (p.id_product = pa.id_product)
            LEFT JOIN {table_prefix}product_lang pl ON (
                p.id_product = pl.id_product AND
                pl.id_lang = :language_id
            )
            LEFT JOIN {table_prefix}product_shop ps ON (
                p.id_product = ps.id_product AND
                ps.id_shop = :shop_id
            )
            LEFT JOIN {table_prefix}stock_available sa ON (p.id_product = sa.id_product)
            LEFT JOIN {table_prefix}image ic ON (
                p.id_product = ic.id_product AND
                ic.cover = 1
            )
            LEFT JOIN {table_prefix}image_shop ims ON (
                p.id_product = ims.id_product AND
                ic.id_image  = ims.id_image AND
                ims.cover = 1
            )
            LEFT JOIN (
                SELECT SUBSTRING_INDEX(
                    GROUP_CONCAT(pai.id_image),
                    ",",
                    1
                ) image_ids,
                pai.id_product_attribute as combination_id
                FROM {table_prefix}product_attribute_image pai
                GROUP BY pai.id_product_attribute
            ) images_per_combination ON (
                pa.id_product_attribute = images_per_combination.combination_id
            )
            LEFT JOIN {table_prefix}image i ON (
                COALESCE(FIND_IN_SET(i.id_image, images_per_combination.image_ids), 0) > 0
            )
            LEFT JOIN {table_prefix}supplier s ON (p.id_supplier = s.id_supplier)
            LEFT JOIN {table_prefix}product_attribute_combination pac ON (
                pac.id_product_attribute = pa.id_product_attribute
            )
            LEFT JOIN {table_prefix}product_attribute_shop pas ON (
                pas.id_product = pa.id_product AND
                pas.id_product_attribute = pa.id_product_attribute AND
                pas.id_shop = :shop_id
            )
            LEFT JOIN {table_prefix}category_product cp ON (
                p.id_product = cp.id_product
            )
            LEFT JOIN {table_prefix}attribute a ON (
                a.id_attribute = pac.id_attribute
            )
            LEFT JOIN {table_prefix}attribute_lang al ON (
                a.id_attribute = al.id_attribute
                AND al.id_lang = :language_id
                AND LENGTH(TRIM(al.name)) > 0
            )
            LEFT JOIN {table_prefix}attribute_group ag ON (
                ag.id_attribute_group = a.id_attribute_group
            )
            LEFT JOIN {table_prefix}attribute_group_lang agl ON (
                ag.id_attribute_group = agl.id_attribute_group
                AND agl.id_lang = :language_id
                AND LENGTH(TRIM(agl.name)) > 0
            )
            LEFT JOIN (
                SELECT GROUP_CONCAT(
                    CONCAT(ag.id_attribute_group, ":", a.id_attribute)
                    ORDER BY ag.id_attribute_group, a.id_attribute
                ) AS "attributes",
                pac.id_product_attribute
                FROM {table_prefix}product_attribute_combination pac
                LEFT JOIN {table_prefix}attribute a ON (
                    pac.id_attribute = a.id_attribute
                )
                LEFT JOIN {table_prefix}attribute_group ag ON (
                    ag.id_attribute_group = a.id_attribute_group
                )
                GROUP BY pac.id_product_attribute
            ) product_attributes ON (
                product_attributes.id_product_attribute = pac.id_product_attribute
            )
            LEFT JOIN (
                SELECT GROUP_CONCAT(
                  CONCAT(f.id_feature, ":", fv.id_feature_value)
                  ORDER BY fv.id_feature_value
                ) AS "features",
                fp.id_product
                FROM {table_prefix}feature_product fp
                LEFT JOIN  {table_prefix}feature f ON (
                    fp.id_feature = f.id_feature
                )
                LEFT JOIN {table_prefix}feature_shop fs ON (
                    fs.id_shop = :shop_id AND
                    fs.id_feature = f.id_feature
                )
                LEFT JOIN {table_prefix}feature_value fv ON (
                    f.id_feature = fv.id_feature AND
                    fp.id_feature_value = fv.id_feature_value
                )
                WHERE fv.custom = 0
                GROUP BY fp.id_product
            ) product_features ON (
                product_features.id_product = p.id_product
            )
            {left_join}
            WHERE
            ps.id_shop = :shop_id AND
            sa.id_shop = :shop_id AND
            ims.id_shop = :shop_id AND
            sa.id_product_attribute = COALESCE(pa.id_product_attribute, 0) AND
            p.state = :state
            {and_where}
            GROUP BY p.id_product, COALESCE(pa.id_product_attribute, 0)
            HAVING 1 {having}
            {order_by}
        ');
    }

    /**
     * @return string
     */
    private function joinLimitingCombinationsPerProduct()
    {
        return 'LEFT JOIN (
            SELECT pa.id_product product_id,
            COUNT(pa.id_product_attribute) total_combinations
            FROM {table_prefix}product_attribute pa
            GROUP BY pa.id_product
        ) combinations_per_product ON (combinations_per_product.product_id = p.id_product) ';
    }

    /**
     * @return string
     */
    private function andWhereLimitingCombinationsPerProduct()
    {
        return 'AND (
            ISNULL(pa.id_product_attribute) OR
            NOT ISNULL(combinations_per_product.total_combinations)
        ) ';
    }

    /**
     * @param Statement $statement
     * @param QueryParamsCollection|null $queryParams
     * @param ProductIdentity|null $productIdentity
     */
    private function bindStockValues(
        Statement $statement,
        QueryParamsCollection $queryParams = null,
        ProductIdentity $productIdentity = null
    )
    {
        $statement->bindValue('shop_id', $this->shopId, PDO::PARAM_INT);
        $statement->bindValue('language_id', $this->languageId, PDO::PARAM_INT);
        $statement->bindValue('state', Product::STATE_SAVED, PDO::PARAM_INT);

        if ($queryParams) {
            $this->bindValuesInStatement($statement, $queryParams);
        }

        if ($productIdentity) {
            $statement->bindValue('product_id', $productIdentity->getProductId(), PDO::PARAM_INT);
            $statement->bindValue('combination_id', $productIdentity->getCombinationId(), PDO::PARAM_INT);
        }
    }

    /**
     * @return string
     */
    private function orderByProductIds()
    {
        return 'ORDER BY p.id_product DESC, COALESCE(pa.id_product_attribute, 0)';
    }

    /**
     * @param QueryParamsCollection $queryParams
     * @return string
     */
    private function andWhere(QueryParamsCollection $queryParams)
    {
        $filters = $queryParams->getSqlFilters();
        $filters = strtr($filters[$queryParams::SQL_CLAUSE_WHERE], array(
            '{product_id}' => 'p.id_product',
            '{supplier_id}' => 'p.id_supplier',
            '{category_id}' => 'cp.id_category',
            '{attributes}' => 'product_attributes.attributes',
            '{features}' => 'product_features.features',
        ));

        return $this->andWhereLimitingCombinationsPerProduct() . $filters;
    }

    /**
     * @param QueryParamsCollection $queryParams
     * @return string
     */
    private function having(QueryParamsCollection $queryParams)
    {
        $filters = $queryParams->getSqlFilters();

        if (!array_key_exists($queryParams::SQL_CLAUSE_HAVING, $filters)) {
            return '';
        }

        return strtr($filters['having'], array(
            '{combination_name}' => 'combination_name',
            '{product_reference}' => 'product_reference',
            '{supplier_name}' => 'supplier_name',
            '{product_name}' => 'product_name'
        ));
    }

    /**
     * @param QueryParamsCollection $queryParams
     * @return string
     */
    private function orderBy(QueryParamsCollection $queryParams)
    {
        $orderByClause = $queryParams->getSqlOrder();

        $descendingOrder = false !== strpos($orderByClause, ' DESC');

        $productColumns = 'product_id, combination_id';
        if ($descendingOrder) {
            $productColumns = 'product_id DESC, combination_id ASC';
        }

        return strtr($orderByClause, array(
            '{product} DESC' => $productColumns,
            '{product}' => $productColumns,
            '{reference}' => 'product_reference',
            '{supplier}' => 'supplier_name',
            '{available_quantity}' => 'product_available_quantity',
            '{physical_quantity}' => 'product_physical_quantity'
        ));
    }

    /**
     * @return string
     */
    private function paginate()
    {
        return sprintf(
            'LIMIT :%s,:%s',
            QueryParamsCollection::SQL_PARAM_FIRST_RESULT,
            QueryParamsCollection::SQL_PARAM_MAX_RESULTS
        );
    }

    /**
     * @param Statement $statement
     * @param QueryParamsCollection $queryParams
     */
    private function bindValuesInStatement(Statement $statement, QueryParamsCollection $queryParams)
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
    private function bindMaxResultsValue(Statement $statement, QueryParamsCollection $queryParams)
    {
        $paginationParams = $queryParams->getSqlPaginationParams();
        $statement->bindValue(
            QueryParamsCollection::SQL_PARAM_MAX_RESULTS,
            $paginationParams[QueryParamsCollection::SQL_PARAM_MAX_RESULTS],
            PDO::PARAM_INT
        );
    }

}
