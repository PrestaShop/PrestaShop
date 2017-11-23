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
use PDO;
use PrestaShop\PrestaShop\Adapter\ImageManager;
use PrestaShop\PrestaShop\Adapter\LegacyContext as ContextAdapter;
use PrestaShopBundle\Entity\StockMvt;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StockMovementRepository extends StockManagementRepository
{
    /**
     * StockMovementRepository constructor.
     * @param ContainerInterface $container
     * @param Connection $connection
     * @param EntityManager $entityManager
     * @param ContextAdapter $contextAdapter
     * @param ImageManager $imageManager
     * @param $tablePrefix
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
        parent::__construct(
            $container,
            $connection,
            $entityManager,
            $contextAdapter,
            $imageManager,
            $tablePrefix
        );
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
            $orderByClause = $this->orderByMovementsIds();
        }

        return str_replace(
            array(
                '{and_where}',
                '{having}',
                '{order_by}',
                '{table_prefix}',
            ),
            array(
                $andWhereClause,
                $having,
                $orderByClause,
                $this->tablePrefix,
            ),
            'SELECT SQL_CALC_FOUND_ROWS
            sm.id_stock_mvt, sm.id_stock, sm.id_order,
            sm.id_employee, sm.employee_lastname, sm.employee_firstname,
            sm.physical_quantity, sm.date_add, sm.sign,
            smrl.id_stock_mvt_reason, smrl.name as movement_reason,
            p.id_product AS product_id,
            COALESCE(pa.id_product_attribute, 0) AS combination_id,
            IF (
                LENGTH(COALESCE(pa.reference, "")) = 0,
                IF (LENGTH(TRIM(p.reference)) > 0, p.reference, "N/A"),
                CONCAT(p.reference, " ", pa.reference)
            ) AS product_reference,
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
            COALESCE(product_attributes.attributes, "") AS product_attributes,
            COALESCE(product_features.features, "") AS product_features
            FROM {table_prefix}stock_mvt sm
            INNER JOIN {table_prefix}stock_mvt_reason_lang smrl ON (
              smrl.id_stock_mvt_reason = sm.id_stock_mvt_reason
              AND smrl.id_lang = :language_id)
            INNER JOIN {table_prefix}stock_available sa ON (sa.id_stock_available = sm.id_stock)
            LEFT JOIN {table_prefix}product p ON (p.id_product = sa.id_product)
            LEFT JOIN {table_prefix}product_attribute pa ON (pa.id_product_attribute = sa.id_product_attribute)
            LEFT JOIN {table_prefix}product_lang pl ON (
                p.id_product = pl.id_product AND
                pl.id_lang = :language_id
            )
            INNER JOIN {table_prefix}product_shop ps ON (
                p.id_product = ps.id_product AND
                ps.id_shop = :shop_id
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
                i.id_product = p.id_product AND
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
            WHERE
            sa.id_shop = :stock_shop_id AND
            sa.id_shop_group = :stock_group_id AND
            sa.id_product_attribute = COALESCE(pa.id_product_attribute, 0)
            {and_where}
            GROUP BY sm.id_stock_mvt
            HAVING 1 {having}
            {order_by}
        ');
    }

    /**
     * @return string
     */
    private function orderByMovementsIds()
    {
        return 'ORDER BY sm.id_stock_mvt DESC';
    }

    /**
     * @param array $rows
     * @return array
     */
    protected function addAdditionalData(array $rows)
    {
        $rows = $this->addImageThumbnailPaths($rows);
        $rows = $this->addOrderLink($rows);

        return $rows;
    }

    /**
     * @param array $rows
     * @return array
     */
    private function addOrderLink(array $rows)
    {
        array_walk($rows, function (&$row) {
            if ($row['id_order']) {
                $row['order_link'] = $this->contextAdapter->getContext()->link->getAdminLink('AdminOrders', true, array(), array('vieworder' => true, 'id_order' => (int)$row['id_order']));
            } else {
                $row['order_link'] = 'N/A';
            }
        });

        return $rows;
    }

    /**
     * Get movements from employees
     * @return mixed
     */
    public function getEmployees()
    {
        $query = str_replace('{table_prefix}', $this->tablePrefix,
            'SELECT DISTINCT sm.id_employee, CONCAT(sm.employee_lastname, \' \', sm.employee_firstname) AS name
            FROM {table_prefix}stock_mvt sm
            INNER JOIN {table_prefix}stock_available sa ON (sa.id_stock_available = sm.id_stock)
            WHERE
            sa.id_shop = :shop_id
            ORDER BY name ASC'
        );

        $statement = $this->connection->prepare($query);
        $statement->bindValue('shop_id', $this->shopId, PDO::PARAM_INT);
        $statement->execute();

        $rows = $statement->fetchAll();
        $employees = $this->castNumericToInt($rows);

        return $employees;
    }

    /**
     * Get type of movements from employees
     *
     * @param bool $grouped
     * @return mixed
     */
    public function getTypes($grouped = false)
    {
        if ($grouped) {
            $select = 'GROUP_CONCAT(DISTINCT sm.id_stock_mvt_reason) as id_stock_mvt_reason, smrl.name AS name';
            $groupBy = 'GROUP BY name';
        } else {
            $select = 'sm.id_stock_mvt_reason, smrl.name AS name';
            $groupBy = 'GROUP BY id_stock_mvt_reason';
        }

        $query = str_replace('{table_prefix}', $this->tablePrefix,
            'SELECT '.$select.'
            FROM {table_prefix}stock_mvt sm
            INNER JOIN {table_prefix}stock_available sa ON (sa.id_stock_available = sm.id_stock)
            INNER JOIN {table_prefix}stock_mvt_reason_lang smrl ON (
              smrl.id_stock_mvt_reason = sm.id_stock_mvt_reason
              AND smrl.id_lang = :language_id)
            WHERE
            sa.id_shop = :shop_id
            '.$groupBy.'
            ORDER BY name ASC'
        );

        $statement = $this->connection->prepare($query);
        $statement->bindValue('language_id', $this->languageId, PDO::PARAM_INT);
        $statement->bindValue('shop_id', $this->shopId, PDO::PARAM_INT);
        $statement->execute();

        $rows = $statement->fetchAll();

        if ($grouped) {
            $types = $this->castIdsToArray($rows);
        } else {
            $types = $this->castNumericToInt($rows);
        }

        return $types;
    }

    /**
     * @param StockMvt $stockMvt
     * @return int
     */
    public function saveStockMvt(StockMvt $stockMvt)
    {
        $this->em->persist($stockMvt);
        $this->em->flush();

        return $stockMvt->getIdStockMvt();
    }
}
