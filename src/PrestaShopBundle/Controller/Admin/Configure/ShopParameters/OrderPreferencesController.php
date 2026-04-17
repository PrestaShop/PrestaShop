<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller responsible for "Configure > Shop Parameters > Order Settings" page.
 */
class OrderPreferencesController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.order_preferences.general.form_handler')]
        FormHandlerInterface $generalFormHandler,
        #[Autowire(service: 'prestashop.admin.order_preferences.gift_options.form_handler')]
        FormHandlerInterface $giftOptionsFormHandler,
    ) {
        $legacyController = $request->attributes->get('_legacy_controller');

        $generalForm = $generalFormHandler->getForm();
        $giftOptionsForm = $giftOptionsFormHandler->getForm();

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/OrderPreferences/order_preferences.html.twig', [
            'layoutTitle' => $this->trans('Order settings', [], 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'generalForm' => $generalForm->createView(),
            'giftOptionsForm' => $giftOptionsForm->createView(),
            'isAtcpShipWrapEnabled' => (bool) $this->getConfiguration()->get('PS_ATCP_SHIPWRAP'),
        ]);
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.', redirectRoute: 'admin_order_preferences')]
    public function processGeneralFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.order_preferences.general.form_handler')]
        FormHandlerInterface $generalFormHandler,
    ): RedirectResponse {
        return $this->processForm(
            $request,
            $generalFormHandler,
            'General'
        );
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.', redirectRoute: 'admin_order_preferences')]
    public function processGiftOptionsFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.order_preferences.gift_options.form_handler')]
        FormHandlerInterface $giftOptionsFormHandler,
    ): RedirectResponse {
        return $this->processForm(
            $request,
            $giftOptionsFormHandler,
            'GiftOptions'
        );
    }

    protected function processForm(Request $request, FormHandlerInterface $formHandler, string $hookName): RedirectResponse
    {
        $this->dispatchHookWithParameters(
            'actionAdminShopParametersOrderPreferencesControllerPostProcess' . $hookName . 'Before',
            ['controller' => $this]
        );

        $this->dispatchHookWithParameters('actionAdminShopParametersOrderPreferencesControllerPostProcessBefore', ['controller' => $this]);

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $saveErrors = $formHandler->save($data);

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));
            } else {
                $this->addFlashErrors($saveErrors);
            }
        }

        return $this->redirectToRoute('admin_order_preferences');
    }
}
