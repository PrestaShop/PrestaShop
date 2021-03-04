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
 * @property Carrier $object
 */
class AdminCarrierWizardControllerCore extends AdminController
{
    protected $wizard_access;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';
        $this->table = 'carrier';
        $this->identifier = 'id_carrier';
        $this->className = 'Carrier';
        $this->lang = false;
        $this->deleted = true;
        $this->step_number = 0;
        $this->type_context = Shop::getContext();
        $this->old_context = Context::getContext();
        $this->multishop_context = Shop::CONTEXT_ALL;
        $this->context = Context::getContext();

        $this->fieldImageSettings = [
            'name' => 'logo',
            'dir' => 's',
        ];

        parent::__construct();

        $this->tabAccess = Profile::getProfileAccess($this->context->employee->id_profile, Tab::getIdFromClassName('AdminCarriers'));
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJqueryPlugin('smartWizard');
        $this->addJqueryPlugin('typewatch');
        $this->addJs(_PS_JS_DIR_ . 'admin/carrier_wizard.js');
    }

    public function initWizard()
    {
        $this->wizard_steps = [
            'name' => 'carrier_wizard',
            'steps' => [
                [
                    'title' => $this->trans('General settings', [], 'Admin.Shipping.Feature'),
                ],
                [
                    'title' => $this->trans('Shipping locations and costs', [], 'Admin.Shipping.Feature'),
                ],
                [
                    'title' => $this->trans('Size, weight, and group access', [], 'Admin.Shipping.Feature'),
                ],
                [
                    'title' => $this->trans('Summary', [], 'Admin.Global'),
                ], ],
        ];

        if (Shop::isFeatureActive()) {
            $multistore_step = [
                [
                    'title' => $this->trans('MultiStore', [], 'Admin.Global'),
                ],
            ];
            array_splice($this->wizard_steps['steps'], 1, 0, $multistore_step);
        }
    }

    public function renderView()
    {
        $this->initWizard();

        if (Tools::getValue('id_carrier') && $this->access('edit')) {
            $carrier = $this->loadObject();
        } elseif ($this->access('add')) {
            $carrier = new Carrier();
        }

        if ((!$this->access('edit') && Tools::getValue('id_carrier')) || (!$this->access('add') && !Tools::getValue('id_carrier'))) {
            $this->errors[] = $this->trans('You do not have permission to use this wizard.', [], 'Admin.Shipping.Notification');

            return;
        }

        $currency = $this->getActualCurrency();

        $this->tpl_view_vars = [
            'currency_sign' => $currency->sign,
            'PS_WEIGHT_UNIT' => Configuration::get('PS_WEIGHT_UNIT'),
            'enableAllSteps' => Validate::isLoadedObject($carrier),
            'wizard_steps' => $this->wizard_steps,
            'validate_url' => $this->context->link->getAdminLink('AdminCarrierWizard'),
            'carrierlist_url' => $this->context->link->getAdminLink('AdminCarriers') . '&conf=' . ((int) Validate::isLoadedObject($carrier) ? 4 : 3),
            'multistore_enable' => Shop::isFeatureActive(),
            'wizard_contents' => [
                'contents' => [
                    0 => $this->renderStepOne($carrier),
                    1 => $this->renderStepThree($carrier),
                    2 => $this->renderStepFour($carrier),
                    3 => $this->renderStepFive($carrier),
                ],
            ],
            'labels' => [
                'next' => $this->trans('Next', [], 'Admin.Global'),
                'previous' => $this->trans('Previous', [], 'Admin.Global'),
                'finish' => $this->trans('Finish', [], 'Admin.Actions'), ],
        ];

        if (Shop::isFeatureActive()) {
            array_splice($this->tpl_view_vars['wizard_contents']['contents'], 1, 0, [0 => $this->renderStepTwo($carrier)]);
        }

        $this->context->smarty->assign([
            'carrier_logo' => (Validate::isLoadedObject($carrier) && file_exists(_PS_SHIP_IMG_DIR_ . $carrier->id . '.jpg') ? _THEME_SHIP_DIR_ . $carrier->id . '.jpg' : false),
        ]);

        $this->context->smarty->assign([
            'logo_content' => $this->createTemplate('logo.tpl')->fetch(),
        ]);

        $this->addjQueryPlugin(['ajaxfileupload']);

        return parent::renderView();
    }

    public function initBreadcrumbs($tab_id = null, $tabs = null)
    {
        if (Tools::getValue('id_carrier')) {
            $this->display = 'edit';
        } else {
            $this->display = 'add';
        }

        parent::initBreadcrumbs((int) Tab::getIdFromClassName('AdminCarriers'));

        $this->display = 'view';
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        $this->page_header_toolbar_btn['cancel'] = [
            'href' => $this->context->link->getAdminLink('AdminCarriers'),
            'desc' => $this->trans('Cancel', [], 'Admin.Actions'),
        ];
    }

    public function renderStepOne($carrier)
    {
        $this->fields_form = [
            'form' => [
                'id_form' => 'step_carrier_general',
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->trans('Carrier name', [], 'Admin.Shipping.Feature'),
                        'name' => 'name',
                        'required' => true,
                        'hint' => [
                            $this->trans('Allowed characters: letters, spaces and "%special_chars%".', ['%special_chars%' => '().-'], 'Admin.Shipping.Help'),
                            $this->trans('The carrier\'s name will be displayed during checkout.', [], 'Admin.Shipping.Help'),
                            $this->trans('For in-store pickup, enter 0 to replace the carrier name with your shop name.', [], 'Admin.Shipping.Help'),
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Transit time', [], 'Admin.Shipping.Feature'),
                        'name' => 'delay',
                        'lang' => true,
                        'required' => true,
                        'maxlength' => 512,
                        'hint' => $this->trans('The delivery time will be displayed during checkout.', [], 'Admin.Shipping.Help'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Speed grade', [], 'Admin.Shipping.Feature'),
                        'name' => 'grade',
                        'required' => false,
                        'size' => 1,
                        'hint' => $this->trans('Enter "0" for a longest shipping delay, or "9" for the shortest shipping delay.', [], 'Admin.Shipping.Help'),
                    ],
                    [
                        'type' => 'logo',
                        'label' => $this->trans('Logo', [], 'Admin.Global'),
                        'name' => 'logo',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Tracking URL', [], 'Admin.Shipping.Feature'),
                        'name' => 'url',
                        'hint' => $this->trans('Delivery tracking URL: Type \'@\' where the tracking number should appear. It will be automatically replaced by the tracking number.', [], 'Admin.Shipping.Help'),
                        'desc' => $this->trans('For example: \'http://example.com/track.php?num=@\' with \'@\' where the tracking number should appear.', [], 'Admin.Shipping.Help'),
                    ],
                ],
            ],
        ];

        $tpl_vars = ['max_image_size' => (int) Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE') / 1024 / 1024];
        $fields_value = $this->getStepOneFieldsValues($carrier);

        return $this->renderGenericForm(['form' => $this->fields_form], $fields_value, $tpl_vars);
    }

    public function renderStepTwo($carrier)
    {
        $this->fields_form = [
            'form' => [
                'id_form' => 'step_carrier_shops',
                'force' => true,
                'input' => [
                    [
                        'type' => 'shop',
                        'label' => $this->trans('Shop association', [], 'Admin.Global'),
                        'name' => 'checkBoxShopAsso',
                    ],
                ],
            ],
        ];
        $fields_value = $this->getStepTwoFieldsValues($carrier);

        return $this->renderGenericForm(['form' => $this->fields_form], $fields_value);
    }

    public function renderStepThree($carrier)
    {
        $this->fields_form = [
            'form' => [
                'id_form' => 'step_carrier_ranges',
                'input' => [
                    'shipping_handling' => [
                        'type' => 'switch',
                        'label' => $this->trans('Add handling costs', [], 'Admin.Shipping.Feature'),
                        'name' => 'shipping_handling',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'shipping_handling_on',
                                'value' => 1,
                                'label' => $this->trans('Yes', [], 'Admin.Global'),
                            ],
                            [
                                'id' => 'shipping_handling_off',
                                'value' => 0,
                                'label' => $this->trans('No', [], 'Admin.Global'),
                            ],
                        ],
                        'hint' => $this->trans('Include the handling costs (as set in Shipping > Preferences) in the final carrier price.', [], 'Admin.Shipping.Help'),
                    ],
                    'is_free' => [
                        'type' => 'switch',
                        'label' => $this->trans('Free shipping', [], 'Admin.Shipping.Feature'),
                        'name' => 'is_free',
                        'required' => false,
                        'class' => 't',
                        'values' => [
                            [
                                'id' => 'is_free_on',
                                'value' => 1,
                                'label' => $this->trans('Yes', [], 'Admin.Global'),
                            ],
                            [
                                'id' => 'is_free_off',
                                'value' => 0,
                                'label' => $this->trans('No', [], 'Admin.Global'),
                            ],
                        ],
                    ],
                    'shipping_method' => [
                        'type' => 'radio',
                        'label' => $this->trans('Billing', [], 'Admin.Shipping.Feature'),
                        'name' => 'shipping_method',
                        'required' => false,
                        'class' => 't',
                        'br' => true,
                        'values' => [
                            [
                                'id' => 'billing_price',
                                'value' => Carrier::SHIPPING_METHOD_PRICE,
                                'label' => $this->trans('According to total price.', [], 'Admin.Shipping.Feature'),
                            ],
                            [
                                'id' => 'billing_weight',
                                'value' => Carrier::SHIPPING_METHOD_WEIGHT,
                                'label' => $this->trans('According to total weight.', [], 'Admin.Shipping.Feature'),
                            ],
                        ],
                    ],
                    'id_tax_rules_group' => [
                        'type' => 'select',
                        'label' => $this->trans('Tax', [], 'Admin.Global'),
                        'name' => 'id_tax_rules_group',
                        'options' => [
                            'query' => TaxRulesGroup::getTaxRulesGroups(true),
                            'id' => 'id_tax_rules_group',
                            'name' => 'name',
                            'default' => [
                                'label' => $this->trans('No tax', [], 'Admin.Global'),
                                'value' => 0,
                            ],
                        ],
                    ],
                    'range_behavior' => [
                        'type' => 'select',
                        'label' => $this->trans('Out-of-range behavior', [], 'Admin.Shipping.Feature'),
                        'name' => 'range_behavior',
                        'options' => [
                            'query' => [
                                [
                                    'id' => 0,
                                    'name' => $this->trans('Apply the cost of the highest defined range', [], 'Admin.Shipping.Feature'),
                                ],
                                [
                                    'id' => 1,
                                    'name' => $this->trans('Disable carrier', [], 'Admin.Shipping.Feature'),
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                        'hint' => $this->trans('Out-of-range behavior occurs when no defined range matches the customer\'s cart (e.g. when the weight of the cart is greater than the highest weight limit defined by the weight ranges).', [], 'Admin.Shipping.Help'),
                    ],
                    'zones' => [
                        'type' => 'zone',
                        'name' => 'zones',
                    ],
                ],
            ],
        ];

        if (Configuration::get('PS_ATCP_SHIPWRAP')) {
            unset($this->fields_form['form']['input']['id_tax_rules_group']);
        }

        $tpl_vars = [];
        $tpl_vars['PS_WEIGHT_UNIT'] = Configuration::get('PS_WEIGHT_UNIT');

        $currency = $this->getActualCurrency();

        $tpl_vars['currency_sign'] = $currency->sign;

        $fields_value = $this->getStepThreeFieldsValues($carrier);

        $this->getTplRangesVarsAndValues($carrier, $tpl_vars, $fields_value);

        return $this->renderGenericForm(['form' => $this->fields_form], $fields_value, $tpl_vars);
    }

    /**
     * @param Carrier $carrier
     *
     * @return string
     */
    public function renderStepFour($carrier)
    {
        $this->fields_form = [
            'form' => [
                'id_form' => 'step_carrier_conf',
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->trans('Maximum package width (%s)', ['%s' => Configuration::get('PS_DIMENSION_UNIT')], 'Admin.Shipping.Feature'),
                        'name' => 'max_width',
                        'required' => false,
                        'hint' => $this->trans('Maximum width managed by this carrier. Set the value to "0", or leave this field blank to ignore.', [], 'Admin.Shipping.Help') . ' ' . $this->trans('The value must be an integer.', [], 'Admin.Shipping.Help'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Maximum package height (%s)', ['%s' => Configuration::get('PS_DIMENSION_UNIT')], 'Admin.Shipping.Feature'),
                        'name' => 'max_height',
                        'required' => false,
                        'hint' => $this->trans('Maximum height managed by this carrier. Set the value to "0", or leave this field blank to ignore.', [], 'Admin.Shipping.Help') . ' ' . $this->trans('The value must be an integer.', [], 'Admin.Shipping.Help'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Maximum package depth (%s)', ['%s' => Configuration::get('PS_DIMENSION_UNIT')], 'Admin.Shipping.Feature'),
                        'name' => 'max_depth',
                        'required' => false,
                        'hint' => $this->trans('Maximum depth managed by this carrier. Set the value to "0", or leave this field blank to ignore.', [], 'Admin.Shipping.Help') . ' ' . $this->trans('The value must be an integer.', [], 'Admin.Shipping.Help'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Maximum package weight (%s)', ['%s' => Configuration::get('PS_WEIGHT_UNIT')], 'Admin.Shipping.Feature'),
                        'name' => 'max_weight',
                        'required' => false,
                        'hint' => $this->trans('Maximum weight managed by this carrier. Set the value to "0", or leave this field blank to ignore.', [], 'Admin.Shipping.Help'),
                    ],
                    [
                        'type' => 'group',
                        'label' => $this->trans('Group access', [], 'Admin.Shipping.Feature'),
                        'name' => 'groupBox',
                        'values' => Group::getGroups(Context::getContext()->language->id),
                        'hint' => $this->trans('Mark the groups that are allowed access to this carrier.', [], 'Admin.Shipping.Help'),
                    ],
                ],
            ],
        ];

        $fields_value = $this->getStepFourFieldsValues($carrier);

        // Added values of object Group
        $carrier_groups = $carrier->getGroups();
        $carrier_groups_ids = [];
        if (is_array($carrier_groups)) {
            foreach ($carrier_groups as $carrier_group) {
                $carrier_groups_ids[] = $carrier_group['id_group'];
            }
        }

        $groups = Group::getGroups($this->context->language->id);

        foreach ($groups as $group) {
            $fields_value['groupBox_' . $group['id_group']] = Tools::getValue('groupBox_' . $group['id_group'], (in_array($group['id_group'], $carrier_groups_ids) || empty($carrier_groups_ids) && !$carrier->id));
        }

        return $this->renderGenericForm(['form' => $this->fields_form], $fields_value);
    }

    public function renderStepFive($carrier)
    {
        $this->fields_form = [
            'form' => [
                'id_form' => 'step_carrier_summary',
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->trans('Enabled', [], 'Admin.Global'),
                        'name' => 'active',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                            ],
                        ],
                        'hint' => $this->trans('Enable the carrier in the front office.', [], 'Admin.Shipping.Help'),
                    ],
                ],
            ],
        ];
        $template = $this->createTemplate('controllers/carrier_wizard/summary.tpl');
        $fields_value = $this->getStepFiveFieldsValues($carrier);
        $active_form = $this->renderGenericForm(['form' => $this->fields_form], $fields_value);
        $active_form = str_replace(['<fieldset id="fieldset_form">', '</fieldset>'], '', $active_form);
        $template->assign('active_form', $active_form);

        return $template->fetch();
    }

    /**
     * @param Carrier $carrier
     * @param array $tpl_vars
     * @param array $fields_value
     */
    protected function getTplRangesVarsAndValues($carrier, &$tpl_vars, &$fields_value)
    {
        $tpl_vars['zones'] = Zone::getZones(false, true);
        $carrier_zones = $carrier->getZones();
        $carrier_zones_ids = [];
        if (is_array($carrier_zones)) {
            foreach ($carrier_zones as $carrier_zone) {
                $carrier_zones_ids[] = $carrier_zone['id_zone'];
            }
        }

        $range_table = $carrier->getRangeTable();
        $shipping_method = $carrier->getShippingMethod();

        $zones = Zone::getZones(false);
        foreach ($zones as $zone) {
            $fields_value['zones'][$zone['id_zone']] = Tools::getValue('zone_' . $zone['id_zone'], (in_array($zone['id_zone'], $carrier_zones_ids)));
        }

        if ($shipping_method == Carrier::SHIPPING_METHOD_FREE) {
            $range_obj = $carrier->getRangeObject($carrier->shipping_method);
            $price_by_range = [];
        } else {
            $range_obj = $carrier->getRangeObject();
            $price_by_range = Carrier::getDeliveryPriceByRanges($range_table, (int) $carrier->id);
        }

        foreach ($price_by_range as $price) {
            $tpl_vars['price_by_range'][$price['id_' . $range_table]][$price['id_zone']] = $price['price'];
        }

        $tmp_range = $range_obj->getRanges((int) $carrier->id);
        $tpl_vars['ranges'] = [];
        if ($shipping_method != Carrier::SHIPPING_METHOD_FREE) {
            foreach ($tmp_range as $id => $range) {
                $tpl_vars['ranges'][$range['id_' . $range_table]] = $range;
                $tpl_vars['ranges'][$range['id_' . $range_table]]['id_range'] = $range['id_' . $range_table];
            }
        }

        // init blank range
        if (!count($tpl_vars['ranges'])) {
            $tpl_vars['ranges'][] = ['id_range' => 0, 'delimiter1' => 0, 'delimiter2' => 0];
        }
    }

    public function renderGenericForm($fields_form, $fields_value, $tpl_vars = [])
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = [];
        $helper->id = (int) Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->tpl_vars = array_merge([
            'fields_value' => $fields_value,
            'languages' => $this->getLanguages(),
            'id_language' => $this->context->language->id,
        ], $tpl_vars);
        $helper->override_folder = 'carrier_wizard/';

        return $helper->generateForm($fields_form);
    }

    public function getStepOneFieldsValues($carrier)
    {
        return [
            'id_carrier' => $this->getFieldValue($carrier, 'id_carrier'),
            'name' => $this->getFieldValue($carrier, 'name'),
            'delay' => $this->getFieldValue($carrier, 'delay'),
            'grade' => $this->getFieldValue($carrier, 'grade'),
            'url' => $this->getFieldValue($carrier, 'url'),
        ];
    }

    public function getStepTwoFieldsValues($carrier)
    {
        return ['shop' => $this->getFieldValue($carrier, 'shop')];
    }

    public function getStepThreeFieldsValues($carrier)
    {
        $id_tax_rules_group = (is_object($this->object) && !$this->object->id) ? Carrier::getIdTaxRulesGroupMostUsed() : Carrier::getIdTaxRulesGroupByIdCarrier($this->object->id);

        $shipping_handling = (is_object($this->object) && !$this->object->id) ? 0 : $this->getFieldValue($carrier, 'shipping_handling');

        return [
            'is_free' => $this->getFieldValue($carrier, 'is_free'),
            'id_tax_rules_group' => (int) $id_tax_rules_group,
            'shipping_handling' => $shipping_handling,
            'shipping_method' => $this->getFieldValue($carrier, 'shipping_method'),
            'range_behavior' => $this->getFieldValue($carrier, 'range_behavior'),
            'zones' => $this->getFieldValue($carrier, 'zones'),
        ];
    }

    public function getStepFourFieldsValues($carrier)
    {
        return [
            'range_behavior' => $this->getFieldValue($carrier, 'range_behavior'),
            'max_height' => $this->getFieldValue($carrier, 'max_height'),
            'max_width' => $this->getFieldValue($carrier, 'max_width'),
            'max_depth' => $this->getFieldValue($carrier, 'max_depth'),
            'max_weight' => $this->getFieldValue($carrier, 'max_weight'),
            'group' => $this->getFieldValue($carrier, 'group'),
        ];
    }

    public function getStepFiveFieldsValues($carrier)
    {
        return ['active' => $this->getFieldValue($carrier, 'active')];
    }

    public function ajaxProcessChangeRanges()
    {
        if ((Validate::isLoadedObject($this->object) && !$this->access('edit')) || !$this->access('add')) {
            $this->errors[] = $this->trans('You do not have permission to use this wizard.', [], 'Admin.Shipping.Notification');

            return;
        }
        if ((!(int) $shipping_method = Tools::getValue('shipping_method')) || !in_array($shipping_method, [Carrier::SHIPPING_METHOD_PRICE, Carrier::SHIPPING_METHOD_WEIGHT])) {
            return;
        }

        $carrier = $this->loadObject(true);
        $carrier->shipping_method = $shipping_method;

        $tpl_vars = [];
        $fields_value = $this->getStepThreeFieldsValues($carrier);
        $this->getTplRangesVarsAndValues($carrier, $tpl_vars, $fields_value);
        $template = $this->createTemplate('controllers/carrier_wizard/helpers/form/form_ranges.tpl');
        $template->assign($tpl_vars);
        $template->assign('change_ranges', 1);

        $template->assign('fields_value', $fields_value);
        $template->assign('input', ['type' => 'zone', 'name' => 'zones']);

        $currency = $this->getActualCurrency();

        $template->assign('currency_sign', $currency->sign);
        $template->assign('PS_WEIGHT_UNIT', Configuration::get('PS_WEIGHT_UNIT'));

        die($template->fetch());
    }

    protected function validateForm($die = true)
    {
        $step_number = (int) Tools::getValue('step_number');
        $return = ['has_error' => false];

        if (!$this->access('edit')) {
            $this->errors[] = $this->trans('You do not have permission to use this wizard.', [], 'Admin.Shipping.Notification');
        } else {
            if (Shop::isFeatureActive() && $step_number == 2) {
                if (!Tools::getValue('checkBoxShopAsso_carrier')) {
                    $return['has_error'] = true;
                    $return['errors'][] = $this->trans('You must choose at least one shop or group shop.', [], 'Admin.Shipping.Notification');
                }
            } else {
                $this->validateRules();
            }
        }

        if (count($this->errors)) {
            $return['has_error'] = true;
            $return['errors'] = $this->errors;
        }
        if (count($this->errors) || $die) {
            die(json_encode($return));
        }
    }

    public function ajaxProcessValidateStep()
    {
        $this->validateForm(true);
    }

    public function processRanges($id_carrier)
    {
        if (!$this->access('edit') || !$this->access('add')) {
            $this->errors[] = $this->trans('You do not have permission to use this wizard.', [], 'Admin.Shipping.Notification');

            return;
        }

        $carrier = new Carrier((int) $id_carrier);
        if (!Validate::isLoadedObject($carrier)) {
            return false;
        }

        $range_inf = Tools::getValue('range_inf');
        $range_sup = Tools::getValue('range_sup');
        $range_type = Tools::getValue('shipping_method');

        $fees = Tools::getValue('fees');

        $carrier->deleteDeliveryPrice($carrier->getRangeTable());
        if ($range_type != Carrier::SHIPPING_METHOD_FREE) {
            foreach ($range_inf as $key => $delimiter1) {
                if (!isset($range_sup[$key])) {
                    continue;
                }
                $range = $carrier->getRangeObject((int) $range_type);
                $range->id_carrier = (int) $carrier->id;
                $range->delimiter1 = (float) $delimiter1;
                $range->delimiter2 = (float) $range_sup[$key];
                $range->save();

                if (!Validate::isLoadedObject($range)) {
                    return false;
                }
                $price_list = [];
                if (is_array($fees) && count($fees)) {
                    foreach ($fees as $id_zone => $fee) {
                        $price_list[] = [
                            'id_range_price' => ($range_type == Carrier::SHIPPING_METHOD_PRICE ? (int) $range->id : null),
                            'id_range_weight' => ($range_type == Carrier::SHIPPING_METHOD_WEIGHT ? (int) $range->id : null),
                            'id_carrier' => (int) $carrier->id,
                            'id_zone' => (int) $id_zone,
                            'price' => isset($fee[$key]) ? (float) str_replace(',', '.', $fee[$key]) : 0,
                        ];
                    }
                }

                if (count($price_list) && !$carrier->addDeliveryPrice($price_list, true)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function ajaxProcessUploadLogo()
    {
        if (!$this->access('edit')) {
            die('<return result="error" message="' . $this->trans('You do not have permission to use this wizard.', [], 'Admin.Shipping.Notification') . '" />');
        }

        $allowedExtensions = ['jpeg', 'gif', 'png', 'jpg'];

        $logo = (isset($_FILES['carrier_logo_input']) ? $_FILES['carrier_logo_input'] : false);
        if ($logo && !empty($logo['tmp_name']) && $logo['tmp_name'] != 'none'
            && (!isset($logo['error']) || !$logo['error'])
            && preg_match('/\.(jpe?g|gif|png)$/', $logo['name'])
            && is_uploaded_file($logo['tmp_name'])
            && ImageManager::isRealImage($logo['tmp_name'], $logo['type'])) {
            $file = $logo['tmp_name'];
            do {
                $tmp_name = uniqid() . '.jpg';
            } while (file_exists(_PS_TMP_IMG_DIR_ . $tmp_name));
            if (!ImageManager::resize($file, _PS_TMP_IMG_DIR_ . $tmp_name)) {
                die('<return result="error" message="Impossible to resize the image into ' . Tools::safeOutput(_PS_TMP_IMG_DIR_) . '" />');
            }
            @unlink($file);
            die('<return result="success" message="' . Tools::safeOutput(_PS_TMP_IMG_ . $tmp_name) . '" />');
        } else {
            die('<return result="error" message="Cannot upload file" />');
        }
    }

    public function ajaxProcessFinishStep()
    {
        $return = ['has_error' => false];
        if (!$this->access('edit')) {
            $return = [
                'has_error' => true,
                $return['errors'][] = $this->trans('You do not have permission to use this wizard.', [], 'Admin.Shipping.Notification'),
            ];
        } else {
            $this->validateForm(false);
            if ($id_carrier = Tools::getValue('id_carrier')) {
                $current_carrier = new Carrier((int) $id_carrier);

                // if update we duplicate current Carrier
                /** @var Carrier $new_carrier */
                $new_carrier = $current_carrier->duplicateObject();

                if (Validate::isLoadedObject($new_carrier)) {
                    // Set flag deteled to true for historization
                    $current_carrier->deleted = true;
                    $current_carrier->update();

                    // Fill the new carrier object
                    $this->copyFromPost($new_carrier, $this->table);
                    $new_carrier->position = $current_carrier->position;
                    $new_carrier->update();

                    $this->updateAssoShop((int) $new_carrier->id);
                    $this->duplicateLogo((int) $new_carrier->id, (int) $current_carrier->id);
                    $this->changeGroups((int) $new_carrier->id);

                    //Copy default carrier
                    if (Configuration::get('PS_CARRIER_DEFAULT') == $current_carrier->id) {
                        Configuration::updateValue('PS_CARRIER_DEFAULT', (int) $new_carrier->id);
                    }

                    // Call of hooks
                    Hook::exec('actionCarrierUpdate', [
                        'id_carrier' => (int) $current_carrier->id,
                        'carrier' => $new_carrier,
                    ]);
                    $this->postImage($new_carrier->id);
                    $this->changeZones($new_carrier->id);
                    $new_carrier->setTaxRulesGroup((int) Tools::getValue('id_tax_rules_group'));
                    $carrier = $new_carrier;
                }
            } else {
                $carrier = new Carrier();
                $this->copyFromPost($carrier, $this->table);
                if (!$carrier->add()) {
                    $return['has_error'] = true;
                    $return['errors'][] = $this->trans('An error occurred while saving this carrier.', [], 'Admin.Shipping.Notification');
                }
            }

            if ($carrier->is_free) {
                //if carrier is free delete shipping cost
                $carrier->deleteDeliveryPrice('range_weight');
                $carrier->deleteDeliveryPrice('range_price');
            }

            if (Validate::isLoadedObject($carrier)) {
                if (!$this->changeGroups((int) $carrier->id)) {
                    $return['has_error'] = true;
                    $return['errors'][] = $this->trans('An error occurred while saving carrier groups.', [], 'Admin.Shipping.Notification');
                }

                if (!$this->changeZones((int) $carrier->id)) {
                    $return['has_error'] = true;
                    $return['errors'][] = $this->trans('An error occurred while saving carrier zones.', [], 'Admin.Shipping.Notification');
                }

                if (!$carrier->is_free) {
                    if (!$this->processRanges((int) $carrier->id)) {
                        $return['has_error'] = true;
                        $return['errors'][] = $this->trans('An error occurred while saving carrier ranges.', [], 'Admin.Shipping.Notification');
                    }
                }

                if (Shop::isFeatureActive() && !$this->updateAssoShop((int) $carrier->id)) {
                    $return['has_error'] = true;
                    $return['errors'][] = $this->trans('An error occurred while saving associations of shops.', [], 'Admin.Shipping.Notification');
                }

                if (!$carrier->setTaxRulesGroup((int) Tools::getValue('id_tax_rules_group'))) {
                    $return['has_error'] = true;
                    $return['errors'][] = $this->trans('An error occurred while saving the tax rules group.', [], 'Admin.Shipping.Notification');
                }

                if (Tools::getValue('logo')) {
                    if (Tools::getValue('logo') == 'null' && file_exists(_PS_SHIP_IMG_DIR_ . $carrier->id . '.jpg')) {
                        unlink(_PS_SHIP_IMG_DIR_ . $carrier->id . '.jpg');
                    } else {
                        $logo = basename(Tools::getValue('logo'));
                        if (!file_exists(_PS_TMP_IMG_DIR_ . $logo) || !copy(_PS_TMP_IMG_DIR_ . $logo, _PS_SHIP_IMG_DIR_ . $carrier->id . '.jpg')) {
                            $return['has_error'] = true;
                            $return['errors'][] = $this->trans('An error occurred while saving carrier logo.', [], 'Admin.Shipping.Notification');
                        }
                    }
                }
                $return['id_carrier'] = $carrier->id;
            }
        }
        die(json_encode($return));
    }

    protected function changeGroups($id_carrier, $delete = true)
    {
        $carrier = new Carrier((int) $id_carrier);
        if (!Validate::isLoadedObject($carrier)) {
            return false;
        }

        return $carrier->setGroups(Tools::getValue('groupBox'));
    }

    public function changeZones($id)
    {
        $return = true;
        $carrier = new Carrier($id);
        if (!Validate::isLoadedObject($carrier)) {
            die($this->trans('The object cannot be loaded.', [], 'Admin.Notifications.Error'));
        }
        $zones = Zone::getZones(false);
        foreach ($zones as $zone) {
            if (count($carrier->getZone($zone['id_zone']))) {
                if (!isset($_POST['zone_' . $zone['id_zone']]) || !$_POST['zone_' . $zone['id_zone']]) {
                    $return &= $carrier->deleteZone((int) $zone['id_zone']);
                }
            } elseif (isset($_POST['zone_' . $zone['id_zone']]) && $_POST['zone_' . $zone['id_zone']]) {
                $return &= $carrier->addZone((int) $zone['id_zone']);
            }
        }

        return $return;
    }

    public function getValidationRules()
    {
        $step_number = (int) Tools::getValue('step_number');
        if (!$step_number) {
            return;
        }

        if ($step_number == 4 && !Shop::isFeatureActive() || $step_number == 5 && Shop::isFeatureActive()) {
            return ['fields' => []];
        }

        $step_fields = [
            1 => ['name', 'delay', 'grade', 'url'],
            2 => ['is_free', 'id_tax_rules_group', 'shipping_handling', 'shipping_method', 'range_behavior'],
            3 => ['range_behavior', 'max_height', 'max_width', 'max_depth', 'max_weight'],
            4 => [],
        ];
        if (Shop::isFeatureActive()) {
            $tmp = $step_fields;
            $step_fields = array_slice($tmp, 0, 1, true) + [2 => ['shop']];
            $step_fields[3] = $tmp[2];
            $step_fields[4] = $tmp[3];
        }

        $definition = ObjectModel::getDefinition('Carrier');
        foreach ($definition['fields'] as $field => $def) {
            if (is_array($step_fields[$step_number]) && !in_array($field, $step_fields[$step_number])) {
                unset($definition['fields'][$field]);
            }
        }

        return $definition;
    }

    public static function displayFieldName($field)
    {
        return $field;
    }

    public function duplicateLogo($new_id, $old_id)
    {
        $old_logo = _PS_SHIP_IMG_DIR_ . '/' . (int) $old_id . '.jpg';
        if (file_exists($old_logo)) {
            copy($old_logo, _PS_SHIP_IMG_DIR_ . '/' . (int) $new_id . '.jpg');
        }

        $old_tmp_logo = _PS_TMP_IMG_DIR_ . '/carrier_mini_' . (int) $old_id . '.jpg';
        if (file_exists($old_tmp_logo)) {
            if (!isset($_FILES['logo'])) {
                copy($old_tmp_logo, _PS_TMP_IMG_DIR_ . '/carrier_mini_' . $new_id . '.jpg');
            }
            unlink($old_tmp_logo);
        }
    }

    public function getActualCurrency()
    {
        if ($this->type_context == Shop::CONTEXT_SHOP) {
            Shop::setContext($this->type_context, $this->old_context->shop->id);
        } elseif ($this->type_context == Shop::CONTEXT_GROUP) {
            Shop::setContext($this->type_context, $this->old_context->shop->id_shop_group);
        }

        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

        Shop::setContext(Shop::CONTEXT_ALL);

        return $currency;
    }
}
