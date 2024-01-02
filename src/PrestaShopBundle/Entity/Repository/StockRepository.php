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

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\ImageManager;
use PrestaShop\PrestaShop\Adapter\LegacyContext as ContextAdapter;
use PrestaShop\PrestaShop\Adapter\StockManager;
use PrestaShop\PrestaShop\Core\Stock\StockManager as StockManagerCore;
use PrestaShopBundle\Api\QueryParamsCollection;
use PrestaShopBundle\Api\Stock\Movement;
use PrestaShopBundle\Api\Stock\MovementsCollection;
use PrestaShopBundle\Entity\ProductIdentity;
use PrestaShopBundle\Exception\ProductNotFoundException;
use Product;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StockRepository extends StockManagementRepository
{
    /**
     * @var StockManager
     */
    private $stockManager;

    /**
     * @var array
     */
    private $orderStates = [];

    /**
     * @var array
     */
    private $totalCombinations = [];

    /**
     * StockRepository constructor.
     *
     * @param ContainerInterface $container
     * @param Connection $connection
     * @param EntityManager $entityManager
     * @param ContextAdapter $contextAdapter
     * @param ImageManager $imageManager
     * @param StockManager $stockManager
     * @param string $tablePrefix
     */
    public function __construct(
        ContainerInterface $container,
        Connection $connection,
        EntityManager $entityManager,
        ContextAdapter $contextAdapter,
        ImageManager $imageManager,
        StockManager $stockManager,
        $tablePrefix
    ) {
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
        $this->orderStates['error'] = (int) $configuration->get('PS_OS_ERROR');
        $this->orderStates['cancellation'] = (int) $configuration->get('PS_OS_CANCELED');
    }

    /**
     * @param MovementsCollection $movements
     *
     * @return array
     */
    public function bulkUpdateStock(MovementsCollection $movements)
    {
        $products = $movements->map(function (Movement $movement) {
            return $this->updateStock($movement);
        });

        return $products;
    }

    /**
     * @param Movement $movement
     * @param bool $syncStock
     *
     * @return mixed
     */
    public function updateStock(Movement $movement, $syncStock = true)
    {
        $productIdentity = $movement->getProductIdentity();
        $delta = $movement->getDelta();

        if ($productIdentity->getProductId() && $delta !== 0) {
            $product = new Product($productIdentity->getProductId());

            if ($product->id) {
                $configurationAdapter = new Configuration();

                (new StockManagerCore())->updateQuantity(
                    $product,
                    $productIdentity->getCombinationId(),
                    $delta,
                    $this->getCurrentShop()->id,
                    true,
                    [
                        'id_stock_mvt_reason' => ($delta >= 1 ? $configurationAdapter->get('PS_STOCK_MVT_INC_EMPLOYEE_EDITION') : $configurationAdapter->get('PS_STOCK_MVT_DEC_EMPLOYEE_EDITION')),
                    ]
                );
            }

            if (true === $syncStock) {
                $this->syncAllStock($productIdentity->getProductId());
            }
        }

        return $this->selectStockBy($productIdentity);
    }

    /**
     * Sync all stock with Manager.
     */
    private function syncAllStock($idProduct)
    {
        (new StockManager())->updatePhysicalProductQuantity(
            $this->getCurrentShop()->id,
            $this->orderStates['error'],
            $this->orderStates['cancellation'],
            (int) $idProduct
        );
    }

    /**
     * @param ProductIdentity $productIdentity
     *
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

        $result = $statement->executeQuery();
        $rows = $result->fetchAllAssociative();
        $result->free();
        $this->foundRows = $this->getFoundRows();

        if (count($rows) === 0) {
            throw new ProductNotFoundException(sprintf('Product with id %d and combination id %d can not be found', $productIdentity->getProductId(), $productIdentity->getCombinationId()));
        }

        $rows = $this->addAdditionalData($rows);

        return $this->castNumericToInt($rows)[0];
    }

    /**
     * @param QueryParamsCollection $queryParams
     *
     * @return mixed
     */
    public function getData(QueryParamsCollection $queryParams)
    {
        $this->stockManager->updatePhysicalProductQuantity(
            $this->getContextualShopId(),
            $this->orderStates['error'],
            $this->orderStates['cancellation']
        );

        return parent::getData($queryParams);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @param QueryParamsCollection $queryParams
     *
     * @return array
     */
    public function getDataExport($offset, $limit, QueryParamsCollection $queryParams)
    {
        $queryParams->setPageIndex($offset);
        $queryParams->setPageSize($limit);

        return $this->getData($queryParams);
    }

    /**
     * @param string $andWhereClause
     * @param string $having
     * @param null $orderByClause
     *
     * @return mixed
     */
    protected function selectSql(
        $andWhereClause = '',
        $having = '',
        $orderByClause = null
    ) {
        if (null === $orderByClause) {
            $orderByClause = $this->orderByProductIds();
        }

        $combinationNameQuery = $this->getCombinationNameSubquery();
        $attributeNameQuery = $this->getAttributeNameSubquery();

        return str_replace(
            [
                '{and_where}',
                '{having}',
                '{order_by}',
                '{table_prefix}',
                '{combination_name}',
                '{attribute_name}',
            ],
            [
                $andWhereClause,
                $having,
                $orderByClause,
                $this->tablePrefix,
                $combinationNameQuery,
                $attributeNameQuery,
            ],
            'SELECT SQL_CALC_FOUND_ROWS
          p.id_product                                                                      AS product_id,
          COALESCE(pa.id_product_attribute, 0)                                              AS combination_id,
          IF(COALESCE(p.reference, "") = "", "N/A", p.reference)                            AS product_reference,
          IF(COALESCE(pa.reference, "") = "", "N/A", pa.reference)                          AS combination_reference,
          pl.name                                                                           AS product_name,
          p.id_supplier                                                                     AS supplier_id,
          COALESCE(s.name, "N/A")                                                           AS supplier_name,
          COALESCE(ic.id_image, 0)                                                          AS product_cover_id,
          p.active,
          sa.quantity                                                                       AS product_available_quantity,
          sa.physical_quantity                                                              AS product_physical_quantity,
          sa.reserved_quantity                                                              AS product_reserved_quantity,
          IF(COALESCE(pa.id_product_attribute, 0) > 0, COALESCE(pas.low_stock_threshold, "N/A"),
             COALESCE(ps.low_stock_threshold,
                      "N/A"))                                                               AS product_low_stock_threshold,
          IF(COALESCE(pa.id_product_attribute, 0) > 0, IF(sa.quantity <= pas.low_stock_threshold, 1, 0),
             IF(sa.quantity <= ps.low_stock_threshold, 1, 0))                               AS product_low_stock_alert,
          {combination_name},
          {attribute_name}
        FROM {table_prefix}product p
          LEFT JOIN {table_prefix}product_attribute pa ON (p.id_product = pa.id_product)
          LEFT JOIN {table_prefix}product_lang pl ON (p.id_product = pl.id_product AND pl.id_lang = :language_id AND pl.id_shop = :shop_id)
          INNER JOIN {table_prefix}product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = :shop_id)
          LEFT JOIN {table_prefix}stock_available sa
            ON (p.id_product = sa.id_product AND sa.id_shop = :stock_shop_id AND sa.id_shop_group = :stock_group_id AND
                sa.id_product_attribute = COALESCE(pa.id_product_attribute, 0))
          LEFT JOIN {table_prefix}image ic ON (p.id_product = ic.id_product AND ic.cover = 1)
          LEFT JOIN {table_prefix}image_shop ims
            ON (p.id_product = ims.id_product AND ic.id_image = ims.id_image AND ims.id_shop = :shop_id AND ims.cover = 1)
          LEFT JOIN {table_prefix}supplier s ON (p.id_supplier = s.id_supplier)
          LEFT JOIN {table_prefix}product_attribute_combination pac ON (pac.id_product_attribute = pa.id_product_attribute)
          LEFT JOIN {table_prefix}product_attribute_shop pas
            ON (pas.id_product = pa.id_product AND pas.id_product_attribute = pa.id_product_attribute AND
                pas.id_shop = :shop_id)
        WHERE
          p.state = :state
          {and_where}
        GROUP BY p.id_product, pa.id_product_attribute
        HAVING 1 {having}
        {order_by}
        '
        );
    }

    /**
     * @param QueryParamsCollection $queryParams
     *
     * @return string
     */
    protected function andWhere(QueryParamsCollection $queryParams)
    {
        return parent::andWhere($queryParams);
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
     *
     * @return array
     */
    protected function addAdditionalData(array $rows)
    {
        $rows = $this->addCombinationsAndFeatures($rows);
        $rows = $this->addImageThumbnailPaths($rows);
        $rows = $this->addEditProductLink($rows);

        return $rows;
    }

    protected function addCombinationsAndFeatures(array $rows)
    {
        $rows = parent::addCombinationsAndFeatures($rows);
        foreach ($rows as &$row) {
            if ($row['combination_id'] != 0) {
                $row['total_combinations'] = $this->getTotalCombinations($row);
            } else {
                $row['total_combinations'] = 'N/A';
            }
        }

        return $rows;
    }

    /**
     * Compute the number of combinations associated with a product.
     *
     * @param array $row
     *
     * @return string
     */
    private function getTotalCombinations(array $row)
    {
        if (!isset($this->totalCombinations[$row['product_id']])) {
            $query = 'SELECT COUNT(*) total_combinations
                        FROM ' . $this->tablePrefix . 'product_attribute pa
                        WHERE id_product=:id_product';
            $statement = $this->connection->prepare($query);
            $statement->bindValue('id_product', (int) $row['product_id'], \PDO::PARAM_INT);
            $result = $statement->executeQuery();
            $this->totalCombinations[$row['product_id']] = $result->fetchOne();
            $result->free();
        }

        return $this->totalCombinations[$row['product_id']];
    }

    private function addEditProductLink(array $rows)
    {
        $router = $this->container->get('router');

        foreach ($rows as &$row) {
            $row['combinations_product_url'] = $router->generate('api_stock_list_product_combinations', [
                'productId' => $row['product_id'],
            ]);

            if (!empty($row['combination_id'])) {
                $row['edit_url'] = $router->generate('api_stock_edit_product_combination', [
                    'productId' => $row['product_id'],
                    'combinationId' => $row['combination_id'],
                ]);
            } else {
                $row['edit_url'] = $router->generate('api_stock_edit_product', [
                    'productId' => $row['product_id'],
                ]);
            }
        }

        return $rows;
    }
}
