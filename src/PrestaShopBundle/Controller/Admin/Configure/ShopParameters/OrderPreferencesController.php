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
