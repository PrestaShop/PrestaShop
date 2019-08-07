<?php

namespace PrestaShopBundle\Controller\Admin\Sell\CustomerService;

use PrestaShop\PrestaShop\Core\Search\Filters\MerchandiseReturnFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MerchandiseReturnController responsible for "Sell > Customer Service > Merchandise Returns" page
 */
class MerchandiseReturnController extends FrameworkBundleAdminController
{
    /**
     * Render merchandise returns grid and options.
     *
     * @param Request $request
     * @param MerchandiseReturnFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, MerchandiseReturnFilters $filters)
    {
        $gridFactory = $this->get('prestashop.core.grid.factory.merchandise_return');
        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');

        return $this->render('@PrestaShop/Admin/Sell/CustomerService/MerchandiseReturn/index.html.twig', [
            'merchandiseReturnsGrid' => $gridPresenter->present($gridFactory->getGrid($filters)),
        ]);
    }
}
