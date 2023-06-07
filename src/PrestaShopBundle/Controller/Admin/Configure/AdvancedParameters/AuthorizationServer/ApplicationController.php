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

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters\AuthorizationServer;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Command\DeleteApplicationCommand;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Exception\ApplicationConstraintException;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Exception\ApplicationException;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Exception\ApplicationNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Exception\DeleteApplicationException;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Exception\DuplicateApplicationNameException;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Query\GetApplicationForEditing;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Query\GetApplicationForViewing;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\QueryResult\EditableApplication;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\QueryResult\ViewableApplication;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerConstraintException;
use PrestaShop\PrestaShop\Core\Search\Filters\ApiAccessesFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\AuthorizedApplicationsFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Exception\NotImplementedException;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manages the "Configure > Advanced Parameters > Authorization Server" page.
 *
 * @experimental
 */
class ApplicationController extends FrameworkBundleAdminController
{
    /**
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller')) || is_granted('update', request.get('_legacy_controller')) || is_granted('delete', request.get('_legacy_controller')) || is_granted('read', request.get('_legacy_controller'))")
     *
     * @param AuthorizedApplicationsFilters $filters the list of filters from the request
     *
     * @return Response
     */
    public function indexAction(AuthorizedApplicationsFilters $filters): Response
    {
        $gridAuthorizedApplicationFactory = $this->get('prestashop.core.grid.factory.authorized_application');
        $grid = $gridAuthorizedApplicationFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/AuthorizationServer/index.html.twig', [
            'help_link' => $this->generateSidebarLink('AdminAuthorizationServer'),
            'layoutTitle' => $this->trans('Authorization Server Management', 'Admin.Navigation.Menu'),
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getApplicationToolbarButtons(),
            'grid' => $this->presentGrid($grid),
        ]);
    }

    /**
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        $authorizedApplicationForm = $this->get('prestashop.core.form.identifiable_object.builder.application_form_builder')->getForm();
        $authorizedApplicationForm->handleRequest($request);

        try {
            $result = $this->get('prestashop.core.form.identifiable_object.handler.application_form_handler')->handle($authorizedApplicationForm);
            if ($result->isSubmitted() && $result->isValid() && null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_authorized_applications_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/AuthorizationServer/Application/create.html.twig', [
            'help_link' => $this->generateSidebarLink('AdminAuthorizationServer'),
            'enableSidebar' => true,
            'applicationForm' => $authorizedApplicationForm->createView(),
            'layoutTitle' => $this->trans('New authorized application', 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param int $applicationId
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, int $applicationId): Response
    {
        try {
            /** @var EditableApplication $editableApplication */
            $editableApplication = $this->getQueryBus()->handle(new GetApplicationForEditing($applicationId));
            $authorizedApplicationForm = $this->get('prestashop.core.form.identifiable_object.builder.application_form_builder')->getFormFor($applicationId);
            $authorizedApplicationForm->handleRequest($request);
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            return $this->redirectToRoute('admin_authorized_applications_index');
        }

        try {
            $result = $this->get('prestashop.core.form.identifiable_object.handler.application_form_handler')->handleFor($applicationId, $authorizedApplicationForm);
            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_authorized_applications_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/AuthorizationServer/Application/edit.html.twig', [
            'help_link' => $this->generateSidebarLink('AdminAuthorizationServer'),
            'enableSidebar' => true,
            'applicationForm' => $authorizedApplicationForm->createView(),
            'editableApplication' => $editableApplication,
            'layoutTitle' => $this->trans(
                'Editing application %application_name%',
                'Admin.Navigation.Menu',
                [
                    '%application_name%' => $editableApplication->getname(),
                ]
            ),
        ]);
    }

    /**
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")
     */
    public function deleteAction(int $applicationId)
    {
        try {
            $this->getCommandBus()->handle(new DeleteApplicationCommand($applicationId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion', 'Admin.Notifications.Success')
            );
        } catch (ApplicationException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_authorized_applications_index');
    }

    /**
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller')) || is_granted('update', request.get('_legacy_controller')) || is_granted('delete', request.get('_legacy_controller')) || is_granted('read', request.get('_legacy_controller'))")
     */
    public function viewAction(ApiAccessesFilters $filters, int $applicationId): Response
    {
        $gridAuthorizedApplicationFactory = $this->get('prestashop.core.grid.factory.api_access');
        $grid = $gridAuthorizedApplicationFactory->getGrid($filters);

        try {
            /** @var ViewableApplication $viewableApplication */
            $viewableApplication = $this->getQueryBus()->handle(new GetApplicationForViewing($applicationId));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            return $this->redirectToRoute('admin_authorized_applications_index');
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/AuthorizationServer/Application/view.html.twig', [
            'help_link' => $this->generateSidebarLink('AdminAuthorizationServer'),
            'enableSidebar' => true,
            'viewableApplication' => $viewableApplication,
            'grid' => $this->presentGrid($grid),
        ]);
    }

    /**
     * @return array
     */
    private function getApplicationToolbarButtons(): array
    {
        $toolbarButtons = [];

        $toolbarButtons['addApplication'] = [
            'href' => $this->generateUrl('admin_authorized_applications_create'),
            'desc' => $this->trans('Add new authorized app', 'Admin.Actions'),
            'icon' => 'add_circle_outline',
            'class' => 'btn-primary',
        ];

        $toolbarButtons['addApiAccess'] = [
            'href' => $this->generateUrl('admin_api_accesses_create'),
            'desc' => $this->trans('Add new API access', 'Admin.Actions'),
            'icon' => 'add_circle_outline',
            'class' => 'btn-primary',
        ];

        return $toolbarButtons;
    }

    /**
     * Provides error messages for exceptions
     *
     * @param Exception $e
     *
     * @return array
     */
    private function getErrorMessages(Exception $e): array
    {
        return [
            ApplicationNotFoundException::class => $this->trans(
                'This application does not exist.',
                'Admin.Notifications.Error'
            ),
            DuplicateApplicationNameException::class => sprintf(
                '%s %s',
                $this->trans(
                    'An application already exists with this name:',
                    'Admin.Notifications.Error'
                ),
                $e instanceof DuplicateApplicationNameException ? $e->getDuplicateActionName() : ''
            ),
            DeleteApplicationException::class => $this->trans(
                'An error occurred during application deletion: %s',
                'Admin.Notifications.Error',
                [sprintf('"%s"', $e->getMessage())]
            ),
            ApplicationConstraintException::class => [
                CustomerConstraintException::INVALID_ID => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('Id', 'Admin.Global'))]
                ),
            ],
        ];
    }
}
