<?php
/**
 * 2007-2018 PrestaShop.
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
 * @property Currency $object
 */
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

class AdminCurrenciesControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'currency';
        $this->className = 'Currency';
        $this->lang = false;
        $this->cldr = Tools::getCldr(Context::getContext());

        parent::__construct();

        $this->fields_list = array(
            'name' => array('title' => $this->trans('Currency', array(), 'Admin.Global'), 'orderby' => false, 'search' => false),
            'sign' => array('title' => $this->trans('Symbol', array(), 'Admin.International.Feature'), 'width' => 20, 'align' => 'center', 'orderby' => false, 'search' => false, 'class' => 'fixed-width-xs'),
            'iso_code' => array('title' => $this->trans('ISO code', array(), 'Admin.International.Feature'), 'align' => 'center', 'class' => 'fixed-width-xs'),
            'conversion_rate' => array('title' => $this->trans('Exchange rate', array(), 'Admin.International.Feature'), 'type' => 'float', 'align' => 'center', 'width' => 130, 'search' => false, 'filter_key' => 'currency_shop!conversion_rate'),
            'active' => array('title' => $this->trans('Enabled', array(), 'Admin.Global'), 'width' => 25, 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false, 'class' => 'fixed-width-sm'),
        );

        $this->_select .= 'currency_shop.conversion_rate conversion_rate';
        $this->_join .= Shop::addSqlAssociation('currency', 'a');
        $this->_group .= 'GROUP BY a.id_currency';
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->_where = 'AND a.`deleted` = 0';

        //retrieve datas list
        $this->getList($this->context->language->id);

        foreach ($this->_list as $k => $v) {
            $currency = $this->cldr->getCurrency($this->_list[$k]['iso_code']);

            $this->_list[$k]['name'] = ucfirst($currency['name']);
            $this->_list[$k]['sign'] = $currency['symbol'];
            $this->_list[$k]['iso_code'] .= ' / ' . $currency['iso_code'];
        }

        $helper = new HelperList();
        $this->setHelperDisplay($helper);

        return $helper->generateList($this->_list, $this->fields_list);
    }

    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        parent::getList($id_lang, $order_by, $order_way, $start, $limit, Context::getContext()->shop->id);
    }

    public function renderForm()
    {
        $currency = null;

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('Currencies', array(), 'Admin.Global'),
                'icon' => 'icon-money',
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'col' => '4',
                    'label' => $this->trans('Currency', array(), 'Admin.Global'),
                    'name' => 'iso_code',
                    'required' => true,
                    'hint' => $this->trans('ISO code (e.g. USD for Dollars, EUR for Euros, etc.).', array(), 'Admin.International.Help'),
                    'options' => array(
                        'query' => $this->cldr->getAllCurrencies(),
                        'name' => 'name',
                        'id' => 'code',
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Exchange rate', array(), 'Admin.International.Feature'),
                    'name' => 'conversion_rate',
                    'maxlength' => 11,
                    'required' => true,
                    'col' => '2',
                    'hint' => $this->trans('Exchange rates are calculated from one unit of your shop\'s default currency. For example, if the default currency is euros and your chosen currency is dollars, type "1.20" (1&euro; = $1.20).', array(), 'Admin.International.Help'),
                ),
                array(
                    'type' => 'hidden',
                    'label' => $this->trans('Enable', array(), 'Admin.Actions'),
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->trans('Enabled', array(), 'Admin.Global'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->trans('Disabled', array(), 'Admin.Global'),
                        ),
                    ),
                ),
            ),
        );

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->trans('Shop association', array(), 'Admin.Global'),
                'name' => 'checkBoxShopAsso',
            );
        }

        $this->fields_form['submit'] = array(
            'title' => $this->trans('Save', array(), 'Admin.Actions'),
        );

        //form preselect : define the default currency or object value
        if (Tools::getValue('id_currency')) {
            $currency = new Currency((int) Tools::getValue('id_currency'));
            if ($currency) {
                $this->fields_value = array(
                    'iso_code' => $currency->iso_code,
                );
            }
        } else {
            $this->fields_value = array(
                'iso_code' => $this->cldr->getCurrency()['code'],
            );
        }

        $this->context->smarty->assign('status', $currency ? $currency->active : 0);
        $this->context->smarty->assign('isForm', true);

        return parent::renderForm();
    }

    protected function checkDeletion($object)
    {
        if (Validate::isLoadedObject($object)) {
            if ($object->id == Configuration::get('PS_CURRENCY_DEFAULT')) {
                $this->errors[] = $this->trans('You cannot delete the default currency', array(), 'Admin.International.Notification');
            } else {
                return true;
            }
        } else {
            $this->errors[] = $this->trans('An error occurred while deleting the object.', array(), 'Admin.Notifications.Error') . '
                <b>' . $this->table . '</b> ' . $this->trans('(cannot load object)', array(), 'Admin.Notifications.Error');
        }

        return false;
    }

    protected function checkDisableStatus($object)
    {
        if (Validate::isLoadedObject($object)) {
            if ($object->active && $object->id == Configuration::get('PS_CURRENCY_DEFAULT')) {
                $this->errors[] = $this->trans('You cannot disable the default currency', array(), 'Admin.International.Notification');
            } else {
                return true;
            }
        } else {
            $this->errors[] = $this->trans('An error occurred while updating the status for an object.', array(), 'Admin.Notifications.Error') . '
				<b>' . $this->table . '</b> ' . $this->trans('(cannot load object)', array(), 'Admin.Notifications.Error');
        }

        return false;
    }

    /**
     * @see AdminController::processDelete()
     */
    public function processDelete()
    {
        $object = $this->loadObject();
        if (!$this->checkDeletion($object)) {
            return false;
        }

        return parent::processDelete();
    }

    protected function processBulkDelete()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id_currency) {
                $object = new Currency((int) $id_currency);
                if (!$this->checkDeletion($object)) {
                    return false;
                }
            }
        }

        return parent::processBulkDelete();
    }

    /**
     * @see AdminController::processStatus()
     */
    public function processStatus()
    {
        $object = $this->loadObject();
        if (!$this->checkDisableStatus($object)) {
            return false;
        }

        return parent::processStatus();
    }

    protected function processBulkDisableSelection()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id_currency) {
                $object = new Currency((int) $id_currency);
                if (!$this->checkDisableStatus($object)) {
                    return false;
                }
            }
        }

        return parent::processBulkDisableSelection();
    }

    /**
     * Update currency exchange rates.
     */
    public function processExchangeRates()
    {
        if (!$this->errors = Currency::refreshCurrencies()) {
            Tools::redirectAdmin(self::$currentIndex . '&conf=6&token=' . $this->token);
        }
    }

    /**
     * @see AdminController::initProcess()
     */
    public function initProcess()
    {
        if (Tools::isSubmit('SubmitExchangesRates')) {
            if ($this->access('edit')) {
                $this->action = 'exchangeRates';
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        }
        if (Tools::isSubmit('submitAddcurrency') && !Tools::getValue('id_currency') && Currency::exists(Tools::getValue('iso_code'))) {
            $this->errors[] = $this->trans('This currency already exists.', array(), 'Admin.International.Notification');
        }
        if (Tools::isSubmit('submitAddcurrency') && (float) Tools::getValue('conversion_rate') <= 0) {
            $this->errors[] = $this->trans('The currency conversion rate cannot be equal to 0.', array(), 'Admin.International.Notification');
        }
        parent::initProcess();
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_currency'] = array(
                'href' => self::$currentIndex . '&addcurrency&token=' . $this->token,
                'desc' => $this->trans('Add new currency', array(), 'Admin.International.Feature'),
                'icon' => 'process-icon-new',
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function ajaxProcessCronjobLiveExchangeRate()
    {
        $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $moduleManager = $moduleManagerBuilder->build();

        if (!$moduleManager->isInstalled('cronjobs')) {
            die(json_encode(array()));
        }

        $enable = (int) Tools::getValue('enable');
        $config = Configuration::get('PS_ACTIVE_CRONJOB_EXCHANGE_RATE', null, null, $this->context->shop->id);
        $cronJobUrl = 'http://' . ShopUrl::getMainShopDomain($this->context->shop->id) . __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/cron_currency_rates.php?secure_key=' . md5(_COOKIE_KEY_ . Configuration::get('PS_SHOP_NAME'));

        if ($config && $enable == 0) {
            Configuration::updateValue('PS_ACTIVE_CRONJOB_EXCHANGE_RATE', 0, false, null, $this->context->shop->id);
            Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'cronjobs WHERE `id_cronjob` = \'' . (int) $config . '\'');
        }

        //The cronjob is not defined, create it
        if ($enable == 1 && (!$config || $config == 0)) {
            $cronJobs = new CronJobs();
            $cronJobs->addOneShotTask(
                $cronJobUrl,
                $this->trans('Live exchange Rate for %shop_name%', array('%shop_name%' => Configuration::get('PS_SHOP_NAME')), 'Admin.International.Feature')
            );

            Configuration::updateValue('PS_ACTIVE_CRONJOB_EXCHANGE_RATE', Db::getInstance()->Insert_ID(), false, null, $this->context->shop->id);
        } else {
            $cronJob = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'cronjobs WHERE `id_cronjob` = \'' . (int) $config . '\'');

            //if cronjob do not exsit anymore OR cronjob dis disabled => disable conf
            if (!$cronJob || $cronJob[0]['active'] == 0) {
                Configuration::updateValue('PS_ACTIVE_CRONJOB_EXCHANGE_RATE', 0, false, null, $this->context->shop->id);
            }
        }

        die(json_encode(array()));
    }
}
