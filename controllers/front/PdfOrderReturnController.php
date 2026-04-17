<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
use PrestaShopBundle\Security\Admin\LegacyAdminTokenValidator;

class PdfOrderReturnControllerCore extends FrontController
{
    /** @var string */
    public $php_self = 'pdf-order-return';
    /** @var bool */
    protected $display_header = false;
    /** @var bool */
    protected $display_footer = false;

    /**
     * @var OrderReturn|null
     */
    public $orderReturn;

    public function postProcess(): void
    {
        $adminToken = Tools::getValue('adtoken');
        if (!empty($adminToken)) {
            $adminTokenValidator = $this->getContainer()->get(LegacyAdminTokenValidator::class);
            $from_admin = $adminTokenValidator->isTokenValid((int) Tools::getValue('id_employee'), $adminToken);
        } else {
            $from_admin = false;
        }

        if (!$from_admin && !$this->context->customer->isLogged()) {
            Tools::redirect($this->context->link->getPageLink(
                'authentication',
                null,
                null,
                ['back' => 'order-follow']
            ));
        }

        if (Tools::getValue('id_order_return') && Validate::isUnsignedId(Tools::getValue('id_order_return'))) {
            $this->orderReturn = new OrderReturn(Tools::getValue('id_order_return'));
        }

        if (!isset($this->orderReturn) || !Validate::isLoadedObject($this->orderReturn)) {
            die($this->trans('Order return not found.', [], 'Shop.Notifications.Error'));
        } elseif (!$from_admin && $this->orderReturn->id_customer != $this->context->customer->id) {
            die($this->trans('Order return not found.', [], 'Shop.Notifications.Error'));
        } elseif ($this->orderReturn->state < 2) {
            die($this->trans('Order return not confirmed.', [], 'Shop.Notifications.Error'));
        }
    }

    /**
     * @return void
     *
     * @throws PrestaShopException
     */
    public function display(): void
    {
        $pdf = new PDF($this->orderReturn, PDF::TEMPLATE_ORDER_RETURN, $this->context->smarty);
        $pdf->render();
    }
}
