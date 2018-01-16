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

namespace PrestaShopBundle\Model\Product;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Product\AdminProductWrapper;
use PrestaShop\PrestaShop\Adapter\Tax\TaxRuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Adapter\Product\ProductDataProvider;
use PrestaShop\PrestaShop\Adapter\Supplier\SupplierDataProvider;
use PrestaShop\PrestaShop\Adapter\Warehouse\WarehouseDataProvider;
use PrestaShop\PrestaShop\Adapter\Feature\FeatureDataProvider;
use PrestaShop\PrestaShop\Adapter\Pack\PackDataProvider;
use PrestaShop\PrestaShop\Adapter\Shop\Context as ShopContext;
use PrestaShopBundle\Utils\FloatParser;
use ProductDownload;
use Attachment;
use Configuration as ConfigurationLegacy;
use \Tools as ToolsLegacy;
use \Product;

/**
 * This form class is responsible to map the form data to the product object
 */
class AdminModelAdapter extends \PrestaShopBundle\Model\AdminModelAdapter
{
    /** @var LegacyContext  */
    private $context;
    /** @var \Context  */
    private $contextShop;
    /** @var AdminProductWrapper  */
    private $adminProductWrapper;
    /** @var \PrestaShop\PrestaShop\Core\Cldr\Repository  */
    private $cldrRepository;
    /** @var array  */
    private $locales;
    /** @var string */
    private $defaultLocale;
    /** @var Tools  */
    private $tools;
    /** @var ProductDataProvider  */
    private $productAdapter;
    /** @var SupplierDataProvider  */
    private $supplierAdapter;
    /** @var FeatureDataProvider  */
    private $featureAdapter;
    /** @var PackDataProvider  */
    private $packAdapter;
    /** @var Configuration  */
    private $configuration;
    /** @var ShopContext  */
    private $shopContext;
    /** @var TaxRuleDataProvider  */
    private $taxRuleDataProvider;
    /** @var array */
    private $productPricePriority;
    /** @var WarehouseDataProvider  */
    private $warehouseAdapter;
    /** @var array  */
    private $multiShopKeys = array(
        'category_box',
        'id_category_default',
        'attribute_wholesale_price',
        'attribute_price_impact',
        'attribute_weight_impact',
        'attribute_unit_impact',
        'attribute_ecotax',
        'attribute_minimal_quantity',
        'attribute_low_stock_threshold',
        'attribute_low_stock_alert',
        'available_date_attribute',
        'attribute_default',
        'uploadable_files',
        'text_fields',
        'active',
        'redirect_type',
        'id_type_redirected',
        'visibility',
        'available_for_order',
        'show_price',
        'online_only',
        'show_condition',
        'condition',
        'wholesale_price',
        'price',
        'id_tax_rules_group',
        'ecotax',
        'unit_price',
        'on_sale',
        'minimal_quantity',
        'low_stock_threshold',
        'low_stock_alert',
        'available_date',
        'ecotax',
    );

    /**
     * Defines translatable key
     *
     * @var array
     */
    private $translatableKeys = array(
        'name',
        'description',
        'description_short',
        'link_rewrite',
        'meta_title',
        'meta_description',
        'available_now',
        'available_later',
        'tags',
        'delivery_in_stock',
        'delivery_out_stock',
    );

    /**
     * Defines unused key for manual binding
     *
     * @var array
     */
    private $unmapKeys = array(
        'name',
        'description',
        'description_short',
        'images',
        'related_products',
        'categories',
        'suppliers',
        'display_options',
        'features',
        'specific_price',
        'virtual_product',
        'attachment_product',
    );

