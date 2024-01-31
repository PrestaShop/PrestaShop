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
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(Request $request): Response
    {
        $generalForm = $this->getGeneralFormHandler()->getForm();
        $passwordPolicyForm = $this->getPasswordPolicyFormHandler()->getForm();

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Security/index.html.twig',
            [
                'enableSidebar' => true,
                'layoutHeaderToolbarBtn' => [],
                'layoutTitle' => $this->trans('Security', 'Admin.Navigation.Menu'),
                'passwordPolicyForm' => $passwordPolicyForm->createView(),
                'generalForm' => $generalForm->createView(),
                'multistoreInfoTip' => $this->trans(
                    'Note that this page is available in all shops context only, this is why your context has just switched.',
                    'Admin.Notifications.Info'
                ),
                'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
                'multistoreIsUsed' => ($this->get('prestashop.adapter.multistore_feature')->isUsed()
                                       && $this->get('prestashop.adapter.shop.context')->isShopContext()),
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
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))")]
    public function processGeneralFormAction(Request $request): RedirectResponse
    {
        return $this->processForm(
            $request,
            $this->getGeneralFormHandler(),
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
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))")]
    public function processPasswordPolicyFormAction(Request $request): RedirectResponse
    {
        return $this->processForm(
            $request,
            $this->getPasswordPolicyFormHandler(),
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
        $this->dispatchHook(
            $hookName,
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
     * @param EmployeeFilters $filters
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function employeeSessionAction(Request $request, EmployeeFilters $filters): Response
    {
        $sessionsEmployeesGridFactory = $this->get('prestashop.core.grid.factory.security.session.employee');

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Security/employees.html.twig',
            [
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
                'layoutTitle' => $this->trans('Employee sessions', 'Admin.Navigation.Menu'),
                'grid' => $this->presentGrid($sessionsEmployeesGridFactory->getGrid($filters)),
                'multistoreInfoTip' => $this->trans(
                    'Note that this page is available in all shops context only, this is why your context has just switched.',
                    'Admin.Notifications.Info'
                ),
                'multistoreIsUsed' => ($this->get('prestashop.adapter.multistore_feature')->isUsed()
                                       && $this->get('prestashop.adapter.shop.context')->isShopContext()),
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
    public function customerSessionAction(Request $request, CustomerFilters $filters): Response
    {
        $sessionsCustomersGridFactory = $this->get('prestashop.core.grid.factory.security.session.customer');

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Security/customers.html.twig',
            [
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
                'layoutTitle' => $this->trans('Customer sessions', 'Admin.Navigation.Menu'),
                'grid' => $this->presentGrid($sessionsCustomersGridFactory->getGrid($filters)),
                'multistoreInfoTip' => $this->trans(
                    'Note that this page is available in all shops context only, this is why your context has just switched.',
                    'Admin.Notifications.Info'
                ),
                'multistoreIsUsed' => ($this->get('prestashop.adapter.multistore_feature')->isUsed()
                                       && $this->get('prestashop.adapter.shop.context')->isShopContext()),
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

            $this->getCommandBus()->handle($clearSessionCommand);

            $this->addFlash('success', $this->trans('Successful deletion', 'Admin.Notifications.Success'));
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
     * @param int $sessionId
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")]
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
     * @param int $sessionId
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")]
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

    /**
     * @return FormHandlerInterface
     */
    protected function getPasswordPolicyFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.adapter.security.password_policy.form_handler');
    }
}
