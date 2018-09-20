<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\WebserviceKeyFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible of "Configure > Advanced Parameters > Webservice" page display.
 *
 * @todo: add unit tests
 */
class WebserviceController extends FrameworkBundleAdminController
{
    /**
     * Displays the Webservice main page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param WebserviceKeyFilters $filters - filters for webservice list
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(WebserviceKeyFilters $filters, Request $request)
    {
        $form = $this->getFormHandler()->getForm();
        $gridWebserviceFactory = $this->get('prestashop.core.grid.factory.webservice_key');
        $grid = $gridWebserviceFactory->getGrid($filters);

        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');
        $presentedGrid = $gridPresenter->present($grid);

        $configurationWarnings = $this->lookForWarnings($request);

        if (false === empty($configurationWarnings)) {
            foreach ($configurationWarnings as $warningMessage) {
                $this->addFlash('warning', $warningMessage);
            }
        }

        $twigValues = [
            'layoutHeaderToolbarBtn' => [
                'add' => [
                    'href' => $this->generateUrl('admin_webservice_list_create'),
                    'desc' => $this->trans('Add new webservice key', 'Admin.Advparameters.Feature'),
                    'icon' => 'add_circle_outline',
                ],
            ],
            'layoutTitle' => $this->trans('Webservice', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => false,
            'requireBulkActions' => false, // temporary
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->get('_legacy_controller')),
            'requireFilterStatus' => false,
            'form' => $form->createView(),
            'grid' => $presentedGrid,
        ];

        return $this->render('@AdvancedParameters/WebservicePage/webservice.html.twig', $twigValues);
    }

    /**
     * Redirects to webservice account form where new webservice account record can be created.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @return RedirectResponse
     */
    public function createAction()
    {
        //@todo: this action should point to new add page
        $legacyLink = $this->getAdminLink('AdminWebservice', [
            'addwebservice_account' => 1,
        ]);

        return $this->redirect($legacyLink);
    }

    /**
     * Redirects to webservice account form where existing webservice account record can be edited.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param $webserviceAccountId
     *
     * @return RedirectResponse
     */
    public function editAction($webserviceAccountId)
    {
        $legacyLink = $this->getAdminLink('AdminWebservice', [
            'id_webservice_account' => $webserviceAccountId,
            'updatewebservice_account' => 1,
        ]);

        return $this->redirect($legacyLink);
    }

    /**
     * Searches for specific records.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request)
    {
        $definitionFactory = $this->get('prestashop.core.grid.definition.factory.webservice_key');
        $webserviceDefinition = $definitionFactory->getDefinition();

        $gridFilterFormFactory = $this->get('prestashop.core.grid.filter.form_factory');
        $searchParametersForm = $gridFilterFormFactory->create($webserviceDefinition);

        $searchParametersForm->handleRequest($request);
        $filters = [];

        if ($searchParametersForm->isSubmitted()) {
            $filters = $searchParametersForm->getData();
        }

        return $this->redirectToRoute('admin_webservice', ['filters' => $filters]);
    }

    /**
     * Deletes single record.
     *
     * @DemoRestricted(redirectRoute="admin_webservice")
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message="You do not have permission to delete this.")
     *
     * @param int $webserviceAccountId
     *
     * @return RedirectResponse
     *
     * @throws \PrestaShopException
     */
    public function deleteSingleWebserviceAction($webserviceAccountId)
    {
        $webserviceEraser = $this->get('prestashop.adapter.webservice.webservice_key_eraser');
        $errors = $webserviceEraser->erase([$webserviceAccountId]);

        if (!empty($errors)) {
            $this->flashErrors($errors);
        } else {
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_webservice');
    }

    /**
     * Deletes selected records.
     *
     * @DemoRestricted(redirectRoute="admin_webservice")
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message="You do not have permission to delete this.")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws \PrestaShopException
     */
    public function deleteMultipleWebserviceAction(Request $request)
    {
        $webserviceToDelete = $request->request->get('webservice_key_bulk_action');

        $webserviceEraser = $this->get('prestashop.adapter.webservice.webservice_key_eraser');
        $errors = $webserviceEraser->erase($webserviceToDelete);

        if (!empty($errors)) {
            $this->flashErrors($errors);
        } else {
            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_webservice');
    }

    /**
     * Enables status for selected rows.
     *
     * @DemoRestricted(redirectRoute="admin_webservice")
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="You do not have permission to edit this.")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function enableMultipleStatusAction(Request $request)
    {
        $webserviceToEnable = $request->request->get('webservice_key_bulk_action');
        $statusModifier = $this->get('prestashop.adapter.webservice.webservice_key_status_modifier');

        $statusModifier->setStatus($webserviceToEnable, 1);

        return $this->redirectToRoute('admin_webservice');
    }

    /**
     * Disables status for selected rows.
     *
     * @DemoRestricted(redirectRoute="admin_webservice")
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="You do not have permission to edit this.")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function disableMultipleStatusAction(Request $request)
    {
        $webserviceToEnable = $request->request->get('webservice_key_bulk_action');
        $statusModifier = $this->get('prestashop.adapter.webservice.webservice_key_status_modifier');

        $statusModifier->setStatus($webserviceToEnable, 0);

        return $this->redirectToRoute('admin_webservice');
    }

    /**
     * Toggles webservice account status.
     *
     * @DemoRestricted(redirectRoute="admin_webservice")
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="You do not have permission to edit this.")
     *
     * @param int $webserviceAccountId
     *
     * @return RedirectResponse
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function toggleStatusAction($webserviceAccountId)
    {
        $statusModifier = $this->get('prestashop.adapter.webservice.webservice_key_status_modifier');
        $errors = $statusModifier->toggleStatus($webserviceAccountId);

        if (!empty($errors)) {
            $this->flashErrors($errors);
        } else {
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_webservice');
    }

    /**
     * Process the Webservice configuration form.
     *
     * @DemoRestricted(redirectRoute="admin_webservice")
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="You do not have permission to edit this.")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws \Exception
     */
    public function processFormAction(Request $request)
    {
        $this->dispatchHook('actionAdminAdminWebserviceControllerPostProcessBefore', array('controller' => $this));

        $form = $this->getFormHandler()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $saveErrors = $this->getFormHandler()->save($form->getData());

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));
            } else {
                $this->flashErrors($saveErrors);
            }
        }

        return $this->redirectToRoute('admin_webservice');
    }

    /**
     * @return FormHandlerInterface
     */
    private function getFormHandler()
    {
        return $this->get('prestashop.adapter.webservice.form_handler');
    }

    /**
     * @return string[]
     */
    private function lookForWarnings()
    {
        $configurationChecker = $this->get('prestashop.core.webservice.server_requirements_checker');

        return $configurationChecker->checkForErrors();
    }
}