    /**
     * Constructor
     * Set all adapters needed and get product
     *
     * @param LegacyContext $legacyContext
     * @param AdminProductWrapper $adminProductWrapper
     * @param Tools $toolsAdapter
     * @param ProductDataProvider $productDataProvider
     * @param SupplierDataProvider $supplierDataProvider
     * @param WarehouseDataProvider $warehouseDataProvider
     * @param FeatureDataProvider $featureDataProvider
     * @param PackDataProvider $packDataProvider
     * @param ShopContext $shopContext
     * @param TaxRuleDataProvider $taxRuleDataProvider
     *
     * @throws \PrestaShopException
     */
    public function __construct(
        LegacyContext $legacyContext,
        AdminProductWrapper $adminProductWrapper,
        Tools $toolsAdapter,
        ProductDataProvider $productDataProvider,
        SupplierDataProvider $supplierDataProvider,
        WarehouseDataProvider $warehouseDataProvider,
        FeatureDataProvider $featureDataProvider,
        PackDataProvider $packDataProvider,
        ShopContext $shopContext,
        TaxRuleDataProvider $taxRuleDataProvider
    ) {
        $this->context = $legacyContext;
        $this->contextShop = $this->context->getContext();
        $this->adminProductWrapper = $adminProductWrapper;
        $this->cldrRepository = ToolsLegacy::getCldr($this->contextShop);
        $this->locales = $this->context->getLanguages();
        $this->defaultLocale = $this->locales[0]['id_lang'];
        $this->tools = $toolsAdapter;
        $this->productAdapter = $productDataProvider;
        $this->supplierAdapter = $supplierDataProvider;
        $this->warehouseAdapter = $warehouseDataProvider;
        $this->featureAdapter = $featureDataProvider;
        $this->packAdapter = $packDataProvider;
        $this->configuration = new Configuration();
        $this->shopContext = $shopContext;
        $this->taxRuleDataProvider = $taxRuleDataProvider;
    }

