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

    public function previewAction($productId)
    {
//        todo: implement
    }

    public function editAction($productId)
    {
//        todo: implement
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
