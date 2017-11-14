<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

class AdminPaymentPreferencesControllerCore extends AdminController
{
    public $payment_modules = array();

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();

        $id_shop = Context::getContext()->shop->id;

        /* Get all modules then select only payment ones */
        $modules = Module::getModulesOnDisk(true);
        $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $moduleRepository = $moduleManagerBuilder->buildRepository();

        foreach ($modules as $module) {
            $addonModule = $moduleRepository->getModule($module->name);
            if ($addonModule->attributes->get('is_paymentModule')) {
                if ($module->id) {
                    if (!get_class($module) == 'SimpleXMLElement') {
                        $module->country = array();
                    }

                    $sql = new DbQuery();
                    $sql->select('`id_country`');
                    $sql->from('module_country');
                    $sql->where('`id_module` = '.(int)$module->id);
                    $sql->where('`id_shop` = '.(int)$id_shop);
                    $countries = Db::getInstance()->executeS($sql);
                    foreach ($countries as $country) {
                        $module->country[] = $country['id_country'];
                    }

                    if (!get_class($module) == 'SimpleXMLElement') {
                        $module->currency = array();
                    }

                    $sql = new DbQuery();
                    $sql->select('`id_currency`');
                    $sql->from('module_currency');
                    $sql->where('`id_module` = '.(int)$module->id);
                    $sql->where('`id_shop` = '.(int)$id_shop);
                    $currencies = Db::getInstance()->executeS($sql);
                    foreach ($currencies as $currency) {
                        $module->currency[] = $currency['id_currency'];
                    }

                    if (!get_class($module) == 'SimpleXMLElement') {
                        $module->group = array();
                    }

                    $sql = new DbQuery();
                    $sql->select('`id_group`');
                    $sql->from('module_group');
                    $sql->where('`id_module` = '.(int)$module->id);
                    $sql->where('`id_shop` = '.(int)$id_shop);
                    $groups = Db::getInstance()->executeS($sql);
                    foreach ($groups as $group) {
                        $module->group[] = $group['id_group'];
                    }

                    if (!get_class($module) == 'SimpleXMLElement') {
                        $module->reference = array();
                    }
                    $sql = new DbQuery();
                    $sql->select('`id_reference`');
                    $sql->from('module_carrier');
                    $sql->where('`id_module` = '.(int)$module->id);
                    $sql->where('`id_shop` = '.(int)$id_shop);
                    $carriers = Db::getInstance()->executeS($sql);
                    foreach ($carriers as $carrier) {
                        $module->reference[] = $carrier['id_reference'];
                    }
                } else {
                    $module->country = null;
                    $module->currency = null;
                    $module->group = null;
                }

                $this->payment_modules[] = $module;
            }
        }
    }

    public function initToolbarTitle()
    {
        $this->toolbar_title = array_unique($this->breadcrumbs);
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        $this->page_header_toolbar_btn = array();
    }

    public function postProcess()
    {
        if (Tools::getValue('action') == 'GetModuleQuickView' && Tools::getValue('ajax') == '1') {
            $this->ajaxProcessGetModuleQuickView();
        }
        if ($this->action) {
            $this->saveRestrictions($this->action);
        }
    }

    public function initProcess()
    {
        if ($this->access('edit')) {
            if (Tools::isSubmit('submitModulecountry')) {
                $this->action = 'country';
            } elseif (Tools::isSubmit('submitModulecurrency')) {
                $this->action = 'currency';
            } elseif (Tools::isSubmit('submitModulegroup')) {
                $this->action = 'group';
            } elseif (Tools::isSubmit('submitModulereference')) {
                $this->action = 'carrier';
            }
        } else {
            $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
        }
    }


    protected function saveRestrictions($type)
    {
        // Delete type restrictions for active module.
        $modules = array();
        foreach ($this->payment_modules as $module) {
            if ($module->active) {
                $modules[] = (int)$module->id;
            }
        }

        Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'module_'.bqSQL($type).'`
			WHERE `id_shop` = '.Context::getContext()->shop->id.'
			AND `id_module` IN ('.implode(', ', $modules).')'
        );

        if ($type === 'carrier') {
            // Fill the new restriction selection for active module.
            $values = array();
            foreach ($this->payment_modules as $module) {
                if ($module->active && isset($_POST[$module->name.'_reference'])) {
                    foreach ($_POST[$module->name.'_reference'] as $selected) {
                        $values[] = '('.(int)$module->id.', '.(int)Context::getContext()->shop->id.', '.(int)$selected.')';
                    }
                }
            }

            if (count($values)) {
                Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'module_carrier`
				(`id_module`, `id_shop`, `id_reference`)
				VALUES '.implode(',', $values));
            }
        } else {
            // Fill the new restriction selection for active module.
            $values = array();
            foreach ($this->payment_modules as $module) {
                if ($module->active && isset($_POST[$module->name.'_'.$type.''])) {
                    foreach ($_POST[$module->name.'_'.$type.''] as $selected) {
                        $values[] = '('.(int)$module->id.', '.(int)Context::getContext()->shop->id.', '.(int)$selected.')';
                    }
                }
            }

            if (count($values)) {
                Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'module_'.bqSQL($type).'`
				(`id_module`, `id_shop`, `id_'.bqSQL($type).'`)
				VALUES '.implode(',', $values));
            }
        }

        Tools::redirectAdmin(self::$currentIndex.'&conf=4'.'&token='.$this->token);
    }

    public function initContent()
    {
        $this->display = 'view';
        return parent::initContent();
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryPlugin('fancybox');
    }

    public function renderView()
    {
        $this->toolbar_title = $this->trans('Payment preferences', array(), 'Admin.Payment.Feature'); // FIXME
        unset($this->toolbar_btn['back']);

        $shop_context = (!Shop::isFeatureActive() || Shop::getContext() == Shop::CONTEXT_SHOP);
        if (!$shop_context) {
            $this->tpl_view_vars = array('shop_context' => $shop_context);
            return parent::renderView();
        }

        $display_restrictions = false;
        foreach ($this->payment_modules as $module) {
            if ($module->active) {
                $display_restrictions = true;
                break;
            }
        }

        $lists = array(
                    array('items' => Currency::getCurrencies(),
                          'title' => $this->trans('Currency restrictions', array(), 'Admin.Payment.Feature'),
                          'desc' => $this->trans('Please mark each checkbox for the currency, or currencies, for which you want the payment module(s) to be available.', array(), 'Admin.Payment.Help'),
                          'name_id' => 'currency',
                          'identifier' => 'id_currency',
                          'icon' => 'icon-money',
                    ),
                    array('items' => Group::getGroups($this->context->language->id),
                          'title' => $this->trans('Group restrictions', array(), 'Admin.Payment.Feature'),
                          'desc' => $this->trans('Please mark each checkbox for the customer group(s), for which you want the payment module(s) to be available.', array(), 'Admin.Payment.Help'),
                          'name_id' => 'group',
                          'identifier' => 'id_group',
                          'icon' => 'icon-group',
                    ),
                    array('items' =>Country::getCountries($this->context->language->id),
                          'title' => $this->trans('Country restrictions', array(), 'Admin.Payment.Feature'),
                          'desc' => $this->trans('Please mark each checkbox for the country, or countries, in which you want the payment module(s) to be available.', array(), 'Admin.Payment.Help'),
                          'name_id' => 'country',
                          'identifier' => 'id_country',
                          'icon' => 'icon-globe',
                    ),
                    array('items' => Carrier::getCarriers($this->context->language->id, false, false, false, null, Carrier::ALL_CARRIERS),
                        'title' => $this->trans('Carrier restrictions', array(), 'Admin.Payment.Feature'),
                        'desc' => $this->trans('Please mark each checkbox for the carrier, or carrier, for which you want the payment module(s) to be available.', array(), 'Admin.Payment.Help'),
                        'name_id' => 'reference',
                        'identifier' => 'id_reference',
                        'icon' => 'icon-truck',
                    )
                );

        foreach ($lists as $key_list => $list) {
            $list['check_list'] = array();
            foreach ($list['items'] as $key_item => $item) {
                $name_id = $list['name_id'];

                if ($name_id === 'currency'
                    && Tools::strpos($list['items'][$key_item]['name'], '('.$list['items'][$key_item]['iso_code'].')') === false) {
                    $list['items'][$key_item]['name'] = sprintf('%1$s (%2$s)', $list['items'][$key_item]['name'],
                        $list['items'][$key_item]['iso_code']);
                }

                foreach ($this->payment_modules as $key_module => $module) {
                    if (isset($module->$name_id) && in_array($item['id_'.$name_id], $module->$name_id)) {
                        $list['items'][$key_item]['check_list'][$key_module] = 'checked';
                    } else {
                        $list['items'][$key_item]['check_list'][$key_module] = 'unchecked';
                    }

                    if (!isset($module->$name_id)) {
                        $module->$name_id = array();
                    }
                    if (!isset($module->currencies_mode)) {
                        $module->currencies_mode = '';
                    }
                    if (!isset($module->currencies)) {
                        $module->currencies = '';
                    }

                    // If is a country list and the country is limited, remove it from list
                    if ($name_id == 'country'
                        && isset($module->limited_countries)
                        && !empty($module->limited_countries)
                        && is_array($module->limited_countries)
                        && !(in_array(strtoupper($item['iso_code']), array_map('strtoupper', $module->limited_countries)))) {
                        $list['items'][$key_item]['check_list'][$key_module] = null;
                    }
                }
            }
            // update list
            $lists[$key_list] = $list;
        }

        $this->tpl_view_vars = array(
            'modules_list' => $this->renderModulesList('back-office,AdminPayment,index'),
            'display_restrictions' => $display_restrictions,
            'lists' => $lists,
            'ps_base_uri' => __PS_BASE_URI__,
            'payment_modules' => $this->payment_modules,
            'url_submit' => self::$currentIndex.'&token='.$this->token,
            'shop_context' => $shop_context
        );

        return parent::renderView();
    }
}