    /**
     * modelMapper
     * Map form data to object model
     *
     * @param array $form_data
     * @param bool $isMultiShopContext If the context is define to multishop, force data to be apply on all shops
     *
     * @return array Transformed form data to model attempt
     */
    public function getModelData($form_data, $isMultiShopContext = false)
    {
        //merge all form steps
        $form_data = array_merge(
            ['id_product' => $form_data['id_product']],
            $form_data['step1'],
            $form_data['step2'],
            $form_data['step3'],
            $form_data['step4'],
            $form_data['step5'],
            $form_data['step6']
        );

        //add some legacy field to execute some add/update methods
        $form_data['submitted_tabs'] = ['Shipping'];
        $form_data['submitted_tabs'][] = 'Associations';

        //map translatable
        foreach ($this->translatableKeys as $field) {
            foreach ($form_data[$field] as $lang_id => $translate_value) {
                $form_data[$field.'_'.$lang_id] = $translate_value;
            }
        }

        //Product type
        if ($form_data['type_product'] == 2) {
            $form_data['condition'] = 'new';
            $form_data['is_virtual'] = 1;
        } else {
            $form_data['is_virtual'] = 0;
        }

        // Product redirection
        $form_data['redirect_type'] = (string)$form_data['redirect_type'];
        if ($form_data['redirect_type'] != '404') {
            if (isset($form_data['id_type_redirected']) && !empty($form_data['id_type_redirected']['data'])) {
                $form_data['id_type_redirected'] = $form_data['id_type_redirected']['data'][0];
            } else {
                $form_data['id_type_redirected'] = 0;
                $form_data['redirect_type'] = '404';
            }
        } else {
            $form_data['id_type_redirected'] = 0;
            $form_data['redirect_type'] = '404';
        }

        //map inputPackItems
        if ($form_data['type_product'] == 1
            && !empty($form_data['inputPackItems'])
            && !empty($form_data['inputPackItems']['data'])
        ) {
            $inputPackItems = '';
            foreach ($form_data['inputPackItems']['data'] as $productIds) {
                $inputPackItems .= $productIds.'-';
            }
            $form_data['inputPackItems'] = $inputPackItems;
        } else {
            $form_data['inputPackItems'] = '';
        }

        //map categories
        foreach ($form_data['categories']['tree'] as $category) {
            $form_data['categoryBox'][] = $category;
        }

        //if empty categories, set default one
        if (empty($form_data['categoryBox'])) {
            $form_data['categoryBox'][] = $this->contextShop->shop->id_category;
        }

        //if default category not define, set the default one
        if (empty($form_data['id_category_default'])) {
            $form_data['id_category_default'] = $this->contextShop->shop->id_category;
        }

        //map combinations and impact price/weight/unit price
        foreach ($form_data['combinations'] as $k => $combination) {
            $form_data['combinations'][$k]['attribute_price_impact'] = 0;
            $form_data['combinations'][$k]['attribute_weight_impact'] = 0;
            $form_data['combinations'][$k]['attribute_unit_impact'] = 0;

            $floatParser = new FloatParser();

            if ($floatParser->fromString($combination['attribute_price']) > 0) {
                $form_data['combinations'][$k]['attribute_price_impact'] = 1;
            } elseif ($floatParser->fromString($combination['attribute_price']) < 0) {
                $form_data['combinations'][$k]['attribute_price_impact'] = -1;
            }

            if ($floatParser->fromString($combination['attribute_weight']) > 0) {
                $form_data['combinations'][$k]['attribute_weight_impact'] = 1;
            } elseif ($floatParser->fromString($combination['attribute_weight']) < 0) {
                $form_data['combinations'][$k]['attribute_weight_impact'] = -1;
            }

            if ($floatParser->fromString($combination['attribute_unity']) > 0) {
                $form_data['combinations'][$k]['attribute_unit_impact'] = 1;
            } elseif ($floatParser->fromString($combination['attribute_unity']) < 0) {
                $form_data['combinations'][$k]['attribute_unit_impact'] = -1;
            }

            $form_data['combinations'][$k]['attribute_price'] = abs(
                $floatParser->fromString($combination['attribute_price'])
            );
            $form_data['combinations'][$k]['attribute_weight'] = abs(
                $floatParser->fromString($combination['attribute_weight'])
            );
            $form_data['combinations'][$k]['attribute_unity'] = abs(
                $floatParser->fromString($combination['attribute_unity'])
            );
        }

        //map suppliers
        $form_data['supplier_loaded'] = 1;
        if (!empty($form_data['suppliers'])) {
            foreach ($form_data['suppliers'] as $id_supplier) {
                $form_data['check_supplier_'.$id_supplier] = 1;

                //map supplier combinations
                foreach ($form_data['supplier_combination_'.$id_supplier] as $combination) {
                    $key = $form_data['id_product'].'_'.$combination['id_product_attribute'].'_'.$id_supplier;
                    $form_data['supplier_reference_'.$key] = $combination['supplier_reference'];
                    $form_data['product_price_'.$key] = $combination['product_price'];
                    $form_data['product_price_currency_'.$key] = $combination['product_price_currency'];

                    unset($form_data['supplier_combination_'.$id_supplier]);
                }
            }
        }

        //map display options
        foreach ($form_data['display_options'] as $option => $value) {
            $form_data[$option] = $value;
        }

        //if empty, set link_rewrite for default locale
        $linkRewriteKey = 'link_rewrite_'.$this->locales[0]['id_lang'];
        if (empty($form_data[$linkRewriteKey])) {
            $form_data[$linkRewriteKey] = $this->tools->link_rewrite($form_data['name_'.$this->locales[0]['id_lang']]);
        }

        //map inputAccessories
        if (!empty($form_data['related_products']) && !empty($form_data['related_products']['data'])) {
            $inputAccessories = '';
            foreach ($form_data['related_products']['data'] as $accessoryIds) {
                $accessoryIds = explode(',', $accessoryIds);
                $inputAccessories .= $accessoryIds[0].'-';
            }
            $form_data['inputAccessories'] = $inputAccessories;
        }

        //map warehouseProductLocations
        $form_data['warehouse_loaded'] = 1;
        $warehouses = $this->warehouseAdapter->getWarehouses();
        foreach ($warehouses as $warehouse) {
            foreach ($form_data['warehouse_combination_' . $warehouse['id_warehouse']] as $combination) {
                $key = $combination['warehouse_id']
                    . '_' . $combination['product_id']
                    . '_' . $combination['id_product_attribute'];
                if ($combination['activated']) {
                    $form_data['check_warehouse_' . $key] = '1';
                }
                $form_data['location_warehouse_' . $key] = $combination['location'];

                unset($form_data['warehouse_combination_' . $warehouse['id_warehouse']]);
            }
        }

        //force customization fields values
        $form_data['uploadable_files'] = 0;
        $form_data['text_fields'] = 0;

        //map all
        $new_form_data = [];
        foreach ($form_data as $k => $v) {
            if (in_array($k, $this->unmapKeys) || in_array($k, $this->translatableKeys)) {
                continue;
            }
            $new_form_data[$k] = $v;
        }

        //map specific price priority
        $new_form_data['specificPricePriority'] = [
            $new_form_data['specificPricePriority_0'],
            $new_form_data['specificPricePriority_1'],
            $new_form_data['specificPricePriority_2'],
            $new_form_data['specificPricePriority_3'],
        ];

        $new_form_data = array_merge(parent::getHookData(), $new_form_data);

        //if multiShop context is defined, simulate multiShop checkbox for all POST DATA
        if ($isMultiShopContext) {
            foreach ($this->multiShopKeys as $multishopKey) {
                $new_form_data['multishop_check'][$multishopKey] = 1;
            }

            //apply multishop rules for translatables fields
            foreach ($this->translatableKeys as $field) {
                foreach ($form_data[$field] as $lang_id => $translate_value) {
                    $new_form_data['multishop_check'][$field][$lang_id] = 1;
                }
            }
        }

        return $new_form_data;
    }

