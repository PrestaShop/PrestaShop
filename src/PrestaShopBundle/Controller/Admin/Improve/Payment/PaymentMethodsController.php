<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Admin\Improve\Payment;

use PrestaShop\PrestaShop\Adapter\Presenter\Module\PaymentModulesPresenter;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PaymentMethodsController is responsible for 'Improve > Payment > Payment Methods' page.
 */
class PaymentMethodsController extends PrestaShopAdminController
{
    /**
     * Show payment method modules.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        PaymentModulesPresenter $paymentMethodsPresenter
    ): Response {
        $legacyController = $request->attributes->get('_legacy_controller');

        $isSingleShopContext = $this->getShopContext()->getShopConstraint()->isSingleShopContext();
        $paymentModules = [];

        if ($isSingleShopContext) {
            $paymentModules = $paymentMethodsPresenter->present();
        }

        return $this->render('@PrestaShop/Admin/Improve/Payment/PaymentMethods/payment_methods.html.twig', [
            'paymentModules' => $paymentModules,
            'isSingleShopContext' => $isSingleShopContext,
            'layoutTitle' => $this->trans('Payment methods', [], 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
        ]);
    }
}
