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

namespace PrestaShop\PrestaShop\Adapter\Import\Handler;

use Doctrine\DBAL\Connection;
use \Module;
use PrestaShop\PrestaShop\Adapter\Import\ImportDataFormatter;
use PrestaShop\PrestaShop\Core\Import\Configuration\ImportConfigInterface;
use PrestaShop\PrestaShop\Core\Import\Configuration\ImportRuntimeConfigInterface;
use PrestaShop\PrestaShop\Core\Import\File\DataRow\DataRowInterface;
use Product;
use Shop;
use Tag;

/**
 * Class ProductImportHandler is responsible for product import.
 */
final class ProductImportHandler extends AbstractImportHandler
{
    /**
     * @var array entity default values
     */
    private $defaultValues = [];

    /**
     * @var Connection database connection
     */
    private $connection;

    /**
     * @var string product database table name
     */
    private $productTable;

    /**
     * @param ImportDataFormatter $dataFormatter
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(
        ImportDataFormatter $dataFormatter,
        Connection $connection,
        $dbPrefix
    ) {
        parent::__construct($dataFormatter);

        $this->connection = $connection;
        $this->productTable = $dbPrefix.'product';
        $this->defaultValues = [
            'reference' => '',
            'supplier_reference' => '',
            'ean13' => '',
            'upc' => '',
            'wholesale_price' => 0,
            'price' => 0,
            'ecotax' => 0,
            'quantity' => 0,
            'minimal_quantity' => 1,
            'low_stock_threshold' => null,
            'low_stock_alert' => false,
            'weight' => 0,
            'default_on' => null,
            'advanced_stock_management' => 0,
            'depends_on_stock' => 0,
            'available_date' => date('Y-m-d'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setUp(ImportConfigInterface $importConfig)
    {
        parent::setUp($importConfig);

        if (!defined('PS_MASS_PRODUCT_CREATION')) {
            define('PS_MASS_PRODUCT_CREATION', true);
        }

        Module::setBatchMode(true);
    }

    /**
     * {@inheritdoc}
     */
    public function importRow(
        ImportConfigInterface $importConfig,
        ImportRuntimeConfigInterface $runtimeConfig,
        DataRowInterface $dataRow
    ) {
        $entityFields = $runtimeConfig->getEntityFields();

        $productId = $this->fetchProductId(
            $dataRow,
            $runtimeConfig->getEntityFields(),
            $importConfig->matchReferences()
        );

        $product = new Product($productId);

        $this->loadStock($product);
        $this->setDefaultValues($product);
        $this->fillEntityData($product, $entityFields, $dataRow, $importConfig->getLanguageIso());

        //@todo WIP
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        Module::processDeferedFuncCall();
        Module::processDeferedClearCache();
        Tag::updateTagCount();
    }

    /**
     * Fetch the product ID.
     *
     * @param DataRowInterface $dataRow
     * @param array $entityFields
     * @param bool $fetchByReference if true, will fallback to finding the product ID by reference
     *
     * @return int|null
     */
    private function fetchProductId(
        DataRowInterface $dataRow,
        array $entityFields,
        $fetchByReference
    ) {
        $productId = $this->fetchDataValueByKey($dataRow, $entityFields, 'id');

        if (!empty($productId)) {
            return (int) $productId;
        }

        if ($fetchByReference) {
            $productReference = $this->fetchDataValueByKey($dataRow, $entityFields, 'reference');

            if ($productReference) {
                $statement = $this->connection->query(
                    'SELECT p.`id_product`
                    FROM `'.$this->productTable.'` p
                    '.Shop::addSqlAssociation('product', 'p').'
                    WHERE p.`reference` = "'.pSQL($productReference).'"'
                );
                $row = $statement->fetch();

                return isset($row['id_product']) ? $row['id_product'] : null;
            }
        }

        return null;
    }

    /**
     * Load stock data for the product.
     *
     * @param Product $product
     */
    private function loadStock(Product $product)
    {
        if (!\Validate::isLoadedObject($product)) {
            return;
        }

        $product->loadStockData();
        $category_data = Product::getProductCategories((int) $product->id);

        if (is_array($category_data)) {
            foreach ($category_data as $tmp) {
                if (!isset($product->category) || !$product->category || is_array($product->category)) {
                    $product->category[] = $tmp;
                }
            }
        }
    }
}
