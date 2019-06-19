<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Exception\DuplicateWebserviceKeyException;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Exception\WebserviceConstraintException;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\WebserviceKeyFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible of "Configure > Advanced Parameters > Webservice" page.
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

        $configurationWarnings = $this->lookForWarnings();

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Webservice/index.html.twig',
            [
                'help_link' => $this->generateSidebarLink($request->get('_legacy_controller')),
                'form' => $form->createView(),
                'grid' => $presentedGrid,
                'configurationWarnings' => $configurationWarnings,
            ]
        );
    }

    /**
     * Shows Webservice Key form and handles its submit
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $formHandler = $this->get('prestashop.core.form.identifiable_object.handler.webservice_key_form_handler');
        $formBuilder = $this->get('prestashop.core.form.identifiable_object.builder.webservice_key_form_builder');

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        try {
            $result = $formHandler->handle($form);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_webservice_keys_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Webservice/create.html.twig',
            [
                'webserviceKeyForm' => $form->createView(),
            ]
        );
    }

    /**
     * Redirects to webservice account form where existing webservice account record can be edited.
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param int $webserviceKeyId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction($webserviceKeyId, Request $request)
    {
        $formHandler = $this->get('prestashop.core.form.identifiable_object.handler.webservice_key_form_handler');
        $formBuilder = $this->get('prestashop.core.form.identifiable_object.builder.webservice_key_form_builder');

        $form = $formBuilder->getFormFor((int) $webserviceKeyId);
        $form->handleRequest($request);

        try {
            $result = $formHandler->handleFor((int) $webserviceKeyId, $form);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_webservice_keys_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Webservice/edit.html.twig',
            [
                'webserviceKeyForm' => $form->createView(),
            ]
        );
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

        return $this->redirectToRoute('admin_webservice_keys_index', ['filters' => $filters]);
    }

    /**
     * Deletes single record.
     *
     * @DemoRestricted(redirectRoute="admin_webservice_keys_index")
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message="You do not have permission to delete this.")
     *
     * @param int $webserviceKeyId
     *
     * @return RedirectResponse
     */
    public function deleteAction($webserviceKeyId)
    {
        $webserviceEraser = $this->get('prestashop.adapter.webservice.webservice_key_eraser');
        $errors = $webserviceEraser->erase([$webserviceKeyId]);

        if (!empty($errors)) {
            $this->flashErrors($errors);
        } else {
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_webservice_keys_index');
    }

    /**
     * Deletes selected records.
     *
     * @DemoRestricted(redirectRoute="admin_webservice_keys_index")
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message="You do not have permission to delete this.")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDeleteAction(Request $request)
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

        return $this->redirectToRoute('admin_webservice_keys_index');
    }

    /**
     * Enables status for selected rows.
     *
     * @DemoRestricted(redirectRoute="admin_webservice_keys_index")
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="You do not have permission to edit this.")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkEnableAction(Request $request)
    {
        $webserviceToEnable = $request->request->get('webservice_key_bulk_action');
        $statusModifier = $this->get('prestashop.adapter.webservice.webservice_key_status_modifier');

        $statusModifier->setStatus($webserviceToEnable, 1);

        return $this->redirectToRoute('admin_webservice_keys_index');
    }

    /**
     * Disables status for selected rows.
     *
     * @DemoRestricted(redirectRoute="admin_webservice_keys_index")
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="You do not have permission to edit this.")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDisableAction(Request $request)
    {
        $webserviceToEnable = $request->request->get('webservice_key_bulk_action');
        $statusModifier = $this->get('prestashop.adapter.webservice.webservice_key_status_modifier');

        $statusModifier->setStatus($webserviceToEnable, 0);

        return $this->redirectToRoute('admin_webservice_keys_index');
    }

    /**
     * Toggles webservice account status.
     *
     * @DemoRestricted(redirectRoute="admin_webservice_keys_index")
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="You do not have permission to edit this.")
     *
     * @param int $webserviceKeyId
     *
     * @return RedirectResponse
     */
    public function toggleStatusAction($webserviceKeyId)
    {
        $statusModifier = $this->get('prestashop.adapter.webservice.webservice_key_status_modifier');
        $errors = $statusModifier->toggleStatus($webserviceKeyId);

        if (!empty($errors)) {
            $this->flashErrors($errors);
        } else {
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_webservice_keys_index');
    }

    /**
     * Process the Webservice configuration form.
     *
     * @DemoRestricted(redirectRoute="admin_webservice_keys_index")
     * @AdminSecurity("is_granted(['create', 'update', 'delete'], request.get('_legacy_controller'))", message="You do not have permission to edit this.")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function saveSettingsAction(Request $request)
    {
        $this->dispatchHook('actionAdminAdminWebserviceControllerPostProcessBefore', ['controller' => $this]);

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

        return $this->redirectToRoute('admin_webservice_keys_index');
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

    /**
     * @return array
     */
    private function getErrorMessages()
    {
        return [
            WebserviceConstraintException::class => [
                WebserviceConstraintException::INVALID_KEY => $this->trans('Key length must be 32 character long.', 'Admin.Advparameters.Notification'),
            ],
            DuplicateWebserviceKeyException::class => $this->trans('This key already exists.', 'Admin.Advparameters.Notification'),
        ];
    }
}
