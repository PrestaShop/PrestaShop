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

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use Exception;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Security\Command\BulkDeleteSessionCommand;
use PrestaShop\PrestaShop\Core\Domain\Security\Command\DeleteEmployeeSessionCommand;
use PrestaShop\PrestaShop\Core\Domain\Session\Exception\CannotDeleteSuperAdminSessionException;
use PrestaShop\PrestaShop\Core\Domain\Session\Exception\FailedToDeleteSessionException;
use PrestaShop\PrestaShop\Core\Domain\Session\Exception\SessionConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Session\Exception\SessionException;
use PrestaShop\PrestaShop\Core\Domain\Session\Exception\SessionNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Session\SessionSettings;
use PrestaShop\PrestaShop\Core\Domain\Session\Query\GetSessionForEditing;
use PrestaShop\PrestaShop\Core\Domain\Session\QueryResult\EditableSession;
use PrestaShop\PrestaShop\Core\Search\Filters\Security\Sessions\EmployeeFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\Security\Sessions\CustomerFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SecurityController is responsible for displaying the
 * "Configure > Advanced parameters > Team > Sessions" page.
 */
class SecurityController extends FrameworkBundleAdminController
{
    /**
     * Show sessions listing page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param EmployeeFilters $filters
     *
     * @return Response
     */
    public function indexAction(EmployeeFilters $filters)
    {
        $generalForm = $this->getGeneralFormHandler()->getForm();

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/Security/index.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Security', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'requireFilterStatus' => false,
            'generalForm' => $generalForm->createView(),
        ]);
    }

    /**
     * Process the Security general configuration form.
     *
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))", message="You do not have permission to update this.", redirectRoute="admin_administration")
     * @DemoRestricted(redirectRoute="admin_administration")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processGeneralFormAction(Request $request)
    {
        return $this->processForm(
            $request,
            $this->getGeneralFormHandler(),
            'General'
        );
    }

    /**
     * Process the Security configuration form.
     *
     * @param Request $request
     * @param FormHandlerInterface $formHandler
     * @param string $hookName
     *
     * @return RedirectResponse
     */
    protected function processForm(Request $request, FormHandlerInterface $formHandler, string $hookName)
    {
        $this->dispatchHook(
            'actionAdminSecurityControllerPostProcess' . $hookName . 'Before',
            ['controller' => $this]
        );

        $this->dispatchHook('actionAdminSecurityControllerPostProcessBefore', ['controller' => $this]);

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

        return $this->redirectToRoute('admin_security');
    }

    /**
     * Show Employees sessions listing page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param EmployeeFilters $filters
     *
     * @return Response
     */
    public function employeesSessionsAction(EmployeeFilters $filters)
    {
        $sessionsEmployeesGridFactory = $this->get('prestashop.core.grid.factory.security.sessions.employees');

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Security/employees.html.twig',
            [
                'enableSidebar' => true,
                'layoutTitle' => $this->trans('Employees Sessions', 'Admin.Navigation.Menu'),
                'grid' => $this->presentGrid($sessionsEmployeesGridFactory->getGrid($filters)),
            ]
        );
    }

    /**
     * Show Customers sessions listing page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param CustomerFilters $filters
     *
     * @return Response
     */
    public function customersSessionsAction(CustomerFilters $filters)
    {
        $sessionsCustomersGridFactory = $this->get('prestashop.core.grid.factory.security.sessions.customers');

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Security/customers.html.twig',
            [
                'enableSidebar' => true,
                'layoutTitle' => $this->trans('Employees Sessions', 'Admin.Navigation.Menu'),
                'grid' => $this->presentGrid($sessionsCustomersGridFactory->getGrid($filters)),
            ]
        );
    }

    /**
     * Used for applying filtering actions.
     *
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request)
    {
        $definitionFactory = $this->get('prestashop.core.grid.definition.factory.profile');
        $definitionFactory = $definitionFactory->getDefinition();

        $gridFilterFormFactory = $this->get('prestashop.core.grid.filter.form_factory');
        $searchParametersForm = $gridFilterFormFactory->create($definitionFactory);
        $searchParametersForm->handleRequest($request);

        $filters = [];

        if ($searchParametersForm->isSubmitted()) {
            $filters = $searchParametersForm->getData();
        }

        return $this->redirectToRoute('admin_sessions_index', ['filters' => $filters]);
    }

    /**
     * Delete a session.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller')~'_')",
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(redirectRoute="admin_sessions_index")
     *
     * @param int $sessionId
     *
     * @return RedirectResponse
     */
    public function deleteEmployeeSessionAction(int $sessionId)
    {
        try {
            $deleteSessionCommand = new DeleteEmployeeSessionCommand($sessionId, $type);

            $this->getCommandBus()->handle($deleteSessionCommand);

            $this->addFlash('success', $this->trans('Successful deletion', 'Admin.Notifications.Success'));
        } catch (SessionException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_security_sessions_employee');
    }

    /**
     * Bulk delete sessions.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller')~'_')",
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(redirectRoute="admin_sessions_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDeleteAction(Request $request)
    {
        $profileIds = $request->request->get('profile_bulk');

        try {
            $deleteSessionsCommand = new BulkDeleteSessionCommand($profileIds);

            $this->getCommandBus()->handle($deleteSessionsCommand);

            $this->addFlash('success', $this->trans('Successful deletion', 'Admin.Notifications.Success'));
        } catch (SessionException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_sessions_index');
    }

    /**
     * Get human readable error for exception.
     *
     * @return array
     */
    protected function getErrorMessages()
    {
        return [
            SessionConstraintException::class => [
                SessionConstraintException::INVALID_NAME => $this->trans(
                    'This field cannot be longer than %limit% characters (incl. HTML tags)',
                    'Admin.Notifications.Error',
                    ['%limit%' => SessionSettings::NAME_MAX_LENGTH]
                ),
            ],
            SessionNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            CannotDeleteSuperAdminSessionException::class => $this->trans(
                'For security reasons, you cannot delete the Administrator\'s profile.',
                'Admin.Advparameters.Notification'
            ),
            FailedToDeleteSessionException::class => [
                FailedToDeleteSessionException::UNEXPECTED_ERROR => $this->trans(
                    'An error occurred while deleting the object.',
                    'Admin.Notifications.Error'
                ),
                FailedToDeleteSessionException::PROFILE_IS_ASSIGNED_TO_EMPLOYEE => $this->trans(
                    'Session(s) assigned to employee cannot be deleted',
                    'Admin.Notifications.Error'
                ),
                FailedToDeleteSessionException::PROFILE_IS_ASSIGNED_TO_CONTEXT_EMPLOYEE => $this->trans(
                    'You cannot delete your own profile',
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getGeneralFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.adapter.security.general.form_handler');
    }
}
