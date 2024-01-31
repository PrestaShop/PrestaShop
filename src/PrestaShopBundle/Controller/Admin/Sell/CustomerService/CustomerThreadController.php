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

namespace PrestaShopBundle\Controller\Admin\Sell\CustomerService;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\BulkDeleteCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\DeleteCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\ForwardCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\ReplyToCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\UpdateCustomerThreadStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CannotDeleteCustomerThreadException;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerServiceException;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerThreadNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerServiceSignature;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerThreadForViewing;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\CustomerThreadView;
use PrestaShop\PrestaShop\Core\Domain\Employee\Query\GetEmployeeEmailById;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;
use PrestaShop\PrestaShop\Core\Search\Filters\CustomerThreadFilter;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\CustomerService\CustomerThread\ForwardCustomerThreadType;
use PrestaShopBundle\Form\Admin\Sell\CustomerService\ReplyToCustomerThreadType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manages page under "Sell > Customer Service > Customer Service"
 */
class CustomerThreadController extends FrameworkBundleAdminController
{
    /**
     * Show list of customer threads
     *
     * @param Request $request
     * @param CustomerThreadFilter $filters
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(Request $request, CustomerThreadFilter $filters): Response
    {
        $customerThreadGridFactory = $this->get('prestashop.core.grid.factory.customer_thread');
        $customerThreadGrid = $customerThreadGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Sell/CustomerService/CustomerThread/index.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'customerThreadGrid' => $this->presentGrid($customerThreadGrid),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Customer service', 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * View customer thread
     *
     * @param Request $request
     * @param int $customerThreadId
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'You do not have permission to view this.', redirectRoute: 'admin_customer_threads_index')]
    public function viewAction(Request $request, int $customerThreadId)
    {
        /** @var CustomerThreadView $customerThreadView */
        $customerThreadView = $this->getQueryBus()->handle(
            new GetCustomerThreadForViewing($customerThreadId)
        );

        /** @var string $customerServiceSignature */
        $customerServiceSignature = $this->getQueryBus()->handle(
            new GetCustomerServiceSignature($customerThreadView->getLanguageId()->getValue())
        );

        $replyToCustomerThreadForm = $this->createForm(ReplyToCustomerThreadType::class, [
            'reply_message' => $customerServiceSignature,
        ]);

        $forwardCustomerThreadForm = $this->createForm(ForwardCustomerThreadType::class);

