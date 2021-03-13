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
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible of "Configure > Shop Parameters > Product Settings" page.
 */
class ProductPreferencesController extends FrameworkBundleAdminController
{
    /**
     * @param Request $request
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $generalForm = $this->getGeneralFormHandler()->getForm();
        $pageForm = $this->getPageFormHandler()->getForm();
        $paginationForm = $this->getPaginationFormHandler()->getForm();
        $stockForm = $this->getStockFormHandler()->getForm();

        return $this->renderForm($request, $generalForm, $pageForm, $paginationForm, $stockForm);
    }

    /**
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     message="You do not have permission to update this.",
     *     redirectRoute="admin_product_preferences"
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function processGeneralFormAction(Request $request)
    {
        $pageForm = $this->getPageFormHandler()->getForm();
        $paginationForm = $this->getPaginationFormHandler()->getForm();
        $stockForm = $this->getStockFormHandler()->getForm();

        $generalForm = $this->processForm(
            $request,
            $this->getGeneralFormHandler(),
            'General'
        );

        return $this->renderForm($request, $generalForm, $pageForm, $paginationForm, $stockForm);
    }

    /**
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     message="You do not have permission to update this.",
     *     redirectRoute="admin_product_preferences"
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function processPageFormAction(Request $request)
    {
        $generalForm = $this->getGeneralFormHandler()->getForm();
        $paginationForm = $this->getPaginationFormHandler()->getForm();
        $stockForm = $this->getStockFormHandler()->getForm();

        $pageForm = $this->processForm(
            $request,
            $this->getPageFormHandler(),
            'Page'
        );

        return $this->renderForm($request, $generalForm, $pageForm, $paginationForm, $stockForm);
    }

    /**
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     message="You do not have permission to update this.",
     *     redirectRoute="admin_product_preferences"
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function processPaginationFormAction(Request $request)
    {
        $generalForm = $this->getGeneralFormHandler()->getForm();
        $pageForm = $this->getPageFormHandler()->getForm();
        $stockForm = $this->getStockFormHandler()->getForm();

        $paginationForm = $this->processForm(
            $request,
            $this->getPaginationFormHandler(),
            'Pagination'
        );

        return $this->renderForm($request, $generalForm, $pageForm, $paginationForm, $stockForm);
    }

    /**
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     message="You do not have permission to update this.",
     *     redirectRoute="admin_product_preferences"
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function processStockFormAction(Request $request)
    {
        $generalForm = $this->getGeneralFormHandler()->getForm();
        $pageForm = $this->getPageFormHandler()->getForm();
        $paginationForm = $this->getPaginationFormHandler()->getForm();

        $stockForm = $this->processForm(
            $request,
            $this->getStockFormHandler(),
            'Stock'
        );

        return $this->renderForm($request, $generalForm, $pageForm, $paginationForm, $stockForm);
    }

    /**
     * @param Request $request
     * @param FormInterface $generalForm
     * @param FormInterface $pageForm
     * @param FormInterface $paginationForm
     * @param FormInterface $stockForm
     *
     * @return Response
     */
    protected function renderForm(
        Request $request,
        FormInterface $generalForm,
        FormInterface $pageForm,
        FormInterface $paginationForm,
        FormInterface $stockForm
    ): Response {
        $legacyController = $request->attributes->get('_legacy_controller');

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/product_preferences.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Product Settings', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
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

    /**
     * Process the Product Preferences configuration form.
     *
     * @param Request $request
     * @param FormHandlerInterface $formHandler
     * @param string $hookName
     *
     * @return FormInterface
     */
    protected function processForm(Request $request, FormHandlerInterface $formHandler, string $hookName): FormInterface
    {
        $this->dispatchHook(
            'actionAdminShopParametersProductPreferencesControllerPostProcess' . $hookName . 'Before',
            ['controller' => $this]
        );

        $this->dispatchHook('actionAdminShopParametersProductPreferencesControllerPostProcessBefore', ['controller' => $this]);

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $saveErrors = $formHandler->save($data);

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));
            } else {
                $this->flashErrors($saveErrors);
            }
        }

        return $form;
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getGeneralFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.product_preferences.general.form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getPaginationFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.product_preferences.pagination.form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getPageFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.product_preferences.page.form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getStockFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.product_preferences.stock.form_handler');
    }
}
