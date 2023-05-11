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
 * @property State $object
 */
class AdminStatesControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'state';
        $this->className = 'State';
        $this->lang = false;

        parent::__construct();

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        if (!Tools::getValue('realedit')) {
            $this->deleted = false;
        }

        $this->bulk_actions = [
            'delete' => ['text' => $this->trans('Delete selected', [], 'Admin.Actions'), 'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning')],
            'AffectZone' => ['text' => $this->trans('Assign to a new zone', [], 'Admin.International.Feature')],
        ];

        $this->_select = 'z.`name` AS zone, cl.`name` AS country';
        $this->_join = '
		LEFT JOIN `' . _DB_PREFIX_ . 'zone` z ON (z.`id_zone` = a.`id_zone`)
		LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON (cl.`id_country` = a.`id_country` AND cl.id_lang = ' . (int) $this->context->language->id . ')';
        $this->_use_found_rows = false;

        $countries_array = $zones_array = [];
        foreach (Zone::getZones() as $zone) {
            $zones_array[$zone['id_zone']] = $zone['name'];
        }
        foreach (Country::getCountries($this->context->language->id, false, true, false) as $country) {
            $countries_array[$country['id_country']] = $country['name'];
        }

        $this->fields_list = [
            'id_state' => [
                'title' => $this->trans('ID', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'name' => [
                'title' => $this->trans('Name', [], 'Admin.Global'),
                'filter_key' => 'a!name',
            ],
            'iso_code' => [
                'title' => $this->trans('ISO code', [], 'Admin.International.Feature'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'zone' => [
                'title' => $this->trans('Zone', [], 'Admin.Global'),
                'type' => 'select',
                'list' => $zones_array,
                'filter_key' => 'z!id_zone',
                'filter_type' => 'int',
                'order_key' => 'zone',
            ],
            'country' => [
                'title' => $this->trans('Country', [], 'Admin.Global'),
                'type' => 'select',
                'list' => $countries_array,
                'filter_key' => 'cl!id_country',
                'filter_type' => 'int',
                'order_key' => 'country',
            ],
            'active' => [
                'title' => $this->trans('Enabled', [], 'Admin.Global'),
                'active' => 'status',
                'filter_key' => 'a!active',
                'align' => 'center',
                'type' => 'bool',
                'orderby' => false,
                'class' => 'fixed-width-sm',
            ],
        ];
    }

    public function initPageHeaderToolbar()
    {
        if ($this->display === null || $this->display === 'list') {
            $this->page_header_toolbar_btn['new_state'] = [
                'href' => self::$currentIndex . '&addstate&token=' . $this->token,
                'desc' => $this->trans('Add new state', [], 'Admin.International.Feature'),
                'icon' => 'process-icon-new',
            ];
        }

        parent::initPageHeaderToolbar();
    }

    public function renderList()
    {
        $this->tpl_list_vars['zones'] = Zone::getZones();
        $this->tpl_list_vars['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
        $this->tpl_list_vars['POST'] = $_POST;

        return parent::renderList();
    }

    public function renderForm()
    {
        // display multistore information message if multistore is used
        if ($this->isMultistoreEnabled()) {
            $this->informations[] = $this->trans(
                'Note that this feature is only available in "all stores" context. It will be added to all your stores.',
                [],
                'Admin.Notifications.Info'
            );
        }

        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('States', [], 'Admin.International.Feature'),
                'icon' => 'icon-globe',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('Name', [], 'Admin.Global'),
                    'name' => 'name',
                    'maxlength' => 80,
                    'required' => true,
                    'hint' => $this->trans('Provide the state name to be displayed in addresses and on invoices.', [], 'Admin.International.Help'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('ISO code', [], 'Admin.International.Feature'),
                    'name' => 'iso_code',
                    'maxlength' => 7,
                    'required' => true,
                    'class' => 'uppercase',
                    'hint' => $this->trans('1 to 4 letter ISO code.', [], 'Admin.International.Help') . ' ' . $this->trans('You can prefix it with the country ISO code if needed.', [], 'Admin.International.Help'),
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Country', [], 'Admin.Global'),
                    'name' => 'id_country',
                    'required' => true,
                    'default_value' => (int) $this->context->country->id,
                    'options' => [
                        'query' => Country::getCountries($this->context->language->id, false, true),
                        'id' => 'id_country',
                        'name' => 'name',
                    ],
                    'hint' => $this->trans('Country where the state is located.', [], 'Admin.International.Help') . ' ' . $this->trans('Only the countries with the option "contains states" enabled are displayed.', [], 'Admin.International.Help'),
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Zone', [], 'Admin.Global'),
                    'name' => 'id_zone',
                    'required' => true,
                    'options' => [
                        'query' => Zone::getZones(),
                        'id' => 'id_zone',
                        'name' => 'name',
                    ],
                    'hint' => [
                        $this->trans('Geographical region where this state is located.', [], 'Admin.International.Help'),
                        $this->trans('Used for shipping', [], 'Admin.International.Help'),
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Status', [], 'Admin.Global'),
                    'name' => 'active',
                    'required' => true,
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
                ],
            ],
            'submit' => [
                'title' => $this->trans('Save', [], 'Admin.Actions'),
            ],
        ];

        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit($this->table . 'Orderby') || Tools::isSubmit($this->table . 'Orderway')) {
            $this->filter = true;
        }

        // Idiot-proof controls
        if (!Tools::getValue('id_' . $this->table)) {
            if (Validate::isStateIsoCode(Tools::getValue('iso_code')) && State::getIdByIso(Tools::getValue('iso_code'), Tools::getValue('id_country'))) {
                $this->errors[] = $this->trans('This ISO code already exists. You cannot create two states with the same ISO code.', [], 'Admin.International.Notification');
            }
        } elseif (Validate::isStateIsoCode(Tools::getValue('iso_code'))) {
            $id_state = State::getIdByIso(Tools::getValue('iso_code'), Tools::getValue('id_country'));
            if ($id_state && $id_state != Tools::getValue('id_' . $this->table)) {
                $this->errors[] = $this->trans('This ISO code already exists. You cannot create two states with the same ISO code.', [], 'Admin.International.Notification');
            }
        }

        /* Delete state */
        if (Tools::isSubmit('delete' . $this->table)) {
            if ($this->access('delete')) {
                if (Validate::isLoadedObject($object = $this->loadObject())) {
                    /** @var State $object */
                    if (!$object->isUsed()) {
                        if ($object->delete()) {
                            Tools::redirectAdmin(self::$currentIndex . '&conf=1&token=' . (Tools::getValue('token') ? Tools::getValue('token') : $this->token));
                        }
                        $this->errors[] = $this->trans('An error occurred during deletion.', [], 'Admin.Notifications.Error');
                    } else {
                        $this->errors[] = $this->trans('This state was used in at least one address. It cannot be removed.', [], 'Admin.International.Notification');
                    }
                } else {
                    $this->errors[] = $this->trans('An error occurred while deleting the object.', [], 'Admin.Notifications.Error') . ' <b>' . $this->table . '</b> ' . $this->trans('(cannot load object)', [], 'Admin.Notifications.Error');
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to delete this.', [], 'Admin.Notifications.Error');
            }
        }

        if (!count($this->errors)) {
            parent::postProcess();
        }
    }

    protected function displayAjaxStates()
    {
        $states = Db::getInstance()->executeS('
		SELECT s.id_state, s.name
		FROM ' . _DB_PREFIX_ . 'state s
		LEFT JOIN ' . _DB_PREFIX_ . 'country c ON (s.`id_country` = c.`id_country`)
		WHERE s.id_country = ' . (int) (Tools::getValue('id_country')) . ' AND s.active = 1 AND c.`contains_states` = 1
		ORDER BY s.`name` ASC');

        if (is_array($states) && !empty($states)) {
            $list = '';
            if ((bool) Tools::getValue('no_empty') != true) {
                $empty_value = (Tools::isSubmit('empty_value')) ? Tools::getValue('empty_value') : '-';
                $list = '<option value="0">' . Tools::htmlentitiesUTF8($empty_value) . '</option>' . "\n";
            }

            foreach ($states as $state) {
                $list .= '<option value="' . (int) ($state['id_state']) . '"' . ((isset($_GET['id_state']) && $_GET['id_state'] == $state['id_state']) ? ' selected="selected"' : '') . '>' . $state['name'] . '</option>' . "\n";
            }
        } else {
            $list = 'false';
        }

        die($list);
    }

    /**
     * Allow the assignation of zone only if the form is displayed.
     *
     * @return void|bool
     */
    protected function processBulkAffectZone()
    {
        $zone_to_affect = Tools::getValue('zone_to_affect');
        if ($zone_to_affect && $zone_to_affect !== 0) {
            parent::processBulkAffectZone();
        }

        if (Tools::getIsset('submitBulkAffectZonestate')) {
            $this->tpl_list_vars['assign_zone'] = true;
        }
    }
}