        return $this->render('@PrestaShop/Admin/Sell/CustomerService/CustomerThread/view.html.twig', [
            'customerThreadView' => $customerThreadView,
            'employeeAvatarUrl' => $this->getContext()->employee->getImage(),
            'customerServiceSignature' => $customerServiceSignature,
            'replyToCustomerThreadForm' => $replyToCustomerThreadForm->createView(),
            'forwardCustomerThreadForm' => $forwardCustomerThreadForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Customer thread #%s', 'Admin.Navigation.Menu', [$customerThreadId]),
        ]);
    }

    /**
     * Reply to customer thread
     *
     * @param Request $request
     * @param int $customerThreadId
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_customer_threads_index')]
    public function replyAction(Request $request, $customerThreadId)
    {
        $replyToCustomerThreadForm = $this->createForm(ReplyToCustomerThreadType::class);
        $replyToCustomerThreadForm->handleRequest($request);

        if (!$replyToCustomerThreadForm->isSubmitted()) {
            return $this->redirectToRoute('admin_customer_threads_view', [
                'customerThreadId' => $customerThreadId,
            ]);
        }

        if (!$replyToCustomerThreadForm->isValid()) {
            foreach ($replyToCustomerThreadForm->getErrors(true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }

            return $this->redirectToRoute('admin_customer_threads_view', [
                'customerThreadId' => $customerThreadId,
            ]);
        }

        $data = $replyToCustomerThreadForm->getData();

        try {
            $this->getCommandBus()->handle(
                new ReplyToCustomerThreadCommand((int) $customerThreadId, $data['reply_message'])
            );

            $this->addFlash(
                'success',
                $this->trans(
                    'The message was successfully sent to the customer.',
                    'Admin.Orderscustomers.Notification'
                )
            );
        } catch (CustomerServiceException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, []));
        }

        return $this->redirectToRoute('admin_customer_threads_view', [
            'customerThreadId' => $customerThreadId,
        ]);
    }

    /**
     * Update customer thread status
     *
     * @param int $customerThreadId
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_customer_threads_index')]
    public function updateStatusFromViewAction(int $customerThreadId, Request $request)
    {
        $this->handleCustomerThreadStatusUpdate($customerThreadId, $request->request->get('newStatus'));

        return $this->redirectToRoute('admin_customer_threads_view', [
            'customerThreadId' => $customerThreadId,
        ]);
    }

    /**
     * Updates customer thread status directly from list page.
     *
     * @param int $customerThreadId
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_customer_threads')]
    public function updateStatusFromListAction(int $customerThreadId, Request $request): RedirectResponse
    {
        $this->handleCustomerThreadStatusUpdate($customerThreadId, $request->request->get('value'));

        return $this->redirectToRoute('admin_customer_threads');
    }

    /**
     * Forward customer thread to another employee
     *
     * @param Request $request
     * @param int $customerThreadId
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_customer_threads_index')]
    public function forwardAction(Request $request, $customerThreadId)
    {
        $forwardCustomerThreadForm = $this->createForm(ForwardCustomerThreadType::class);
        $forwardCustomerThreadForm->handleRequest($request);

        if (!$forwardCustomerThreadForm->isSubmitted()) {
            return $this->redirectToRoute('admin_customer_threads_view', [
                'customerThreadId' => $customerThreadId,
            ]);
        }

        if (!$forwardCustomerThreadForm->isValid()) {
            foreach ($forwardCustomerThreadForm->getErrors(true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }

            return $this->redirectToRoute('admin_customer_threads_view', [
                'customerThreadId' => $customerThreadId,
            ]);
        }

        $data = $forwardCustomerThreadForm->getData();

        if (!$data['employee_id'] && empty($data['someone_else_email'])) {
            $this->addFlash('error', $this->trans('The email address is invalid.', 'Admin.Notifications.Error'));

            return $this->redirectToRoute('admin_customer_threads_view', [
                'customerThreadId' => $customerThreadId,
            ]);
        }

        if ($data['employee_id']) {
            /** @var Email $employeeEmail */
            $employeeEmail = $this->getQueryBus()->handle(new GetEmployeeEmailById((int) $data['employee_id']));
            $forwardEmail = $employeeEmail->getValue();

            $command = ForwardCustomerThreadCommand::toAnotherEmployee(
                (int) $customerThreadId,
                (int) $data['employee_id'],
                $data['comment']
            );
        } else {
            $forwardEmail = $data['someone_else_email'];

            $command = ForwardCustomerThreadCommand::toSomeoneElse(
                (int) $customerThreadId,
                $data['someone_else_email'],
                $data['comment']
            );
        }

        try {
            $this->getCommandBus()->handle($command);

            $this->addFlash(
                'success',
                sprintf('%s %s', $this->trans('Message forwarded to', 'Admin.Catalog.Feature'), $forwardEmail)
            );
        } catch (CustomerServiceException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, []));
        }

        return $this->redirectToRoute('admin_customer_threads_view', [
            'customerThreadId' => $customerThreadId,
        ]);
    }

    /**
     * Delete customer thread
     *
     * @param int $customerThreadId
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_customer_threads')]
    public function deleteAction(int $customerThreadId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(new DeleteCustomerThreadCommand($customerThreadId));
            $this->addFlash('success', $this->trans('Successful deletion', 'Admin.Notifications.Success'));
        } catch (CustomerServiceException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_customer_threads');
        }

        return $this->redirectToRoute('admin_customer_threads');
    }

    /**
     * Bulk delete customer thread
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_customer_threads')]
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        $customerThreadId = $this->getBulkCustomerThreadsFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteCustomerThreadCommand($customerThreadId));

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', 'Admin.Notifications.Success')
            );
        } catch (CustomerThreadNotFoundException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_customer_threads');
    }

    /**
     * Returns customer thread error messages mapping.
     *
     * @return array
     */
    private function getErrorMessages(): array
    {
        return [
            CustomerThreadNotFoundException::class => $this->trans(
                'This customer thread does not exist.',
                'Admin.International.Notification'
            ),
            CannotDeleteCustomerThreadException::class => $this->trans(
                'Cannot delete this customer thread.',
                'Admin.International.Notification'
            ),
            CustomerServiceException::class => [
                CustomerServiceException::FAILED_TO_ADD_CUSTOMER_MESSAGE => $this->trans(
                    'Failed to add customer message.',
                    'Admin.International.Notification'
                ),
                CustomerServiceException::FAILED_TO_UPDATE_STATUS => $this->trans(
                    'Failed to update customer thread status.',
                    'Admin.International.Notification'
                ),
                CustomerServiceException::INVALID_COMMENT => $this->trans(
                    'Comment is not valid.',
                    'Admin.International.Notification'
                ),
            ],
        ];
    }

    /**
     * Collects customer thread IDs from request.
     *
     * @param Request $request
     *
     * @return array
     */
    private function getBulkCustomerThreadsFromRequest(Request $request): array
    {
        $customerThreadIds = $request->request->all('customer_thread_bulk');

        if (!is_array($customerThreadIds)) {
            return [];
        }

        return array_map('intval', $customerThreadIds);
    }

    private function handleCustomerThreadStatusUpdate(int $customerThreadId, string $newStatus)
    {
        try {
            $this->getCommandBus()->handle(
                new UpdateCustomerThreadStatusCommand((int) $customerThreadId, $newStatus)
            );

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CustomerServiceException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, []));
        }
    }
}
