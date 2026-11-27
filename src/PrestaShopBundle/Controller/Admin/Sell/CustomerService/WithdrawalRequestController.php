<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Sell\CustomerService;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Command\UpdateWithdrawalRequestStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Exception\CannotSaveWithdrawalRequestException;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Exception\WithdrawalRequestConstraintException;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Exception\WithdrawalRequestNotFoundException;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\WithdrawalRequestGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\WithdrawalRequestFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Backs the "Sell > Customer Service > Withdrawal Requests" page: the withdrawal statements customers submitted
 * with the withdrawal function, and the options of the function.
 */
final class WithdrawalRequestController extends PrestaShopAdminController
{
    private const TAB_CLASS_NAME = 'AdminWithdrawalRequests';

    #[AdminSecurity("is_granted('read', 'AdminWithdrawalRequests')")]
    public function indexAction(
        WithdrawalRequestFilters $filters,
        #[Autowire(service: 'prestashop.core.grid.factory.withdrawal_request')]
        GridFactoryInterface $gridFactory,
        #[Autowire(service: 'prestashop.admin.withdrawal_request_options.form_handler')]
        FormHandlerInterface $optionsFormHandler,
    ): Response {
        return $this->renderIndex($filters, $gridFactory, $optionsFormHandler->getForm());
    }

    #[AdminSecurity("is_granted('read', 'AdminWithdrawalRequests')")]
    public function searchAction(
        Request $request,
        #[Autowire(service: WithdrawalRequestGridDefinitionFactory::class)]
        GridDefinitionFactoryInterface $gridDefinitionFactory,
    ): RedirectResponse {
        return $this->buildSearchResponse(
            $gridDefinitionFactory,
            $request,
            WithdrawalRequestGridDefinitionFactory::GRID_ID,
            'admin_withdrawal_requests_index'
        );
    }

    #[DemoRestricted(redirectRoute: 'admin_withdrawal_requests_index')]
    #[AdminSecurity("is_granted('update', 'AdminWithdrawalRequests')", redirectRoute: 'admin_withdrawal_requests_index')]
    public function saveOptionsAction(
        Request $request,
        WithdrawalRequestFilters $filters,
        #[Autowire(service: 'prestashop.core.grid.factory.withdrawal_request')]
        GridFactoryInterface $gridFactory,
        #[Autowire(service: 'prestashop.admin.withdrawal_request_options.form_handler')]
        FormHandlerInterface $optionsFormHandler,
    ): Response {
        $optionsForm = $optionsFormHandler->getForm();
        $optionsForm->handleRequest($request);

        if ($optionsForm->isSubmitted() && $optionsForm->isValid()) {
            $errors = $optionsFormHandler->save($optionsForm->getData());
            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_withdrawal_requests_index');
            }

            $this->addFlashErrors($errors);
        }

        return $this->renderIndex($filters, $gridFactory, $optionsForm);
    }

    #[DemoRestricted(redirectRoute: 'admin_withdrawal_requests_index')]
    #[AdminSecurity("is_granted('update', 'AdminWithdrawalRequests')", redirectRoute: 'admin_withdrawal_requests_index')]
    public function updateStatusAction(int $withdrawalRequestId, string $status): RedirectResponse
    {
        try {
            $this->dispatchCommand(new UpdateWithdrawalRequestStatusCommand($withdrawalRequestId, $status));
            $this->addFlash('success', $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success'));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_withdrawal_requests_index');
    }

    private function renderIndex(
        WithdrawalRequestFilters $filters,
        GridFactoryInterface $gridFactory,
        FormInterface $optionsForm,
    ): Response {
        return $this->render('@PrestaShop/Admin/Sell/CustomerService/WithdrawalRequest/index.html.twig', [
            'help_link' => $this->generateSidebarLink(self::TAB_CLASS_NAME),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Withdrawal requests', [], 'Admin.Navigation.Menu'),
            'withdrawalRequestGrid' => $this->presentGrid($gridFactory->getGrid($filters)),
            'withdrawalRequestOptionsForm' => $optionsForm->createView(),
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function getErrorMessages(): array
    {
        return [
            WithdrawalRequestNotFoundException::class => $this->trans('The object cannot be loaded (or found).', [], 'Admin.Notifications.Error'),
            WithdrawalRequestConstraintException::class => $this->trans('The object cannot be loaded (the identifier is missing or invalid)', [], 'Admin.Notifications.Error'),
            CannotSaveWithdrawalRequestException::class => $this->trans('An error occurred while updating the status.', [], 'Admin.Notifications.Error'),
        ];
    }
}
