<?php

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDisableProductStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkEnableProductStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\DeleteProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\ToggleProductStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDeleteProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDisableProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotEnableProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotToggleProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manages "Sell -> Catalog -> Products" page actions.
 */
class ProductController extends FrameworkBundleAdminController
{
    /**
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
     * @param $productId
     *
     * @todo: open in new window
     *
     * @return Response
     */
    public function previewAction($productId)
    {
        return $this->redirect(
            $this->get('prestashop.adapter.product.product_url_provider')->getUrl($productId)
        );
    }

    public function editAction($productId)
    {
        return $this->redirectToRoute('admin_product_form', ['id' => $productId]);
    }

    public function editQuantityAction($productId)
    {
        $response = $this->redirectToRoute('admin_product_form', [
            'id' => $productId,
        ]);

        return $response->setTargetUrl(
            $response->getTargetUrl() . '#tab-step3'
        );
    }

    public function editPriceAction($productId)
    {
        $response = $this->redirectToRoute('admin_product_form', [
            'id' => $productId,
        ]);

        return $response->setTargetUrl(
            $response->getTargetUrl() . '#tab-step2'
        );
    }

    public function duplicateProductAction($productId)
    {
//        todo: implement
    }

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

    public function exportAction()
    {
//        todo: implement
    }

    //todo: check called hooks - each action has a hook in legacy. Ask if we need so much hooks
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

    public function bulkDeleteAction()
    {
//        todo: implement
    }

    /**
     * Gets error message mapping.
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

        return array_map(static function ($item){ return (int) $item; }, $productIds);
    }
}
