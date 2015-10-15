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

class OrderFollowControllerCore extends FrontController
{
    public $auth = true;
    public $php_self = 'order-follow';
    public $authRedirection = 'order-follow';
    public $ssl = true;

    /**
     * Start forms process
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitReturnMerchandise')) {
            $customizationQtyInput = Tools::getValue('customization_qty_input');
            $order_qte_input = Tools::getValue('order_qte_input');
            $customizationIds = Tools::getValue('customization_ids');

            if (!$id_order = (int)Tools::getValue('id_order')) {
                Tools::redirect('index.php?controller=history');
            }
            if (!$order_qte_input && !$customizationQtyInput && !$customizationIds) {
                Tools::redirect('index.php?controller=order-follow&errorDetail1');
            }
            if (!$customizationIds && !$ids_order_detail = Tools::getValue('ids_order_detail')) {
                Tools::redirect('index.php?controller=order-follow&errorDetail2');
            }

            $order = new Order((int)$id_order);
            if (!$order->isReturnable()) {
                Tools::redirect('index.php?controller=order-follow&errorNotReturnable');
            }
            if ($order->id_customer != $this->context->customer->id) {
                die(Tools::displayError());
            }
            $orderReturn = new OrderReturn();
            $orderReturn->id_customer = (int)$this->context->customer->id;
            $orderReturn->id_order = $id_order;
            $orderReturn->question = htmlspecialchars(Tools::getValue('returnText'));
            if (empty($orderReturn->question)) {
                Tools::redirect('index.php?controller=order-follow&errorMsg&'.
                    http_build_query(array(
                        'ids_order_detail' => $ids_order_detail,
                        'order_qte_input' => $order_qte_input,
                        'id_order' => Tools::getValue('id_order'),
                    )));
            }

            if (!$orderReturn->checkEnoughProduct($ids_order_detail, $order_qte_input, $customizationIds, $customizationQtyInput)) {
                Tools::redirect('index.php?controller=order-follow&errorQuantity');
            }

            $orderReturn->state = 1;
            $orderReturn->add();
            $orderReturn->addReturnDetail($ids_order_detail, $order_qte_input, $customizationIds, $customizationQtyInput);
            Hook::exec('actionOrderReturn', array('orderReturn' => $orderReturn));
            Tools::redirect('index.php?controller=order-follow');
        }
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $ordersReturn = $this->getTemplateVarOrdersReturns();
        if (Tools::isSubmit('errorQuantity')) {
            $this->errors[] = $this->l('You do not have enough products to request an additional merchandise return.');
        } elseif (Tools::isSubmit('errorMsg')) {
            $this->errors[] = $this->l('Please provide an explanation for your RMA.');
            $this->context->smarty->assign(
                array(
                    'errorMsg' => true,
                    'ids_order_detail' => Tools::getValue('ids_order_detail', array()),
                    'order_qte_input' => Tools::getValue('order_qte_input', array()),
                    'id_order' => (int)Tools::getValue('id_order'),
                )
            );
        } elseif (Tools::isSubmit('errorDetail1')) {
            $this->errors[] = $this->l('Please check at least one product you would like to return.');
        } elseif (Tools::isSubmit('errorDetail2')) {
            $this->errors[] = $this->l('For each product you wish to add, please specify the desired quantity.');
        } elseif (Tools::isSubmit('errorNotReturnable')) {
            $this->errors[] = $this->l('This order cannot be returned');
        } elseif (count($ordersReturn) <= 0) {
            $this->errors[] = $this->l('You have no merchandise return authorizations.');
        }

        $this->context->smarty->assign('ordersReturn', $ordersReturn);

        $this->setTemplate('customer/order-follow.tpl');
    }

    public function getTemplateVarOrdersReturns()
    {
        $orders_returns = [];
        $orders_return = OrderReturn::getOrdersReturn($this->context->customer->id);

        foreach ($orders_return as $id_order_return => $order_return) {
            $orders_returns[$id_order_return] = $order_return;
            $orders_returns[$id_order_return]['return_number'] = sprintf('#%06d', $order_return['id_order_return']);
            $orders_returns[$id_order_return]['return_date'] = Tools::displayDate($order_return['date_add'], null, false);
            $orders_returns[$id_order_return]['print_url'] = ($order_return['date_add'] == 2) ? $this->context->link->getPageLink('pdf-order-return', true, null, 'id_order_return='.$order_return['id_order_return']) : '';
            $orders_returns[$id_order_return]['details_url'] = $this->context->link->getPageLink('order-detail', true, null, 'id_order='.(int)$order_return['id_order']);
            $orders_returns[$id_order_return]['return_url'] = $this->context->link->getPageLink('order-return', true, null, 'id_order_return='.(int)$order_return['id_order_return']);
        }

        return $orders_returns;
    }
}
