<?php
/**
 * 2007-2017 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManager;
use PrestaShop\PrestaShop\Adapter\ImageManager;
use PrestaShop\PrestaShop\Adapter\LegacyContext as ContextAdapter;
use PrestaShop\PrestaShop\Adapter\Product\ProductDataProvider;
use PrestaShop\PrestaShop\Adapter\StockManager;
use PrestaShop\PrestaShop\Core\Stock\StockManager as StockManagerCore;
use PrestaShopBundle\Api\QueryParamsCollection;
use PrestaShopBundle\Api\Stock\Movement;
use PrestaShopBundle\Api\Stock\MovementsCollection;
use PrestaShopBundle\Entity\ProductIdentity;
use PrestaShopBundle\Exception\ProductNotFoundException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use PrestaShop\PrestaShop\Adapter\Configuration;

class StockRepository extends StockManagementRepository
{
    /**
     * @var StockManager
     */
    private $stockManager;

    /**
     * @var array
     */
    private $orderStates = array();

    /**
     * StockRepository constructor.
     * @param ContainerInterface $container
     * @param Connection $connection
     * @param EntityManager $entityManager
     * @param ContextAdapter $contextAdapter
     * @param ImageManager $imageManager
     * @param StockManager $stockManager
     * @param $tablePrefix
     */
    public function __construct(
        ContainerInterface $container,
        Connection $connection,
        EntityManager $entityManager,
        ContextAdapter $contextAdapter,
        ImageManager $imageManager,
        StockManager $stockManager,
        $tablePrefix
    )
    {
        parent::__construct(
            $container,
            $connection,
            $entityManager,
            $contextAdapter,
            $imageManager,
            $tablePrefix
        );

        $this->stockManager = $stockManager;

        $configuration = new Configuration();
        $this->orderStates['error'] = (int)$configuration->get('PS_OS_ERROR');
        $this->orderStates['cancellation'] = (int)$configuration->get('PS_OS_CANCELED');
    }

    /**
     * @param MovementsCollection $movements
     * @return array
     */
    public function bulkUpdateStock(MovementsCollection $movements)
    {
        $products = $movements->map(function (Movement $movement) {
            return $this->updateStock($movement, true);
        });

        $this->syncAllStock();

        return $products;
    }

    /**
     * @param Movement $movement
     * @param bool $syncStock
     * @return mixed
     */
    public function updateStock(Movement $movement, $syncStock = true)
    {
        $productIdentity = $movement->getProductIdentity();
        $delta = $movement->getDelta();

        if ($productIdentity->getProductId() && $delta !== 0) {
            $product = (new ProductDataProvider())->getProduct($productIdentity->getProductId());

            if ($product->id) {
                $configurationAdapter = new Configuration();

                (new StockManagerCore())->updateQuantity(
                    $product,
                    $productIdentity->getCombinationId(),
                    $delta,
                    $this->contextAdapter->getContext()->shop->id,
                    $add_movement = true,
                    array(
                        'id_stock_mvt_reason' => ($delta >= 1 ? $configurationAdapter->get('PS_STOCK_MVT_INC_EMPLOYEE_EDITION') : $configurationAdapter->get('PS_STOCK_MVT_DEC_EMPLOYEE_EDITION')),
                    )
                );
            }

            if (true === $syncStock) {
                $this->syncAllStock();
            }
        }

        return $this->selectStockBy($productIdentity);
    }

    /**
     * Sync all stock with Manager
     */
    private function syncAllStock()
    {
        (new StockManager())->updatePhysicalProductQuantity(
            $this->contextAdapter->getContext()->shop->id,
            $this->orderStates['error'],
            $this->orderStates['cancellation']
        );
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
        $query = $this->selectSql($andWhereClause);

        $statement = $this->connection->prepare($query);
        $this->bindStockManagementValues($statement, null, $productIdentity);

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

        $rows = $this->addAdditionalData($rows);

        return $this->castNumericToInt($rows)[0];
    }

    /**
     * @param QueryParamsCollection $queryParams
     * @return mixed
     */
    public function getData(QueryParamsCollection $queryParams)
    {
        $this->stockManager->updatePhysicalProductQuantity(
            $this->shopId,
            $this->orderStates['error'],
            $this->orderStates['cancellation']
        );

        return parent::getData($queryParams);
    }


    /**
     * @param string $andWhereClause
     * @param string $having
     * @param null $orderByClause
     * @return mixed
     */
    protected function selectSql(
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
              COALESCE(p.reference, "") = "",
              "N/A",
              p.reference
            ) as product_reference,
            IF (
              COALESCE(pa.reference, "") = "",
              "N/A",
              pa.reference
            ) as combination_reference,
            pl.name AS product_name,
            IF (
                COALESCE(pa.id_product_attribute, 0) > 0,
                GROUP_CONCAT(
                    DISTINCT CONCAT(agl.name, " - ", al.name)
                    SEPARATOR ", "
                ),
                "N/A"
            ) AS combination_name,
            p.id_supplier AS supplier_id,
            COALESCE(s.name, "N/A") AS supplier_name,
            COALESCE(ic.id_image, 0) AS product_cover_id,
            COALESCE(i.id_image, 0) as combination_cover_id,
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
            INNER JOIN {table_prefix}product_shop ps ON (
                p.id_product = ps.id_product AND
                ps.id_shop = :shop_id
            )
            LEFT JOIN {table_prefix}stock_available sa ON (
              p.id_product = sa.id_product AND
              sa.id_shop = :stock_shop_id AND
              sa.id_shop_group = :stock_group_id AND
              sa.id_product_attribute = COALESCE(pa.id_product_attribute, 0)
            )
            LEFT JOIN {table_prefix}image ic ON (
                p.id_product = ic.id_product AND
                ic.cover = 1
            )
            LEFT JOIN {table_prefix}image_shop ims ON (
                p.id_product = ims.id_product AND
                ic.id_image  = ims.id_image AND
                ims.id_shop = :shop_id AND
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
            p.state = :state
            {and_where}
            GROUP BY p.id_product, COALESCE(pa.id_product_attribute, 0)
            HAVING 1 {having}
            {order_by}
        ');
    }

    /**
     * @param QueryParamsCollection $queryParams
     * @return string
     */
    protected function andWhere(QueryParamsCollection $queryParams)
    {
        return $this->andWhereLimitingCombinationsPerProduct() . parent::andWhere($queryParams);
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
     * @return string
     */
    private function orderByProductIds()
    {
        return 'ORDER BY p.id_product DESC, COALESCE(pa.id_product_attribute, 0) DESC';
    }

    /**
     * @param array $rows
     * @return array
     */
    protected function addAdditionalData(array $rows)
    {
        $rows = $this->addImageThumbnailPaths($rows);
        $rows = $this->addEditProductLink($rows);

        return $rows;
    }

    private function addEditProductLink(array $rows)
    {
        $router = $this->container->get('router');

        array_walk($rows, function (&$row) use ($router) {
            $row['combinations_product_url'] = $router->generate('api_stock_list_product_combinations', array(
                'productId' => $row['product_id'],
            ));

            if (!empty($row['combination_id'])) {
                $row['edit_url'] = $router->generate('api_stock_edit_product_combination', array(
                    'productId' => $row['product_id'],
                    'combinationId' => $row['combination_id'],
                ));
            } else {
                $row['edit_url'] = $router->generate('api_stock_edit_product', array(
                    'productId' => $row['product_id'],
                ));
            }
        });

        return $rows;
    }
}
