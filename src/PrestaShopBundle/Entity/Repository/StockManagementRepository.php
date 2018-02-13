<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
     * @param ContainerInterface $container
     * @param Connection $connection
     * @param ContextAdapter $contextAdapter
     * @param ImageManager $imageManager
     * @param $tablePrefix
     * @throws NotImplementedException
     */
    public function __construct(
        ContainerInterface $container,
        Connection $connection,
        EntityManager $entityManager,
        ContextAdapter $contextAdapter,
        ImageManager $imageManager,
        $tablePrefix
    )
    {
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
        $this->languageId = (int)$languageId;

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
     * @return array
     */
    protected function addAdditionalData(array $rows)
    {
        $rows = $this->addImageThumbnailPaths($rows);

        return $rows;
    }

    /**
     * @param array $rows
     * @return array
     */
    protected function addImageThumbnailPaths(array $rows)
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
     * @param QueryParamsCollection $queryParams
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

        $rows = $this->addAdditionalData($rows);

        return $this->castNumericToInt($rows);
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
    ) {
        throw new RuntimeException('You need to implement your own `selectSql` function.');
    }

    /**
     * @param QueryParamsCollection $queryParams
     * @return bool|string
     */
    public function countPages(QueryParamsCollection $queryParams)
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
     * @param QueryParamsCollection $queryParams
     * @return string
     */
    protected function andWhere(QueryParamsCollection $queryParams)
    {
        $filters = $queryParams->getSqlFilters();
        $filters = strtr($filters[$queryParams::SQL_CLAUSE_WHERE], array(
            '{product_id}' => 'p.id_product',
            '{supplier_id}' => 'p.id_supplier',
            '{category_id}' => 'cp.id_category',
            '{attributes}' => 'product_attributes.attributes',
            '{features}' => 'product_features.features',
            '{id_employee}' => 'sm.id_employee',
            '{date_add}' => 'sm.date_add',
            '{id_stock_mvt_reason}' => 'sm.id_stock_mvt_reason',
            '{active}' => 'p.active',
        ));

        return $filters;
    }

    /**
     * @param QueryParamsCollection $queryParams
     * @return string
     */
    protected function having(QueryParamsCollection $queryParams)
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
    protected function orderBy(QueryParamsCollection $queryParams)
    {
        $orderByClause = $queryParams->getSqlOrder();

        $descendingOrder = false !== strpos($orderByClause, ' DESC');

        $productColumns = 'product_id, combination_id';
        if ($descendingOrder) {
            $productColumns = 'product_id DESC, combination_id DESC';
        }

        return strtr($orderByClause, array(
            '{product} DESC' => $productColumns,
            '{product}' => $productColumns,
            '{reference}' => 'product_reference',
            '{supplier}' => 'supplier_name',
            '{available_quantity}' => 'product_available_quantity',
            '{physical_quantity}' => 'product_physical_quantity',
            '{id_stock_mvt}' => 'id_stock_mvt',
            '{date_add}' => 'date_add',
            '{product_low_stock_alert}' => 'product_low_stock_alert',
        ));
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
    )
    {
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
}
