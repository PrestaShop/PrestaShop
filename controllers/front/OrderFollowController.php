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
use PrestaShop\PrestaShop\Adapter\Order\OrderReturnPresenter;

class OrderFollowControllerCore extends FrontController
{
    public $auth = true;
    public $php_self = 'order-follow';
    public $authRedirection = 'order-follow';
    public $ssl = true;

    /**
     * Start forms process.
     *
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitReturnMerchandise')) {
            $customizationQtyInput = Tools::getValue('customization_qty_input');
            $order_qte_input = Tools::getValue('order_qte_input');
            $customizationIds = Tools::getValue('customization_ids');

            if (!$id_order = (int) Tools::getValue('id_order')) {
                Tools::redirect('index.php?controller=history');
            }
            if (!($ids_order_detail = Tools::getValue('ids_order_detail')) && !$customizationQtyInput && !$customizationIds) {
                Tools::redirect('index.php?controller=order-detail&id_order='.$id_order.'&errorDetail1');
            }
            if (!$customizationIds && !$order_qte_input) {
                Tools::redirect('index.php?controller=order-detail&id_order='.$id_order.'&errorDetail2');
            }

            $order = new Order((int) $id_order);
            if (!$order->isReturnable()) {
                Tools::redirect('index.php?controller=order-detail&id_order='.$id_order.'&errorNotReturnable');
            }
            if ($order->id_customer != $this->context->customer->id) {
                die(Tools::displayError());
            }
            $orderReturn = new OrderReturn();
            $orderReturn->id_customer = (int) $this->context->customer->id;
            $orderReturn->id_order = $id_order;
            $orderReturn->question = htmlspecialchars(Tools::getValue('returnText'));
            if (empty($orderReturn->question)) {
                Tools::redirect('index.php?controller=order-detail&id_order='.$id_order.'&errorMsg&'.
                    http_build_query(array(
                        'ids_order_detail' => $ids_order_detail,
                        'order_qte_input' => $order_qte_input,
                        'id_order' => Tools::getValue('id_order'),
                    )));
            }

            if (!$orderReturn->checkEnoughProduct($ids_order_detail, $order_qte_input, $customizationIds, $customizationQtyInput)) {
                Tools::redirect('index.php?controller=order-detail&id_order='.$id_order.'&errorQuantity');
            }

            $orderReturn->state = 1;
            $orderReturn->add();
            $orderReturn->addReturnDetail($ids_order_detail, $order_qte_input, $customizationIds, $customizationQtyInput);
            Hook::exec('actionOrderReturn', array('orderReturn' => $orderReturn));
            Tools::redirect('index.php?controller=order-follow');
        }
    }

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        if (Configuration::isCatalogMode()) {
            Tools::redirect('index.php');
        }

        $ordersReturn = $this->getTemplateVarOrdersReturns();
        if (count($ordersReturn) <= 0) {
            $this->errors[] = $this->trans(
                'You have no merchandise return authorizations.', array(), 'Shop.Notifications.Error'
            );
        }

        $this->context->smarty->assign('ordersReturn', $ordersReturn);

        parent::initContent();
        $this->setTemplate('customer/order-follow');
    }

    public function getTemplateVarOrdersReturns()
    {
        $orders_returns = array();
        $orders_return = OrderReturn::getOrdersReturn($this->context->customer->id);

        $orderReturnPresenter = new OrderReturnPresenter(
            Configuration::get('PS_RETURN_PREFIX', $this->context->language->id),
            $this->context->link
        );

        foreach ($orders_return as $id_order_return => $order_return) {
            $orders_returns[$id_order_return] = $orderReturnPresenter->present($order_return);
        }

        return $orders_returns;
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        return $breadcrumb;
    }
}