    /**
     * formMapper
     * Map object model to form data
     *
     * @param Product $product
     * @return array Transformed model data to form attempt
     */
    public function getFormData(Product $product)
    {
        $product->loadStockData();
        $this->productPricePriority = $this->adminProductWrapper->getPricePriority($product->id);

        $form_data = [
            'id_product' => $product->id,
            'step1' => [
                'type_product' => $product->getType(),
                'inputPackItems' => [
                    'data' => array_map(
                        function ($p) {
                            return [
                                "id" => $p->id,
                                "id_product_attribute" => isset($p->id_pack_product_attribute)
                                    ? $p->id_pack_product_attribute
                                    : 0,
                                "name" => $p->name,
                                "ref" => $p->reference,
                                "quantity" => $p->pack_quantity,
                                "image" => $p->image,
                            ];
                        },
                        $this->packAdapter->getItems($product->id, $this->locales[0]['id_lang'])
                    )
                ],
                'name' => $product->name,
                'description' => $product->description,
                'description_short' => $product->description_short,
                'active' => $product->active == 0 ? false : true,
                'price_shortcut' => $product->price,
                'qty_0_shortcut' => $product->getQuantity($product->id),
                'categories' => ['tree' => $product->getCategories()],
                'id_category_default' => $product->id_category_default,
                'related_products' => [
                    'data' => array_map(
                        function ($p) {
                            return($p['id_product']);
                        },
                        call_user_func_array(
                            array($product, "getAccessoriesLight"),
                            array($this->locales[0]['id_lang'], $product->id)
                        )
                    )
                ],
                'id_manufacturer' => $product->id_manufacturer,
                'features' => $this->getFormFeatures($product),
                'images' => $this->productAdapter->getImages($product->id, $this->locales[0]['id_lang'])
            ],
            'step2' => [
                'price' => $product->price,
                'ecotax' => $product->ecotax,
                'id_tax_rules_group' => isset($product->id_tax_rules_group)
                    ? (int)$product->id_tax_rules_group
                    : $this->taxRuleDataProvider->getIdTaxRulesGroupMostUsed(),
                'on_sale' => (bool) $product->on_sale,
                'wholesale_price' => $product->wholesale_price,
                'unit_price' => $product->unit_price_ratio != 0
                    ? $product->price / $product->unit_price_ratio
                    : 0,
                'unity' => $product->unity,
                'specific_price' => [ // extra form to be saved separately. Here this is the default form values.
                    'sp_from_quantity' => 1,
                    'sp_reduction' => 0,
                    'sp_reduction_tax' => 1,
                    'leave_bprice' => true,
                    'sp_id_shop' => $this->shopContext->getContextShopID(),
                ],
                'specificPricePriority_0' => $this->productPricePriority[0],
                'specificPricePriority_1' => $this->productPricePriority[1],
                'specificPricePriority_2' => $this->productPricePriority[2],
                'specificPricePriority_3' => $this->productPricePriority[3],
            ],
            'step3' => [
                'advanced_stock_management' => (bool) $product->advanced_stock_management,
                'depends_on_stock' => $product->depends_on_stock?"1":"0",
                'qty_0' => $product->getQuantity($product->id),
                'id_product_attributes' => $this->getProductAttributes($product),
                'out_of_stock' => $product->out_of_stock,
                'minimal_quantity' => $product->minimal_quantity,
                'low_stock_threshold' => $product->low_stock_threshold,
                'low_stock_alert' => (bool) $product->low_stock_alert,
                'available_now' => $product->available_now,
                'available_later' => $product->available_later,
                'available_date' => $product->available_date,
                'pack_stock_type' => $product->pack_stock_type,
                'virtual_product' => $this->getVirtualProductData($product),
            ],
            'step4' => [
                'width' => $product->width,
                'height' => $product->height,
                'depth' => $product->depth,
                'weight' => $product->weight,
                'additional_shipping_cost' => $product->additional_shipping_cost,
                'selectedCarriers' => $this->getFormProductCarriers($product),
                'additional_delivery_times' => $product->additional_delivery_times,
                'delivery_in_stock' => $product->delivery_in_stock,
                'delivery_out_stock' => $product->delivery_out_stock,
            ],
            'step5' => [
                'link_rewrite' => $product->link_rewrite,
                'meta_title' => $product->meta_title,
                'meta_description' => $product->meta_description,
                'redirect_type' => $product->redirect_type,
                'id_type_redirected' => [
                    'data' => [$product->id_type_redirected]
                ],
            ],
            'step6' => [
                'visibility' => $product->visibility,
                'tags' => $this->getTags($product),
                'display_options' => [
                    'available_for_order' => (bool) $product->available_for_order,
                    'show_price' => (bool) $product->show_price,
                    'online_only' => (bool) $product->online_only,
                ],
                'upc' => $product->upc,
                'ean13' => $product->ean13,
                'isbn' => $product->isbn,
                'reference' => $product->reference,
                'show_condition' => (bool) $product->show_condition,
                'condition' => $product->condition,
                'suppliers' => array_map(
                    function ($s) {
                        return($s->id_supplier);
                    },
                    $this->supplierAdapter->getProductSuppliers($product->id)
                ),
                'default_supplier' => $product->id_supplier,
                'custom_fields' => $this->getCustomFields($product),
                'attachments' => $this->getProductAttachments($product),
            ]
        ];

        //Inject data form for supplier combinations
        $form_data['step6'] = array_merge($form_data['step6'], $this->getDataSuppliersCombinations($product));

        //Inject data form for warehouse combinations
        $form_data['step4'] = array_merge($form_data['step4'], $this->getDataWarehousesCombinations($product));

        return $form_data;
    }

