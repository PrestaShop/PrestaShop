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

namespace PrestaShopBundle\Controller\Admin\Improve\Shipping;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller responsible for "Improve > Shipping > Preferences" page.
 */
class PreferencesController extends PrestaShopAdminController
{
    /**
     * Show shipping preferences page.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.shipping_preferences.handling.form_handler')]
        FormHandlerInterface $handlingFormHandler,
        #[Autowire(service: 'prestashop.admin.shipping_preferences.carrier_options.form_handler')]
        FormHandlerInterface $carrierOptionsFormHandler
    ): Response {
        $handlingForm = $handlingFormHandler->getForm();
        $carrierOptionsForm = $carrierOptionsFormHandler->getForm();

        return $this->doRenderForm($handlingForm, $carrierOptionsForm, $request);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.', redirectRoute: 'admin_shipping_preferences')]
    public function processCarrierOptionsFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.shipping_preferences.handling.form_handler')]
        FormHandlerInterface $handlingFormHandler,
        #[Autowire(service: 'prestashop.admin.shipping_preferences.carrier_options.form_handler')]
        FormHandlerInterface $carrierOptionsFormHandler
    ): Response {
        $this->dispatchHookWithParameters(
            'actionAdminShippingPreferencesControllerPostProcessCarrierOptionsBefore',
            ['controller' => $this]
        );

        $this->dispatchHookWithParameters(
            'actionAdminShippingPreferencesControllerPostProcessBefore',
            ['controller' => $this]
        );

        $form = $carrierOptionsFormHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $saveErrors = $carrierOptionsFormHandler->save($data);

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_shipping_preferences');
            }
            $this->addFlashErrors($saveErrors);
        }

        return $this->doRenderForm($handlingFormHandler->getForm(), $form, $request);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.', redirectRoute: 'admin_shipping_preferences')]
    public function processHandlingFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.shipping_preferences.handling.form_handler')]
        FormHandlerInterface $handlingFormHandler,
        #[Autowire(service: 'prestashop.admin.shipping_preferences.carrier_options.form_handler')]
        FormHandlerInterface $carrierOptionsFormHandler
    ): Response {
        $this->dispatchHookWithParameters(
            'actionAdminShippingPreferencesControllerPostProcessHandlingBefore',
            ['controller' => $this]
        );

        $this->dispatchHookWithParameters(
            'actionAdminShippingPreferencesControllerPostProcessBefore',
            ['controller' => $this]
        );

        $form = $handlingFormHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $saveErrors = $handlingFormHandler->save($data);

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_shipping_preferences');
            }
        }

        return $this->doRenderForm($form, $carrierOptionsFormHandler->getForm(), $request);
    }

    /**
     * @param FormInterface $handlingForm
     * @param FormInterface $carrierOptionsForm
     * @param Request $request
     *
     * @return Response
     */
    private function doRenderForm($handlingForm, $carrierOptionsForm, $request): Response
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        return $this->render('@PrestaShop/Admin/Improve/Shipping/Preferences/preferences.html.twig', [
            'layoutTitle' => $this->trans('Preferences', [], 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'handlingForm' => $handlingForm->createView(),
            'carrierOptionsForm' => $carrierOptionsForm->createView(),
        ]);
    }
}
