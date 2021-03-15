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

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Security\Command\BulkDeleteCustomerSessionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Security\Command\BulkDeleteEmployeeSessionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Security\Command\ClearCustomerSessionCommand;
use PrestaShop\PrestaShop\Core\Domain\Security\Command\ClearEmployeeSessionCommand;
use PrestaShop\PrestaShop\Core\Domain\Security\Command\DeleteCustomerSessionCommand;
use PrestaShop\PrestaShop\Core\Domain\Security\Command\DeleteEmployeeSessionCommand;
use PrestaShop\PrestaShop\Core\Domain\Security\Exception\CannotBulkDeleteCustomerSessionException;
use PrestaShop\PrestaShop\Core\Domain\Security\Exception\CannotBulkDeleteEmployeeSessionException;
use PrestaShop\PrestaShop\Core\Domain\Security\Exception\CannotClearCustomerSessionException;
use PrestaShop\PrestaShop\Core\Domain\Security\Exception\CannotClearEmployeeSessionException;
use PrestaShop\PrestaShop\Core\Domain\Security\Exception\CannotDeleteCustomerSessionException;
use PrestaShop\PrestaShop\Core\Domain\Security\Exception\CannotDeleteEmployeeSessionException;
use PrestaShop\PrestaShop\Core\Domain\Security\Exception\SessionNotFoundException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\Security\Session\CustomerFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\Security\Session\EmployeeFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SecurityController is responsible for displaying the
 * "Configure > Advanced parameters > Security" page.
 */
