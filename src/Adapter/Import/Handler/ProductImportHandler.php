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

namespace PrestaShop\PrestaShop\Adapter\Import\Handler;

use Address;
use Category;
use Doctrine\DBAL\Connection;
use Feature;
use FeatureValue;
use Image;
use Manufacturer;
use Module;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Database;
use PrestaShop\PrestaShop\Adapter\Import\ImageCopier;
use PrestaShop\PrestaShop\Adapter\Import\ImportDataFormatter;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Adapter\Validate;
use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;
use PrestaShop\PrestaShop\Core\Import\Configuration\ImportConfigInterface;
use PrestaShop\PrestaShop\Core\Import\Configuration\ImportRuntimeConfigInterface;
use PrestaShop\PrestaShop\Core\Import\Entity;
use PrestaShop\PrestaShop\Core\Import\File\DataRow\DataRowInterface;
use Product;
use ProductDownload;
use ProductSupplier;
use Psr\Log\LoggerInterface;
use Shop;
use SpecificPrice;
use StockAvailable;
use Supplier;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tag;
use TaxManagerFactory;
use TaxRulesGroup;

/**
 * Class ProductImportHandler is responsible for product import.
 */
final class ProductImportHandler extends AbstractImportHandler
{
    /**
     * @var Connection database connection
     */
    private $connection;

    /**
     * @var string product database table name
     */
    private $productTable;

    /**
     * @var string accessory database table name
     */
    private $accessoryTable;

    /**
     * @var Address
     */
    private $shopAddress;

    /**
     * @var Tools
     */
    private $tools;

    /**
     * @var ImageCopier
     */
    private $imageCopier;

    /**
     * @param ImportDataFormatter $dataFormatter
     * @param array $allShopIds
     * @param array $contextShopIds
     * @param int $currentContextShopId
     * @param bool $isMultistoreEnabled
     * @param int $contextLanguageId
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     * @param int $employeeId
     * @param Database $legacyDatabase
     * @param CacheClearerInterface $cacheClearer
     * @param Connection $connection
     * @param string $dbPrefix
     * @param Configuration $configuration
     * @param Address $shopAddress
     * @param Validate $validate
     * @param Tools $tools
     * @param ImageCopier $imageCopier
     */
    public function __construct(
        ImportDataFormatter $dataFormatter,
        array $allShopIds,
        array $contextShopIds,
        $currentContextShopId,
        $isMultistoreEnabled,
        $contextLanguageId,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        $employeeId,
        Database $legacyDatabase,
        CacheClearerInterface $cacheClearer,
        Connection $connection,
        $dbPrefix,
        Configuration $configuration,
        Address $shopAddress,
        Validate $validate,
        Tools $tools,
        ImageCopier $imageCopier
    ) {
        parent::__construct(
            $dataFormatter,
            $allShopIds,
            $contextShopIds,
            $currentContextShopId,
            $isMultistoreEnabled,
            $contextLanguageId,
            $translator,
            $logger,
            $employeeId,
            $legacyDatabase,
            $cacheClearer,
            $configuration,
            $validate
        );

        $this->connection = $connection;
        $this->productTable = $dbPrefix . 'product';
        $this->accessoryTable = $dbPrefix . 'accessory';
        $this->defaultValues = [
            'id_category' => [$this->configuration->getInt('PS_HOME_CATEGORY')],
            'id_category_default' => null,
            'active' => '1',
            'width' => 0.000000,
            'height' => 0.000000,
            'depth' => 0.000000,
            'weight' => 0.000000,
            'visibility' => 'both',
            'additional_shipping_cost' => 0.00,
            'unit_price' => 0,
            'quantity' => 0,
            'minimal_quantity' => 1,
            'low_stock_threshold' => null,
            'low_stock_alert' => false,
            'price' => 0,
            'id_tax_rules_group' => 0,
            'description_short' => [$this->defaultLanguageId => ''],
            'link_rewrite' => [$this->defaultLanguageId => ''],
            'online_only' => 0,
            'condition' => 'new',
            'available_date' => date('Y-m-d'),
            'date_add' => date('Y-m-d H:i:s'),
            'date_upd' => date('Y-m-d H:i:s'),
            'customizable' => 0,
            'uploadable_files' => 0,
            'text_fields' => 0,
            'is_virtual' => 0,
        ];
        $this->shopAddress = $shopAddress;
        $this->tools = $tools;
        $this->imageCopier = $imageCopier;
        $this->importTypeLabel = $this->translator->trans('Products', [], 'Admin.Global');
    }