    /**
     * Get product attachments
     *
     * @param Product $product
     * @return array
     */
    private function getProductAttachments(Product $product)
    {
        return array_map(
            function ($a) {
                return($a['id_attachment']);
            },
            Attachment::getAttachments($this->locales[0]['id_lang'], $product->id, true)
        );
    }

    /**
     * Get virtual product data
     *
     * @param Product $product
     * @return array
     */
    private function getVirtualProductData(Product $product)
    {
        //force virtual product feature
        ConfigurationLegacy::updateGlobalValue('PS_VIRTUAL_PROD_FEATURE_ACTIVE', '1');

        $id_product_download = ProductDownload::getIdFromIdProduct((int)$product->id, false);
        if ($id_product_download) {
            $download = new ProductDownload($id_product_download);
            $dateValue = $download->date_expiration == '0000-00-00 00:00:00'
                ? ''
                : date('Y-m-d', strtotime($download->date_expiration));

            $res = [
                'is_virtual_file' => $download->active,
                'name' => $download->display_filename,
                'nb_downloadable' => $download->nb_downloadable,
                'expiration_date' => $dateValue,
                'nb_days' => $download->nb_days_accessible,
            ];

            if ($download->filename) {
                $res['filename'] = $download->filename;
                $res['file_download_link'] = $this->context->getAdminBaseUrl().$download->getTextLink(true);
            }

            return $res;
        }

        return [
            'is_virtual_file' => 0,
            'nb_days' => 0,
        ];
    }

