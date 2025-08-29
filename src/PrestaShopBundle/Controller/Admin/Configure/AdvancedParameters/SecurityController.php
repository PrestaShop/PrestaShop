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
use PrestaShop\PrestaShop\Core\Domain\Security\Command\ClearOutdatedCustomerSessionCommand;
use PrestaShop\PrestaShop\Core\Domain\Security\Command\ClearOutdatedEmployeeSessionCommand;
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
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\Security\Session\CustomerFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\Security\Session\EmployeeFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Controller\Attribute\AllShopContext;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SecurityController is responsible for displaying the
 * "Configure > Advanced parameters > Security" page.
 */
#[AllShopContext]
class SecurityController extends PrestaShopAdminController
{
    /**
     * Show sessions listing page.
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.security.general.form_handler')]
        FormHandlerInterface $generalFormHandler,
        #[Autowire(service: 'prestashop.adapter.security.password_policy.form_handler')]
        FormHandlerInterface $passwordPolicyFormHandler,
    ): Response {
        $generalForm = $generalFormHandler->getForm();
        $passwordPolicyForm = $passwordPolicyFormHandler->getForm();

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Security/index.html.twig',
            [
                'enableSidebar' => true,
                'layoutHeaderToolbarBtn' => [],
                'layoutTitle' => $this->trans('Security', [], 'Admin.Navigation.Menu'),
                'passwordPolicyForm' => $passwordPolicyForm->createView(),
                'generalForm' => $generalForm->createView(),
                'multistoreInfoTip' => $this->trans(
                    'Note that this page is available in all shops context only, this is why your context has just switched.',
                    [],
                    'Admin.Notifications.Info'
                ),
                'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
                'multistoreIsUsed' => $this->getShopContext()->isMultiShopUsed() && $this->getShopContext()->getShopConstraint()->getShopId() !== null,
            ]
        );
    }

    /**
     * Process the Security general configuration form.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_security')]
    public function processGeneralFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.security.general.form_handler')]
        FormHandlerInterface $generalFormHandler,
    ): RedirectResponse {
        return $this->processForm(
            $request,
            $generalFormHandler,
            'actionAdminSecurityControllerPostProcessGeneralBefore'
        );
    }

    /**
     * Process the Security password policy configuration form.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_security')]
    public function processPasswordPolicyFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.security.password_policy.form_handler')]
        FormHandlerInterface $passwordPolicyFormHandler,
    ): RedirectResponse {
        return $this->processForm(
            $request,
            $passwordPolicyFormHandler,
            'actionAdminSecurityControllerPostProcessPasswordPolicyBefore'
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
        $this->dispatchHookWithParameters(
            $hookName,
            ['controller' => $this]
        );

        $this->dispatchHookWithParameters('actionAdminSecurityControllerPostProcessBefore', ['controller' => $this]);

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

        return $this->redirectToRoute('admin_security');
    }

    /**
     * Show Employees sessions listing page.
     *
     * @param EmployeeFilters $filters
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function employeeSessionAction(
        Request $request,
        EmployeeFilters $filters,
        #[Autowire(service: 'prestashop.core.grid.factory.security.session.employee')]
        GridFactoryInterface $sessionsEmployeesGridFactory,
    ): Response {
        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Security/employees.html.twig',
            [
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
                'layoutTitle' => $this->trans('Employee sessions', [], 'Admin.Navigation.Menu'),
                'grid' => $this->presentGrid($sessionsEmployeesGridFactory->getGrid($filters)),
                'multistoreInfoTip' => $this->trans(
                    'Note that this page is available in all shops context only, this is why your context has just switched.',
                    [],
                    'Admin.Notifications.Info'
                ),
                'multistoreIsUsed' => $this->getShopContext()->isMultiShopUsed() && $this->getShopContext()->getShopConstraint()->getShopId() !== null,
            ]
        );
    }

    /**
     * Show Customers sessions listing page.
     *
     * @param CustomerFilters $filters
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function customerSessionAction(
        Request $request,
        CustomerFilters $filters,
        #[Autowire(service: 'prestashop.core.grid.factory.security.session.customer')]
        GridFactoryInterface $sessionsCustomersGridFactory,
    ): Response {
        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Security/customers.html.twig',
            [
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
                'layoutTitle' => $this->trans('Customer sessions', [], 'Admin.Navigation.Menu'),
                'grid' => $this->presentGrid($sessionsCustomersGridFactory->getGrid($filters)),
                'multistoreInfoTip' => $this->trans(
                    'Note that this page is available in all shops context only, this is why your context has just switched.',
                    [],
                    'Admin.Notifications.Info'
                ),
                'multistoreIsUsed' => $this->getShopContext()->isMultiShopUsed() && $this->getShopContext()->getShopConstraint()->getShopId() !== null,
            ]
        );
    }

    /**
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")]
    public function clearCustomerSessionAction(): RedirectResponse
    {
        try {
            $clearSessionCommand = new ClearOutdatedCustomerSessionCommand();

            $this->dispatchCommand($clearSessionCommand);

            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        } catch (CoreException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_security_sessions_customer_list');
    }

    /**
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")]
    public function clearEmployeeSessionAction(): RedirectResponse
    {
        try {
            $clearSessionCommand = new ClearOutdatedEmployeeSessionCommand();

            $this->dispatchCommand($clearSessionCommand);

            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        } catch (CoreException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_security_sessions_employee_list');
    }

    /**
     * Delete an employee session.
     *
     * @param int $sessionId
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_security_sessions_employee_list')]
    public function deleteEmployeeSessionAction(int $sessionId): RedirectResponse
    {
        try {
            $deleteSessionCommand = new DeleteEmployeeSessionCommand($sessionId);

            $this->dispatchCommand($deleteSessionCommand);

            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        } catch (CoreException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_security_sessions_employee_list');
    }

    /**
     * Delete a customer session.
     *
     * @param int $sessionId
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_security_sessions_customer_list')]
    public function deleteCustomerSessionAction(int $sessionId): RedirectResponse
    {
        try {
            $deleteSessionCommand = new DeleteCustomerSessionCommand($sessionId);

            $this->dispatchCommand($deleteSessionCommand);

            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        } catch (CoreException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_security_sessions_customer_list');
    }

    /**
     * Bulk delete customer session.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")]
    public function bulkDeleteCustomerSessionAction(Request $request): RedirectResponse
    {
        $sessionIds = $request->request->all('security_session_customer_bulk');

        try {
            $deleteSessionsCommand = new BulkDeleteCustomerSessionsCommand($sessionIds);

            $this->dispatchCommand($deleteSessionsCommand);

            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        } catch (CoreException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_security_sessions_customer_list');
    }

    /**
     * Bulk delete employee session.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")]
    public function bulkDeleteEmployeeSessionAction(Request $request): RedirectResponse
    {
        $sessionIds = $request->request->all('security_session_employee_bulk');

        try {
            $deleteSessionsCommand = new BulkDeleteEmployeeSessionsCommand($sessionIds);

            $this->dispatchCommand($deleteSessionsCommand);

            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        } catch (CoreException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_security_sessions_employee_list');
    }

    /**
     * Get human-readable error for exception.
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
                [],
                'Admin.Notifications.Error'
            ),
            CannotDeleteCustomerSessionException::class => $this->trans(
                'An error occurred while deleting the object.',
                [],
                'Admin.Notifications.Error'
            ),
            CannotClearCustomerSessionException::class => $this->trans(
                'An error occurred while clearing objects.',
                [],
                'Admin.Notifications.Error'
            ),
            CannotBulkDeleteCustomerSessionException::class => $this->trans(
                '%s: %s',
                [
                    $this->trans(
                        'An error occurred while deleting this selection.',
                        [],
                        'Admin.Notifications.Error'
                    ),
                    $e instanceof CannotBulkDeleteCustomerSessionException ? implode(', ', $e->getSessionIds()) : '',
                ],
                'Admin.Global',
            ),
            CannotDeleteEmployeeSessionException::class => $this->trans(
                'An error occurred while deleting the object.',
                [],
                'Admin.Notifications.Error'
            ),
            CannotClearEmployeeSessionException::class => $this->trans(
                'An error occurred while clearing objects.',
                [],
                'Admin.Notifications.Error'
            ),
            CannotBulkDeleteEmployeeSessionException::class => $this->trans(
                '%s: %s',
                [
                    $this->trans(
                        'An error occurred while deleting this selection.',
                        [],
                        'Admin.Notifications.Error'
                    ),
                    $e instanceof CannotBulkDeleteEmployeeSessionException ? implode(', ', $e->getSessionIds()) : '',
                ],
                'Admin.Global',
            ),
        ];
    }
}
