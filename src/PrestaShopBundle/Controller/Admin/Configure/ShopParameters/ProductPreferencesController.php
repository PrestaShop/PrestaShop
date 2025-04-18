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
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for "Configure > Shop Parameters > Product Settings" page.
 */
class ProductPreferencesController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.product_preferences.general.form_handler')]
        FormHandlerInterface $generalFormHandler,
        #[Autowire(service: 'prestashop.admin.product_preferences.page.form_handler')]
        FormHandlerInterface $pageFormHandler,
        #[Autowire(service: 'prestashop.admin.product_preferences.pagination.form_handler')]
        FormHandlerInterface $paginationFormHandler,
        #[Autowire(service: 'prestashop.admin.product_preferences.stock.form_handler')]
        FormHandlerInterface $stockFormHandler,
    ): Response {
        $legacyController = $request->attributes->get('_legacy_controller');

        $generalForm = $generalFormHandler->getForm();
        $pageForm = $pageFormHandler->getForm();
        $paginationForm = $paginationFormHandler->getForm();
        $stockForm = $stockFormHandler->getForm();

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/product_preferences.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Product settings', [], 'Admin.Navigation.Menu'),
            'requireBulkAction' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'requireFilterStatus' => false,
            'generalForm' => $generalForm->createView(),
            'pageForm' => $pageForm->createView(),
            'paginationForm' => $paginationForm->createView(),
            'stockForm' => $stockForm->createView(),
        ]);
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_product_preferences')]
    public function processGeneralFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.product_preferences.general.form_handler')]
        FormHandlerInterface $generalFormHandler,
    ): RedirectResponse {
        return $this->processForm(
            $request,
            $generalFormHandler,
            'General'
        );
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_product_preferences')]
    public function processPageFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.product_preferences.page.form_handler')]
        FormHandlerInterface $pageFormHandler,
    ): RedirectResponse {
        return $this->processForm(
            $request,
            $pageFormHandler,
            'Page'
        );
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_product_preferences')]
    public function processPaginationFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.product_preferences.pagination.form_handler')]
        FormHandlerInterface $paginationFormHandler,
    ): RedirectResponse {
        return $this->processForm(
            $request,
            $paginationFormHandler,
            'Pagination'
        );
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_product_preferences')]
    public function processStockFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.product_preferences.stock.form_handler')]
        FormHandlerInterface $stockFormHandler,
    ): RedirectResponse {
        return $this->processForm(
            $request,
            $stockFormHandler,
            'Stock'
        );
    }

    protected function processForm(Request $request, FormHandlerInterface $formHandler, string $hookName): RedirectResponse
    {
        $this->dispatchHookWithParameters(
            'actionAdminShopParametersProductPreferencesControllerPostProcess' . $hookName . 'Before',
            ['controller' => $this]
        );

        $this->dispatchHookWithParameters('actionAdminShopParametersProductPreferencesControllerPostProcessBefore', ['controller' => $this]);

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

        return $this->redirectToRoute('admin_product_preferences');
    }
}
