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

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller responsible of "Configure > Shop Parameters > Order Settings" page.
 */
class OrderPreferencesController extends FrameworkBundleAdminController
{
    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        $generalForm = $this->getGeneralFormHandler()->getForm();
        $giftOptionsForm = $this->getGiftOptionsFormHandler()->getForm();

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/OrderPreferences/order_preferences.html.twig', [
            'layoutTitle' => $this->trans('Order settings', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'generalForm' => $generalForm->createView(),
            'giftOptionsForm' => $giftOptionsForm->createView(),
            'isMultishippingEnabled' => $this->configuration->getBoolean('PS_ALLOW_MULTISHIPPING'),
            'isAtcpShipWrapEnabled' => $this->configuration->getBoolean('PS_ATCP_SHIPWRAP'),
        ]);
    }

    /**
     * Process the General configuration form.
     *
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     message="You do not have permission to edit this.",
     *     redirectRoute="admin_order_preferences")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processGeneralFormAction(Request $request)
    {
        $this->dispatchHook(
            'actionAdminAdminPreferencesControllerPostProcessUploadQuotaBefore',
            ['controller' => $this]
        );

        return $this->processForm(
            $request,
            $this->getGeneralFormHandler()
        );
    }

    /**
     * Process the Gift Options configuration form.
     *
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     message="You do not have permission to edit this.",
     *     redirectRoute="admin_order_preferences")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processGiftOptionsFormAction(Request $request)
    {
        $this->dispatchHook(
            'actionAdminAdminPreferencesControllerPostProcessUploadQuotaBefore',
            ['controller' => $this]
        );

        return $this->processForm(
            $request,
            $this->getGiftOptionsFormHandler()
        );
    }

    /**
     * Process the Preferences configuration form.
     *
     * @param Request $request
     * @param FormHandlerInterface $formHandler
     *
     * @return RedirectResponse
     */
    protected function processForm(Request $request, FormHandlerInterface $formHandler)
    {
        $this->dispatchHook('actionAdminOrderPreferencesControllerPostProcessBefore', ['controller' => $this]);

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

        return $this->redirectToRoute('admin_order_preferences');
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getGeneralFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.order_preferences.general.form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getGiftOptionsFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.order_preferences.gift_options.form_handler');
    }
}
