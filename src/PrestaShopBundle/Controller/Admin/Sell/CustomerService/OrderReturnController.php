<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Admin\Sell\CustomerService;

use Exception;
use PrestaShop\PrestaShop\Adapter\OrderReturn\Repository\OrderReturnRepository;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command\BulkDeleteOrderReturnsCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command\DeleteOrderReturnCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\BulkDeleteOrderReturnsException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\BulkDeleteProductsFromOrderReturnException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\CannotDeleteLastProductFromOrderReturnException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\DeleteOrderReturnException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\DeleteProductFromOrderReturnException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnConstraintException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnOrderStateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\UpdateOrderReturnException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Query\GetOrderReturnForEditing;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Query\GetOrderReturnProducts;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryResult\OrderReturnForEditing;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnId;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\OrderReturnStateSettings;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface as OptionFormHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\PDF\PDFGeneratorInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\OrderReturnFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * OrderReturnController backs the "Sell > Customer Service > Merchandise Returns" admin page.
 *
 * The user-facing label remains "Merchandise Returns"; the canonical domain name is OrderReturn.
 */
class OrderReturnController extends PrestaShopAdminController
{
    /**
     * Render the order returns grid and the options block.
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute: 'admin_merchandise_returns_index')]
    public function indexAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.grid.factory.order_return')]
        GridFactoryInterface $gridFactory,
        OrderReturnFilters $filters,
        #[Autowire(service: 'prestashop.admin.order_return_options.form_handler')]
        OptionFormHandlerInterface $optionFormHandler
    ): Response {
        $optionsForm = $optionFormHandler->getForm();
        $optionsForm->handleRequest($request);

        if ($optionsForm->isSubmitted() && $optionsForm->isValid()) {
            $errors = $optionFormHandler->save($optionsForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_merchandise_returns_index');
            } else {
                $this->addFlashErrors($errors);
            }
        }

        return $this->render('@PrestaShop/Admin/Sell/CustomerService/OrderReturn/index.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'orderReturnsGrid' => $this->presentGrid($gridFactory->getGrid($filters)),
            'orderReturnsOptionsForm' => $optionsForm->createView(),
            'enableSidebar' => true,
        ]);
    }

    /**
     * Edit existing order return
     *
     * @param int $orderReturnId
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_merchandise_returns_index')]
    public function editAction(
        int $orderReturnId,
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.order_return_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.order_return_form_handler')]
        FormHandlerInterface $formHandler
    ): Response {
        try {
            $form = $formBuilder->getFormFor($orderReturnId);
            $form->handleRequest($request);

            $result = $formHandler->handleFor($orderReturnId, $form);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));

                if ($request->request->has('submitAddorder_returnAndStay')) {
                    return $this->redirectToRoute('admin_order_returns_edit', ['orderReturnId' => $orderReturnId]);
                }

                return $this->redirectToRoute('admin_merchandise_returns_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_merchandise_returns_index');
        }

        $products = $this->dispatchQuery(new GetOrderReturnProducts($orderReturnId));
        /** @var OrderReturnForEditing $orderReturnForEditing */
        $orderReturnForEditing = $this->dispatchQuery(new GetOrderReturnForEditing($orderReturnId));

