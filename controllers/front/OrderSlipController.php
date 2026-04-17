<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class OrderSlipControllerCore extends FrontController
{
    /** @var bool */
    public $auth = true;
    /** @var string */
    public $php_self = 'order-slip';
    /** @var string */
    public $authRedirection = 'order-slip';
    /** @var bool */
    public $ssl = true;

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent(): void
    {
        if (Configuration::isCatalogMode()) {
            Tools::redirect('index.php');
        }

        $this->context->smarty->assign([
            'credit_slips' => $this->getTemplateVarCreditSlips(),
        ]);

        parent::initContent();
        $this->setTemplate('customer/order-slip');
    }

    public function getTemplateVarCreditSlips(): array
    {
        $credit_slips = [];
        $orders_slip = OrderSlip::getOrdersSlip((int) $this->context->cookie->id_customer);

        foreach ($orders_slip as $order_slip) {
            $order = new Order($order_slip['id_order']);
            $credit_slips[$order_slip['id_order_slip']] = $order_slip;
            $credit_slips[$order_slip['id_order_slip']]['credit_slip_number'] = $this->trans('#%id%', ['%id%' => $order_slip['id_order_slip']], 'Shop.Theme.Customeraccount');
            $credit_slips[$order_slip['id_order_slip']]['order_number'] = $this->trans('#%id%', ['%id%' => $order_slip['id_order']], 'Shop.Theme.Customeraccount');
            $credit_slips[$order_slip['id_order_slip']]['order_reference'] = $order->reference;
            $credit_slips[$order_slip['id_order_slip']]['credit_slip_date'] = Tools::displayDate($order_slip['date_add'], false);
            $credit_slips[$order_slip['id_order_slip']]['url'] = $this->context->link->getPageLink('pdf-order-slip', null, null, 'id_order_slip=' . (int) $order_slip['id_order_slip']);
            $credit_slips[$order_slip['id_order_slip']]['order_url_details'] = $this->context->link->getPageLink('order-detail', null, null, 'id_order=' . (int) $order_slip['id_order']);
        }

        return $credit_slips;
    }

    public function getBreadcrumbLinks(): array
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        $breadcrumb['links'][] = [
            'title' => $this->trans('Credit slips', [], 'Shop.Theme.Customeraccount'),
            'url' => $this->context->link->getPageLink('order-slip'),
        ];

        return $breadcrumb;
    }
}
