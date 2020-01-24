<?php

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDeleteProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDisableProductStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDuplicateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkEnableProductStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\DeleteProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\DuplicateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\ToggleProductStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDeleteProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDisableProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotEnableProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotToggleProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductExportableData;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductPreviewUrl;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductExportableData;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductFilters;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manages "Sell -> Catalog -> Products" page actions.
 */
class ProductController extends FrameworkBundleAdminController
{
    /**
     * Shows products listing.
     *
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param ProductFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, ProductFilters $filters)
    {
        $productGridFactory = $this->get('prestashop.core.grid.factory.product');
        $productGrid = $productGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Product/index.html.twig', [
            'productGrid' => $this->presentGrid($productGrid),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminProducts'),
        ]);
    }

    /**
     * Changes products "enabled" or "disabled" state.
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_products_index",
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(redirectRoute="admin_products_index")
     *
     * @param int $productId
     *
     * @return RedirectResponse
     */
    public function toggleAction($productId)
    {
        try {
            $this->getCommandBus()->handle(new ToggleProductStatusCommand((int) $productId));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (ProductException $exception) {
            $this->addFlash('error', $this->getErrorMessageForException($exception, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_products_index');
    }

    /**
     * Previews product in front page.
     *
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))")
     *
     * @param int $productId
     *
     * @return Response
     */
    public function previewAction($productId)
    {
        $link = $this->getQueryBus()->handle(new GetProductPreviewUrl((int) $productId));

        return $this->redirect($link);
    }

    /**
     * Redirects to edit product form.
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_products_index",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $productId
     *
     * @return RedirectResponse
     */
    public function editAction($productId)
    {
        return $this->redirectToRoute('admin_product_form', ['id' => $productId]);
    }

    /**
     * Redirects to product creation form.
     *
     * @AdminSecurity(
     *     "is_granted(['create'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_products_index",
     *     message="You do not have permission to create this."
     * )
     *
     * @return RedirectResponse
     */
    public function createAction()
    {
        return $this->redirectToRoute('admin_product_new');
    }

    /**
     * Redirects to step where product quantity can be edited.
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_products_index",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $productId
     *
     * @return RedirectResponse
     */
    public function editQuantityAction($productId)
    {
        $response = $this->redirectToRoute('admin_product_form', [
            'id' => $productId,
        ]);

        return $response->setTargetUrl(
            $response->getTargetUrl() . '#tab-step3'
        );
    }

    /**
     * Redirects to step where product price can be edited.
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_products_index",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $productId
     *
     * @return RedirectResponse
     */
    public function editPriceAction($productId)
    {
        $response = $this->redirectToRoute('admin_product_form', [
            'id' => $productId,
        ]);

        return $response->setTargetUrl(
            $response->getTargetUrl() . '#tab-step2'
        );
    }

    /**
     * Duplicates given product and creates new product from duplicate.
     *
     * @AdminSecurity(
     *     "is_granted(['create'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_products_index",
     *     message="You do not have permission to create this."
     * )
     * @DemoRestricted(redirectRoute="admin_products_index")
     *
     * @param int $productId
     *
     * @return RedirectResponse
     */
    public function duplicateProductAction($productId)
    {
        try {
            /** @var ProductId $productId */
            $productId = $this->getCommandBus()->handle(new DuplicateProductCommand((int) $productId));

            $this->addFlash(
                'success',
                $this->trans('Product(s) successfully duplicated.', 'Admin.Catalog.Notification')
            );
        } catch (ProductException $exception) {
            $this->addFlash('error', $this->getErrorMessageForException($exception, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_products_index');
        }

        return $this->redirectToRoute('admin_product_form', [
            'id' => $productId->getValue(),
        ]);
    }

    /**
     * Duplicates multiple products.
     *
     * @AdminSecurity(
     *     "is_granted(['create'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_products_index",
     *     message="You do not have permission to create this."
     * )
     * @DemoRestricted(redirectRoute="admin_products_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDuplicateProductsAction(Request $request)
    {
        $productIds = $this->getProductsIdsFromBulkAction($request);

        try {
            $this->getCommandBus()->handle(new BulkDuplicateProductCommand($productIds));

            $this->addFlash(
                'success',
                $this->trans('Product(s) successfully duplicated.', 'Admin.Catalog.Notification')
            );
        } catch (ProductException $exception) {
            $this->addFlash('error', $this->getErrorMessageForException($exception, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_products_index');
    }

    /**
     * Deletes product.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_products_index",
     *     message="You do not have permission to delete this."
     * )
     * @DemoRestricted(redirectRoute="admin_products_index")
     *
     * @param int $productId
     *
     * @return RedirectResponse
     */
    public function deleteAction($productId)
    {
        try {
            $this->getCommandBus()->handle(new DeleteProductCommand((int) $productId));

            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (ProductException $exception) {
            $this->addFlash('error', $this->getErrorMessageForException($exception, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_products_index');
    }

    /**
     * Exports products to csv.
     *
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))")
     *
     * @param ProductFilters $filters
     *
     * @return CsvResponse
     */
    public function exportAction(ProductFilters $filters)
    {
        $gridFactory = $this->get('prestashop.core.grid.factory.exportable_product')->getGrid($filters);
        $columns = $gridFactory->getDefinition()->getColumns();

        /** @var ProductExportableData $exportableData */
        $exportableData = $this->getQueryBus()->handle(
            new GetProductExportableData(
                $columns->toArray(),
                $gridFactory->getData()->getRecords()->all()
            )
        );

        return (new CsvResponse())
            ->setData($exportableData->getData())
            ->setHeadersData($exportableData->getHeaders())
            ->setFileName('product_' . date('Y-m-d_His') . '.csv');
    }

    /**
     * Bulk enables products.
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_products_index",
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(redirectRoute="admin_products_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkEnableAction(Request $request)
    {
        $productIds = $this->getProductsIdsFromBulkAction($request);

        try {
            $this->getCommandBus()->handle(new BulkEnableProductStatusCommand($productIds));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (ProductException $exception) {
            $this->addFlash('error', $this->getErrorMessageForException($exception, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_products_index');
    }

    /**
     * Bulk disables products.
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_products_index",
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(redirectRoute="admin_products_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDisableAction(Request $request)
    {
        $productIds = $this->getProductsIdsFromBulkAction($request);

        try {
            $this->getCommandBus()->handle(new BulkDisableProductStatusCommand($productIds));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (ProductException $exception) {
            $this->addFlash('error', $this->getErrorMessageForException($exception, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_products_index');
    }

    /**
     * Bulk deletes products.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_products_index",
     *     message="You do not have permission to delete this."
     * )
     * @DemoRestricted(redirectRoute="admin_products_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDeleteAction(Request $request)
    {
        $productIds = $this->getProductsIdsFromBulkAction($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteProductCommand($productIds));

            $this->addFlash(
                'success',
                $this->trans('Product(s) successfully deleted.', 'Admin.Catalog.Notification')
            );
        } catch (ProductException $exception) {
            $this->addFlash('error', $this->getErrorMessageForException($exception, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_products_index');
    }

    /**
     * Gets error message mapping.
     *
     * @return array
     */
    private function getErrorMessages()
    {
        return [
            ProductNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            CannotToggleProductException::class => $this->trans(
                'An error occurred while updating the status.',
                'Admin.Notifications.Error'
            ),
            CannotEnableProductException::class => $this->trans(
                'An error occurred while updating the status.',
                'Admin.Notifications.Error'
            ),
            CannotDisableProductException::class => $this->trans(
                'An error occurred while updating the status.',
                'Admin.Notifications.Error'
            ),
            CannotDeleteProductException::class => $this->trans(
                'An error occurred while deleting the object.',
                'Admin.Notifications.Error'
            ),
        ];
    }

    /**
     * Gets product ids from bulk action selection.
     *
     * @param Request $request
     *
     * @return int[]
     */
    private function getProductsIdsFromBulkAction(Request $request)
    {
        $productIds = $request->request->get('product_bulk');

        return array_map(static function ($item) { return (int) $item; }, $productIds);
    }
}
