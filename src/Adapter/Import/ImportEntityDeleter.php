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

namespace PrestaShop\PrestaShop\Adapter\Import;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Image\Deleter\ImageFileDeleterInterface;
use PrestaShop\PrestaShop\Core\Import\Entity;
use PrestaShop\PrestaShop\Core\Import\Entity\ImportEntityDeleterInterface;
use PrestaShop\PrestaShop\Core\Import\Exception\NotSupportedImportEntityException;

/**
 * Class ImportEntityDeleter is responsible for deleting import entities.
 */
final class ImportEntityDeleter implements ImportEntityDeleterInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string database prefix
     */
    private $dbPrefix;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ImageFileDeleterInterface
     */
    private $imageFileDeleter;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param Configuration $configuration
     * @param ImageFileDeleterInterface $imageFileDeleter
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        Configuration $configuration,
        ImageFileDeleterInterface $imageFileDeleter
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->configuration = $configuration;
        $this->imageFileDeleter = $imageFileDeleter;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAll($importEntity)
    {
        switch ($importEntity) {
            case Entity::TYPE_CATEGORIES:
                $this->deleteCategories();

                break;

            case Entity::TYPE_PRODUCTS:
                $this->deleteProducts();

                break;

            case Entity::TYPE_COMBINATIONS:
                $this->deleteCombinations();

                break;

            case Entity::TYPE_CUSTOMERS:
                $this->truncateTables([
                    'customer',
                ]);

                break;

            case Entity::TYPE_ADDRESSES:
                $this->truncateTables([
                    'address',
                ]);

                break;

            case Entity::TYPE_MANUFACTURERS:
                $this->deleteManufacturers();

                break;

            case Entity::TYPE_SUPPLIERS:
                $this->deleteSuppliers();

                break;

            case Entity::TYPE_ALIAS:
                $this->truncateTables([
                    'alias',
                ]);

                break;

            default:
                throw new NotSupportedImportEntityException("Import entity \"{$importEntity}\" is not supported");
        }

        $this->imageFileDeleter->deleteAllImages($this->configuration->get('_PS_TMP_IMG_DIR_'));
    }

    /**
     * Delete all suppliers data and images.
     */
    private function deleteSuppliers()
    {
        $this->truncateTables([
            'supplier',
            'supplier_lang',
            'supplier_shop',
        ]);

        $this->imageFileDeleter->deleteFromPath($this->configuration->get('_PS_SUPP_IMG_DIR_'));
    }

    /**
     * Delete all manufacturers images and data.
     */
    private function deleteManufacturers()
    {
        $this->truncateTables([
            'manufacturer',
            'manufacturer_lang',
            'manufacturer_shop',
        ]);

        $this->imageFileDeleter->deleteFromPath($this->configuration->get('_PS_MANU_IMG_DIR_'));
    }

    /**
     * Delete all categories images and data, except Root and Home.
     */
    private function deleteCategories()
    {
        $protectedCategoriesIds = [
            $this->configuration->getInt('PS_HOME_CATEGORY'),
            $this->configuration->getInt('PS_ROOT_CATEGORY'),
        ];

        $this->connection->executeQuery(
            "DELETE FROM {$this->dbPrefix}category WHERE id_category NOT IN (?)",
            [$protectedCategoriesIds],
            [Connection::PARAM_INT_ARRAY]
        );

        $this->connection->executeQuery(
            "DELETE FROM {$this->dbPrefix}category_lang WHERE id_category NOT IN (?)",
            [$protectedCategoriesIds],
            [Connection::PARAM_INT_ARRAY]
        );

        $this->connection->executeQuery(
            "DELETE FROM {$this->dbPrefix}category_shop WHERE id_category NOT IN (?)",
            [$protectedCategoriesIds],
            [Connection::PARAM_INT_ARRAY]
        );

        $this->connection->executeQuery("ALTER TABLE {$this->dbPrefix}category AUTO_INCREMENT = 3");

        $this->imageFileDeleter->deleteFromPath($this->configuration->get('_PS_CAT_IMG_DIR_'));
    }

    /**
     * Delete all products images and data.
     */
    private function deleteProducts()
    {
        $truncateTables = [
            'product',
            'product_shop',
            'feature_product',
            'product_lang',
            'category_product',
            'product_tag',
            'image',
            'image_lang',
            'image_shop',
            'specific_price',
            'specific_price_priority',
            'product_carrier',
            'cart_product',
            'product_attachment',
            'product_country_tax',
            'product_download',
            'product_group_reduction_cache',
            'product_sale',
            'product_supplier',
            'warehouse_product_location',
            'stock',
            'stock_available',
            'stock_mvt',
            'customization',
            'customization_field',
            'supply_order_detail',
            'attribute_impact',
            'product_attribute',
            'product_attribute_shop',
            'product_attribute_combination',
            'product_attribute_image',
            'pack',
        ];

        $this->truncateTables($truncateTables);

        $truncateIfExists = [
            'favorite_product',
        ];

        $this->truncateTablesIfExist($truncateIfExists);

        $imgDir = $this->configuration->get('_PS_PROD_IMG_DIR_');
        $this->imageFileDeleter->deleteFromPath($imgDir, true, true);
    }

    /**
     * Delete all combinations data.
     */
    private function deleteCombinations()
    {
        $truncateTables = [
            'attribute',
            'attribute_impact',
            'attribute_lang',
            'attribute_group',
            'attribute_group_lang',
            'attribute_group_shop',
            'attribute_shop',
            'product_attribute',
            'product_attribute_shop',
            'product_attribute_combination',
            'product_attribute_image',
        ];

        $this->truncateTables($truncateTables);
        $this->connection->executeQuery(
            "DELETE FROM `{$this->dbPrefix}stock_available` WHERE id_product_attribute != 0"
        );
    }

    /**
     * Truncate multiple tables.
     *
     * @param array $tables
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function truncateTables(array $tables)
    {
        foreach ($tables as $table) {
            $this->connection->executeQuery("TRUNCATE TABLE `{$this->dbPrefix}{$table}`");
        }
    }

    /**
     * Truncate tables if they exist. Truncates them one by one.
     *
     * @param array $tables
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function truncateTablesIfExist(array $tables)
    {
        foreach ($tables as $table) {
            $tableExists = $this->connection->getSchemaManager()->tablesExist(
                [
                    "{$this->dbPrefix}{$table}",
                ]
            );

            if ($tableExists) {
                $this->connection->executeQuery("TRUNCATE TABLE `{$this->dbPrefix}{$table}`");
            }
        }
    }
}
