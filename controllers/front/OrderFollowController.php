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
use PrestaShop\PrestaShop\Adapter\Presenter\Order\OrderReturnPresenter;

class OrderFollowControllerCore extends FrontController
{
    /** @var bool */
    public $auth = true;
    /** @var string */
    public $php_self = 'order-follow';
    /** @var string */
    public $authRedirection = 'order-follow';
    /** @var bool */
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
                Tools::redirect('index.php?controller=order-detail&id_order=' . $id_order . '&errorDetail1');
            }
            if (!$customizationIds && !$order_qte_input) {
                Tools::redirect('index.php?controller=order-detail&id_order=' . $id_order . '&errorDetail2');
            }

            $order = new Order((int) $id_order);
            if (!$order->isReturnable()) {
                Tools::redirect('index.php?controller=order-detail&id_order=' . $id_order . '&errorNotReturnable');
            }
            if ($order->id_customer != $this->context->customer->id) {
                Tools::redirect('index.php?controller=order-detail&id_order=' . $id_order . '&errorNotReturnable');
            }
            $orderReturn = new OrderReturn();
            $orderReturn->id_customer = (int) $this->context->customer->id;
            $orderReturn->id_order = $id_order;
            $orderReturn->question = htmlspecialchars(Tools::getValue('returnText'));
            if (empty($orderReturn->question)) {
                Tools::redirect('index.php?controller=order-detail&id_order=' . $id_order . '&errorMsg&' .
                    http_build_query([
                        'ids_order_detail' => $ids_order_detail,
                        'order_qte_input' => $order_qte_input,
                        'id_order' => Tools::getValue('id_order'),
                    ]));
            }

            if (!$orderReturn->checkEnoughProduct($ids_order_detail, $order_qte_input, $customizationIds, $customizationQtyInput)) {
                Tools::redirect('index.php?controller=order-detail&id_order=' . $id_order . '&errorQuantity');
            }

            $orderReturn->state = 1;
            $orderReturn->add();
            $orderReturn->addReturnDetail($ids_order_detail, $order_qte_input, $customizationIds, $customizationQtyInput);
            Hook::exec('actionOrderReturn', ['orderReturn' => $orderReturn]);
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
        if ((bool) Configuration::get('PS_ORDER_RETURN') === false) {
            $this->redirect_after = '404';
            $this->redirect();
        }

        if (Configuration::isCatalogMode()) {
            Tools::redirect('index.php');
        }

        $this->context->smarty->assign('ordersReturn', $this->getTemplateVarOrdersReturns());

        parent::initContent();
        $this->setTemplate('customer/order-follow');
    }

    public function getTemplateVarOrdersReturns()
    {
        $orders_returns = [];
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
        $breadcrumb['links'][] = [
            'title' => $this->trans('Merchandise returns', [], 'Shop.Theme.Global'),
            'url' => $this->context->link->getPageLink('order-follow'),
        ];

        return $breadcrumb;
    }
}
