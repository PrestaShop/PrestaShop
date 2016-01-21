<?php
/**
 * 2007-2015 PrestaShop
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class OrderSlipControllerCore extends FrontController
{
    public $auth = true;
    public $php_self = 'order-slip';
    public $authRedirection = 'order-slip';
    public $ssl = true;

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $credit_slips = $this->getTemplateVarCreditSlips();

        if (count($credit_slips) <= 0) {
            $this->warning[] = $this->l('You have not received any credit slips.');
        }

        $this->context->smarty->assign([
            'credit_slips' => $credit_slips,
        ]);
        $this->setTemplate('customer/order-slip.tpl');
    }

    public function getTemplateVarCreditSlips()
    {
        $credit_slips = [];
        $orders_slip = OrderSlip::getOrdersSlip(((int)$this->context->cookie->id_customer));

        foreach ($orders_slip as $order_slip) {
            $credit_slips[$order_slip['id_order_slip']] = $order_slip;
            $credit_slips[$order_slip['id_order_slip']]['credit_slip_number'] = sprintf($this->l('#%06d'), $order_slip['id_order_slip']);
            $credit_slips[$order_slip['id_order_slip']]['order_number'] = sprintf($this->l('#%06d'), $order_slip['id_order']);
            $credit_slips[$order_slip['id_order_slip']]['credit_slip_date'] = Tools::displayDate($order_slip['date_add'], null, false);
            $credit_slips[$order_slip['id_order_slip']]['url'] = $this->context->link->getPageLink('pdf-order-slip', true, null, 'id_order_slip='.(int)$order_slip['id_order_slip']);
        }

        return $credit_slips;
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        return $breadcrumb;
    }
}
