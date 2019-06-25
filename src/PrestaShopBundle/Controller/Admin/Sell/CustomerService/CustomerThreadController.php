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

namespace PrestaShopBundle\Controller\Admin\Sell\CustomerService;

use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\ReplyToCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\UpdateCustomerThreadStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerServiceSignature;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerThreadForViewing;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\CustomerThreadView;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Sell\CustomerService\ReplyToCustomerThreadType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerThreadController extends FrameworkBundleAdminController
{
    /**
     * @param int $customerThreadId
     *
     * @return Response
     */
    public function viewAction($customerThreadId)
    {
        /** @var CustomerThreadView $customerThreadView */
        $customerThreadView = $this->getQueryBus()->handle(new GetCustomerThreadForViewing((int) $customerThreadId));

        /** @var string $customerServiceSignature */
        $customerServiceSignature = $this->getQueryBus()->handle(
            new GetCustomerServiceSignature($customerThreadView->getLanguageId()->getValue())
        );

        $replyToCustomerThreadForm = $this->createForm(ReplyToCustomerThreadType::class, [
            'reply_message' => $customerServiceSignature,
        ]);

        dump($customerThreadView);

        return $this->render('@PrestaShop/Admin/Sell/CustomerService/CustomerThread/view.html.twig', [
            'customerThreadView' => $customerThreadView,
            'employeeAvatarUrl' => $this->getContext()->employee->getImage(),
            'customerServiceSignature' => $customerServiceSignature,
            'replyToCustomerThreadForm' => $replyToCustomerThreadForm->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param int $customerThreadId
     *
     * @return RedirectResponse
     */
    public function replyAction(Request $request, $customerThreadId)
    {
        $replyToCustomerThreadForm = $this->createForm(ReplyToCustomerThreadType::class);
        $replyToCustomerThreadForm->handleRequest($request);

        if ($replyToCustomerThreadForm->isSubmitted() && $replyToCustomerThreadForm->isValid()) {
            $data = $replyToCustomerThreadForm->getData();

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

            return $this->redirectToRoute('admin_customer_threads_view', [
                'customerThreadId' => $customerThreadId,
            ]);
        }

        foreach ($replyToCustomerThreadForm->getErrors(true) as $error) {
            $this->addFlash('error', $error->getMessage());
        }

        return $this->redirectToRoute('admin_customer_threads_view', [
            'customerThreadId' => $customerThreadId,
        ]);
    }

    /**
     * Update customer thread status
     *
     * @param int $customerThreadId
     * @param string $newStatus
     *
     * @return RedirectResponse
     */
    public function updateStatusAction($customerThreadId, $newStatus)
    {
        $this->getCommandBus()->handle(
            new UpdateCustomerThreadStatusCommand((int) $customerThreadId, $newStatus)
        );

        $this->addFlash(
            'success',
            $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
        );

        return $this->redirectToRoute('admin_customer_threads_view', [
            'customerThreadId' => $customerThreadId,
        ]);
    }
}
