<?php
/**
 * 2007-2016 PrestaShop
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
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\Driver\Connection;
use Employee;
use PrestaShop\PrestaShop\Adapter\ImageManager;
use PrestaShop\PrestaShop\Adapter\LegacyContext as ContextAdapter;
use PrestaShopBundle\Exception\NotImplementedException;
use PDO;
use Product;
use RuntimeException;
use Shop;

class ProductStockRepository
{
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
     * @param Connection $connection
     * @param ContextAdapter $contextAdapter
     * @param ImageManager $imageManager
     * @param $tablePrefix
     * @throws NotImplementedException
     */
    public function __construct(
        Connection $connection,
        ContextAdapter $contextAdapter,
        ImageManager $imageManager,
        $tablePrefix
    ) {
        $this->connection = $connection;
        $this->imageManager = $imageManager;

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

        $this->shopId = $shop->getContextualShopId();
    }

    public function getStockOverviewRows()
    {
        $query = '
            SELECT
            p.id_product AS product_id,
            COALESCE(pa.id_product_attribute, 0) AS product_attribute_id,
            IF (LENGTH(p.reference) = 0, "N/A", p.reference) AS product_reference,
            p.id_supplier AS supplier_id,
            i.id_image AS image_id,
            COALESCE(s.name, "N/A") AS supplier_name,
            pl.name AS product_name,
            SUM(sa.quantity) as product_available_quantity
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
            WHERE
            ps.id_shop = :shop_id AND
            pl.id_lang = :language_id AND
            sa.id_shop = :shop_id AND
            sa.id_product_attribute = COALESCE(pa.id_product_attribute, 0) AND
            ims.cover = 1 AND
            p.state = :state
            GROUP BY p.id_product, COALESCE(pa.id_product_attribute, 0)
            ORDER BY p.id_product DESC, COALESCE(pa.id_product_attribute, 0)
            LIMIT :first_result, :max_result
        ';

        $query = str_replace('{prefix}', $this->tablePrefix, $query);

        $statement = $this->connection->prepare($query);

        $statement->bindValue('shop_id', $this->shopId, PDO::PARAM_INT);
        $statement->bindValue('language_id', $this->languageId, PDO::PARAM_INT);
        $statement->bindValue('first_result', 0, PDO::PARAM_INT);
        $statement->bindValue('max_result', 100, PDO::PARAM_INT);
        $statement->bindValue('state', Product::STATE_SAVED, PDO::PARAM_INT);

        $statement->execute();

        $rows = $statement->fetchAll();

        $rows = $this->addReservedProductQuantities($rows);
        $rows = $this->addImageThumbnailPath($rows);

        return $this->castNumericToInt($rows);
    }

    /**
     * @param $rows
     * @return array
     */
    private function formatProductsIdentifiersForWhereInClause($rows)
    {
        $productIdentifiers = array_map(function ($row) {
            return array($row['product_id'], $row['product_attribute_id']);
        }, $rows);

        return implode(',', array_map(function ($identifiers) {
            return '(' . implode(',', $identifiers) . ')';
        }, $productIdentifiers));
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
     * @param $productIdentifiers
     * @return array
     */
    private function getReservedProductsQuantities($productIdentifiers)
    {
        $query = '
            SELECT
            od.product_id,
            od.product_attribute_id,
            SUM(od.product_quantity) as product_reserved_quantity
            FROM {prefix}orders o
            LEFT JOIN {prefix}order_detail od ON (o.id_order = od.id_order)
            WHERE (od.product_id, od.product_attribute_id) IN ({product_identifiers}) AND
            od.id_shop = :shop_id
            GROUP BY product_id, product_attribute_id
        ';

        $query = str_replace([
            '{prefix}',
            '{product_identifiers}'
        ], array(
            $this->tablePrefix,
            $productIdentifiers
        ), $query);

        $statement = $this->connection->prepare($query);

        $statement->bindValue('shop_id', $this->shopId, PDO::PARAM_INT);

        $statement->execute();

        $productQuantities = $statement->fetchAll();

        $productIdentifiersAsKeys = array_map(function ($row) {
            return $row['product_id'] . '-' . $row['product_attribute_id'];
        }, $productQuantities);

        return array_combine($productIdentifiersAsKeys, $productQuantities);
    }

    /**
     * @param $rows
     * @return mixed
     */
    private function addReservedProductQuantities($rows)
    {
        $productIdentifiers = $this->formatProductsIdentifiersForWhereInClause($rows);
        $productQuantities = $this->getReservedProductsQuantities($productIdentifiers);

        array_walk($rows, function (&$row) use ($productQuantities) {
            $productIdentifier = $row['product_id'] . '-' . $row['product_attribute_id'];
            $row['product_reserved_quantity'] = 0;

            if (array_key_exists($productIdentifier, $productQuantities)) {
                $row['product_reserved_quantity'] = $productQuantities[$productIdentifier]['product_reserved_quantity'];
            }
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
}
