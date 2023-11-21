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
class AdminCarriersControllerCore extends AdminController
{
    /** @var string */
    protected $position_identifier = 'id_carrier';

    public function __construct()
    {
        if ($id_carrier = Tools::getValue('id_carrier') && !Tools::isSubmit('deletecarrier') && !Tools::isSubmit('statuscarrier') && !Tools::isSubmit('isFreecarrier')) {
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminCarrierWizard', true, [], ['id_carrier' => (int) $id_carrier]));
        }

        $this->bootstrap = true;
        $this->table = 'carrier';
        $this->className = 'Carrier';
        $this->lang = false;
        $this->deleted = true;

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->_defaultOrderBy = 'position';

        parent::__construct();

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Admin.Notifications.Info'),
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Info'),
                'icon' => 'icon-trash',
            ],
        ];

        $this->fieldImageSettings = [
            'name' => 'logo',
            'dir' => 's',
        ];

        $this->fields_list = [
            'id_carrier' => [
                'title' => $this->trans('ID', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'id_reference' => [
                'title' => $this->trans('Reference', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'name' => [
                'title' => $this->trans('Name', [], 'Admin.Global'),
            ],
            'image' => [
                'title' => $this->trans('Logo', [], 'Admin.Global'),
                'align' => 'center',
                'image' => 's',
                'class' => 'fixed-width-xs',
                'orderby' => false,
                'search' => false,
            ],
            'delay' => [
                'title' => $this->trans('Delay', [], 'Admin.Shipping.Feature'),
                'orderby' => false,
            ],
            'active' => [
                'title' => $this->trans('Status', [], 'Admin.Global'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'class' => 'fixed-width-sm',
                'orderby' => false,
            ],
            'is_free' => [
                'title' => $this->trans('Free Shipping', [], 'Admin.Shipping.Feature'),
                'align' => 'center',
                'active' => 'isFree',
                'type' => 'bool',
                'class' => 'fixed-width-sm',
                'orderby' => false,
            ],
            'position' => [
                'title' => $this->trans('Position', [], 'Admin.Global'),
                'filter_key' => 'a!position',
                'align' => 'center',
                'class' => 'fixed-width-sm',
                'position' => 'position',
            ],
        ];
    }

    public function initToolbar()
    {
        parent::initToolbar();

        if (isset($this->toolbar_btn['new']) && $this->display != 'view') {
            $this->toolbar_btn['new']['href'] = $this->context->link->getAdminLink('AdminCarrierWizard');
        }
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_title = $this->trans('Carriers', [], 'Admin.Shipping.Feature');
        if ($this->display != 'view') {
            $this->page_header_toolbar_btn['new_carrier'] = [
                'href' => $this->context->link->getAdminLink('AdminCarrierWizard'),
                'desc' => $this->trans('Add new carrier', [], 'Admin.Shipping.Feature'),
                'icon' => 'process-icon-new',
            ];
        }

        parent::initPageHeaderToolbar();
    }

    public function renderList()
    {
        $this->_select = 'b.*';
        $this->_join = 'INNER JOIN `' . _DB_PREFIX_ . 'carrier_lang` b ON a.id_carrier = b.id_carrier' . Shop::addSqlRestrictionOnLang('b') . ' AND b.id_lang = ' . (int) $this->context->language->id . ' LEFT JOIN `' . _DB_PREFIX_ . 'carrier_tax_rules_group_shop` ctrgs ON (a.`id_carrier` = ctrgs.`id_carrier` AND ctrgs.id_shop=' . (int) $this->context->shop->id . ')';
        $this->_use_found_rows = false;

        // test if need to show header alert.

        $this->context->smarty->assign([
            'showHeaderAlert' => (Db::getInstance()->executeS(
                    'SELECT COUNT(1) FROM `' . _DB_PREFIX_ . 'carrier` WHERE deleted = 0 AND id_reference > 2',
                    false
                )->fetchColumn(0) == 0),
        ]);

        return parent::renderList();
    }

    /**
     * @return string|void
     *
     * @throws SmartyException
     */
    public function renderForm()
    {
        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('Carriers', [], 'Admin.Shipping.Feature'),
                'icon' => 'icon-truck',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('Company', [], 'Admin.Global'),
                    'name' => 'name',
                    'required' => true,
                    'hint' => [
                        $this->trans('Allowed characters: letters, spaces and "%special_chars%".', ['%special_chars%' => '().-'], 'Admin.Shipping.Help'),
                        $this->trans('Carrier name displayed during checkout', [], 'Admin.Shipping.Help'),
                        $this->trans('For in-store pickup, enter 0 to replace the carrier name with your shop name.', [], 'Admin.Shipping.Help'),
                    ],
                ],
                [
                    'type' => 'file',
                    'label' => $this->trans('Logo', [], 'Admin.Global'),
                    'name' => 'logo',
                    'hint' => $this->trans('Upload a logo from your computer.', [], 'Admin.Shipping.Help') .
                        ' (.gif, .jpg, .jpeg, .webp ' .
                        $this->trans('or', [], 'Admin.Shipping.Help') . ' .png)',
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Transit time', [], 'Admin.Shipping.Feature'),
                    'name' => 'delay',
                    'lang' => true,
                    'required' => true,
                    'maxlength' => 512,
                    'hint' => $this->trans('Estimated delivery time will be displayed during checkout.', [], 'Admin.Shipping.Help'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Speed grade', [], 'Admin.Shipping.Feature'),
                    'name' => 'grade',
                    'required' => false,
                    'hint' => $this->trans('Enter "0" for a longest shipping delay, or "9" for the shortest shipping delay.', [], 'Admin.Shipping.Help'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('URL', [], 'Admin.Global'),
                    'name' => 'url',
                    'hint' => $this->trans('Delivery tracking URL: Type \'@\' where the tracking number should appear. It will then be automatically replaced by the tracking number.', [], 'Admin.Shipping.Help'),
                ],
                [
                    'type' => 'checkbox',
                    'label' => $this->trans('Zone', [], 'Admin.Global'),
                    'name' => 'zone',
                    'values' => [
                        'query' => Zone::getZones(false),
                        'id' => 'id_zone',
                        'name' => 'name',
                    ],
                    'hint' => $this->trans('The zones in which this carrier will be used.', [], 'Admin.Shipping.Help'),
                ],
                [
                    'type' => 'group',
                    'label' => $this->trans('Group access', [], 'Admin.Shipping.Help'),
                    'name' => 'groupBox',
                    'values' => Group::getGroups(Context::getContext()->language->id),
                    'hint' => $this->trans('Mark the groups that are allowed access to this carrier.', [], 'Admin.Shipping.Help'),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Status', [], 'Admin.Global'),
                    'name' => 'active',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->trans('Yes', [], 'Admin.Global'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->trans('No', [], 'Admin.Global'),
                        ],
                    ],
                    'hint' => $this->trans('Enable the carrier in the front office.', [], 'Admin.Shipping.Help'),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Apply shipping cost', [], 'Admin.Shipping.Feature'),
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
                    'hint' => $this->trans('Apply both regular shipping cost and product-specific shipping costs.', [], 'Admin.Shipping.Help'),
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Tax', [], 'Admin.Global'),
                    'name' => 'id_tax_rules_group',
                    'options' => [
                        'query' => TaxRulesGroup::getTaxRulesGroups(true),
                        'id' => 'id_tax_rules_group',
                        'name' => 'name',
                        'default' => [
                            'label' => $this->trans('No Tax', [], 'Admin.Global'),
                            'value' => 0,
                        ],
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Shipping and handling', [], 'Admin.Shipping.Feature'),
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
                    'hint' => $this->trans('Include the shipping and handling costs in the carrier price.', [], 'Admin.Shipping.Help'),
                ],
                [
                    'type' => 'radio',
                    'label' => $this->trans('Billing', [], 'Admin.Shipping.Feature'),
                    'name' => 'shipping_method',
                    'required' => false,
                    'class' => 't',
                    'br' => true,
                    'values' => [
                        [
                            'id' => 'billing_default',
                            'value' => Carrier::SHIPPING_METHOD_DEFAULT,
                            'label' => $this->trans('Default behavior', [], 'Admin.Shipping.Feature'),
                        ],
                        [
                            'id' => 'billing_price',
                            'value' => Carrier::SHIPPING_METHOD_PRICE,
                            'label' => $this->trans('According to total price', [], 'Admin.Shipping.Feature'),
                        ],
                        [
                            'id' => 'billing_weight',
                            'value' => Carrier::SHIPPING_METHOD_WEIGHT,
                            'label' => $this->trans('According to total weight', [], 'Admin.Shipping.Feature'),
                        ],
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Out-of-range behavior', [], 'Admin.Shipping.Feature'),
                    'name' => 'range_behavior',
                    'options' => [
                        'query' => [
                            [
                                'id' => 0,
                                'name' => $this->trans('Apply the cost of the highest defined range', [], 'Admin.Shipping.Help'),
                            ],
                            [
                                'id' => 1,
                                'name' => $this->trans('Disable carrier', [], 'Admin.Shipping.Feature'),
                            ],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'hint' => $this->trans('Out-of-range behavior occurs when none is defined (e.g. when a customer\'s cart weight is greater than the highest range limit).', [], 'Admin.Shipping.Help'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Maximum package height', [], 'Admin.Shipping.Feature'),
                    'name' => 'max_height',
                    'required' => false,
                    'hint' => $this->trans('Maximum height managed by this carrier. Set the value to "0," or leave this field blank to ignore.', [], 'Admin.Shipping.Help'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Maximum package width', [], 'Admin.Shipping.Feature'),
                    'name' => 'max_width',
                    'required' => false,
                    'hint' => $this->trans('Maximum width managed by this carrier. Set the value to "0," or leave this field blank to ignore.', [], 'Admin.Shipping.Help'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Maximum package depth', [], 'Admin.Shipping.Feature'),
                    'name' => 'max_depth',
                    'required' => false,
                    'hint' => $this->trans('Maximum depth managed by this carrier. Set the value to "0," or leave this field blank to ignore.', [], 'Admin.Shipping.Help'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Maximum package weight', [], 'Admin.Shipping.Feature'),
                    'name' => 'max_weight',
                    'required' => false,
                    'hint' => $this->trans('Maximum weight managed by this carrier. Set the value to "0," or leave this field blank to ignore.', [], 'Admin.Shipping.Help'),
                ],
                [
                    'type' => 'hidden',
                    'name' => 'is_module',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'external_module_name',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'shipping_external',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'need_range',
                ],
            ],
        ];

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = [
                'type' => 'shop',
                'label' => $this->trans('Store association', [], 'Admin.Global'),
                'name' => 'checkBoxShopAsso',
            ];
        }

        $this->fields_form['submit'] = [
            'title' => $this->trans('Save', [], 'Admin.Actions'),
        ];

        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $this->getFieldsValues($obj);

        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::getValue('submitAdd' . $this->table)) {
            /* Checking fields validity */
            $this->validateRules();
            if (!count($this->errors)) {
                $id = (int) Tools::getValue('id_' . $this->table);

                /* Object update */
                if (!empty($id)) {
                    try {
                        if ($this->access('edit')) {
                            $current_carrier = new Carrier($id);
                            if (!Validate::isLoadedObject($current_carrier)) {
                                throw new PrestaShopException('Cannot load Carrier object');
                            }

                            /** @var Carrier $new_carrier */
                            // Duplicate current Carrier
                            $new_carrier = $current_carrier->duplicateObject();
                            if (Validate::isLoadedObject($new_carrier)) {
                                // Set flag deteled to true for historization
                                $current_carrier->deleted = true;
                                $current_carrier->update();

                                // Fill the new carrier object
                                $this->copyFromPost($new_carrier, $this->table);
                                $new_carrier->position = $current_carrier->position;
                                $new_carrier->update();

                                $this->updateAssoShop($new_carrier->id);
                                $new_carrier->copyCarrierData((int) $current_carrier->id);
                                $this->changeGroups($new_carrier->id);
                                // Call of hooks
                                Hook::exec('actionCarrierUpdate', [
                                    'id_carrier' => (int) $current_carrier->id,
                                    'carrier' => $new_carrier,
                                ]);
                                $this->postImage($new_carrier->id);
                                $this->changeZones($new_carrier->id);
                                $new_carrier->setTaxRulesGroup((int) Tools::getValue('id_tax_rules_group'));
                                Tools::redirectAdmin(self::$currentIndex . '&id_' . $this->table . '=' . $current_carrier->id . '&conf=4&token=' . $this->token);
                            } else {
                                $this->errors[] = $this->trans('An error occurred while updating an object.', [], 'Admin.Notifications.Error') . ' <b>' . $this->table . '</b>';
                            }
                        } else {
                            $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');
                        }
                    } catch (PrestaShopException $e) {
                        $this->errors[] = $e->getMessage();
                    }
                } else {
                    // Object creation
                    if ($this->access('add')) {
                        // Create new Carrier
                        $carrier = new Carrier();
                        $this->copyFromPost($carrier, $this->table);
                        $carrier->position = Carrier::getHigherPosition() + 1;
                        if ($carrier->add()) {
                            if (($_POST['id_' . $this->table] = $carrier->id /* voluntary */) && $this->postImage($carrier->id) && $this->_redirect) {
                                $carrier->setTaxRulesGroup((int) Tools::getValue('id_tax_rules_group'), true);
                                $this->changeZones($carrier->id);
                                $this->changeGroups($carrier->id);
                                $this->updateAssoShop($carrier->id);
                                Tools::redirectAdmin(self::$currentIndex . '&id_' . $this->table . '=' . $carrier->id . '&conf=3&token=' . $this->token);
                            }
                        } else {
                            $this->errors[] = $this->trans('An error occurred while creating an object.', [], 'Admin.Notifications.Error') . ' <b>' . $this->table . '</b>';
                        }
                    } else {
                        $this->errors[] = $this->trans('You do not have permission to add this.', [], 'Admin.Notifications.Error');
                    }
                }
            }
            parent::postProcess();
        } elseif (isset($_GET['isFree' . $this->table])) {
            if (!$this->access('edit')) {
                $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');

                return;
            }

            $this->processIsFree();
        } else {
            parent::postProcess();
            Carrier::cleanPositions();
        }
    }

    public function processIsFree()
    {
        $carrier = new Carrier($this->id_object);
        if (!Validate::isLoadedObject($carrier)) {
            $this->errors[] = $this->trans('An error occurred while updating carrier information.', [], 'Admin.Shipping.Notification');
        }
        $carrier->is_free = !$carrier->is_free;
        if (!$carrier->update()) {
            $this->errors[] = $this->trans('An error occurred while updating carrier information.', [], 'Admin.Shipping.Notification');
        }
        Tools::redirectAdmin(self::$currentIndex . '&conf=5&token=' . $this->token);
    }

    /**
     * Overload the property $fields_value.
     *
     * @param object $obj
     */
    public function getFieldsValues($obj)
    {
        if ($this->getFieldValue($obj, 'is_module')) {
            $this->fields_value['is_module'] = 1;
        }

        if ($this->getFieldValue($obj, 'shipping_external')) {
            $this->fields_value['shipping_external'] = 1;
        }

        if ($this->getFieldValue($obj, 'need_range')) {
            $this->fields_value['need_range'] = 1;
        }
        // Added values of object Zone
        $carrier_zones = $obj->getZones();
        $carrier_zones_ids = [];
        if (is_array($carrier_zones)) {
            foreach ($carrier_zones as $carrier_zone) {
                $carrier_zones_ids[] = $carrier_zone['id_zone'];
            }
        }

        $zones = Zone::getZones(false);
        foreach ($zones as $zone) {
            $this->fields_value['zone_' . $zone['id_zone']] = Tools::getValue('zone_' . $zone['id_zone'], (in_array($zone['id_zone'], $carrier_zones_ids)));
        }

        // Added values of object Group
        $carrier_groups = $obj->getGroups();
        $carrier_groups_ids = [];
        if (is_array($carrier_groups)) {
            foreach ($carrier_groups as $carrier_group) {
                $carrier_groups_ids[] = $carrier_group['id_group'];
            }
        }

        $groups = Group::getGroups($this->context->language->id);

        foreach ($groups as $group) {
            $this->fields_value['groupBox_' . $group['id_group']] = Tools::getValue('groupBox_' . $group['id_group'], (in_array($group['id_group'], $carrier_groups_ids) || empty($carrier_groups_ids) && !$obj->id));
        }

        $this->fields_value['id_tax_rules_group'] = $this->object->getIdTaxRulesGroup($this->context);
    }

    /**
     * @param Carrier $object
     *
     * @return bool
     */
    protected function beforeDelete($object)
    {
        return (bool) $object->isUsed();
    }

    protected function changeGroups($id_carrier, $delete = true)
    {
        if ($delete) {
            Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'carrier_group WHERE id_carrier = ' . (int) $id_carrier);
        }
        $groups = Db::getInstance()->executeS('SELECT id_group FROM `' . _DB_PREFIX_ . 'group`');
        foreach ($groups as $group) {
            if (Tools::getIsset('groupBox') && in_array($group['id_group'], Tools::getValue('groupBox'))) {
                Db::getInstance()->execute('
					INSERT INTO ' . _DB_PREFIX_ . 'carrier_group (id_group, id_carrier)
					VALUES(' . (int) $group['id_group'] . ',' . (int) $id_carrier . ')
				');
            }
        }
    }

    public function changeZones($id)
    {
        /** @var Carrier $carrier */
        $carrier = new $this->className($id);
        if (!Validate::isLoadedObject($carrier)) {
            die($this->trans('The object cannot be loaded.', [], 'Admin.Notifications.Error'));
        }
        $zones = Zone::getZones(false);
        foreach ($zones as $zone) {
            if (count($carrier->getZone($zone['id_zone']))) {
                if (!isset($_POST['zone_' . $zone['id_zone']]) || !$_POST['zone_' . $zone['id_zone']]) {
                    $carrier->deleteZone($zone['id_zone']);
                }
            } elseif (isset($_POST['zone_' . $zone['id_zone']]) && $_POST['zone_' . $zone['id_zone']]) {
                $carrier->addZone($zone['id_zone']);
            }
        }
    }

    public function ajaxProcessUpdatePositions()
    {
        $way = (bool) (Tools::getValue('way'));
        $id_carrier = (int) (Tools::getValue('id'));
        $positions = Tools::getValue($this->table);

        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);

            if (isset($pos[2]) && (int) $pos[2] === $id_carrier) {
                $carrier = new Carrier((int) $pos[2]);
                if (Validate::isLoadedObject($carrier)) {
                    if (isset($position) && $carrier->updatePosition($way, $position)) {
                        echo 'ok position ' . (int) $position . ' for carrier ' . (int) $pos[1] . '\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update carrier ' . (int) $id_carrier . ' to position ' . (int) $position . ' "}';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "This carrier (' . (int) $id_carrier . ') can t be loaded"}';
                }

                break;
            }
        }
    }

    public function displayEditLink($token, $id, $name = null)
    {
        if ($this->access('edit')) {
            $tpl = $this->createTemplate('helpers/list/list_action_edit.tpl');
            if (!array_key_exists('Edit', self::$cache_lang)) {
                self::$cache_lang['Edit'] = $this->trans('Edit', [], 'Admin.Actions');
            }

            $tpl->assign([
                'href' => $this->context->link->getAdminLink('AdminCarrierWizard', true, [], ['id_carrier' => (int) $id]),
                'action' => self::$cache_lang['Edit'],
                'id' => $id,
            ]);

            return $tpl->fetch();
        } else {
            return;
        }
    }

    public function displayDeleteLink($token, $id, $name = null)
    {
        if ($this->access('delete')) {
            $tpl = $this->createTemplate('helpers/list/list_action_delete.tpl');

            if (!array_key_exists('Delete', self::$cache_lang)) {
                self::$cache_lang['Delete'] = $this->trans('Delete', [], 'Admin.Actions');
            }

            if (!array_key_exists('DeleteItem', self::$cache_lang)) {
                self::$cache_lang['DeleteItem'] = $this->trans('Delete selected item?', [], 'Admin.Notifications.Info');
            }

            if (!array_key_exists('Name', self::$cache_lang)) {
                self::$cache_lang['Name'] = $this->trans('Name:', [], 'Admin.Shipping.Feature');
            }

            if (null !== $name) {
                $name = '\n\n' . self::$cache_lang['Name'] . ' ' . $name;
            }

            $data = [
                $this->identifier => $id,
                'href' => $this->context->link->getAdminLink('AdminCarriers', true, [], ['id_carrier' => (int) $id, 'deletecarrier' => 1]),
                'action' => self::$cache_lang['Delete'],
            ];

            if ($this->specificConfirmDelete !== false) {
                $data['confirm'] = null !== $this->specificConfirmDelete ? '\r' . $this->specificConfirmDelete : addcslashes(Tools::htmlentitiesDecodeUTF8(self::$cache_lang['DeleteItem'] . $name), '\'');
            }

            $tpl->assign(array_merge($this->tpl_delete_link_vars, $data));

            return $tpl->fetch();
        } else {
            return;
        }
    }
}
