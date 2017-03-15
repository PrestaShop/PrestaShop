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
use PrestaShop\PrestaShop\Adapter\ImageManager;
use PrestaShop\PrestaShop\Adapter\LegacyContext as ContextAdapter;
use PrestaShop\PrestaShop\Adapter\StockManager;
use PrestaShopBundle\Api\QueryParamsCollection;
use PrestaShopBundle\Exception\NotImplementedException;
use PDO;
use PrestaShopBundle\Exception\ProductNotFoundException;
use Product;
use RuntimeException;
use Shop;

class ProductStockRepository
{
    const MAX_COMBINATIONS_PER_PRODUCT = 50;

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
     * ProductStockRepository constructor.
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
    ) {
        $this->connection = $connection;
        $this->imageManager = $imageManager;
        $this->stockManager = $stockManager;

        $this->tablePrefix = $tablePrefix;

        $context = $contextAdapter->getContext();

        if (!$context->employee instanceof Employee) {
            throw new RuntimeException('Determining the active language requires a contextual employee instance.');
        }

        $languageId = $context->employee->id_lang;
        $this->languageId = (int) $languageId;

        if (!$context->shop instanceof Shop) {
            throw new RuntimeException('Determining the active shop requires a contextual shop instance.');
        }

        $shop = $context->shop;
        if ($shop->getContextType() !== $shop::CONTEXT_SHOP) {
            throw new NotImplementedException('Shop context types other than "single shop" are not supported');
        }

        $this->orderStates['error'] = (int) Configuration::get('PS_OS_ERROR');
        $this->orderStates['cancellation'] = (int) Configuration::get('PS_OS_CANCELED');

        $this->shopId = $shop->getContextualShopId();
    }

    /**
     * @param $productId
     * @param $quantity
     * @return mixed
     */
    public function updateProductQuantity($productId, $quantity)
    {
        return $this->updateProductCombinationQuantity($productId, 0, $quantity);
    }

    /**
     * @param $productId
     * @param $productAttributeId
     * @param $quantity
     * @return mixed
     */
    public function updateProductCombinationQuantity($productId, $productAttributeId, $quantity)
    {
        $query = '
            UPDATE {prefix}stock_available
            SET quantity = :quantity,
            physical_quantity = reserved_quantity + quantity
            WHERE id_product = :product_id
            AND id_product_attribute = :product_attribute_id
        ';

        $query = str_replace('{prefix}', $this->tablePrefix, $query);

        $statement = $this->connection->prepare($query);

        $statement->bindValue('product_id', $productId, PDO::PARAM_INT);
        $statement->bindValue('product_attribute_id', $productAttributeId, PDO::PARAM_INT);
        $statement->bindValue('quantity', $quantity, PDO::PARAM_INT);

        $statement->execute();

        return $this->getStockRowById($productId, $productAttributeId);
    }

    /**
     * @param $productId
     * @param $productAttributeId
     * @return mixed
     * @throws ProductNotFoundException
     */
    private function getStockRowById($productId, $productAttributeId)
    {
        $andWhereClause = '
            AND p.id_product = :product_id AND
            COALESCE(pa.id_product_attribute, 0) = :product_attribute_id'
        ;
        $query = $this->selectProductStock($andWhereClause);
        $query = str_replace('{prefix}', $this->tablePrefix, $query);

        $statement = $this->connection->prepare($query);

        $this->bindSelectProductStockParams($statement);
        $statement->bindValue('product_id', $productId, PDO::PARAM_INT);
        $statement->bindValue('product_attribute_id', $productAttributeId, PDO::PARAM_INT);

        $statement->execute();

        $rows = $statement->fetchAll();

        if (count($rows) === 0) {
            throw new ProductNotFoundException(
                sprintf(
                    'Product with id %d and attribute id %d can not be found',
                    $productId,
                    $productAttributeId
                )
            );
        }

        return $this->castNumericToInt($rows)[0];
    }

    /**
     * @param QueryParamsCollection $queryParams
     * @return mixed
     */
    public function getStockOverviewRows(QueryParamsCollection $queryParams)
    {
        $this->stockManager->updatePhysicalProductQuantity(
            $this->shopId,
            $this->orderStates['error'],
            $this->orderStates['cancellation']
        );

        $orderClause = $this->getOrderClause($queryParams);

        $clauses = $this->getClausesRestrictingTotalCombinationsPerProduct();
        $query = $this->selectProductStock($clauses['and_where'], $orderClause, $clauses['left_join']);

        $query = $query . $this->getLimitClause($queryParams);

        $query = str_replace('{prefix}', $this->tablePrefix, $query);

        $statement = $this->connection->prepare($query);

        $this->bindSelectProductStockParams($statement);

        $sqlClauses = $queryParams->toSqlClauses();
        $limitClauseParams = $sqlClauses[$queryParams::SQL_CLAUSE_LIMIT_PARAMS];

        foreach ($limitClauseParams  as $name => $value) {
            $statement->bindValue($name, $value, PDO::PARAM_INT);
        }

        $statement->execute();

        $rows = $statement->fetchAll();

        $rows = $this->addImageThumbnailPath($rows);

        return $this->castNumericToInt($rows);
    }

    /**
     * @param $rows
     * @return mixed
     */
    private function castNumericToInt($rows)
    {
        $castIdentifiersToIntegers = function (&$columnValue, $columnName) {
            if (false !== strpos($columnName, '_id') || false !== strpos($columnName, '_quantity')) {
                $columnValue = (int)$columnValue;
            }
        };

        array_walk($rows, function (&$rowColumns) use ($castIdentifiersToIntegers) {
            array_walk($rowColumns, $castIdentifiersToIntegers);
        });

        return $rows;
    }

    /**
     * @param $rows
     * @return mixed
     */
    private function addImageThumbnailPath($rows)
    {
        array_walk($rows, function (&$row) {
            $row['image_thumbnail_path'] = $this->imageManager->getThumbnailPath($row['image_id']);
        });

        return $rows;
    }

    /**
     * @param string $andWhereClause
     * @param null $orderClause
     * @param string $leftJoinClause
     * @return mixed
     */
    private function selectProductStock(
        $andWhereClause = '',
        $orderClause = null,
        $leftJoinClause = ''
    ) {
        if (is_null($orderClause)) {
            $orderClause = $this->getDefaultProductStockOrderClause();
        }

        return str_replace(
            array(
                '{and_where}',
                '{left_join}'
            ),
            array(
                $andWhereClause,
                $leftJoinClause
            ),
            'SELECT
            p.id_product AS product_id,
            COALESCE(pa.id_product_attribute, 0) AS product_attribute_id,
            IF (LENGTH(p.reference) = 0, "N/A", p.reference) AS product_reference,
            p.id_supplier AS supplier_id,
            i.id_image AS image_id,
            COALESCE(s.name, "N/A") AS supplier_name,
            pl.name AS product_name,
            sa.quantity as product_available_quantity,
            sa.physical_quantity as product_physical_quantity,
            sa.reserved_quantity as product_reserved_quantity
            FROM {prefix}product p
            LEFT JOIN {prefix}product_attribute pa ON (p.id_product = pa.id_product)
            LEFT JOIN {prefix}product_lang pl ON (p.id_product = pl.id_product)
            LEFT JOIN {prefix}product_shop ps ON (p.id_product = ps.id_product)
            LEFT JOIN {prefix}stock_available sa ON (p.id_product = sa.id_product)
            LEFT JOIN {prefix}image i ON (p.id_product = i.id_product)
            LEFT JOIN {prefix}image_shop ims ON (
                p.id_product = ims.id_product AND
                i.id_image = ims.id_image
            )
            LEFT JOIN {prefix}supplier s ON (p.id_supplier = s.id_supplier)
            {left_join}
            WHERE
            ps.id_shop = :shop_id AND
            pl.id_lang = :language_id AND
            sa.id_shop = :shop_id AND
            sa.id_product_attribute = COALESCE(pa.id_product_attribute, 0) AND
            ims.cover = 1 AND
            p.state = :state
            {and_where}
            GROUP BY p.id_product, COALESCE(pa.id_product_attribute, 0)
        ' . $orderClause);
    }

    /**
     * @return string
     */
    public function getClausesRestrictingTotalCombinationsPerProduct()
    {
        return array(
            'left_join' => '
                LEFT JOIN (
                    SELECT SUBSTRING_INDEX(
                        GROUP_CONCAT(pa.id_product_attribute),
                        \',\',
                        :max_combinations_per_product
                    ) product_attribute_ids,
                    pa.id_product
                    FROM ps_product_attribute pa
                    GROUP BY pa.id_product
                ) select_ ON (COALESCE(FIND_IN_SET(pa.id_product_attribute, select_.product_attribute_ids), 0) > 0)',
            'and_where' => '
                AND (
                    ISNULL(pa.id_product_attribute) OR
                    NOT ISNULL(select_.product_attribute_ids)
                )'
        );
    }

    /**
     * @param $statement
     */
    private function bindSelectProductStockParams(Statement $statement)
    {
        $statement->bindValue('shop_id', $this->shopId, PDO::PARAM_INT);
        $statement->bindValue('language_id', $this->languageId, PDO::PARAM_INT);
        $statement->bindValue('state', Product::STATE_SAVED, PDO::PARAM_INT);
        $statement->bindValue('max_combinations_per_product', self::MAX_COMBINATIONS_PER_PRODUCT, PDO::PARAM_INT);
    }

    /**
     * @return string
     */
    private function getDefaultProductStockOrderClause()
    {
        return 'ORDER BY p.id_product DESC, COALESCE(pa.id_product_attribute, 0)';
    }

    /**
     * @param QueryParamsCollection $queryParams
     * @return string
     */
    private function getOrderClause(QueryParamsCollection $queryParams)
    {
        $sqlClauses = $queryParams->toSqlClauses();

        $descendingOrder = false !== strpos($sqlClauses[$queryParams::SQL_CLAUSE_ORDER], ' DESC');

        $productColumns = 'product_id, product_attribute_id';
        if ($descendingOrder) {
            $productColumns = 'product_id DESC, product_attribute_id ASC';
        }

        return strtr($sqlClauses[$queryParams::SQL_CLAUSE_ORDER], array(
            '{product} DESC' => $productColumns,
            '{product}' => $productColumns,
            '{reference}' => 'product_reference',
            '{supplier}' => 'supplier_name',
            '{available_quantity}' => 'product_available_quantity',
            '{physical_quantity}' => 'product_physical_quantity'
        ));
    }

    /**
     * @param QueryParamsCollection $queryParams
     * @return mixed
     */
    private function getLimitClause(QueryParamsCollection $queryParams)
    {
        $sqlClauses = $queryParams->toSqlClauses();

        return $sqlClauses[$queryParams::SQL_CLAUSE_LIMIT];
    }
}