        return $this->render('@PrestaShop/Admin/Sell/CustomerService/OrderReturn/edit.html.twig', [
            'orderReturnId' => $orderReturnId,
            'orderReturnForm' => $form->createView(),
            'orderReturnProducts' => $products,
            'orderReturnStateId' => $orderReturnForEditing->getOrderReturnStateId(),
            'pdfDownloadStateId' => OrderReturnStateSettings::STATE_WAITING_FOR_PACKAGE,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Return merchandise authorization (RMA)', [], 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * Streams the merchant-facing return PDF.
     *
     * Mirrors the legacy gating: the PDF is only available when the merchandise return is in
     * the "Waiting for package" state (id 2). For any other state the user is bounced back to
     * the edit page with a flash error rather than served a misleading document.
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute: 'admin_merchandise_returns_index')]
    public function downloadPdfAction(
        int $orderReturnId,
        #[Autowire(service: 'prestashop.adapter.order_return.repository.order_return_repository')]
        OrderReturnRepository $orderReturnRepository,
        #[Autowire(service: 'prestashop.adapter.pdf.order_return_pdf_generator')]
        PDFGeneratorInterface $pdfGenerator,
    ): Response {
        try {
            $orderReturn = $orderReturnRepository->get(new OrderReturnId($orderReturnId));
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_merchandise_returns_index');
        }

        if ((int) $orderReturn->state !== OrderReturnStateSettings::STATE_WAITING_FOR_PACKAGE) {
            $this->addFlash('error', $this->trans(
                'The return PDF is only available when the return is waiting for the package.',
                [],
                'Admin.Orderscustomers.Notification'
            ));

            return $this->redirectToRoute('admin_order_returns_edit', ['orderReturnId' => $orderReturnId]);
        }

        $body = $pdfGenerator->generatePDF([$orderReturnId]);

        return new Response($body, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf('attachment; filename="OrderReturn_%d.pdf"', $orderReturnId),
        ]);
    }

    /**
     * Deletes a single merchandise return from the grid row action.
     */
    #[DemoRestricted(redirectRoute: 'admin_merchandise_returns_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message: 'You need permission to delete this.', redirectRoute: 'admin_merchandise_returns_index')]
    public function deleteAction(int $orderReturnId): Response
    {
        try {
            $this->dispatchCommand(new DeleteOrderReturnCommand($orderReturnId));
            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        } catch (OrderReturnException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_merchandise_returns_index');
    }

    /**
     * Deletes the merchandise returns selected via the grid bulk checkboxes.
     */
    #[DemoRestricted(redirectRoute: 'admin_merchandise_returns_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_merchandise_returns_index')]
    public function bulkDeleteAction(Request $request): Response
    {
        $orderReturnIds = $this->getBulkOrderReturnsFromRequest($request);

        try {
            $this->dispatchCommand(new BulkDeleteOrderReturnsCommand($orderReturnIds));
            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        } catch (OrderReturnException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_merchandise_returns_index');
    }

    /**
     * @return int[]
     */
    private function getBulkOrderReturnsFromRequest(Request $request): array
    {
        $ids = $request->request->all('merchandise_return_order_return_bulk');

        return array_map('intval', $ids);
    }

    /**
     * Provides error messages for exceptions
     *
     * @return array
     */
    private function getErrorMessages(): array
    {
        return [
            OrderReturnConstraintException::class => [
                OrderReturnConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
            OrderReturnNotFoundException::class => $this->trans(
                'Merchandise return not found.',
                [],
                'Admin.Orderscustomers.Notification'
            ),
            OrderReturnOrderStateConstraintException::class => [
                OrderReturnOrderStateConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
            UpdateOrderReturnException::class => $this->trans(
                'An error occurred while trying to update merchandise return.',
                [],
                'Admin.Orderscustomers.Notification'
            ),
            CannotDeleteLastProductFromOrderReturnException::class => $this->trans(
                'A merchandise return must contain at least one product.',
                [],
                'Admin.Orderscustomers.Notification'
            ),
            DeleteProductFromOrderReturnException::class => $this->trans(
                'An error occurred while trying to remove a product from the merchandise return.',
                [],
                'Admin.Orderscustomers.Notification'
            ),
            BulkDeleteProductsFromOrderReturnException::class => $this->trans(
                'Some products could not be removed from the merchandise return.',
                [],
                'Admin.Orderscustomers.Notification'
            ),
            DeleteOrderReturnException::class => $this->trans(
                'An error occurred while trying to delete the merchandise return.',
                [],
                'Admin.Orderscustomers.Notification'
            ),
            BulkDeleteOrderReturnsException::class => $this->trans(
                'Some merchandise returns could not be deleted.',
                [],
                'Admin.Orderscustomers.Notification'
            ),
        ];
    }
}
