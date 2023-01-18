<?php

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use PrestaShop\PrestaShop\Core\Search\Filters\CustomerGroupsFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\TitleFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller responsible for "Configure > Shop Parameters > Customer Settings > Groups" page.
 */
class CustomerGroupsController extends FrameworkBundleAdminController
{
    /**
     * Show Groups tab.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     * @param CustomerGroupsFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, CustomerGroupsFilters $filters): Response
    {
        $customerGroupsGridFactory = $this->get('prestashop.core.grid.factory.customer_groups');
        $customerGroupsGrid = $customerGroupsGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/CustomerSettings/Groups/index.html.twig', [
            'customerGroupsGrid' => $this->presentGrid($customerGroupsGrid),
            'layoutTitle' => $this->trans('Titles', 'Admin.Navigation.Menu'),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }
}
