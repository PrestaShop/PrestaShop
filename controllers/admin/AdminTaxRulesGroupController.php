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

/**
 * @property TaxRulesGroup $object
 */
class AdminTaxRulesGroupControllerCore extends AdminController
{
    public $tax_rule;
    public $selected_countries = array();
    public $selected_states = array();
    public $errors_tax_rule;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'tax_rules_group';
        $this->className = 'TaxRulesGroup';
        $this->lang = false;

        parent::__construct();

        $this->fields_list = array(
            'id_tax_rules_group' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->trans('Name', array(), 'Admin.Global')
            ),
            'active' => array(
                'title' => $this->trans('Enabled', array(), 'Admin.Global'),
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'align' => 'center',
                'class' => 'fixed-width-sm'
            )
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash'
            )
        );

        $this->_where .= ' AND a.deleted = 0';
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_tax_rules_group'] = array(
                'href' => self::$currentIndex.'&addtax_rules_group&token='.$this->token,
                'desc' => $this->trans('Add new tax rules group', array(), 'Admin.International.Feature'),
                'icon' => 'process-icon-new'
            );
        }
        if ($this->display === "edit") {
            $this->page_header_toolbar_btn['new'] = array(
                'href' => '#',
                'desc' => $this->trans('Add a new tax rule', array(), 'Admin.International.Feature')
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function initRulesList($id_group)
    {
        $this->table = 'tax_rule';
        $this->list_id = 'tax_rule';
        $this->identifier = 'id_tax_rule';
        $this->className = 'TaxRule';
        $this->lang = false;
        $this->list_simple_header = false;
        $this->toolbar_btn = null;
        $this->list_no_link = true;

        $this->bulk_actions = array(
            'delete' => array('text' => $this->trans('Delete selected', array(), 'Admin.Actions'), 'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Warning'), 'icon' => 'icon-trash')
        );

        $this->fields_list = array(
            'country_name' => array(
                'title' => $this->trans('Country', array(), 'Admin.Global')
            ),
            'state_name' => array(
                'title' => $this->trans('State', array(), 'Admin.Global')
            ),
            'zipcode' => array(
                'title' => $this->trans('Zip/Postal code', array(), 'Admin.Global'),
                'class' => 'fixed-width-md'
            ),
            'behavior' => array(
                'title' => $this->trans('Behavior', array(), 'Admin.International.Feature')
            ),
            'rate' => array(
                'title' => $this->trans('Tax', array(), 'Admin.Global'),
                'class' => 'fixed-width-sm'
            ),
            'description' => array(
                'title' => $this->trans('Description', array(), 'Admin.Global')
            )
        );

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->_select = '
			c.`name` AS country_name,
			s.`name` AS state_name,
			CONCAT_WS(" - ", a.`zipcode_from`, a.`zipcode_to`) AS zipcode,
			t.rate';

        $this->_join = '
			LEFT JOIN `'._DB_PREFIX_.'country_lang` c
				ON (a.`id_country` = c.`id_country` AND id_lang = '.(int)$this->context->language->id.')
			LEFT JOIN `'._DB_PREFIX_.'state` s
				ON (a.`id_state` = s.`id_state`)
			LEFT JOIN `'._DB_PREFIX_.'tax` t
				ON (a.`id_tax` = t.`id_tax`)';
        $this->_where = 'AND `id_tax_rules_group` = '.(int)$id_group;
        $this->_use_found_rows = false;

        $this->show_toolbar = false;
        $this->tpl_list_vars = array('id_tax_rules_group' => (int)$id_group);

        $this->_filter = false;

        return parent::renderList();
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('Tax Rules', array(), 'Admin.International.Feature'),
                'icon' => 'icon-money'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->trans('Name', array(), 'Admin.Global'),
                    'name' => 'name',
                    'required' => true,
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' <>;=#{}'
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->trans('Enable', array(), 'Admin.Actions'),
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->trans('Enabled', array(), 'Admin.Global')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->trans('Disabled', array(), 'Admin.Global')
                        )
                    )
                )
            ),
            'submit' => array(
                'title' => $this->trans('Save and stay', array(), 'Admin.Actions'),
                'stay' => true
            )
        );

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->trans('Shop association', array(), 'Admin.Global'),
                'name' => 'checkBoxShopAsso',
            );
        }

        if (!($obj = $this->loadObject(true))) {
            return;
        }
        if (!isset($obj->id)) {
            $this->no_back = false;
            $content = parent::renderForm();
        } else {
            $this->no_back = true;
            $this->page_header_toolbar_btn['new'] = array(
                'href' => '#',
                'desc' => $this->trans('Add a new tax rule', array(), 'Admin.International.Feature')
            );
            $content = parent::renderForm();
            $this->tpl_folder = 'tax_rules/';
            $content .= $this->initRuleForm();

            // We change the variable $ tpl_folder to avoid the overhead calling the file in list_action_edit.tpl in intList ();

            $content .= $this->initRulesList((int)$obj->id);
        }
        return $content;
    }

    public function initRuleForm()
    {
        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->trans('New tax rule', array(), 'Admin.International.Feature'),
                'icon' => 'icon-money'
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->trans('Country', array(), 'Admin.Global'),
                    'name' => 'country',
                    'id' => 'country',
                    'options' => array(
                        'query' => Country::getCountries($this->context->language->id),
                        'id' => 'id_country',
                        'name' => 'name',
                        'default' => array(
                            'value' => 0,
                            'label' => $this->trans('All', array(), 'Admin.Global')
                        )
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->trans('State', array(), 'Admin.Global'),
                    'name' => 'states[]',
                    'id' => 'states',
                    'multiple' => true,
                    'options' => array(
                        'query' => array(),
                        'id' => 'id_state',
                        'name' => 'name',
                        'default' => array(
                            'value' => 0,
                            'label' => $this->trans('All', array(), 'Admin.Global')
                        )
                    )
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'action'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Zip/postal code range', array(), 'Admin.International.Feature'),
                    'name' => 'zipcode',
                    'required' => false,
                    'hint' => $this->trans('You can define a range of Zip/postal codes (e.g., 75000-75015) or simply use one Zip/postal code.', array(), 'Admin.International.Help')
                ),
                array(
                    'type' => 'select',
                    'label' => $this->trans('Behavior', array(), 'Admin.International.Feature'),
                    'name' => 'behavior',
                    'required' => false,
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 0,
                                'name' => $this->trans('This tax only', array(), 'Admin.International.Feature')
                            ),
                            array(
                                'id' => 1,
                                'name' => $this->trans('Combine', array(), 'Admin.International.Feature')
                            ),
                            array(
                                'id' => 2,
                                'name' => $this->trans('One after another', array(), 'Admin.International.Feature')
                            )
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'hint' => array(
                        $this->trans('You must define the behavior if an address matches multiple rules:', array(), 'Admin.International.Help').'<br>',
                        $this->trans('- This tax only: Will apply only this tax', array(), 'Admin.International.Help').'<br>',
                        $this->trans('- Combine: Combine taxes (e.g.: 10% + 5% = 15%)', array(), 'Admin.International.Help').'<br>',
                        $this->trans('- One after another: Apply taxes one after another (e.g.: 100 + 10% => 110 + 5% = 115.5)', array(), 'Admin.International.Help')
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->trans('Tax', array(), 'Admin.Global'),
                    'name' => 'id_tax',
                    'required' => false,
                    'options' => array(
                        'query' => Tax::getTaxes((int)$this->context->language->id),
                        'id' => 'id_tax',
                        'name' => 'name',
                        'default' => array(
                            'value' => 0,
                            'label' => $this->trans('No Tax', array(), 'Admin.International.Help')
                        )
                    ),
                    'hint' => $this->trans('(Total tax: 9%)', array(), 'Admin.International.Help'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Description', array(), 'Admin.Global'),
                    'name' => 'description',
                )
            ),
            'submit' => array(
                'title' => $this->trans('Save and stay', array(), 'Admin.Actions'),
                'stay' => true
            )
        );

        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $this->fields_value = array(
            'action' => 'create_rule',
            'id_tax_rules_group' => $obj->id,
            'id_tax_rule' => ''
        );

        $this->getlanguages();
        $helper = new HelperForm();
        $helper->override_folder = $this->tpl_folder;
        $helper->currentIndex = self::$currentIndex;
        $helper->token = $this->token;
        $helper->table = 'tax_rule';
        $helper->identifier = 'id_tax_rule';
        $helper->id = $obj->id;
        $helper->toolbar_scroll = true;
        $helper->show_toolbar = true;
        $helper->languages = $this->_languages;
        $helper->default_form_language = $this->default_form_language;
        $helper->allow_employee_form_lang = $this->allow_employee_form_lang;
        $helper->fields_value = $this->getFieldsValue($this->object);
        $helper->toolbar_btn['save_new_rule'] = array(
            'href' => self::$currentIndex.'&amp;id_tax_rules_group='.$obj->id.'&amp;action=create_rule&amp;token='.$this->token,
            'desc' => 'Save tax rule',
            'class' => 'process-icon-save'
        );
        $helper->submit_action = 'create_rule';

        return $helper->generateForm($this->fields_form);
    }


    public function initProcess()
    {
        if (Tools::isSubmit('deletetax_rule')) {
            if ($this->access('delete')) {
                $this->action = 'delete_tax_rule';
            } else {
                $this->errors[] = $this->trans('You do not have permission to delete this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('submitBulkdeletetax_rule')) {
            if ($this->access('delete')) {
                $this->action = 'bulk_delete_tax_rules';
            } else {
                $this->errors[] = $this->trans('You do not have permission to delete this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (Tools::getValue('action') == 'create_rule') {
            if ($this->access('add')) {
                $this->action = 'create_rule';
            } else {
                $this->errors[] = $this->trans('You do not have permission to add this.', array(), 'Admin.Notifications.Error');
            }
        } else {
            parent::initProcess();
        }
    }

    protected function processCreateRule()
    {
        $zip_code = Tools::getValue('zipcode');
        $zip_code = ("" === $zip_code) ? 0 : $zip_code;
        $id_rule = (int)Tools::getValue('id_tax_rule');
        $id_tax = (int)Tools::getValue('id_tax');
        $id_tax_rules_group = (int)Tools::getValue('id_tax_rules_group');
        $behavior = (int)Tools::getValue('behavior');
        $description = pSQL(Tools::getValue('description'));

        if ((int)($id_country = Tools::getValue('country')) == 0) {
            $countries = Country::getCountries($this->context->language->id);
            $this->selected_countries = array();
            foreach ($countries as $country) {
                $this->selected_countries[] = (int)$country['id_country'];
            }
        } else {
            $this->selected_countries = array($id_country);
        }
        $this->selected_states = Tools::getValue('states');

        if (empty($this->selected_states) || count($this->selected_states) == 0) {
            $this->selected_states = array(0);
        }
        $tax_rules_group = new TaxRulesGroup((int)$id_tax_rules_group);
        foreach ($this->selected_countries as $id_country) {
            $first = true;
            foreach ($this->selected_states as $id_state) {
                if ($tax_rules_group->hasUniqueTaxRuleForCountry($id_country, $id_state, $id_rule)) {
                    $this->errors[] = $this->trans('A tax rule already exists for this country/state with tax only behavior.', array(), 'Admin.International.Notification');
                    continue;
                }
                $tr = new TaxRule();

                // update or creation?
                if (isset($id_rule) && $first) {
                    $tr->id = $id_rule;
                    $first = false;
                }

                $tr->id_tax = $id_tax;
                $tax_rules_group = new TaxRulesGroup((int)$id_tax_rules_group);
                $tr->id_tax_rules_group = (int)$tax_rules_group->id;
                $tr->id_country = (int)$id_country;
                $tr->id_state = (int)$id_state;
                list($tr->zipcode_from, $tr->zipcode_to) = $tr->breakDownZipCode($zip_code);

                // Construct Object Country
                $country = new Country((int)$id_country, (int)$this->context->language->id);

                if ($zip_code && $country->need_zip_code) {
                    if ($country->zip_code_format) {
                        foreach (array($tr->zipcode_from, $tr->zipcode_to) as $zip_code) {
                            if ($zip_code) {
                                if (!$country->checkZipCode($zip_code)) {
                                    $this->errors[] = $this->trans(
                                        'The Zip/postal code is invalid. It must be typed as follows: %format% for %country%.',
                                        array(
                                            '%format%' => str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format))),
                                            '%country%' => $country->name
                                        ),
                                        'Admin.International.Notification'
                                    );
                                }
                            }
                        }
                    }
                }

                $tr->behavior = (int)$behavior;
                $tr->description = $description;
                $this->tax_rule = $tr;
                $_POST['id_state'] = $tr->id_state;

                $this->errors = array_merge($this->errors, $this->validateTaxRule($tr));

                if (count($this->errors) == 0) {
                    $tax_rules_group = $this->updateTaxRulesGroup($tax_rules_group);
                    $tr->id = (int)$tax_rules_group->getIdTaxRuleGroupFromHistorizedId((int)$tr->id);
                    $tr->id_tax_rules_group = (int)$tax_rules_group->id;

                    if (!$tr->save()) {
                        $this->errors[] = $this->trans('An error has occurred: Cannot save the current tax rule.', array(), 'Admin.International.Notification');
                    }
                }
            }
        }

        if (count($this->errors) == 0) {
            Tools::redirectAdmin(
                self::$currentIndex.'&'.$this->identifier.'='.(int)$tax_rules_group->id.'&conf=4&update'.$this->table.'&token='.$this->token
            );
        } else {
            $this->display = 'edit';
        }
    }

    protected function processBulkDeleteTaxRules()
    {
        $this->deleteTaxRule(Tools::getValue('tax_ruleBox'));
    }

    protected function processDeleteTaxRule()
    {
        $this->deleteTaxRule(array(Tools::getValue('id_tax_rule')));
    }

    protected function deleteTaxRule(array $id_tax_rule_list)
    {
        $result = true;

        foreach ($id_tax_rule_list as $id_tax_rule) {
            $tax_rule = new TaxRule((int)$id_tax_rule);
            if (Validate::isLoadedObject($tax_rule)) {
                $tax_rules_group = new TaxRulesGroup((int)$tax_rule->id_tax_rules_group);
                $tax_rules_group = $this->updateTaxRulesGroup($tax_rules_group);
                $tax_rule = new TaxRule($tax_rules_group->getIdTaxRuleGroupFromHistorizedId((int)$id_tax_rule));
                if (Validate::isLoadedObject($tax_rule)) {
                    $result &= $tax_rule->delete();
                }
            }
        }

        Tools::redirectAdmin(
            self::$currentIndex.'&'.$this->identifier.'='.(int)$tax_rules_group->id.'&conf=4&update'.$this->table.'&token='.$this->token
        );
    }

    /**
     * Check if the tax rule could be added in the database
     *
     * @param TaxRule $tr
     *
     * @return array
     */
    protected function validateTaxRule(TaxRule $tr)
    {
        // @TODO: check if the rule already exists
        return $tr->validateController();
    }

    protected function displayAjaxUpdateTaxRule()
    {
        if ($this->access('view')) {
            $id_tax_rule = Tools::getValue('id_tax_rule');
            $tax_rules = new TaxRule((int)$id_tax_rule);
            $output = array();
            foreach ($tax_rules as $key => $result) {
                $output[$key] = $result;
            }
            die(json_encode($output));
        }
    }

    /**
     * @param TaxRulesGroup $object
     *
     * @return TaxRulesGroup
     */
    protected function updateTaxRulesGroup($object)
    {
        static $tax_rules_group = null;
        if ($tax_rules_group === null) {
            $object->update();
            $tax_rules_group = $object;
        }

        return $tax_rules_group;
    }
}