class SecurityController extends FrameworkBundleAdminController
{
    /**
     * Show sessions listing page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        $generalForm = $this->getGeneralFormHandler()->getForm();

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Security/index.html.twig',
            [
                'layoutHeaderToolbarBtn' => [],
                'layoutTitle' => $this->trans('Security', 'Admin.Navigation.Menu'),
                'generalForm' => $generalForm->createView(),
            ]
        );
    }

    /**
     * Process the Security general configuration form.
     *
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processGeneralFormAction(Request $request): RedirectResponse
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
    protected function processForm(Request $request, FormHandlerInterface $formHandler, string $hookName): RedirectResponse
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
    public function employeeSessionAction(EmployeeFilters $filters): Response
    {
        $toolbarButtons = [
            'clear_cache' => [
                'href' => $this->generateUrl('admin_security_sessions_employee_clear'),
                'desc' => $this->trans('Clear outdated sessions', 'Admin.Advparameters.Feature'),
                'icon' => 'delete',
            ],
        ];

        $sessionsEmployeesGridFactory = $this->get('prestashop.core.grid.factory.security.session.employee');

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Security/employees.html.twig',
            [
                'enableSidebar' => true,
                'layoutHeaderToolbarBtn' => $toolbarButtons,
                'layoutTitle' => $this->trans('Employee sessions', 'Admin.Navigation.Menu'),
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
    public function customerSessionAction(CustomerFilters $filters): Response
    {
        $toolbarButtons = [
            'clear_cache' => [
                'href' => $this->generateUrl('admin_security_sessions_customer_clear'),
                'desc' => $this->trans('Clear outdated sessions', 'Admin.Advparameters.Feature'),
                'icon' => 'delete',
            ],
        ];

        $sessionsCustomersGridFactory = $this->get('prestashop.core.grid.factory.security.session.customer');

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Security/customers.html.twig',
            [
                'enableSidebar' => true,
                'layoutHeaderToolbarBtn' => $toolbarButtons,
                'layoutTitle' => $this->trans('Customer sessions', 'Admin.Navigation.Menu'),
                'grid' => $this->presentGrid($sessionsCustomersGridFactory->getGrid($filters)),
            ]
        );
    }

    /**
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")
     *
     * @return RedirectResponse
     */
    public function clearCustomerSessionAction()
    {
        try {
            $clearSessionCommand = new ClearCustomerSessionCommand();

            $this->getCommandBus()->handle($clearSessionCommand);

            $this->addFlash('success', $this->trans('Successful deletion', 'Admin.Notifications.Success'));
        } catch (CoreException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_security_sessions_customer_list');
    }

    /**
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")
     *
     * @return RedirectResponse
     */
    public function clearEmployeeSessionAction()
    {
        try {
            $clearSessionCommand = new ClearEmployeeSessionCommand();

            $this->getCommandBus()->handle($clearSessionCommand);

            $this->addFlash('success', $this->trans('Successful deletion', 'Admin.Notifications.Success'));
        } catch (CoreException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_security_sessions_employee_list');
    }

    /**
     * Delete an employee session.
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")
     *
     * @param int $sessionId
     *
     * @return RedirectResponse
     */
    public function deleteEmployeeSessionAction(int $sessionId): RedirectResponse
    {
        try {
            $deleteSessionCommand = new DeleteEmployeeSessionCommand($sessionId);

            $this->getCommandBus()->handle($deleteSessionCommand);

            $this->addFlash('success', $this->trans('Successful deletion', 'Admin.Notifications.Success'));
        } catch (CoreException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_security_sessions_employee_list');
    }

    /**
     * Delete a customer session.
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")
     *
     * @param int $sessionId
     *
     * @return RedirectResponse
     */
    public function deleteCustomerSessionAction(int $sessionId): RedirectResponse
    {
        try {
            $deleteSessionCommand = new DeleteCustomerSessionCommand($sessionId);

            $this->getCommandBus()->handle($deleteSessionCommand);

            $this->addFlash('success', $this->trans('Successful deletion', 'Admin.Notifications.Success'));
        } catch (CoreException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_security_sessions_customer_list');
    }

    /**
     * Bulk delete customer session.
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDeleteCustomerSessionAction(Request $request): RedirectResponse
    {
        $sessionIds = $request->request->get('security_session_customer_bulk');

        try {
            $deleteSessionsCommand = new BulkDeleteCustomerSessionsCommand($sessionIds);

            $this->getCommandBus()->handle($deleteSessionsCommand);

            $this->addFlash('success', $this->trans('Successful deletion', 'Admin.Notifications.Success'));
        } catch (CoreException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_security_sessions_customer_list');
    }

    /**
     * Bulk delete employee session.
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDeleteEmployeeSessionAction(Request $request): RedirectResponse
    {
        $sessionIds = $request->request->get('security_session_employee_bulk');

        try {
            $deleteSessionsCommand = new BulkDeleteEmployeeSessionsCommand($sessionIds);

            $this->getCommandBus()->handle($deleteSessionsCommand);

            $this->addFlash('success', $this->trans('Successful deletion', 'Admin.Notifications.Success'));
        } catch (CoreException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_security_sessions_employee_list');
    }

    /**
     * Get human readable error for exception.
     *
     * @param Exception $e
     *
     * @return array
     */
    protected function getErrorMessages(Exception $e): array
    {
        return [
            SessionNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                'Admin.Notifications.Error'
            ),
            CannotDeleteCustomerSessionException::class => $this->trans(
                'An error occurred while deleting the object.',
                'Admin.Notifications.Error'
            ),
            CannotClearCustomerSessionException::class => $this->trans(
                'An error occurred while clearing objects.',
                'Admin.Notifications.Error'
            ),
            CannotBulkDeleteCustomerSessionException::class => $this->trans(
                '%s: %s',
                'Admin.Global',
                [
                    $this->trans(
                        'An error occurred while deleting this selection.',
                        'Admin.Notifications.Error'
                    ),
                    $e instanceof CannotBulkDeleteCustomerSessionException ? implode(', ', $e->getSessionIds()) : '',
                ]
            ),
            CannotDeleteEmployeeSessionException::class => $this->trans(
                'An error occurred while deleting the object.',
                'Admin.Notifications.Error'
            ),
            CannotClearEmployeeSessionException::class => $this->trans(
                'An error occurred while clearing objects.',
                'Admin.Notifications.Error'
            ),
            CannotBulkDeleteEmployeeSessionException::class => $this->trans(
                '%s: %s',
                'Admin.Global',
                [
                    $this->trans(
                        'An error occurred while deleting this selection.',
                        'Admin.Notifications.Error'
                    ),
                    $e instanceof CannotBulkDeleteEmployeeSessionException ? implode(', ', $e->getSessionIds()) : '',
                ]
            ),
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
