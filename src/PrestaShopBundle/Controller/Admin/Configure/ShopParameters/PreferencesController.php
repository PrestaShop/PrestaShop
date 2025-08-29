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

use PrestaShop\PrestaShop\Core\Domain\Tab\Command\UpdateTabStatusByClassNameCommand;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for "Configure > Shop Parameters > General" page.
 */
class PreferencesController extends PrestaShopAdminController
{
    public const CONTROLLER_NAME = 'AdminPreferences';

    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.preferences.form_handler')]
        FormHandlerInterface $preferencesFormHandler,
    ): Response {
        $form = $preferencesFormHandler->getForm();

        return $this->doRenderForm($request, $form);
    }

    #[DemoRestricted(redirectRoute: 'admin_preferences')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_preferences')]
    public function processFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.preferences.form_handler')]
        FormHandlerInterface $preferencesFormHandler,
    ): Response {
        $this->dispatchHookWithParameters('actionAdminPreferencesControllerPostProcessBefore', ['controller' => $this]);

        $form = $preferencesFormHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $saveErrors = $preferencesFormHandler->save($data);

            if (0 === count($saveErrors)) {
                $this->dispatchCommand(
                    new UpdateTabStatusByClassNameCommand(
                        'AdminShopGroup',
                        $this->getConfiguration()->get('PS_MULTISHOP_FEATURE_ACTIVE')
                    )
                );

                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_preferences');
            }

            $this->addFlashErrors($saveErrors);
        }

        return $this->doRenderForm($request, $form);
    }

    private function doRenderForm(Request $request, FormInterface $form): Response
    {
        // SSL URI is used for the merchant to check if he has SSL enabled
        $sslUri = 'https://' . $this->getShopContext()->getDomainSSL() . $request->getRequestUri();

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/preferences.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Preferences', [], 'Admin.Navigation.Menu'),
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminPreferences'),
            'requireFilterStatus' => false,
            'generalForm' => $form->createView(),
            'isSslEnabled' => $this->getConfiguration()->get('PS_SSL_ENABLED'),
            'sslUri' => $sslUri,
        ]);
    }
}
