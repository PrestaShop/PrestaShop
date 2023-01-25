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
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller responsible of "Improve > Shipping > Preferences" page.
 */
class PreferencesController extends FrameworkBundleAdminController
{
    /**
     * Show shipping preferences page.
     *
     * @param Request $request
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $handlingForm = $this->getHandlingFormHandler()->getForm();
        $carrierOptionsForm = $this->getCarrierOptionsFormHandler()->getForm();

        return $this->doRenderForm($handlingForm, $carrierOptionsForm, $request);
    }

    /**
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))",
     *     message="You do not have permission to edit this.",
     *     redirectRoute="admin_shipping_preferences")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function processCarrierOptionsFormAction(Request $request)
    {
        $formHandler = $this->getCarrierOptionsFormHandler();
        $this->dispatchHook(
            'actionAdminShippingPreferencesControllerPostProcessCarrierOptionsBefore',
                ['controller' => $this]
        );

        $this->dispatchHook('actionAdminShippingPreferencesControllerPostProcessBefore', ['controller' => $this]);

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $saveErrors = $formHandler->save($data);

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_shipping_preferences');
            }
            $this->flashErrors($saveErrors);
        }

        return $this->doRenderForm($this->getHandlingFormHandler()->getForm(), $form, $request);
    }

    /**
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))",
     *     message="You do not have permission to edit this.",
     *     redirectRoute="admin_shipping_preferences")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function processHandlingFormAction(Request $request)
    {
        $formHandler = $this->getHandlingFormHandler();
        $this->dispatchHook(
            'actionAdminShippingPreferencesControllerPostProcessHandlingBefore',
            ['controller' => $this]
        );

        $this->dispatchHook('actionAdminShippingPreferencesControllerPostProcessBefore', ['controller' => $this]);

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $saveErrors = $formHandler->save($data);

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_shipping_preferences');
            }
        }

        return $this->doRenderForm($form, $this->getCarrierOptionsFormHandler()->getForm(), $request);
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getHandlingFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.shipping_preferences.handling.form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getCarrierOptionsFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.shipping_preferences.carrier_options.form_handler');
    }

    /**
     * @deprecated since 8.1.0 and will be removed in next major version.
     */
    protected function renderForm($handlingForm, $carrierOptionsForm, $request)
    {
        @trigger_error(
            sprintf(
                '%s is deprecated since version 8.1.0 and will be removed in the next major version. Use doRenderForm() instead.',
                __METHOD__
            ),
            E_USER_DEPRECATED
        );

        return $this->doRenderForm($handlingForm, $carrierOptionsForm, $request);
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
            'layoutTitle' => $this->trans('Preferences', 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'handlingForm' => $handlingForm->createView(),
            'carrierOptionsForm' => $carrierOptionsForm->createView(),
        ]);
    }
}
