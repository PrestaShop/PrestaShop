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

/**
 * @property Tax $object
 */
class AdminTaxesControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'tax';
        $this->className = 'Tax';
        $this->lang = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        parent::__construct();

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash'
            )
        );

        $this->fields_list = array(
            'id_tax' => array('title' => $this->trans('ID', array(), 'Admin.Global'), 'align' => 'center', 'class' => 'fixed-width-xs'),
            'name' => array('title' => $this->trans('Name', array(), 'Admin.Global'), 'width' => 'auto'),
            'rate' => array('title' => $this->trans('Rate', array(), 'Admin.International.Feature'), 'align' => 'center', 'suffix' => '%' , 'class' => 'fixed-width-md'),
            'active' => array('title' => $this->trans('Enabled', array(), 'Admin.Global'), 'width' => 25, 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false, 'class' => 'fixed-width-sm', 'remove_onclick' => true)
            );

        $ecotax_desc = '';
        if (Configuration::get('PS_USE_ECOTAX')) {
            $ecotax_desc = $this->trans('If you disable the ecotax, the ecotax for all your products will be set to 0.', array(), 'Admin.International.Help');
        }

        $this->fields_options = array(
            'general' => array(
                'title' =>    $this->trans('Tax options', array(), 'Admin.International.Feature'),
                'fields' =>    array(
                    'PS_TAX' => array(
                        'title' => $this->trans('Enable tax', array(), 'Admin.International.Feature'),
                        'desc' => $this->trans('Select whether or not to include tax on purchases.', array(), 'Admin.International.Help'),
                        'cast' => 'intval', 'type' => 'bool'),
                    'PS_TAX_DISPLAY' => array(
                        'title' => $this->trans('Display tax in the shopping cart', array(), 'Admin.International.Feature'),
                        'desc' => $this->trans('Select whether or not to display tax on a distinct line in the cart.', array(), 'Admin.International.Help'),
                        'cast' => 'intval',
                        'type' => 'bool'),
                    'PS_TAX_ADDRESS_TYPE' => array(
                        'title' => $this->trans('Based on', array(), 'Admin.International.Feature'),
                        'cast' => 'pSQL',
                        'type' => 'select',
                        'list' => array(
                            array(
                                'name' => $this->trans('Invoice address', array(), 'Admin.International.Feature'),
                                'id' => 'id_address_invoice'
                                ),
                            array(
                                'name' => $this->trans('Delivery address', array(), 'Admin.International.Feature'),
                                'id' => 'id_address_delivery')
                                ),
                        'identifier' => 'id'
                        ),
                    'PS_USE_ECOTAX' => array(
                        'title' => $this->trans('Use ecotax', array(), 'Admin.International.Feature'),
                        'desc' => $ecotax_desc,
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                        ),
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions'))
            ),
        );

        if (Configuration::get('PS_USE_ECOTAX') || Tools::getValue('PS_USE_ECOTAX')) {
            $this->fields_options['general']['fields']['PS_ECOTAX_TAX_RULES_GROUP_ID'] = array(
                'title' => $this->trans('Ecotax', array(), 'Admin.International.Feature'),
                'hint' => $this->trans('Define the ecotax (e.g. French ecotax: 19.6%).', array(), 'Admin.International.Help'),
                'cast' => 'intval',
                'type' => 'select',
                'identifier' => 'id_tax_rules_group',
                'list' => TaxRulesGroup::getTaxRulesGroupsForOptions()
                );
        }

        $this->_where .= ' AND a.deleted = 0';
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_tax'] = array(
                'href' => self::$currentIndex.'&addtax&token='.$this->token,
                'desc' => $this->trans('Add new tax', array(), 'Admin.International.Feature'),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    /**
     * Display delete action link
     *
     * @param string|null $token
     * @param int         $id
     *
     * @return string
     * @throws Exception
     * @throws SmartyException
     */
    public function displayDeleteLink($token = null, $id)
    {
        if (!array_key_exists('Delete', self::$cache_lang)) {
            self::$cache_lang['Delete'] = $this->trans('Delete', array(), 'Admin.Actions');
        }

        if (!array_key_exists('DeleteItem', self::$cache_lang)) {
            self::$cache_lang['DeleteItem'] = $this->trans('Delete item #', array(), 'Admin.International.Feature');
        }

        if (TaxRule::isTaxInUse($id)) {
            $confirm = $this->trans('This tax is currently in use as a tax rule. Are you sure you\'d like to continue?', array(), 'Admin.International.Notification');
        }

        $this->context->smarty->assign(array(
            'href' => self::$currentIndex.'&'.$this->identifier.'='.$id.'&delete'.$this->table.'&token='.($token != null ? $token : $this->token),
            'confirm' => (isset($confirm) ? '\r'.$confirm : self::$cache_lang['DeleteItem'].$id.' ? '),
            'action' => self::$cache_lang['Delete'],
        ));

        return $this->context->smarty->fetch('helpers/list/list_action_delete.tpl');
    }

    /**
     * Fetch the template for action enable
     *
     * @param string $token
     * @param int $id
     * @param int $value state enabled or not
     * @param string $active status
     * @param int $id_category
     * @param int $id_product
     */
    public function displayEnableLink($token, $id, $value, $active, $id_category = null, $id_product = null)
    {
        if ($value && TaxRule::isTaxInUse($id)) {
            $confirm = $this->trans('This tax is currently in use as a tax rule. If you continue, this tax will be removed from the tax rule. Are you sure you\'d like to continue?', array(), 'Admin.International.Notification');
        }
        $tpl_enable = $this->context->smarty->createTemplate('helpers/list/list_action_enable.tpl');
        $tpl_enable->assign(array(
            'enabled' => (bool)$value,
            'url_enable' => self::$currentIndex.'&'.$this->identifier.'='.(int)$id.'&'.$active.$this->table.
                ((int)$id_category && (int)$id_product ? '&id_category='.(int)$id_category : '').'&token='.($token != null ? $token : $this->token),
            'confirm' => isset($confirm) ? $confirm : null,
        ));

        return $tpl_enable->fetch();
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('Taxes', array(), 'Admin.Global'),
                'icon' => 'icon-money'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->trans('Name', array(), 'Admin.Global'),
                    'name' => 'name',
                    'required' => true,
                    'lang' => true,
                    'hint' => $this->trans('Tax name to display in carts and on invoices (e.g. "VAT").', array(), 'Admin.International.Help').' - '.$this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' <>;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Rate', array(), 'Admin.International.Feature'),
                    'name' => 'rate',
                    'maxlength' => 6,
                    'required' => true,
                    'hint' => $this->trans('Format: XX.XX or XX.XXX (e.g. 19.60 or 13.925)', array(), 'Admin.International.Help').' - '.$this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' <>;=#{}'
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
                'title' => $this->trans('Save', array(), 'Admin.Actions')
            )
        );

        return parent::renderForm();
    }

    public function postProcess()
    {
        if ($this->action == 'save') {
            /* Checking fields validity */
            $this->validateRules();
            if (!count($this->errors)) {
                $id = (int)(Tools::getValue('id_'.$this->table));

                /* Object update */
                if (isset($id) && !empty($id)) {
                    /** @var Tax $object */
                    $object = new $this->className($id);
                    if (Validate::isLoadedObject($object)) {
                        $this->copyFromPost($object, $this->table);
                        $result = $object->update(false, false);

                        if (!$result) {
                            $this->errors[] = $this->trans('An error occurred while updating an object.', array(), 'Admin.Notifications.Error').' <b>'.$this->table.'</b>';
                        } elseif ($this->postImage($object->id)) {
                            Tools::redirectAdmin(self::$currentIndex.'&id_'.$this->table.'='.$object->id.'&conf=4'.'&token='.$this->token);
                        }
                    } else {
                        $this->errors[] = $this->trans('An error occurred while updating an object.', array(), 'Admin.Notifications.Error').' <b>'.$this->table.'</b> '.$this->trans('(cannot load object)', array(), 'Admin.Notifications.Error');
                    }
                }

                /* Object creation */
                else {
                    /** @var Tax $object */
                    $object = new $this->className();
                    $this->copyFromPost($object, $this->table);
                    if (!$object->add()) {
                        $this->errors[] = $this->trans('An error occurred while creating an object.', array(), 'Admin.Notifications.Error').' <b>'.$this->table.'</b>';
                    } elseif (($_POST['id_'.$this->table] = $object->id /* voluntary */) && $this->postImage($object->id) && $this->_redirect) {
                        Tools::redirectAdmin(self::$currentIndex.'&id_'.$this->table.'='.$object->id.'&conf=3'.'&token='.$this->token);
                    }
                }
            }
        } else {
            parent::postProcess();
        }
    }

    public function updateOptionPsUseEcotax($value)
    {
        $old_value = (int)Configuration::get('PS_USE_ECOTAX');

        if ($old_value != $value) {
            // Reset ecotax
            if ($value == 0) {
                Product::resetEcoTax();
            }

            Configuration::updateValue('PS_USE_ECOTAX', (int)$value);
        }
    }
}
