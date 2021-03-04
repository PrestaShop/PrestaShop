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
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\ForwardCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\ReplyToCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\UpdateCustomerThreadStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerServiceSignature;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerThreadForViewing;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\CustomerThreadView;
use PrestaShop\PrestaShop\Core\Domain\Employee\Query\GetEmployeeEmailById;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;
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
     * @AdminSecurity(
     *     "is_granted(['read'], request.get('_legacy_controller'))",
     *     message="You do not have permission to view this.",
     *     redirectRoute="admin_customer_threads_index"
     * )
     *
     * @param Request $request
     * @param int $customerThreadId
     *
     * @return Response
     */
    public function viewAction(Request $request, $customerThreadId)
    {
        /** @var CustomerThreadView $customerThreadView */
        $customerThreadView = $this->getQueryBus()->handle(
            new GetCustomerThreadForViewing((int) $customerThreadId)
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
            'layoutTitle' => $this->trans('View', 'Admin.Actions'),
        ]);
    }

    /**
     * Reply to customer thread
     *
     * @AdminSecurity(
     *     "is_granted(['create', 'update'], request.get('_legacy_controller'))",
     *     message="You do not have permission to update this.",
     *     redirectRoute="admin_customer_threads_index"
     * )
     *
     * @param Request $request
     * @param int $customerThreadId
     *
     * @return RedirectResponse
     */
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
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))",
     *     message="You do not have permission to update this.",
     *     redirectRoute="admin_customer_threads_index"
     * )
     *
     * @param int $customerThreadId
     * @param string $newStatus
     *
     * @return RedirectResponse
     */
    public function updateStatusAction($customerThreadId, $newStatus)
    {
        try {
            $this->getCommandBus()->handle(
                new UpdateCustomerThreadStatusCommand((int) $customerThreadId, $newStatus)
            );

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, []));
        }

        return $this->redirectToRoute('admin_customer_threads_view', [
            'customerThreadId' => $customerThreadId,
        ]);
    }

    /**
     * Forward customer thread to another employee
     *
     * @AdminSecurity(
     *     "is_granted(['create', 'update'], request.get('_legacy_controller'))",
     *     message="You do not have permission to update this.",
     *     redirectRoute="admin_customer_threads_index"
     * )
     *
     * @param Request $request
     * @param int $customerThreadId
     *
     * @return RedirectResponse
     */
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
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, []));
        }

        return $this->redirectToRoute('admin_customer_threads_view', [
            'customerThreadId' => $customerThreadId,
        ]);
    }
}
