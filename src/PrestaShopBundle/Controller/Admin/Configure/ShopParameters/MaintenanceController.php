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
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for "Configure > Shop Parameters > General > Maintenance" page.
 */
class MaintenanceController extends PrestaShopAdminController
{
    public const CONTROLLER_NAME = 'AdminMaintenance';

    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        #[Autowire(service: 'prestashop.adapter.maintenance.form_handler')]
        FormHandlerInterface $maintenanceFormHandler,
    ): Response {
        $form = $maintenanceFormHandler->getForm();

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/maintenance.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Maintenance', [], 'Admin.Navigation.Menu'),
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminMaintenance'),
            'requireFilterStatus' => false,
            'generalForm' => $form->createView(),
        ]);
    }

    #[DemoRestricted(redirectRoute: 'admin_maintenance')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.', redirectRoute: 'admin_maintenance')]
    public function processFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.maintenance.form_handler')]
        FormHandlerInterface $maintenanceFormHandler,
    ): RedirectResponse {
        $redirectResponse = $this->redirectToRoute('admin_maintenance');

        $this->dispatchHookWithParameters('actionAdminMaintenanceControllerPostProcessBefore', ['controller' => $this]);
        $form = $maintenanceFormHandler->getForm();
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $redirectResponse;
        }

        $data = $form->getData();
        $saveErrors = $maintenanceFormHandler->save($data);

        if (0 === count($saveErrors)) {
            $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

            return $redirectResponse;
        }

        $this->addFlashErrors($saveErrors);

        return $redirectResponse;
    }
}
