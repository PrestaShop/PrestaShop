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

    public function postProcess()
    {
        $adminToken = Tools::getValue('adtoken');
        if (!empty($adminToken)) {
            $adminTokenValidator = $this->getContainer()->get(LegacyAdminTokenValidator::class);
            $from_admin = $adminTokenValidator->isTokenValid('AdminReturn', (int) Tools::getValue('id_employee'), $adminToken);
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
     * @return bool|void
     *
     * @throws PrestaShopException
     */
    public function display()
    {
        $pdf = new PDF($this->orderReturn, PDF::TEMPLATE_ORDER_RETURN, $this->context->smarty);
        $pdf->render();
    }
}
