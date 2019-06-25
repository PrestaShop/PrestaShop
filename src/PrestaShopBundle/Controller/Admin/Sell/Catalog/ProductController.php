<?php

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

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
//        todo: implement
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
//        todo: implement
    }

    public function exportAction()
    {
//        todo: implement
    }

    public function bulkEnable()
    {
//        todo: implement
    }

    public function bulkDisable()
    {
//        todo: implement
    }

    public function bulkDelete()
    {
//        todo: implement
    }
}
