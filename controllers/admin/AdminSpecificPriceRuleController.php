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

/**
 * @property SpecificPriceRule $object
 */
class AdminSpecificPriceRuleControllerCore extends AdminController
{
    public $list_reduction_type;

    /**
     * @var int
     */
    public $id_shop;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'specific_price_rule';
        $this->className = 'SpecificPriceRule';
        $this->lang = false;
        $this->multishop_context = Shop::CONTEXT_ALL;

        parent::__construct();

        /* if $_GET['id_shop'] is transmitted, virtual url can be loaded in config.php, so we wether transmit shop_id in hrefs */
        if ($this->id_shop = (int) Tools::getValue('shop_id')) {
            $_GET['id_shop'] = $this->id_shop;
            $_POST['id_shop'] = $this->id_shop;
        }

        $this->list_reduction_type = [
            'percentage' => $this->trans('Percentage', [], 'Admin.Global'),
            'amount' => $this->trans('Amount', [], 'Admin.Global'),
        ];

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->_select = 's.name shop_name, cul.name as currency_name, cl.name country_name, gl.name group_name';
        $this->_join = 'LEFT JOIN ' . _DB_PREFIX_ . 'shop s ON (s.id_shop = a.id_shop)
		LEFT JOIN ' . _DB_PREFIX_ . 'currency_lang cul ON (cul.id_currency = a.id_currency AND cul.id_lang=' . (int) $this->context->language->id . ')
		LEFT JOIN ' . _DB_PREFIX_ . 'country_lang cl ON (cl.id_country = a.id_country AND cl.id_lang=' . (int) $this->context->language->id . ')
		LEFT JOIN ' . _DB_PREFIX_ . 'group_lang gl ON (gl.id_group = a.id_group AND gl.id_lang=' . (int) $this->context->language->id . ')';
        $this->_use_found_rows = false;

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash',
            ],
        ];

        $this->fields_list = [
            'id_specific_price_rule' => [
                'title' => $this->trans('ID', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'name' => [
                'title' => $this->trans('Name', [], 'Admin.Global'),
                'filter_key' => 'a!name',
                'width' => 'auto',
            ],
            'id_currency' => [
                'title' => $this->trans('Currency', [], 'Admin.Global'),
                'align' => 'center',
                'filter_key' => 'cul!name',
            ],
            'country_name' => [
                'title' => $this->trans('Country', [], 'Admin.Global'),
                'align' => 'center',
                'filter_key' => 'cl!name',
            ],
            'group_name' => [
                'title' => $this->trans('Group', [], 'Admin.Global'),
                'align' => 'center',
                'filter_key' => 'gl!name',
            ],
            'from_quantity' => [
                'title' => $this->trans('From quantity', [], 'Admin.Catalog.Feature'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'reduction_type' => [
                'title' => $this->trans('Reduction type', [], 'Admin.Catalog.Feature'),
                'align' => 'center',
                'type' => 'select',
                'filter_key' => 'a!reduction_type',
                'list' => $this->list_reduction_type,
            ],
            'reduction' => [
                'title' => $this->trans('Reduction', [], 'Admin.Catalog.Feature'),
                'align' => 'center',
                'type' => 'decimal',
                'class' => 'fixed-width-xs',
            ],
            'from' => [
                'title' => $this->trans('Beginning', [], 'Admin.Catalog.Feature'),
                'align' => 'right',
                'type' => 'datetime',
                'filter_key' => 'a!from',
                'order_key' => 'a!from',
            ],
            'to' => [
                'title' => $this->trans('End', [], 'Admin.Catalog.Feature'),
                'align' => 'right',
                'type' => 'datetime',
                'filter_key' => 'a!to',
                'order_key' => 'a!to',
            ],
        ];

        if (Shop::isFeatureActive()) {
            $this->fields_list = Tools::arrayInsertElementAfterKey(
                $this->fields_list,
                'name',
                'shop_name',
                [
                    'title' => $this->trans('Store', [], 'Admin.Global'),
                    'filter_key' => 's!name',
                ]
            );
        }
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_specific_price_rule'] = [
                'href' => self::$currentIndex . '&addspecific_price_rule&token=' . $this->token,
                'desc' => $this->trans('Add new catalog price rule', [], 'Admin.Catalog.Feature'),
                'icon' => 'process-icon-new',
            ];
        }

        parent::initPageHeaderToolbar();
    }

    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

        foreach ($this->_list as $k => $list) {
            if (null !== $this->_list[$k]['id_currency']) {
                $currency = new Currency(
                    (int) $this->_list[$k]['id_currency'],
                    (int) $this->context->language->id,
                    (int) $this->context->shop->id
                );
                $this->_list[$k]['id_currency'] = Validate::isLoadedObject($currency) ? $currency->getName() : null;
            }

            if ($list['reduction_type'] == 'amount') {
                $this->_list[$k]['reduction_type'] = $this->list_reduction_type['amount'];
            } elseif ($list['reduction_type'] == 'percentage') {
                $this->_list[$k]['reduction_type'] = $this->list_reduction_type['percentage'];
            }
        }
    }

    public function renderForm()
    {
        if (!$this->object->id) {
            $this->object->price = -1;
        }

        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('Catalog price rules', [], 'Admin.Catalog.Feature'),
                'icon' => 'icon-dollar',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('Name', [], 'Admin.Global'),
                    'name' => 'name',
                    'maxlength' => 255,
                    'required' => true,
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Store', [], 'Admin.Global'),
                    'name' => 'shop_id',
                    'options' => [
                        'query' => Shop::getShops(),
                        'id' => 'id_shop',
                        'name' => 'name',
                    ],
                    'condition' => Shop::isFeatureActive(),
                    'default_value' => Shop::getContextShopID(),
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Currency', [], 'Admin.Global'),
                    'name' => 'id_currency',
                    'options' => [
                        'query' => array_merge([0 => ['id_currency' => 0, 'name' => $this->trans('All currencies', [], 'Admin.Global')]], Currency::getCurrencies(false, true, true)),
                        'id' => 'id_currency',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Country', [], 'Admin.Global'),
                    'name' => 'id_country',
                    'options' => [
                        'query' => array_merge([0 => ['id_country' => 0, 'name' => $this->trans('All countries', [], 'Admin.Global')]], Country::getCountries((int) $this->context->language->id)),
                        'id' => 'id_country',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Group', [], 'Admin.Global'),
                    'name' => 'id_group',
                    'options' => [
                        'query' => array_merge([0 => ['id_group' => 0, 'name' => $this->trans('All groups', [], 'Admin.Global')]], Group::getGroups((int) $this->context->language->id)),
                        'id' => 'id_group',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('From quantity', [], 'Admin.Catalog.Feature'),
                    'name' => 'from_quantity',
                    'maxlength' => 10,
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Price (tax excl.)', [], 'Admin.Catalog.Feature'),
                    'name' => 'price',
                    'disabled' => ($this->object->price == -1 ? 1 : 0),
                    'maxlength' => 10,
                    'suffix' => $this->context->currency->getSign('right'),
                ],
                [
                    'type' => 'checkbox',
                    'name' => 'leave_bprice',
                    'values' => [
                        'query' => [
                            [
                                'id' => 'on',
                                'name' => $this->trans('Leave initial price', [], 'Admin.Catalog.Feature'),
                                'val' => '1',
                                'checked' => '1',
                            ],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'datetime',
                    'label' => $this->trans('From', [], 'Admin.Global'),
                    'name' => 'from',
                ],
                [
                    'type' => 'datetime',
                    'label' => $this->trans('To', [], 'Admin.Global'),
                    'name' => 'to',
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Reduction type', [], 'Admin.Catalog.Feature'),
                    'name' => 'reduction_type',
                    'options' => [
                        'query' => [['reduction_type' => 'amount', 'name' => $this->trans('Amount', [], 'Admin.Global')], ['reduction_type' => 'percentage', 'name' => $this->trans('Percentage', [], 'Admin.Global')]],
                        'id' => 'reduction_type',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Reduction with or without taxes', [], 'Admin.Catalog.Feature'),
                    'name' => 'reduction_tax',
                    'align' => 'center',
                    'options' => [
                        'query' => [
                            ['lab' => $this->trans('Tax included', [], 'Admin.Global'), 'val' => 1],
                            ['lab' => $this->trans('Tax excluded', [], 'Admin.Global'), 'val' => 0],
                        ],
                        'id' => 'val',
                        'name' => 'lab',
                    ],
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Reduction', [], 'Admin.Catalog.Feature'),
                    'name' => 'reduction',
                    'required' => true,
                ],
            ],
            'submit' => [
                'title' => $this->trans('Save', [], 'Admin.Actions'),
            ],
        ];

        $value = $this->getFieldValue($this->object, 'price');
        if ($value !== '' && $value != -1) {
            $price = number_format($value, 6);
        } else {
            $price = '';
        }

        $this->fields_value = [
            'price' => $price,
            'from_quantity' => (($value = $this->getFieldValue($this->object, 'from_quantity')) ? $value : 1),
            'reduction' => number_format((($value = $this->getFieldValue($this->object, 'reduction')) ? $value : 0), 6),
            'leave_bprice_on' => $price ? 0 : 1,
            'shop_id' => (($value = $this->getFieldValue($this->object, 'id_shop')) ? $value : 1),
        ];

        $attribute_groups = [];
        $attributes = ProductAttribute::getAttributes((int) $this->context->language->id);
        foreach ($attributes as $attribute) {
            if (!isset($attribute_groups[$attribute['id_attribute_group']])) {
                $attribute_groups[$attribute['id_attribute_group']] = [
                    'id_attribute_group' => $attribute['id_attribute_group'],
                    'name' => $attribute['attribute_group'],
                ];
            }
            $attribute_groups[$attribute['id_attribute_group']]['attributes'][] = [
                'id_attribute' => $attribute['id_attribute'],
                'name' => $attribute['name'],
            ];
        }
        $features = Feature::getFeatures((int) $this->context->language->id);
        foreach ($features as &$feature) {
            $feature['values'] = FeatureValue::getFeatureValuesWithLang((int) $this->context->language->id, $feature['id_feature'], true);
        }

        $this->tpl_form_vars = [
            'manufacturers' => Manufacturer::getManufacturers(false, (int) $this->context->language->id, true, false, false, false, true),
            'suppliers' => Supplier::getSuppliers(),
            'attributes_group' => $attribute_groups,
            'features' => $features,
            'categories' => Category::getSimpleCategories((int) $this->context->language->id),
            'conditions' => $this->object->getConditions(),
            'is_multishop' => Shop::isFeatureActive(),
        ];

        return parent::renderForm();
    }

    public function processSave()
    {
        $_POST['price'] = Tools::getValue('leave_bprice_on') ? '-1' : Tools::getValue('price');

        /** @var SpecificPriceRule $object */
        $object = parent::processSave();
        if (Validate::isLoadedObject($object)) {
            $object->deleteConditions();
            foreach ($_POST as $key => $values) {
                if (preg_match('/^condition_group_([0-9]+)$/Ui', $key, $condition_group)) {
                    $conditions = [];
                    foreach ($values as $value) {
                        $condition = explode('_', $value);
                        $conditions[] = ['type' => $condition[0], 'value' => $condition[1]];
                    }
                    $object->addConditions($conditions);
                }
            }
            $object->apply();

            return $object;
        }
    }

    public function postProcess()
    {
        Tools::clearSmartyCache();

        return parent::postProcess();
    }
}