    /**
     * Generate form custom fields configuration
     *
     * @param Product $product
     * @return array
     */
    private function getCustomFields(Product $product)
    {
        $finalCustomFields = [];
        $customizationFields = [];
        $productCustomizationFields = $product->getCustomizationFields();

        if (!$productCustomizationFields) {
            return [];
        }

        foreach ($productCustomizationFields as $customizationField) {
            $customizationFields = array_merge($customizationFields, $customizationField);
        }

        foreach ($customizationFields as $customizationField) {
            $baseObject = [
                'id_customization_field' => $customizationField[$this->locales[0]['id_lang']]['id_customization_field'],
                'label' => [],
                'type' => $customizationField[$this->locales[0]['id_lang']]['type'],
                'require' => $customizationField[$this->locales[0]['id_lang']]['required'] == 1 ? true : false,
            ];

            //add translation name
            foreach ($this->locales as $locale) {
                $baseObject['label'][$locale['id_lang']] = $customizationField[$locale['id_lang']]['name'];
            }
            $finalCustomFields[] = $baseObject;
        }

        return $finalCustomFields;
    }

    /**
     * Generate form supplier/combinations references
     *
     * @param Product $product
     * @return array filled data form references combinations
     */
    private function getDataSuppliersCombinations(Product $product)
    {
        $combinations = $product->getAttributesResume($this->locales[0]['id_lang']);
        if (!$combinations || empty($combinations)) {
            $combinations[] = array(
                'id_product' => $product->id,
                'id_product_attribute' => 0,
                'attribute_designation' => $product->name[$this->locales[0]['id_lang']]
            );
        }

        //for each supplier, generate combinations list
        $dataSuppliersCombinations = [];

        foreach ($this->supplierAdapter->getProductSuppliers($product->id) as $supplier) {
            foreach ($combinations as $combination) {
<<<<<<< 5edc559a6d20f367a96363bbf2a5edf6de06f34f
                $productSupplierData = $this->supplierAdapter->getProductSupplierData(
                    $this->product->id,
                    $combination['id_product_attribute'],
                    $supplier->id_supplier
                );
                $dataSuppliersCombinations['supplier_combination_' . $supplier->id_supplier][] = [
                    'label'                  => $combination['attribute_designation'],
                    'supplier_reference'     => isset($productSupplierData['product_supplier_reference'])
                        ? $productSupplierData['product_supplier_reference']
                        : '',
                    'product_price'          => isset($productSupplierData['price'])
                        ? $productSupplierData['price']
                        : 0,
                    'product_price_currency' => isset($productSupplierData['id_currency'])
                        ? $productSupplierData['id_currency']
                        : 1,
                    'supplier_id'            => $supplier->id_supplier,
                    'product_id'             => $this->product->id,
                    'id_product_attribute'   => $combination['id_product_attribute'],
||||||| merged common ancestors
                $productSupplierData = $this->supplierAdapter->getProductSupplierData($this->product->id, $combination['id_product_attribute'], $supplier->id_supplier);
                $dataSuppliersCombinations['supplier_combination_'.$supplier->id_supplier][] = [
                    'label' => $combination['attribute_designation'],
                    'supplier_reference' => isset($productSupplierData['product_supplier_reference']) ? $productSupplierData['product_supplier_reference'] : '',
                    'product_price' => isset($productSupplierData['price']) ? $productSupplierData['price'] : 0,
                    'product_price_currency' => isset($productSupplierData['id_currency']) ? $productSupplierData['id_currency'] : 1,
                    'supplier_id' => $supplier->id_supplier,
                    'product_id' => $this->product->id,
                    'id_product_attribute' => $combination['id_product_attribute'],
=======
                $productSupplierData = $this->supplierAdapter->getProductSupplierData($product->id, $combination['id_product_attribute'], $supplier->id_supplier);
                $dataSuppliersCombinations['supplier_combination_'.$supplier->id_supplier][] = [
                    'label' => $combination['attribute_designation'],
                    'supplier_reference' => isset($productSupplierData['product_supplier_reference']) ? $productSupplierData['product_supplier_reference'] : '',
                    'product_price' => isset($productSupplierData['price']) ? $productSupplierData['price'] : 0,
                    'product_price_currency' => isset($productSupplierData['id_currency']) ? $productSupplierData['id_currency'] : 1,
                    'supplier_id' => $supplier->id_supplier,
                    'product_id' => $product->id,
                    'id_product_attribute' => $combination['id_product_attribute'],
>>>>>>> Changes to improve the product controller and enable more than 62 combinations
                ];
            }
        }

        return $dataSuppliersCombinations;
    }

    /**
     * Generate form warehouses/combinations references
     *
     * @param Product $product
     * @return array filled data form references combinations
     */
    private function getDataWarehousesCombinations(Product $product)
    {
        $combinations = $product->getAttributesResume($this->locales[0]['id_lang']);
        if (!$combinations || empty($combinations)) {
            $combinations[] = array(
                'id_product' => $product->id,
                'id_product_attribute' => 0,
                'attribute_designation' => $product->name[$this->locales[0]['id_lang']]
            );
        }

        //for each warehouse, generate combinations list
        $dataWarehousesCombinations = [];

        foreach ($this->warehouseAdapter->getWarehouses() as $warehouse) {
            $warehouseId = $warehouse['id_warehouse'];
            foreach ($combinations as $combination) {
<<<<<<< 5edc559a6d20f367a96363bbf2a5edf6de06f34f
                $warehouseProductLocationData = $this->warehouseAdapter->getWarehouseProductLocationData(
                    $this->product->id,
                    $combination['id_product_attribute'],
                    $warehouseId
                );
||||||| merged common ancestors
                $warehouseProductLocationData = $this->warehouseAdapter->getWarehouseProductLocationData($this->product->id, $combination['id_product_attribute'], $warehouseId);
=======
                $warehouseProductLocationData = $this->warehouseAdapter->getWarehouseProductLocationData($product->id, $combination['id_product_attribute'], $warehouseId);
>>>>>>> Changes to improve the product controller and enable more than 62 combinations
                $dataWarehousesCombinations['warehouse_combination_'.$warehouseId][] = [
                    'label' => $combination['attribute_designation'],
                    'activated' => (bool) $warehouseProductLocationData['activated'],
                    'warehouse_id' => $warehouseId,
                    'product_id' => $product->id,
                    'id_product_attribute' => $combination['id_product_attribute'],
                    'location' => isset($warehouseProductLocationData['location'])
                        ? $warehouseProductLocationData['location']
                        : '',
                ];
            }
        }

        return $dataWarehousesCombinations;
    }

    /**
     * get form product features
     *
     * @param Product $product
     * @return array features with translation
     */
    private function getFormFeatures(Product $product)
    {
<<<<<<< 5edc559a6d20f367a96363bbf2a5edf6de06f34f
        $formFeaturesData = [];
        foreach ($this->product->getFeatures() as $featureData) {
||||||| merged common ancestors
        $formDataFeatures = [];
        foreach ($this->product->getFeatures() as $dataFeature) {
=======
        $formDataFeatures = [];
        foreach ($product->getFeatures() as $dataFeature) {
>>>>>>> Changes to improve the product controller and enable more than 62 combinations
            $itemForm = [
                'feature'      => $featureData['id_feature'],
                'value'        => $featureData['id_feature_value'],
                'custom_value' => null,
            ];

            if ($featureData['custom'] == 1) {
                $customLangs      = [];
                $featureLangsData = $this->featureAdapter->getFeatureValueLang($featureData['id_feature_value']);
                foreach ($featureLangsData as $langData) {
                    $customLangs[$langData['id_lang']] = $langData['value'];
                }
                $itemForm['custom_value'] = $customLangs;
            }

            $formFeaturesData[] = $itemForm;
        }

        return $formFeaturesData;
    }

    /**
     * get product carrier
     *
     * @param Product $product
     * @return array carrier
     */
    private function getFormProductCarriers(Product $product)
    {
        $formDataCarriers = [];
        foreach ($product->getCarriers() as $carrier) {
            $formDataCarriers[] = $carrier['id_reference'];
        }

        return $formDataCarriers;
    }

    /**
     * Get all product id_product_attribute
     *
     * @param Product $product
     * @return array id_product_attribute
     */
    private function getProductAttributes(Product $product)
    {
        $combinations = $product->getAttributesResume($this->context->getContext()->language->id);
        $idsProductAttribute = array();

        if (is_array($combinations)) {
            foreach ($combinations as $combination) {
                $idsProductAttribute[] = $combination['id_product_attribute'];
            }
        }

        return $idsProductAttribute;
    }

    /**
     * Get a localized tags for product
     *
     * @param Product $product
     * @return array
     */
    private function getTags(Product $product)
    {
        $tags = [];
        foreach ($this->locales as $locale) {
            $tags[$locale['id_lang']] = $product->getTags($locale['id_lang']);
        }
        return $tags;
    }
}
