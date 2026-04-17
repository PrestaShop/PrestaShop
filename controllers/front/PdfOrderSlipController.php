<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class PdfOrderSlipControllerCore extends FrontController
{
    /** @var string */
    public $php_self = 'pdf-order-slip';
    /** @var bool */
    protected $display_header = false;
    /** @var bool */
    protected $display_footer = false;

    protected $order_slip;

    public function postProcess(): void
    {
        if (!$this->context->customer->isLogged()) {
            Tools::redirect($this->context->link->getPageLink(
                'authentication',
                null,
                null,
                ['back' => 'order-follow']
            ));
        }

        if (isset($_GET['id_order_slip']) && Validate::isUnsignedId($_GET['id_order_slip'])) {
            $this->order_slip = new OrderSlip($_GET['id_order_slip']);
        }

        if (!isset($this->order_slip) || !Validate::isLoadedObject($this->order_slip)) {
            die($this->trans('Order return not found.', [], 'Shop.Notifications.Error'));
        } elseif ($this->order_slip->id_customer != $this->context->customer->id) {
            die($this->trans('Order return not found.', [], 'Shop.Notifications.Error'));
        }
    }

    /**
     * @return void
     *
     * @throws PrestaShopException
     */
    public function display(): void
    {
        $pdf = new PDF($this->order_slip, PDF::TEMPLATE_ORDER_SLIP, $this->context->smarty);
        $pdf->render();
    }
}
