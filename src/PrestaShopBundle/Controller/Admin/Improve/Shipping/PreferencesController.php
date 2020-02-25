<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Improve\Shipping;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

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
     * @return array Template parameters
     */
    public function indexAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        $handlingForm = $this->getHandlingFormHandler()->getForm();
        $carrierOptionsForm = $this->getCarrierOptionsFormHandler()->getForm();

        return $this->render('@PrestaShop/Admin/Improve/Shipping/Preferences/preferences.html.twig', [
            'layoutTitle' => $this->trans('Preferences', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'handlingForm' => $handlingForm->createView(),
            'carrierOptionsForm' => $carrierOptionsForm->createView(),
        ]);
    }

    /**
     * Process the Performance Smarty configuration form.
     *
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     message="You do not have permission to edit this.",
     *     redirectRoute="admin_shipping_preferences")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processHandlingFormAction(Request $request)
    {
        $this->dispatchHook(
            'actionAdminAdminPreferencesControllerPostProcessHandlingBefore',
            ['controller' => $this]
        );

        return $this->processForm(
            $request,
            $this->getHandlingFormHandler()
        );
    }

    /**
     * Process the Performance Smarty configuration form.
     *
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     message="You do not have permission to edit this.",
     *     redirectRoute="admin_shipping_preferences")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processCarrierOptionsFormAction(Request $request)
    {
        $this->dispatchHook(
            'actionAdminAdminPreferencesControllerPostProcessCarrierOptionsBefore',
            ['controller' => $this]
        );

        return $this->processForm(
            $request,
            $this->getCarrierOptionsFormHandler()
        );
    }

    /**
     * Process the Performance configuration form.
     *
     * @param Request $request
     * @param FormHandlerInterface $formHandler
     *
     * @return RedirectResponse
     */
    protected function processForm(Request $request, FormHandlerInterface $formHandler)
    {
        $this->dispatchHook('actionAdminAdminPreferencesControllerPostProcessBefore', ['controller' => $this]);

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $saveErrors = $formHandler->save($data);

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));
            } else {
                $this->flashErrors($saveErrors);
            }
        }

        return $this->redirectToRoute('admin_shipping_preferences');
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
}
