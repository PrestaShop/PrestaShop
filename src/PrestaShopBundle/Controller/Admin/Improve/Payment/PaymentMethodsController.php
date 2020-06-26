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

namespace PrestaShopBundle\Controller\Admin\Improve\Payment;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PaymentMethodsController is responsible for 'Improve > Payment > Payment Methods' page.
 */
class PaymentMethodsController extends FrameworkBundleAdminController
{
    /**
     * Show payment method modules.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        $shopContext = $this->get('prestashop.adapter.shop.context');
        $isSingleShopContext = $shopContext->isSingleShopContext();
        $paymentModules = [];

        if ($isSingleShopContext) {
            $paymentMethodsPresenter = $this->get('prestashop.adapter.module.presenter.payment');
            $paymentModules = $paymentMethodsPresenter->present();
        }

        return $this->render('@PrestaShop/Admin/Improve/Payment/PaymentMethods/payment_methods.html.twig', [
            'paymentModules' => $paymentModules,
            'isSingleShopContext' => $isSingleShopContext,
            'layoutTitle' => $this->trans('Payment Methods', 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
        ]);
    }
}
