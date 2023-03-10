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
 * @property Country $object
 */
class AdminCountriesControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'country';
        $this->className = 'Country';
        $this->lang = true;
        $this->deleted = false;
        $this->_defaultOrderBy = 'name';
        $this->_defaultOrderWay = 'ASC';

        $this->explicitSelect = true;
        $this->addRowAction('edit');

        parent::__construct();

        $this->bulk_actions = [
            'delete' => ['text' => $this->trans('Delete selected', [], 'Admin.Actions'), 'confirm' => $this->trans('Delete selected items?', [], 'Admin.Actions')],
            'AffectZone' => ['text' => $this->trans('Assign to a new zone', [], 'Admin.International.Feature')],
        ];

        $this->fieldImageSettings = [
            'name' => 'logo',
            'dir' => 'st',
        ];

        $this->fields_options = [
            'general' => [
                'title' => $this->trans('Country options', [], 'Admin.International.Feature'),
                'fields' => [
                    'PS_RESTRICT_DELIVERED_COUNTRIES' => [
                        'title' => $this->trans('Restrict country selections in front office to those covered by active carriers', [], 'Admin.International.Help'),
                        'cast' => 'intval',
                        'type' => 'bool',
                        'default' => '0',
                    ],
                ],
                'submit' => ['title' => $this->trans('Save', [], 'Admin.Actions')],
            ],
        ];

        $zones_array = [];
        foreach (Zone::getZones() as $zone) {
            $zones_array[$zone['id_zone']] = $zone['name'];
        }

        $this->fields_list = [
            'id_country' => [
                'title' => $this->trans('ID', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'name' => [
                'title' => $this->trans('Country', [], 'Admin.Global'),
                'filter_key' => 'b!name',
            ],
            'iso_code' => [
                'title' => $this->trans('ISO code', [], 'Admin.International.Feature'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'call_prefix' => [
                'title' => $this->trans('Call prefix', [], 'Admin.International.Feature'),
                'align' => 'center',
                'callback' => 'displayCallPrefix',
                'class' => 'fixed-width-sm',
            ],
            'zone' => [
                'title' => $this->trans('Zone', [], 'Admin.Global'),
                'type' => 'select',
                'list' => $zones_array,
                'filter_key' => 'z!id_zone',
                'filter_type' => 'int',
                'order_key' => 'z!name',
            ],
            'active' => [
                'title' => $this->trans('Enabled', [], 'Admin.Global'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'a!active',
                'class' => 'fixed-width-sm',
            ],
        ];
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_country'] = [
                'href' => self::$currentIndex . '&addcountry&token=' . $this->token,
                'desc' => $this->trans('Add new country', [], 'Admin.International.Feature'),
                'icon' => 'process-icon-new',
            ];
        }

        parent::initPageHeaderToolbar();
    }

    /**
     * AdminController::setMedia() override.
     *
     * @see AdminController::setMedia()
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addJqueryPlugin('fieldselection');
    }

    public function renderList()
    {
        $this->_select = 'z.`name` AS zone';
        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'zone` z ON (z.`id_zone` = a.`id_zone`)';
        $this->_use_found_rows = false;

        $this->tpl_list_vars['zones'] = Zone::getZones();
        $this->tpl_list_vars['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
        $this->tpl_list_vars['POST'] = $_POST;

        return parent::renderList();
    }

    /**
     * @return string|void
     *
     * @throws SmartyException
     */
    public function renderForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $address_layout = AddressFormat::getAddressCountryFormat($obj->id);
        if ($value = Tools::getValue('address_layout')) {
            $address_layout = $value;
        }

        $default_layout = '';

        // TODO: Use format from XML
        $default_layout_tab = [
            ['firstname', 'lastname'],
            ['company'],
            ['vat_number'],
            ['address1'],
            ['address2'],
            ['postcode', 'city'],
            ['Country:name'],
            ['phone'],
        ];

        foreach ($default_layout_tab as $line) {
            $default_layout .= implode(' ', $line) . AddressFormat::FORMAT_NEW_LINE;
        }

        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('Countries', [], 'Admin.International.Feature'),
                'icon' => 'icon-globe',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('Country', [], 'Admin.Global'),
                    'name' => 'name',
                    'lang' => true,
                    'required' => true,
                    'hint' => $this->trans('Country name', [], 'Admin.International.Feature') . ' - ' . $this->trans('Invalid characters:', [], 'Admin.Global') . ' &lt;&gt;;=#{} ',
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('ISO code', [], 'Admin.International.Feature'),
                    'name' => 'iso_code',
                    'maxlength' => 3,
                    'class' => 'uppercase',
                    'required' => true,
                    'hint' => $this->trans('Two -- or three -- letter ISO code (e.g. "us" for United States).', [], 'Admin.International.Help'),
                    /* @TODO - add two lines for the hint? */
                    /*'desc' => $this->trans('Two -- or three -- letter ISO code (e.g. U.S. for United States)', [], 'Admin.International.Help').'.
                            <a href="http://www.iso.org/iso/country_codes/iso_3166_code_lists/country_names_and_code_elements.htm" target="_blank">'.
                                $this->trans('Official list here', [], 'Admin.International.Feature').'
                            </a>.'*/
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Call prefix', [], 'Admin.International.Feature'),
                    'name' => 'call_prefix',
                    'maxlength' => 3,
                    'class' => 'uppercase',
                    'required' => true,
                    'hint' => $this->trans('International call prefix, (e.g. 1 for United States).', [], 'Admin.International.Help'),
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Default currency', [], 'Admin.International.Feature'),
                    'name' => 'id_currency',
                    'options' => [
                        'query' => Currency::getCurrencies(false, true, true),
                        'id' => 'id_currency',
                        'name' => 'name',
                        'default' => [
                            'label' => $this->trans('Default store currency', [], 'Admin.International.Feature'),
                            'value' => 0,
                        ],
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Zone', [], 'Admin.Global'),
                    'name' => 'id_zone',
                    'options' => [
                        'query' => Zone::getZones(),
                        'id' => 'id_zone',
                        'name' => 'name',
                    ],
                    'hint' => $this->trans('Geographical region.', [], 'Admin.International.Help'),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Does it need a ZIP/Postal code?', [], 'Admin.International.Feature'),
                    'name' => 'need_zip_code',
                    'required' => false,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'need_zip_code_on',
                            'value' => 1,
                            'label' => $this->trans('Yes', [], 'Admin.Global'),
                        ],
                        [
                            'id' => 'need_zip_code_off',
                            'value' => 0,
                            'label' => $this->trans('No', [], 'Admin.Global'),
                        ],
                    ],
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('ZIP/Postal code format', [], 'Admin.International.Feature'),
                    'name' => 'zip_code_format',
                    'required' => true,
                    'desc' => $this->trans('Indicate the format of the postal code: use L for a letter, N for a number, and C for the country\'s ISO 3166-1 alpha-2 code. For example, NNNNN for the United States, France, Poland and many other; LNNNNLLL for Argentina, etc. If you do not want PrestaShop to verify the postal code for this country, leave it blank.', [], 'Admin.International.Help'),
                ],
                [
                    'type' => 'address_layout',
                    'label' => $this->trans('Address format', [], 'Admin.International.Feature'),
                    'name' => 'address_layout',
                    'address_layout' => $address_layout,
                    'encoding_address_layout' => urlencode($address_layout),
                    'encoding_default_layout' => urlencode($default_layout),
                    'display_valid_fields' => $this->displayValidFields(),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Active', [], 'Admin.Global'),
                    'name' => 'active',
                    'required' => false,
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
                    'hint' => $this->trans('Display this country to your customers (the selected country will always be displayed in the Back Office).', [], 'Admin.International.Help'),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Contains states', [], 'Admin.International.Feature'),
                    'name' => 'contains_states',
                    'required' => false,
                    'values' => [
                        [
                            'id' => 'contains_states_on',
                            'value' => 1,
                            'label' => $this->trans('Yes', [], 'Admin.Global'),
                        ],
                        [
                            'id' => 'contains_states_off',
                            'value' => 0,
                            'label' => $this->trans('No', [], 'Admin.Global'),
                        ],
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Do you need a tax identification number?', [], 'Admin.International.Feature'),
                    'name' => 'need_identification_number',
                    'required' => false,
                    'values' => [
                        [
                            'id' => 'need_identification_number_on',
                            'value' => 1,
                            'label' => $this->trans('Yes', [], 'Admin.Global'),
                        ],
                        [
                            'id' => 'need_identification_number_off',
                            'value' => 0,
                            'label' => $this->trans('No', [], 'Admin.Global'),
                        ],
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Display tax label (e.g. "Tax incl.")', [], 'Admin.International.Feature'),
                    'name' => 'display_tax_label',
                    'required' => false,
                    'values' => [
                        [
                            'id' => 'display_tax_label_on',
                            'value' => 1,
                            'label' => $this->trans('Yes', [], 'Admin.Global'),
                        ],
                        [
                            'id' => 'display_tax_label_off',
                            'value' => 0,
                            'label' => $this->trans('No', [], 'Admin.Global'),
                        ],
                    ],
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

        return parent::renderForm();
    }

    public function processUpdate()
    {
        /** @var Country $country */
        $country = $this->loadObject();
        if (Validate::isLoadedObject($country) && Tools::getValue('id_zone')) {
            $old_id_zone = $country->id_zone;
            $results = Db::getInstance()->executeS('SELECT `id_state` FROM `' . _DB_PREFIX_ . 'state` WHERE `id_country` = ' . (int) $country->id . ' AND `id_zone` = ' . (int) $old_id_zone);

            if ($results && count($results)) {
                $ids = [];
                foreach ($results as $res) {
                    $ids[] = (int) $res['id_state'];
                }

                if (count($ids)) {
                    $res = Db::getInstance()->execute(
                            'UPDATE `' . _DB_PREFIX_ . 'state`
							SET `id_zone` = ' . (int) Tools::getValue('id_zone') . '
							WHERE `id_state` IN (' . implode(',', $ids) . ')'
                    );
                }
            }
        }

        return parent::processUpdate();
    }

    public function postProcess()
    {
        if (!Tools::getValue('id_' . $this->table)) {
            if (Validate::isLanguageIsoCode(Tools::getValue('iso_code')) && (int) Country::getByIso(Tools::getValue('iso_code'))) {
                $this->errors[] = $this->trans('This ISO code already exists.You cannot create two countries with the same ISO code.', [], 'Admin.International.Notification');
            }
        } elseif (Validate::isLanguageIsoCode(Tools::getValue('iso_code'))) {
            $id_country = (int) Country::getByIso(Tools::getValue('iso_code'));
            if ($id_country != 0 && $id_country != Tools::getValue('id_' . $this->table)) {
                $this->errors[] = $this->trans('This ISO code already exists.You cannot create two countries with the same ISO code.', [], 'Admin.International.Notification');
            }
        }

        return parent::postProcess();
    }

    public function processSave()
    {
        if (!$this->id_object) {
            $tmp_addr_format = new AddressFormat();
        } else {
            $tmp_addr_format = new AddressFormat($this->id_object);
        }

        $tmp_addr_format->format = Tools::getValue('address_layout');

        if (!$tmp_addr_format->checkFormatFields()) {
            $error_list = $tmp_addr_format->getErrorList();
            foreach ($error_list as $error) {
                $this->errors[] = $error;
            }
        }
        if (strlen($tmp_addr_format->format) <= 0) {
            $this->errors[] = $this->trans('Address format invalid', [], 'Admin.Notifications.Error');
        }

        $country = parent::processSave();

        if (!count($this->errors)) {
            if (null === $tmp_addr_format->id_country) {
                $tmp_addr_format->id_country = $country->id;
            }

            if (!$tmp_addr_format->save()) {
                $this->errors[] = $this->trans('Invalid address layout %s', [Db::getInstance()->getMsgError()], 'Admin.International.Notification');
            }
        }

        return $country;
    }

    /**
     * @return bool|ObjectModel|null
     *
     * @throws PrestaShopException
     */
    public function processStatus()
    {
        parent::processStatus();

        $object = $this->loadObject();
        /** @var Country $object */
        if (Validate::isLoadedObject($object) && $object->active == 1) {
            return Country::addModuleRestrictions([], [['id_country' => $object->id]], []);
        }

        return false;
    }

    /**
     * Allow the assignation of zone only if the form is displayed.
     *
     * @return bool|void
     */
    protected function processBulkAffectZone()
    {
        $zone_to_affect = Tools::getValue('zone_to_affect');
        if ($zone_to_affect && $zone_to_affect !== 0) {
            parent::processBulkAffectZone();
        }

        if (Tools::getIsset('submitBulkAffectZonecountry')) {
            $this->tpl_list_vars['assign_zone'] = true;
        }
    }

    protected function displayValidFields()
    {
        /* The following translations are needed later - don't remove the comments!
        $this->trans('Customer', [], 'Admin.Global');
        $this->trans('Warehouse', [], 'Admin.Global');
        $this->trans('Country', [], 'Admin.Global');
        $this->trans('State', [], 'Admin.Global');
        $this->trans('Address', [], 'Admin.Global');
         */

        $html_tabnav = '<ul class="nav nav-tabs" id="custom-address-fields">';
        $html_tabcontent = '<div class="tab-content" >';

        $object_list = AddressFormat::getLiableClass('Address');
        $object_list['Address'] = null;

        // Get the available properties for each class
        $i = 0;
        $class_tab_active = 'active';
        foreach ($object_list as $class_name => &$object) {
            if ($i != 0) {
                $class_tab_active = '';
            }
            $fields = [];
            $html_tabnav .= '<li' . ($class_tab_active ? ' class="' . $class_tab_active . '"' : '') . '>
				<a href="#availableListFieldsFor_' . $class_name . '"><i class="icon-caret-down"></i>&nbsp;' . Translate::getAdminTranslation($class_name, 'AdminCountries') . '</a></li>';

            foreach (AddressFormat::getValidateFields($class_name) as $name) {
                $fields[] = '<a href="javascript:void(0);" class="addPattern btn btn-default btn-xs" id="' . ($class_name == 'Address' ? $name : $class_name . ':' . $name) . '">
					<i class="icon-plus-sign"></i>&nbsp;' . ObjectModel::displayFieldName($name, $class_name) . '</a>';
            }
            $html_tabcontent .= '
				<div class="tab-pane availableFieldsList panel ' . $class_tab_active . '" id="availableListFieldsFor_' . $class_name . '">
				' . implode(' ', $fields) . '</div>';
            unset($object);
            ++$i;
        }
        $html_tabnav .= '</ul>';
        $html_tabcontent .= '</div>';

        return $html_tabnav . $html_tabcontent;
    }

    public static function displayCallPrefix($prefix)
    {
        return (int) $prefix ? '+' . $prefix : '-';
    }
}
