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

namespace PrestaShopBundle\Model\Product;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Product\AdminProductWrapper;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Adapter\Product\ProductDataProvider;
use PrestaShop\PrestaShop\Adapter\Supplier\SupplierDataProvider;
use PrestaShop\PrestaShop\Adapter\Warehouse\WarehouseDataProvider;
use PrestaShop\PrestaShop\Adapter\Feature\FeatureDataProvider;
use PrestaShop\PrestaShop\Adapter\Pack\PackDataProvider;
use PrestaShop\PrestaShop\Adapter\Shop\Context as ShopContext;

/**
 * This form class is responsible to map the form data to the product object
 */
class AdminModelAdapter extends \PrestaShopBundle\Model\AdminModelAdapter
{
    private $context;
    private $adminProductWrapper;
    private $cldrRepository;
    private $locales;
    private $defaultLocale;
    private $tools;
    private $productAdapter;
    private $supplierAdapter;
    private $featureAdapter;
    private $packAdapter;
    private $product;
    private $translatableKeys;
    private $unmapKeys;
    private $configuration;
    private $shopContext;

    /**
     * Constructor
     * Set all adapters needed and get product
     *
     * @param \ProductCore $product The product object
     * @param LegacyContext $legacyContext
     * @param AdminProductWrapper $adminProductWrapper
     * @param Tools $toolsAdapter
     * @param ProductDataProvider $productDataProvider
     * @param SupplierDataProvider $supplierDataProvider
     * @param WarehouseDataProvider $warehouseDataProvider
     * @param FeatureDataProvider $featureDataProvider
     * @param PackDataProvider $packDataProvider
     * @param ShopContext $shopContext
     */
    public function __construct(
        \ProductCore $product,
        LegacyContext $legacyContext,
        AdminProductWrapper $adminProductWrapper,
        Tools $toolsAdapter,
        ProductDataProvider $productDataProvider,
        SupplierDataProvider $supplierDataProvider,
        WarehouseDataProvider $warehouseDataProvider,
        FeatureDataProvider $featureDataProvider,
        PackDataProvider $packDataProvider,
        ShopContext $shopContext
    ) {
        $this->context = $legacyContext;
        $this->contextShop = $this->context->getContext();
        $this->adminProductWrapper = $adminProductWrapper;
        $this->cldrRepository = \Tools::getCldr($this->contextShop);
        $this->locales = $this->context->getLanguages();
        $this->defaultLocale = $this->locales[0]['id_lang'];
        $this->tools = $toolsAdapter;
        $this->productAdapter = $productDataProvider;
        $this->supplierAdapter = $supplierDataProvider;
        $this->warehouseAdapter = $warehouseDataProvider;
        $this->featureAdapter = $featureDataProvider;
        $this->packAdapter = $packDataProvider;
        $this->product = $product;
        $this->productPricePriority = $this->adminProductWrapper->getPricePriority($product->id);
        $this->configuration = new Configuration();
        $this->product->loadStockData();
        $this->shopContext = $shopContext;

        //define translatable key
        $this->translatableKeys = array(
            'name',
            'description',
            'description_short',
            'link_rewrite',
            'meta_title',
            'meta_description',
            'available_now',
            'available_later',
            'tags',
        );

        //define unused key for manual binding
        $this->unmapKeys = array('name',
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

        //define multishop keys
        $this->multishopKeys = array('category_box',
            'id_category_default',
            'attribute_wholesale_price',
            'attribute_price_impact',
            'attribute_weight_impact',
            'attribute_unit_impact',
            'attribute_ecotax',
            'attribute_minimal_quantity',
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
            'available_date',
            'ecotax',
        );
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
        $form_data = array_merge(['id_product' => $form_data['id_product']], $form_data['step1'], $form_data['step2'], $form_data['step3'], $form_data['step4'], $form_data['step5'], $form_data['step6']);

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
        if ($form_data['type_product'] == 1 && !empty($form_data['inputPackItems']) && !empty($form_data['inputPackItems']['data'])) {
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

            if ($combination['attribute_price'] > 0) {
                $form_data['combinations'][$k]['attribute_price_impact'] = 1;
            } elseif ($combination['attribute_price'] < 0) {
                $form_data['combinations'][$k]['attribute_price_impact'] = -1;
            }

            if ($combination['attribute_weight'] > 0) {
                $form_data['combinations'][$k]['attribute_weight_impact'] = 1;
            } elseif ($combination['attribute_weight'] < 0) {
                $form_data['combinations'][$k]['attribute_weight_impact'] = -1;
            }

            if ($combination['attribute_unity'] > 0) {
                $form_data['combinations'][$k]['attribute_unit_impact'] = 1;
            } elseif ($combination['attribute_unity'] < 0) {
                $form_data['combinations'][$k]['attribute_unit_impact'] = -1;
            }

            $form_data['combinations'][$k]['attribute_price'] = abs($combination['attribute_price']);
            $form_data['combinations'][$k]['attribute_weight'] = abs($combination['attribute_weight']);
            $form_data['combinations'][$k]['attribute_unity'] = abs($combination['attribute_unity']);
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
        if (empty($form_data['link_rewrite_'.$this->locales[0]['id_lang']])) {
            $form_data['link_rewrite_'.$this->locales[0]['id_lang']] = $this->tools->link_rewrite($form_data['name_'.$this->locales[0]['id_lang']]);
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

        //map features
        if (!empty($form_data['features'])) {
            foreach ($form_data['features'] as $dataFeature) {
                $idFeature = $dataFeature['feature'];

                //custom value is defined
                if ($dataFeature['custom_value'][$this->defaultLocale]) {
                    foreach ($this->locales as $locale) {
                        $form_data['feature_'.$idFeature.'_value'] = null;
                        $form_data['custom_'.$idFeature.'_'.$locale['id_lang']] = $dataFeature['custom_value'][$locale['id_lang']];
                    }
                } elseif ($dataFeature['value']) {
                    $form_data['feature_'.$idFeature.'_value'] = $dataFeature['value'];
                }
            }
        }

        //map warehouseProductLocations
        $form_data['warehouse_loaded'] = 1;
        $warehouses = $this->warehouseAdapter->getWarehouses();
        foreach ($warehouses as $warehouse) {
            foreach ($form_data['warehouse_combination_' . $warehouse['id_warehouse']] as $combination) {
                $key = $combination['warehouse_id'] . '_' . $combination['product_id'] . '_' . $combination['id_product_attribute'];
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

        //if multishop context is defined, simulate multishop checkbox for all POST DATA
        if ($isMultiShopContext) {
            foreach ($this->multishopKeys as $multishopKey) {
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
     * @return array Transformed model data to form attempt
     */
    public function getFormData()
    {
        $form_data = [
            'id_product' => $this->product->id,
            'step1' => [
                'type_product' => $this->product->getType(),
                'inputPackItems' => [
                    'data' => array_map(
                        function ($p) {
                            return [
                                "id" => $p->id,
                                "id_product_attribute" => isset($p->id_pack_product_attribute) ? $p->id_pack_product_attribute : 0,
                                "name" => $p->name,
                                "ref" => $p->reference,
                                "quantity" => $p->pack_quantity,
                                "image" => $p->image,
                            ];
                        },
                        $this->packAdapter->getItems($this->product->id, $this->locales[0]['id_lang'])
                    )
                ],
                'name' => $this->product->name,
                'description' => $this->product->description,
                'description_short' => $this->product->description_short,
                'active' => $this->product->active == 0 ? false : true,
                'price_shortcut' => $this->product->price,
                'qty_0_shortcut' => $this->product->getQuantity($this->product->id),
                'categories' => ['tree' => $this->product->getCategories()],
                'id_category_default' => $this->product->id_category_default,
                'related_products' => [
                    'data' => array_map(
                        function ($p) {
                            return($p['id_product']);
                        },
                        call_user_func_array(
                            array($this->product, "getAccessoriesLight"),
                            array($this->locales[0]['id_lang'], $this->product->id)
                        )
                    )
                ],
                'id_manufacturer' => $this->product->id_manufacturer,
                'features' => $this->getFormFeatures(),
                'images' => $this->productAdapter->getImages($this->product->id, $this->locales[0]['id_lang'])
            ],
            'step2' => [
                'price' => $this->product->price,
                'ecotax' => $this->product->ecotax,
                'id_tax_rules_group' => $this->product->id_tax_rules_group,
                'on_sale' => (bool) $this->product->on_sale,
                'wholesale_price' => $this->product->wholesale_price,
                'unit_price' => $this->product->unit_price_ratio != 0  ? $this->product->price / $this->product->unit_price_ratio : 0,
                'unity' => $this->product->unity,
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
                'advanced_stock_management' => (bool) $this->product->advanced_stock_management,
                'depends_on_stock' => $this->product->depends_on_stock?"1":"0",
                'qty_0' => $this->product->getQuantity($this->product->id),
                'id_product_attributes' => $this->getProductAttributes(),
                'out_of_stock' => $this->product->out_of_stock,
                'minimal_quantity' => $this->product->minimal_quantity,
                'available_now' => $this->product->available_now,
                'available_later' => $this->product->available_later,
                'available_date' => $this->product->available_date,
                'pack_stock_type' => $this->product->pack_stock_type,
                'virtual_product' => $this->getVirtualProductData(),
            ],
            'step4' => [
                'width' => $this->product->width,
                'height' => $this->product->height,
                'depth' => $this->product->depth,
                'weight' => $this->product->weight,
                'additional_shipping_cost' => $this->product->additional_shipping_cost,
                'selectedCarriers' => $this->getFormProductCarriers(),
            ],
            'step5' => [
                'link_rewrite' => $this->product->link_rewrite,
                'meta_title' => $this->product->meta_title,
                'meta_description' => $this->product->meta_description,
                'redirect_type' => $this->product->redirect_type,
                'id_type_redirected' => [
                    'data' => [$this->product->id_type_redirected]
                ],
            ],
            'step6' => [
                'visibility' => $this->product->visibility,
                'tags' => $this->getTags(),
                'display_options' => [
                    'available_for_order' => (bool) $this->product->available_for_order,
                    'show_price' => (bool) $this->product->show_price,
                    'online_only' => (bool) $this->product->online_only,
                ],
                'upc' => $this->product->upc,
                'ean13' => $this->product->ean13,
                'isbn' => $this->product->isbn,
                'reference' => $this->product->reference,
                'show_condition' => (bool) $this->product->show_condition,
                'condition' => $this->product->condition,
                'suppliers' => array_map(
                    function ($s) {
                        return($s->id_supplier);
                    },
                    $this->supplierAdapter->getProductSuppliers($this->product->id)
                ),
                'default_supplier' => $this->product->id_supplier,
                'custom_fields' => $this->getCustomFields(),
                'attachments' => $this->getProductAttachments(),
            ]
        ];

        //Inject data form for supplier combinations
        $form_data['step6'] = array_merge($form_data['step6'], $this->getDataSuppliersCombinations());

        //Inject data form for warehouse combinations
        $form_data['step4'] = array_merge($form_data['step4'], $this->getDataWarehousesCombinations());

        return $form_data;
    }

    public function getAttributesResume()
    {
        return $this->product->getAttributesResume($this->context->getContext()->language->id);
    }

    /**
     * Get product attachments
     *
     * @return array
     */
    private function getProductAttachments()
    {
        return array_map(
            function ($a) {
                return($a['id_attachment']);
            },
            \AttachmentCore::getAttachments($this->locales[0]['id_lang'], $this->product->id, true)
        );
    }

    /**
     * Get virtual product data
     *
     * @return array
     */
    private function getVirtualProductData()
    {
        //force virtual product feature
        \ConfigurationCore::updateGlobalValue('PS_VIRTUAL_PROD_FEATURE_ACTIVE', '1');

        $id_product_download = \ProductDownloadCore::getIdFromIdProduct((int)$this->product->id, false);
        if ($id_product_download) {
            $download = new \ProductDownloadCore($id_product_download);
            $dateValue = $download->date_expiration == '0000-00-00 00:00:00' ? '' : date('Y-m-d', strtotime($download->date_expiration));

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
     * @return array
     */
    private function getCustomFields()
    {
        $finalCustomFields = [];
        $customizationFields = [];
        $productCustomizationFields = $this->product->getCustomizationFields();

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
     * @return array filled data form references combinations
     */
    private function getDataSuppliersCombinations()
    {
        $combinations = $this->product->getAttributesResume($this->locales[0]['id_lang']);
        if (!$combinations || empty($combinations)) {
            $combinations[] = array(
                'id_product' => $this->product->id,
                'id_product_attribute' => 0,
                'attribute_designation' => $this->product->name[$this->locales[0]['id_lang']]
            );
        }

        //for each supplier, generate combinations list
        $dataSuppliersCombinations = [];

        foreach ($this->supplierAdapter->getProductSuppliers($this->product->id) as $supplier) {
            foreach ($combinations as $combination) {
                $productSupplierData = $this->supplierAdapter->getProductSupplierData($this->product->id, $combination['id_product_attribute'], $supplier->id_supplier);
                $dataSuppliersCombinations['supplier_combination_'.$supplier->id_supplier][] = [
                    'label' => $combination['attribute_designation'],
                    'supplier_reference' => isset($productSupplierData['product_supplier_reference']) ? $productSupplierData['product_supplier_reference'] : '',
                    'product_price' => isset($productSupplierData['price']) ? $productSupplierData['price'] : 0,
                    'product_price_currency' => isset($productSupplierData['id_currency']) ? $productSupplierData['id_currency'] : 1,
                    'supplier_id' => $supplier->id_supplier,
                    'product_id' => $this->product->id,
                    'id_product_attribute' => $combination['id_product_attribute'],
                ];
            }
        }

        return $dataSuppliersCombinations;
    }

    /**
     * Generate form warehouses/combinations references
     *
     * @return array filled data form references combinations
     */
    private function getDataWarehousesCombinations()
    {
        $combinations = $this->product->getAttributesResume($this->locales[0]['id_lang']);
        if (!$combinations || empty($combinations)) {
            $combinations[] = array(
                'id_product' => $this->product->id,
                'id_product_attribute' => 0,
                'attribute_designation' => $this->product->name[$this->locales[0]['id_lang']]
            );
        }

        //for each warehouse, generate combinations list
        $dataWarehousesCombinations = [];

        foreach ($this->warehouseAdapter->getWarehouses() as $warehouse) {
            $warehouseId = $warehouse['id_warehouse'];
            foreach ($combinations as $combination) {
                $warehouseProductLocationData = $this->warehouseAdapter->getWarehouseProductLocationData($this->product->id, $combination['id_product_attribute'], $warehouseId);
                $dataWarehousesCombinations['warehouse_combination_'.$warehouseId][] = [
                    'label' => $combination['attribute_designation'],
                    'activated' => (bool) $warehouseProductLocationData['activated'],
                    'warehouse_id' => $warehouseId,
                    'product_id' => $this->product->id,
                    'id_product_attribute' => $combination['id_product_attribute'],
                    'location' => isset($warehouseProductLocationData['location']) ? $warehouseProductLocationData['location'] : '',
                ];
            }
        }

        return $dataWarehousesCombinations;
    }

    /**
     * get form product features
     *
     * @return array features with translation
     */
    private function getFormFeatures()
    {
        $formDataFeatures = [];
        foreach ($this->product->getFeatures() as $dataFeature) {
            $itemForm = [
                'feature' => $dataFeature['id_feature'],
                'value' => $dataFeature['id_feature_value'],
                'custom_value' => null,
            ];

            if ($dataFeature['custom'] == 1) {
                $customLangs = [];
                foreach ($this->featureAdapter->getFeatureValueLang($dataFeature['id_feature_value']) as $customValues) {
                    $customLangs[$customValues['id_lang']] = $customValues['value'];
                }
                $itemForm['custom_value'] = $customLangs;
            }

            $formDataFeatures[] = $itemForm;
        }

        return $formDataFeatures;
    }

    /**
     * get product carrier
     *
     * @return array carrier
     */
    private function getFormProductCarriers()
    {
        $formDataCarriers = [];
        foreach ($this->product->getCarriers() as $carrier) {
            $formDataCarriers[] = $carrier['id_reference'];
        }

        return $formDataCarriers;
    }

    /**
     * Get all product id_product_attribute
     *
     * @return array id_product_attribute
     */
    private function getProductAttributes()
    {
        $combinations = $this->getAttributesResume();
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
     * @return array
     */
    private function getTags()
    {
        $tags = [];
        foreach ($this->locales as $locale) {
            $tags[$locale['id_lang']] = $this->product->getTags($locale['id_lang']);
        }
        return $tags;
    }
}