    /**
     * {@inheritdoc}
     */
    public function setUp(ImportConfigInterface $importConfig, ImportRuntimeConfigInterface $runtimeConfig)
    {
        parent::setUp($importConfig, $runtimeConfig);

        if (!defined('PS_MASS_PRODUCT_CREATION')) {
            define('PS_MASS_PRODUCT_CREATION', true);
        }

        if (!$runtimeConfig->shouldValidateData()) {
            Module::setBatchMode(true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function importRow(
        ImportConfigInterface $importConfig,
        ImportRuntimeConfigInterface $runtimeConfig,
        DataRowInterface $dataRow
    ) {
        parent::importRow($importConfig, $runtimeConfig, $dataRow);

        $entityFields = $runtimeConfig->getEntityFields();
        $productId = $this->fetchProductId(
            $dataRow,
            $runtimeConfig->getEntityFields(),
            $importConfig->matchReferences()
        );
        $productName = $this->fetchDataValueByKey($dataRow, $entityFields, 'name');
        $product = new Product($productId);

        $this->loadStock($product);
        $this->setDefaultValues($product);
        $this->fillEntityData($product, $entityFields, $dataRow, $this->languageId);
        $this->loadShops($product, $importConfig, $productName);
        $this->loadTaxes($product);
        $this->loadManufacturer($product, false);
        $this->loadSupplier($product, false);
        $this->loadPrice($product);
        $this->loadCategory($product, false);
        $this->loadMetaData($product, $importConfig);
        $this->fixFloatValues($product);

        $productExistsById = $this->entityExists($product, 'product');
        $productExistsByReference = $importConfig->matchReferences() &&
            $product->reference &&
            $product->existsRefInDatabase($product->reference)
        ;

        if ($productExistsByReference || $productExistsById) {
            $product->date_upd = date('Y-m-d H:i:s');
        }

        $unfriendlyError = $this->configuration->getBoolean('UNFRIENDLY_ERROR');
        $fieldsError = $product->validateFields($unfriendlyError, true);
        $langFieldsError = $product->validateFieldsLang($unfriendlyError, true);
        $isValid = true === $fieldsError && true === $langFieldsError;

        if ($isValid) {
            $productSaved = $this->loadProductData(
                $product,
                $importConfig,
                $productExistsById,
                $productExistsByReference,
                $runtimeConfig->shouldValidateData(),
                $dataRow,
                $entityFields
            );

            if (!$productSaved) {
                $productId = $this->fetchDataValueByKey($dataRow, $entityFields, 'id');

                $this->error(sprintf(
                    $this->translator->trans('%1$s (ID: %2$s) cannot be saved', [], 'Admin.Advparameters.Notification'),
                    !empty($productName) ? $this->tools->sanitize($productName) : 'No Name',
                    !empty($productId) ? $this->tools->sanitize($productId) : 'No ID'
                ));

                $this->error($fieldsError . $langFieldsError . $this->legacyDatabase->getErrorMessage());
            } else {
                if (!$runtimeConfig->shouldValidateData()) {
                    $this->saveProductSupplier($product);
                    $this->saveProductTags($product, $importConfig, $productName);
                    $this->saveProductImages($product, $importConfig);
                    $this->saveFeatures($product, $importConfig);
                }

                $this->saveSpecificPrice(
                    $product,
                    $this->fetchDataValueByKey($dataRow, $entityFields, 'reduction_price'),
                    $this->fetchDataValueByKey($dataRow, $entityFields, 'reduction_percent'),
                    $this->fetchDataValueByKey($dataRow, $entityFields, 'reduction_from'),
                    $this->fetchDataValueByKey($dataRow, $entityFields, 'reduction_to'),
                    $runtimeConfig->shouldValidateData(),
                    $productName
                );
                $this->updateAdditionalData($product, $runtimeConfig->shouldValidateData());
                $this->saveStock(
                    $product,
                    $runtimeConfig->shouldValidateData(),
                    $productExistsById || $productExistsByReference
                );

                $this->linkAccessories($product, $runtimeConfig);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown(ImportConfigInterface $importConfig, ImportRuntimeConfigInterface $runtimeConfig)
    {
        parent::tearDown($importConfig, $runtimeConfig);

        if ($runtimeConfig->isFinished() && !$runtimeConfig->shouldValidateData()) {
            $this->importAccessories($runtimeConfig);
        }

        if (!$runtimeConfig->shouldValidateData()) {
            Module::processDeferedFuncCall();
            Module::processDeferedClearCache();
            Tag::updateTagCount();
        }
    }

    /**
     * Legacy logic to create category.
     * This method is internally called by legacy Category::searchByPath(), so it has to be public.
     *
     * @param int $defaultLanguageId
     * @param string $categoryName
     * @param int|null $parentCategoryId
     */
    public function createCategory($defaultLanguageId, $categoryName, $parentCategoryId = null)
    {
        $unfriendlyError = $this->configuration->getBoolean('UNFRIENDLY_ERROR');
        $homeCategoryId = $this->configuration->getInt('PS_HOME_CATEGORY');
        $category = new Category();

        $category->id_shop_default = $this->isMultistoreEnabled ? (int) $this->currentContextShopId : 1;
        $category->name = $this->dataFormatter->createMultiLangField(trim($categoryName));
        $category->active = true;
        $category->id_parent = (int) ($parentCategoryId ? $parentCategoryId : $homeCategoryId);
        $category->link_rewrite = $this->dataFormatter->createMultiLangField(
            $this->dataFormatter->createFriendlyUrl($category->name[$defaultLanguageId])
        );

        $fieldsError = $category->validateFields($unfriendlyError, true);
        $langFieldsError = $category->validateFieldsLang($unfriendlyError, true);
        $isValid = true === $fieldsError && true === $langFieldsError;

        if (!$isValid || !$category->add()) {
            $this->error(sprintf(
                $this->translator->trans(
                    '%1$s (ID: %2$s) cannot be saved',
                    [],
                    'Admin.Advparameters.Notification'
                ),
                $category->name[$defaultLanguageId],
                !empty($category->id) ? $category->id : 'null'
            ));

            if (!$isValid) {
                $error = true !== $fieldsError ? $fieldsError : '';
                $error .= true !== $langFieldsError ? $langFieldsError : '';

                $this->error($error . $this->legacyDatabase->getErrorMessage());
            }
        }
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
                    FROM `' . $this->productTable . '` p
                    ' . Shop::addSqlAssociation('product', 'p') . '
                    WHERE p.`reference` = "' . pSQL($productReference) . '"'
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
        if (!Validate::isLoadedObject($product)) {
            return;
        }

        $product->loadStockData();
        $category_data = Product::getProductCategories((int) $product->id);

        if (is_array($category_data)) {
            foreach ($category_data as $tmp) {
                if ($product->category && !is_array($product->category)) {
                    continue;
                }
                $product->category[] = $tmp;
            }
        }
    }

    /**
     * Load shops data into the product object.
     *
     * @param Product $product
     * @param ImportConfigInterface $importConfig
     * @param string $productName used for error messages
     */
    private function loadShops(Product $product, ImportConfigInterface $importConfig, $productName)
    {
        $defaultShopId = $this->configuration->getInt('PS_SHOP_DEFAULT');

        if (!$this->isMultistoreEnabled) {
            $product->shop = $defaultShopId;
            $product->id_shop_default = $defaultShopId;
        } elseif (!isset($product->shop) || empty($product->shop)) {
            $product->shop = implode($importConfig->getMultipleValueSeparator(), $this->contextShopIds);
            $product->id_shop_default = $this->currentContextShopId;
        }

        // link product to shops
        $product->id_shop_list = [];

        $multipleValueSeparator = $importConfig->getMultipleValueSeparator();
        if (empty($multipleValueSeparator)) {
            return;
        }
        $productShops = explode($multipleValueSeparator, $product->shop);

        if (is_array($productShops)) {
            foreach ($productShops as $shop) {
                if (!empty($shop)) {
                    $shop = is_numeric($shop) ? $shop : Shop::getIdByName($shop);

                    if (!in_array($shop, $this->allShopIds)) {
                        $this->addEntityWarning(
                            $this->translator->trans('Shop is not valid', [], 'Admin.Advparameters.Notification'),
                            $productName,
                            $product->id
                        );
                    } else {
                        $product->id_shop_list[] = $shop;
                    }
                }
            }
        }
    }

    /**
     * Load taxes data into the product object.
     *
     * @param Product $product
     */
    private function loadTaxes(Product $product)
    {
        if ($product->id_tax_rules_group) {
            if (Validate::isLoadedObject(new TaxRulesGroup($product->id_tax_rules_group))) {
                $taxManager = TaxManagerFactory::getManager($this->shopAddress, $product->id_tax_rules_group);
                $taxCalculator = $taxManager->getTaxCalculator();
                $product->tax_rate = $taxCalculator->getTotalRate();
            } else {
                $this->addEntityWarning(
                    $this->translator->trans(
                        'Unknown tax rule group ID. You need to create a group with this ID first.',
                        [],
                        'Admin.Advparameters.Notification'
                    ),
                    'id_tax_rules_group',
                    $product->id_tax_rules_group
                );
            }
        }

        if (!$this->configuration->getBoolean('PS_USE_ECOTAX')) {
            $product->ecotax = 0;
        }
    }

    /**
     * Load manufacturer data into the product object.
     *
     * @param Product $product
     * @param bool $validateOnly if true, will not create new manufacturer if not exists
     */
    private function loadManufacturer(Product $product, $validateOnly)
    {
        if (!isset($product->manufacturer)) {
            return;
        }

        if (is_numeric($product->manufacturer) && Manufacturer::manufacturerExists($product->manufacturer)) {
            $product->id_manufacturer = (int) $product->manufacturer;
        } elseif (is_string($product->manufacturer) && !empty($product->manufacturer)) {
            if ($manufacturer = Manufacturer::getIdByName($product->manufacturer)) {
                $product->id_manufacturer = (int) $manufacturer;
            } else {
                $unfriendlyError = $this->configuration->getBoolean('UNFRIENDLY_ERROR');

                $manufacturer = new Manufacturer();
                $manufacturer->name = $product->manufacturer;
                $manufacturer->active = true;

                $fieldsError = $manufacturer->validateFields($unfriendlyError, true);
                $langFieldsError = $manufacturer->validateFieldsLang($unfriendlyError, true);
                $isValid = true === $fieldsError && true === $langFieldsError;

                // Creating the manufacturer if it's not validation step
                if ($isValid && !$validateOnly && $manufacturer->add()) {
                    $product->id_manufacturer = (int) $manufacturer->id;
                    $manufacturer->associateTo($product->id_shop_list);
                } else {
                    if (!$validateOnly) {
                        $this->error(sprintf(
                            $this->translator->trans(
                                '%1$s (ID: %2$s) cannot be saved',
                                [],
                                'Admin.Advparameters.Notification'
                            ),
                            $manufacturer->name,
                            !empty($manufacturer->id) ? $manufacturer->id : 'null'
                        ));
                    }

                    if (!$isValid) {
                        $error = true !== $fieldsError ? $fieldsError : '';
                        $error .= true !== $langFieldsError ? $langFieldsError : '';

                        $this->error($error . $this->legacyDatabase->getErrorMessage());
                    }
                }
            }
        }
    }

    /**
     * Load supplier data into the product object.
     *
     * @param Product $product
     * @param bool $validateOnly if true, will not create new supplier if not exists
     */
    private function loadSupplier(Product $product, $validateOnly)
    {
        if (!isset($product->supplier)) {
            return;
        }

        if (is_numeric($product->supplier) && Supplier::supplierExists($product->supplier)) {
            $product->id_supplier = (int) $product->supplier;
        } elseif (is_string($product->supplier) && !empty($product->supplier)) {
            if ($supplier = Supplier::getIdByName($product->supplier)) {
                $product->id_supplier = (int) $supplier;
            } else {
                $unfriendlyError = $this->configuration->getBoolean('UNFRIENDLY_ERROR');

                $supplier = new Supplier();
                $supplier->name = $product->supplier;
                $supplier->active = true;

                $fieldsError = $supplier->validateFields($unfriendlyError, true);
                $langFieldsError = $supplier->validateFieldsLang($unfriendlyError, true);
                $isValid = true === $fieldsError && true === $langFieldsError;

                // Creating the supplier if it's not validation step
                if ($isValid && !$validateOnly && $supplier->add()) {
                    $product->id_supplier = (int) $supplier->id;
                    $supplier->associateTo($product->id_shop_list);
                } else {
                    if (!$validateOnly) {
                        $this->error(sprintf(
                            $this->translator->trans(
                                '%1$s (ID: %2$s) cannot be saved',
                                [],
                                'Admin.Advparameters.Notification'
                            ),
                            $supplier->name,
                            !empty($supplier->id) ? $supplier->id : 'null'
                        ));
                    }

                    if (!$isValid) {
                        $error = true !== $fieldsError ? $fieldsError : '';
                        $error .= true !== $langFieldsError ? $langFieldsError : '';

                        $this->error($error . $this->legacyDatabase->getErrorMessage());
                    }
                }
            }
        }
    }

    /**
     * Load prices into product object.
     *
     * @param Product $product
     */
    private function loadPrice(Product $product)
    {
        if (isset($product->price_tex) && !isset($product->price_tin)) {
            $product->price = $product->price_tex;
        } elseif (isset($product->price_tin) && !isset($product->price_tex)) {
            $product->price = $product->price_tin;
            // If a tax is already included in price, withdraw it from price
            if ($product->tax_rate) {
                $product->price = (float) number_format($product->price / (1 + $product->tax_rate / 100), 6, '.', '');
            }
        } elseif (isset($product->price_tin, $product->price_tex)) {
            $product->price = $product->price_tex;
        }
    }

    /**
     * Load category data into product object.
     *
     * @param Product $product
     * @param bool $validateOnly
     */
    private function loadCategory(Product $product, $validateOnly)
    {
        if (is_array($product->category) && count($product->category)) {
            $unfriendlyError = $this->configuration->getBoolean('UNFRIENDLY_ERROR');
            $defaultLanguageId = $this->configuration->getInt('PS_LANG_DEFAULT');
            $homeCategoryId = $this->configuration->getInt('PS_HOME_CATEGORY');
            $product->id_category = []; // Reset default values array

            foreach ($product->category as $value) {
                if (is_numeric($value)) {
                    if (Category::categoryExists((int) $value)) {
                        $product->id_category[] = (int) $value;
                    } else {
                        $category = new Category();
                        $category->id = (int) $value;
                        $category->name = $this->dataFormatter->createMultiLangField($value);
                        $category->active = true;
                        $category->id_parent = $homeCategoryId;
                        $category->link_rewrite = $this->dataFormatter->createMultiLangField(
                            $this->dataFormatter->createFriendlyUrl($category->name[$defaultLanguageId])
                        );

                        $fieldsError = $category->validateFields($unfriendlyError, true);
                        $langFieldsError = $category->validateFieldsLang($unfriendlyError, true);
                        $isValid = true === $fieldsError && true === $langFieldsError;

                        if ($isValid && !$validateOnly && $category->add()) {
                            $product->id_category[] = (int) $category->id;
                        } else {
                            if (!$validateOnly) {
                                $this->error(sprintf(
                                    $this->translator->trans(
                                        '%1$s (ID: %2$s) cannot be saved',
                                        [],
                                        'Admin.Advparameters.Notification'
                                    ),
                                    $category->name[$defaultLanguageId],
                                    !empty($category->id) ? $category->id : 'null'
                                ));
                            }

                            if (!$isValid) {
                                $error = true !== $fieldsError ? $fieldsError : '';
                                $error .= true !== $langFieldsError ? $langFieldsError : '';

                                $this->error($error . $this->legacyDatabase->getErrorMessage());
                            }
                        }
                    }
                } elseif (!$validateOnly && is_string($value) && !empty($value)) {
                    $category = Category::searchByPath(
                        $defaultLanguageId,
                        trim($value),
                        $this,
                        'createCategory'
                    );
                    if ($category['id_category']) {
                        $product->id_category[] = (int) $category['id_category'];
                    } else {
                        $this->error(
                            $this->translator->trans(
                                '%data% cannot be saved',
                                [
                                    '%data%' => trim($value),
                                ],
                                'Admin.Advparameters.Notification'
                            )
                        );
                    }
                }
            }

            $product->id_category = array_values(array_unique($product->id_category));
        }

        // Category default now takes the value of the first new category during import
        if (isset($product->id_category[0])) {
            $product->id_category_default = (int) $product->id_category[0];
        } elseif (!empty($product->id_category_default)) {
            $defaultProductShop = new Shop($product->id_shop_default);
            $product->id_category_default = Category::getRootCategory(
                null,
                Validate::isLoadedObject($defaultProductShop) ? $defaultProductShop : null
            )->id;
        }
    }

    /**
     * Load meta data into the product object.
     *
     * @param Product $product
     * @param ImportConfigInterface $importConfig
     */
    private function loadMetaData(Product $product, ImportConfigInterface $importConfig)
    {
        $linkRewrite = '';

        $linkRewriteExists = is_array($product->link_rewrite) && isset($product->link_rewrite[$this->languageId]);

        if ($linkRewriteExists) {
            $linkRewrite = trim($product->link_rewrite[$this->languageId]);
        }

        $validLink = $this->validate->isLinkRewrite($linkRewrite);

        if (($linkRewriteExists && empty($product->link_rewrite[$this->languageId])) || !$validLink) {
            $linkRewrite = $this->dataFormatter->createFriendlyUrl($product->name[$this->languageId]);

            if ($linkRewrite == '') {
                $linkRewrite = 'friendly-url-autogeneration-failed';
            }
        }

        if (!$validLink) {
            $this->notice($this->translator->trans(
                'Rewrite link for %1$s (ID %2$s): re-written as %3$s.',
                [
                    '%1$s' => $product->name[$this->languageId],
                    '%2$s' => 'null',
                    '%3$s' => $linkRewrite,
                ],
                'Admin.Advparameters.Notification'
            ));
        }

        if (!$validLink || !(is_array($product->link_rewrite) && count($product->link_rewrite))) {
            $product->link_rewrite = $this->dataFormatter->createMultiLangField($linkRewrite);
        } else {
            $product->link_rewrite[(int) $this->languageId] = $linkRewrite;
        }

        $multipleValueSeparator = $importConfig->getMultipleValueSeparator();

        // replace the value of separator by coma
        if ($multipleValueSeparator != ',') {
            if (is_array($product->meta_keywords)) {
                foreach ($product->meta_keywords as &$metaKeyword) {
                    if (!empty($metaKeyword)) {
                        $metaKeyword = str_replace($multipleValueSeparator, ',', $metaKeyword);
                    }
                }
            }
        }
    }

    /**
     * Fix float values.
     *
     * @param Product $product
     */
    private function fixFloatValues(Product $product)
    {
        // Convert comma into dot for all floating values
        foreach (Product::$definition['fields'] as $key => $array) {
            if ($array['type'] == Product::TYPE_FLOAT) {
                $product->{$key} = str_replace(',', '.', $product->{$key});
            }
        }
    }

    /**
     * Load other product data.
     *
     * @param Product $product
     * @param ImportConfigInterface $importConfig
     * @param bool $productExistsById
     * @param bool $productExistsByReference
     * @param bool $validateOnly
     * @param DataRowInterface $dataRow
     * @param array $entityFields
     *
     * @return bool
     */
    private function loadProductData(
        Product $product,
        ImportConfigInterface $importConfig,
        $productExistsById,
        $productExistsByReference,
        $validateOnly,
        DataRowInterface $dataRow,
        array $entityFields
    ) {
        if (!$product->quantity) {
            $product->quantity = 0;
        }

        $product->force_id = (bool) $importConfig->forceIds();
        $result = true;

        if ($productExistsById || $productExistsByReference) {
            $sqlPart = 'SELECT product_shop.`date_add`, p.`id_product`
                FROM `' . _DB_PREFIX_ . 'product` p
                ' . Shop::addSqlAssociation('product', 'p') . '
                WHERE ';

            if ($productExistsByReference) {
                $sqlPart .= 'p.`reference` = "' . pSQL($product->reference) . '"';
            } else {
                $sqlPart .= 'p.`id_product` = ' . (int) $product->id;
            }

            $statement = $this->connection->query($sqlPart);
            $row = $statement->fetch();

            if ($productExistsByReference) {
                $product->id = (int) $row['id_product'];
            }

            $product->date_add = $row['date_add'];

            if (!$validateOnly) {
                $result = $product->update();
            }
        } else {
            $result = $product->add($product->date_add == '');
        }

        if (!$validateOnly) {
            if ($product->getType() == Product::PTYPE_VIRTUAL) {
                StockAvailable::setProductOutOfStock((int) $product->id, 1);
            } else {
                StockAvailable::setProductOutOfStock((int) $product->id, (int) $product->out_of_stock);
            }

            if ($productDownload_id = ProductDownload::getIdFromIdProduct((int) $product->id)) {
                $productDownload = new ProductDownload($productDownload_id);
                $productDownload->delete(true);
            }

            if ($product->getType() == Product::PTYPE_VIRTUAL) {
                $downloadDir = $this->configuration->get('_PS_DOWNLOAD_DIR_');

                $productDownload = new ProductDownload();
                $productDownload->filename = ProductDownload::getNewFilename();
                $virtualProductFileUrl = $this->fetchDataValueByKey(
                    $dataRow,
                    $entityFields,
                    'file_url'
                );
                $this->tools->copy($virtualProductFileUrl, $downloadDir . $productDownload->filename);
                $productDownload->id_product = (int) $product->id;
                $productDownload->nb_downloadable = (int) $this->fetchDataValueByKey(
                    $dataRow,
                    $entityFields,
                    'nb_downloadable'
                );
                $productDownload->date_expiration = $this->fetchDataValueByKey(
                    $dataRow,
                    $entityFields,
                    'date_expiration'
                );
                $productDownload->nb_days_accessible = (int) $this->fetchDataValueByKey(
                    $dataRow,
                    $entityFields,
                    'nb_days_accessible'
                );
                $productDownload->display_filename = basename($virtualProductFileUrl);
                $productDownload->add();
            }
        }

        return $result;
    }

    /**
     * Save product supplier data.
     *
     * @param Product $product
     */
    private function saveProductSupplier(Product $product)
    {
        if ($product->id && property_exists($product, 'supplier_reference')) {
            $productSupplierId = (int) ProductSupplier::getIdByProductAndSupplier(
                (int) $product->id,
                0,
                (int) $product->id_supplier
            );

            $productSupplier = new ProductSupplier($productSupplierId);
            $productSupplier->id_product = (int) $product->id;
            $productSupplier->id_product_attribute = 0;
            $productSupplier->id_supplier = (int) $product->id_supplier;
            $productSupplier->product_supplier_price_te = $product->wholesale_price;
            $productSupplier->product_supplier_reference = $product->supplier_reference;
            $productSupplier->save();
        }
    }

    /**
     * Save specific price for a product.
     *
     * @param Product $product
     * @param string $reductionPrice
     * @param string $reductionPercent
     * @param string $reductionFrom
     * @param string $reductionTo
     * @param bool $validateOnly
     * @param string $productName
     */
    private function saveSpecificPrice(
        Product $product,
        $reductionPrice,
        $reductionPercent,
        $reductionFrom,
        $reductionTo,
        $validateOnly,
        $productName
    ) {
        $reductionPercent = (float) $reductionPercent;
        $reductionPrice = (float) $reductionPrice;

        if (!$reductionPrice <= 0 && $reductionPercent <= 0) {
            return;
        }

        foreach ($product->id_shop_list as $shopId) {
            $specificPrice = SpecificPrice::getSpecificPrice($product->id, $shopId, 0, 0, 0, 1, 0, 0, 0, 0);

            if (is_array($specificPrice) && isset($specificPrice['id_specific_price'])) {
                $specificPrice = new SpecificPrice((int) $specificPrice['id_specific_price']);
            } else {
                $specificPrice = new SpecificPrice();
            }
            $specificPrice->id_product = (int) $product->id;
            $specificPrice->id_specific_price_rule = 0;
            $specificPrice->id_shop = $shopId;
            $specificPrice->id_currency = 0;
            $specificPrice->id_country = 0;
            $specificPrice->id_group = 0;
            $specificPrice->price = -1;
            $specificPrice->id_customer = 0;
            $specificPrice->from_quantity = 1;
            $specificPrice->reduction = $reductionPrice ? $reductionPrice : $reductionPercent / 100;
            $specificPrice->reduction_type = $reductionPrice ? 'amount' : 'percentage';
            $specificPrice->from = Validate::isDate($reductionFrom) ? $reductionFrom : '0000-00-00 00:00:00';
            $specificPrice->to = Validate::isDate($reductionTo) ? $reductionTo : '0000-00-00 00:00:00';

            if (!$validateOnly && !$specificPrice->save()) {
                $this->addEntityWarning(
                    $this->translator->trans('Discount is invalid', [], 'Admin.Advparameters.Notification'),
                    $this->tools->sanitize($productName),
                    $product->id
                );
            }
        }
    }

    /**
     * Save product tags data.
     *
     * @param Product $product
     * @param ImportConfigInterface $importConfig
     * @param string $productName product name, used for error messages
     */
    private function saveProductTags(Product $product, ImportConfigInterface $importConfig, $productName)
    {
        if (empty($product->tags)) {
            return;
        }

        $multipleValueSeparator = $importConfig->getMultipleValueSeparator();

        if (isset($product->id) && $product->id) {
            $tags = Tag::getProductTags($product->id);
            if (is_array($tags) && count($tags)) {
                if (is_string($product->tags) && !empty($multipleValueSeparator)) {
                    $product->tags = explode($multipleValueSeparator, $product->tags);
                }
                if (is_array($product->tags)) {
                    foreach ($product->tags as $key => $tag) {
                        if (!empty($tag)) {
                            $product->tags[$key] = trim($tag);
                        }
                    }
                    $tags[$this->languageId] = $product->tags;
                    $product->tags = $tags;
                }
            }
        }
        // Delete tags for this id product, for no duplicating error
        Tag::deleteTagsForProduct($product->id);

        if (!is_array($product->tags) && !empty($product->tags)) {
            $product->tags = $this->dataFormatter->createMultiLangField($product->tags);
            foreach ($product->tags as $key => $tags) {
                $isTagAdded = Tag::addTags($key, $product->id, $tags, $multipleValueSeparator);
                if (!$isTagAdded) {
                    $this->addEntityWarning(
                        $this->translator->trans('Tags list is invalid', [], 'Admin.Advparameters.Notification'),
                        $this->tools->sanitize($productName),
                        $product->id
                    );
                    break;
                }
            }
        } else {
            foreach ($product->tags as $key => $tags) {
                $str = '';

                foreach ($tags as $one_tag) {
                    $str .= $one_tag . $multipleValueSeparator;
                }

                $str = rtrim($str, $multipleValueSeparator);
                $isTagAdded = Tag::addTags($key, $product->id, $str, $multipleValueSeparator);

                if (!$isTagAdded) {
                    $this->addEntityWarning(
                        $this->translator->trans(
                            'Invalid tag(s) (%s)',
                            [
                                $str,
                            ],
                            'Admin.Notifications.Error'
                        ),
                        $this->tools->sanitize($productName),
                        (int) $product->id
                    );
                    break;
                }
            }
        }
    }

    /**
     * Save product images.
     *
     * @param Product $product
     * @param ImportConfigInterface $importConfig
     */
    private function saveProductImages(Product $product, ImportConfigInterface $importConfig)
    {
        //delete existing images if "delete_existing_images" is set to 1
        if (isset($product->delete_existing_images)) {
            if ((bool) $product->delete_existing_images) {
                $product->deleteImages();
            }
        }

        if (isset($product->image) && is_array($product->image) && count($product->image)) {
            $unfriendlyError = $this->configuration->getBoolean('UNFRIENDLY_ERROR');
            $product_has_images = (bool) Image::getImages($this->languageId, (int) $product->id);

            foreach ($product->image as $key => $url) {
                $url = trim($url);
                $error = false;
                if (!empty($url)) {
                    $url = str_replace(' ', '%20', $url);

                    $image = new Image();
                    $image->id_product = (int) $product->id;
                    $image->position = Image::getHighestPosition($product->id) + 1;
                    $image->cover = (!$key && !$product_has_images) ? true : false;
                    $alt = $product->image_alt[$key];
                    if (strlen($alt) > 0) {
                        $image->legend = $this->dataFormatter->createMultiLangField($alt);
                    }

                    $fieldsError = $image->validateFields($unfriendlyError, true);
                    $langFieldsError = $image->validateFieldsLang($unfriendlyError, true);
                    $isValid = true === $fieldsError && true === $langFieldsError;

                    if ($isValid && $image->add()) {
                        // associate image to selected shops
                        $image->associateTo($product->id_shop_list);
                        $copySucceeded = $this->imageCopier->copyImg(
                            $product->id,
                            $image->id,
                            $url,
                            'products',
                            !$importConfig->skipThumbnailRegeneration()
                        );

                        if (!$copySucceeded) {
                            $image->delete();
                            $this->warning(
                                $this->translator->trans(
                                    'Error copying image: %url%',
                                    [
                                        '%url%' => $url,
                                    ],
                                    'Admin.Advparameters.Notification'
                                )
                            );
                        }
                    } else {
                        $error = true;
                    }
                } else {
                    $error = true;
                }

                if ($error) {
                    $this->warning(
                        $this->translator->trans(
                            'Product #%id%: the picture (%url%) cannot be saved.',
                            [
                                '%id%' => isset($image) ? $image->id_product : '',
                                '%url%' => $url,
                            ],
                            'Admin.Advparameters.Notification'
                        )
                    );
                }
            }
        }
    }

    /**
     * Update additional product data.
     *
     * @param Product $product
     * @param bool $validateOnly
     */
    private function updateAdditionalData(Product $product, $validateOnly)
    {
        if (!$validateOnly && isset($product->id_category) && is_array($product->id_category)) {
            $product->updateCategories(array_map('intval', $product->id_category));
        }

        $product->checkDefaultAttributes();
        if (!$validateOnly && !$product->cache_default_attribute) {
            Product::updateDefaultAttribute($product->id);
        }
    }

    /**
     * Save product features.
     *
     * @param Product $product
     * @param ImportConfigInterface $importConfig
     */
    private function saveFeatures(Product $product, ImportConfigInterface $importConfig)
    {
        //delete existing features if "delete_existing_features" is set to 1
        if (isset($product->delete_existing_features)) {
            if ((bool) $product->delete_existing_features) {
                $product->deleteProductFeatures();
            }
        }

        // Features import
        $features = get_object_vars($product);
        $multipleValueSeparator = $importConfig->getMultipleValueSeparator();

        if (empty($features['features']) || empty($multipleValueSeparator)) {
            return;
        }

        foreach (explode($multipleValueSeparator, $features['features']) as $singleFeature) {
            if (empty($singleFeature)) {
                continue;
            }
            $feature = explode(':', $singleFeature);
            $featureName = isset($feature[0]) ? trim($feature[0]) : '';
            $featureValue = isset($feature[1]) ? trim($feature[1]) : '';
            $position = isset($feature[2]) ? (int) $feature[2] - 1 : false;
            $custom = isset($feature[3]) ? (int) $feature[3] : false;
            $action = (isset($feature[4]) && in_array(trim($feature[4]), ['add', 'delete'])) ? trim($feature[4]) : 'add';

            if (!empty($featureName) && !empty($featureValue)) {
                $featureId = (int) Feature::getFeatureImport($featureName, $position, ($action == 'add'));

                if ($featureId) {
                    $productId = null;
                    if ($importConfig->forceIds() || $importConfig->matchReferences()) {
                        $productId = (int) $product->id;
                    }
                    $featureValueId = (int) FeatureValue::getFeatureValueImport(
                        $featureId,
                        $featureValue,
                        $productId,
                        $this->languageId,
                        $custom,
                        ($action == 'add')
                    );
                    if ($featureValueId) {
                        if ($action == 'delete') {
                            Product::deleteFeatureProductImport($product->id, $featureId, $featureValueId);
                        } else {
                            Product::addFeatureProductImport($product->id, $featureId, $featureValueId);
                        }
                    }
                }
            }
        }

        // clean feature positions to avoid conflict
        Feature::cleanPositions();
    }

    /**
     * Save stock data for the product.
     *
     * @param Product $product
     * @param bool $validateOnly
     * @param bool $productExists
     */
    private function saveStock(Product $product, $validateOnly, $productExists)
    {
        if ($this->isMultistoreEnabled) {
            $shopIds = $product->id_shop_list;
        } else {
            $shopIds = [
                $this->currentContextShopId,
            ];
        }

        if (!$validateOnly) {
            foreach ($shopIds as $shop) {
                StockAvailable::setQuantity((int) $product->id, 0, (int) $product->quantity, (int) $shop);
            }
        }
    }

    /**
     * Link product accessories.
     *
     * @param Product $product
     * @param ImportRuntimeConfigInterface $runtimeConfig
     */
    private function linkAccessories(Product $product, ImportRuntimeConfigInterface $runtimeConfig)
    {
        // Accessories linkage
        if ($runtimeConfig->shouldValidateData()) {
            return;
        }

        $hasAccessories =
            isset($product->accessories) &&
            is_array($product->accessories) &&
            count($product->accessories)
        ;

        if ($hasAccessories) {
            $sharedData = $runtimeConfig->getSharedData();
            $accessories = isset($sharedData['accessories']) ? $sharedData['accessories'] : [];
            $accessories[$product->id] = $product->accessories;
            $runtimeConfig->addSharedDataItem('accessories', $accessories);
        }
    }

    /**
     * Import accessories.
     *
     * @param ImportRuntimeConfigInterface $runtimeConfig
     */
    private function importAccessories(ImportRuntimeConfigInterface $runtimeConfig)
    {
        $sharedData = $runtimeConfig->getSharedData();

        if (!isset($sharedData['accessories'])) {
            return;
        }

        foreach ($sharedData['accessories'] as $productId => $links) {
            if (count($links) > 0) { // We delete and relink only if there is accessories to link...
                // Bulk jobs: for performances, we need to do a minimum amount of SQL queries. No product inflation.
                $uniqueIds = Product::getExistingIdsFromIdsOrRefs($links);
                $this->connection->delete(
                    $this->accessoryTable,
                    [
                        'id_product_1' => (int) $productId,
                    ]
                );
                Product::changeAccessoriesForProduct($uniqueIds, $productId);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($importEntityType)
    {
        return $importEntityType === Entity::TYPE_PRODUCTS;
    }
}
