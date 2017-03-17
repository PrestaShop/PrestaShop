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
use PrestaShopBundle\Entity\ProductIdentity;
use PrestaShopBundle\Exception\NotImplementedException;
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
     * @param ProductIdentity $productIdentity
     * @param $delta
     * @return mixed
     */
    public function updateProductQuantity(ProductIdentity $productIdentity, $delta)
    {
        $query = '
            UPDATE {prefix}stock_available
            SET quantity = quantity + :delta,
            physical_quantity = reserved_quantity + quantity
            WHERE id_product = :product_id
            AND id_product_attribute = :combination_id
        ';

        $query = str_replace('{prefix}', $this->tablePrefix, $query);

        $statement = $this->connection->prepare($query);

        $statement->bindValue('product_id', $productIdentity->getProductId(), PDO::PARAM_INT);
        $statement->bindValue('combination_id', $productIdentity->getCombinationId(), PDO::PARAM_INT);
        $statement->bindValue('delta', $delta, PDO::PARAM_INT);

        $statement->execute();

        return $this->selectProductsStockById($productIdentity);
    }

    /**
     * @param ProductIdentity $productIdentity
     * @return mixed
     */
    private function selectProductsStockById(ProductIdentity $productIdentity)
    {
        $andWhereClause = '
            AND p.id_product = :product_id AND
            COALESCE(pa.id_product_attribute, 0) = :combination_id'
        ;
        $query = $this->selectProductsStock(
            $leftJoinClause = '',
            $andWhereClause
        );

        $statement = $this->connection->prepare($query);

        $this->bindProductsSelectionValues($statement);
        $statement->bindValue('product_id', $productIdentity->getProductId(), PDO::PARAM_INT);
        $statement->bindValue('combination_id', $productIdentity->getCombinationId(), PDO::PARAM_INT);

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
    public function getProducts(QueryParamsCollection $queryParams)
    {
        $this->stockManager->updatePhysicalProductQuantity(
            $this->shopId,
            $this->orderStates['error'],
            $this->orderStates['cancellation']
        );

        $query = $this->selectProductsStock(
            $this->joinCeilingCombinationsPerProduct(),
            $this->andWhere($queryParams),
            $this->orderBy($queryParams)
        ) . $this->paginate();

        $statement = $this->connection->prepare($query);

        $this->bindProductsSelectionValues($statement);
        $queryParams->bindValuesInStatement($statement);
        $statement->bindValue('max_combinations_per_product', self::MAX_COMBINATIONS_PER_PRODUCT, PDO::PARAM_INT);

        $statement->execute();

        $rows = $statement->fetchAll();

        $rows = $this->addImageThumbnailPaths($rows);

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
    private function addImageThumbnailPaths($rows)
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
     * @param string $leftJoinClause
     * @param string $andWhereClause
     * @param null $orderByClause
     * @return mixed
     */
    private function selectProductsStock(
        $leftJoinClause = '',
        $andWhereClause = '',
        $orderByClause = null
    ) {
        if (is_null($orderByClause)) {
            $orderByClause = $this->orderByProductIds();
        }

        return str_replace(
            array(
                '{left_join}',
                '{and_where}',
                '{order_by}',
                '{prefix}',
            ),
            array(
                $leftJoinClause,
                $andWhereClause,
                $orderByClause,
                $this->tablePrefix,
            ),
            'SELECT
            p.id_product AS product_id,
            COALESCE(pa.id_product_attribute, 0) AS combination_id,
            IF (
              COALESCE(pa.reference, 0) = 0,
              IF (LENGTH(TRIM(p.reference)) > 0, p.reference, "N/A"),
              IF (LENGTH(TRIM(pa.reference)) > 0, pa.reference, "N/A")
            ) AS product_reference,
            IF (
                COALESCE(pa.id_product_attribute, 0) > 0,
                GROUP_CONCAT(
                  CONCAT(agl.name, " - ", al.name)
                  ORDER BY pa.id_product_attribute
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
            sa.reserved_quantity as product_reserved_quantity
            FROM {prefix}product p
            LEFT JOIN {prefix}product_attribute pa ON (p.id_product = pa.id_product)
            LEFT JOIN {prefix}product_lang pl ON (p.id_product = pl.id_product)
            LEFT JOIN {prefix}product_shop ps ON (
                p.id_product = ps.id_product AND
                ps.id_shop = :shop_id
            )
            LEFT JOIN {prefix}stock_available sa ON (p.id_product = sa.id_product)
            LEFT JOIN {prefix}image ic ON (
                pa.id_product = ic.id_product AND
                ic.cover = 1
            )
            LEFT JOIN {prefix}image_shop ims ON (
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
                FROM {prefix}product_attribute_image pai
                GROUP BY pai.id_product_attribute
            ) images_per_combination ON (
                pa.id_product_attribute = images_per_combination.combination_id
            )
            LEFT JOIN {prefix}image i ON (
                COALESCE(FIND_IN_SET(i.id_image, images_per_combination.image_ids), 0) > 0
            )
            LEFT JOIN {prefix}supplier s ON (p.id_supplier = s.id_supplier)
            LEFT JOIN {prefix}product_attribute_combination pac ON (
                pac.id_product_attribute = pa.id_product_attribute
            )
            LEFT JOIN {prefix}product_attribute_shop pas ON (
                pas.id_product = pa.id_product AND
                pas.id_product_attribute = pa.id_product_attribute AND
                pas.id_shop = :shop_id
            )
            LEFT JOIN {prefix}attribute a ON (
                a.id_attribute = pac.id_attribute
            )
            LEFT JOIN {prefix}attribute_lang al ON (
                a.id_attribute = al.id_attribute
                AND al.id_lang = :language_id
                AND LENGTH(TRIM(al.name)) > 0
            )
            LEFT JOIN {prefix}attribute_group ag ON (
                ag.id_attribute_group = a.id_attribute_group
            )
            LEFT JOIN {prefix}attribute_group_lang agl ON (
                ag.id_attribute_group = agl.id_attribute_group
                AND agl.id_lang = :language_id
                AND LENGTH(TRIM(agl.name)) > 0
            )
            {left_join}
            WHERE
            ps.id_shop = :shop_id AND
            sa.id_shop = :shop_id AND
            ims.id_shop = :shop_id AND
            pl.id_lang = :language_id AND
            sa.id_product_attribute = COALESCE(pa.id_product_attribute, 0) AND
            p.state = :state
            {and_where}
            GROUP BY p.id_product, COALESCE(pa.id_product_attribute, 0)
            {order_by}
        ');
    }

    /**
     * @return string
     */
    public function joinCeilingCombinationsPerProduct()
    {
        return 'LEFT JOIN (
            SELECT SUBSTRING_INDEX(
                GROUP_CONCAT(pa.id_product_attribute),
                \',\',
                :max_combinations_per_product
            ) product_attribute_ids
            FROM {prefix}product_attribute pa
            GROUP BY pa.id_product
        ) combinations_per_product ON (
            COALESCE(FIND_IN_SET(pa.id_product_attribute, combinations_per_product.product_attribute_ids), 0) > 0
        ) ';
    }

    /**
     * @return string
     */
    public function andWhereCeilingCombinationsPerProduct()
    {
        return 'AND (
            ISNULL(pa.id_product_attribute) OR
            NOT ISNULL(combinations_per_product.product_attribute_ids)
        ) ';
    }

    /**
     * @param $statement
     */
    private function bindProductsSelectionValues(Statement $statement)
    {
        $statement->bindValue('shop_id', $this->shopId, PDO::PARAM_INT);
        $statement->bindValue('language_id', $this->languageId, PDO::PARAM_INT);
        $statement->bindValue('state', Product::STATE_SAVED, PDO::PARAM_INT);
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
        $filter = $queryParams->getSqlFilter();
        $filter = strtr($filter, array('{product_id}' => 'p.id_product'));

        return $this->andWhereCeilingCombinationsPerProduct() . $filter;
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
            QueryParamsCollection::SQL_PARAM_MAX_RESULT
        );
    }
}
