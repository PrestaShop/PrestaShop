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

class AdminPaymentControllerCore extends AdminController
{
    public $payment_modules = array();

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
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
        $this->toolbar_title = $this->trans('Payment', array(), 'Admin.Global');
        unset($this->toolbar_btn['back']);

        $shop_context = (!Shop::isFeatureActive() || Shop::getContext() == Shop::CONTEXT_SHOP);
        if (!$shop_context) {
            $this->tpl_view_vars = array('shop_context' => $shop_context);
            return parent::renderView();
        }

        $this->tpl_view_vars = array(
            'modules_list' => $this->renderModulesList('back-office,AdminPayment,index'),
            'ps_base_uri' => __PS_BASE_URI__,
            'url_submit' => self::$currentIndex.'&token='.$this->token,
            'shop_context' => $shop_context
        );

        return parent::renderView();
    }

    public function renderModulesList($tracking_source = false)
    {
        if ($this->getModulesList($this->filter_modules_list, $tracking_source)) {
            $active_list = array();
            $unactive_list = array();
            foreach ($this->modules_list as $key => $module) {
                if (in_array($module->name, $this->list_partners_modules)) {
                    $this->modules_list[$key]->type = 'addonsPartner';
                }
                if (isset($module->description_full) && trim($module->description_full) != '') {
                    $module->show_quick_view = true;
                }

                // Remove all options but 'configure' and install
                // All other operation should take place in new Module page
                if (($module->installed && $module->active) || !$module->installed) {
                    // Unfortunately installed but disabled module will have $module->installed = false
                    if (strstr($module->optionsHtml[0], 'enable=1')) {
                        $module->optionsHtml = array();
                    } else {
                        $module->optionsHtml = array($module->optionsHtml[0]);
                    }
                } else {
                    $module->optionsHtml = array();
                }

                if ($module->active) {
                    $active_list[] = $module;
                } else {
                    $unactive_list[] = $module;
                }
            }

            $helper = new Helper();
            $fetch = '';

            if (isset($active_list)) {
                $this->context->smarty->assign('panel_title', $this->trans('Active payment', array(), 'Admin.Payment.Feature'));
                $fetch = $helper->renderModulesList($active_list);
            }

            $this->context->smarty->assign(array(
                'panel_title' => $this->trans('Recommended payment gateways', array(), 'Admin.Payment.Feature'),
                'view_all' => true
            ));
            $fetch .= $helper->renderModulesList($unactive_list);
            return $fetch;
        }
    }
}
