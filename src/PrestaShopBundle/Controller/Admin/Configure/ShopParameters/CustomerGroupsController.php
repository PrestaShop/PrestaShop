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


    /**
     * Displays and handles customer group form.
     *
     * @AdminSecurity(
     *     "is_granted('create', request.get('_legacy_controller'))",
     *     redirectRoute="admin_customer_groups_index",
     *     message="You need permission to create this."
     * )
     *
     * @return Response
     */
    public function createAction(): Response
    {
        return $this->redirect(
            $this->getContext()->link->getAdminLink(
                'AdminGroups',
                true,
                [],
                [
                    'addgroup' => '',
                ]
            )
        );
    }


    /**
     * Displays title form.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_customer_groups_index",
     *     message="You need permission to edit this."
     * )
     *
     * @param int $groupId
     *
     * @return Response
     */
    public function editAction(int $groupId): Response
    {
        return $this->redirect(
            $this->getContext()->link->getAdminLink(
                'AdminGroups',
                true,
                [],
                [
                    'updategroup' => '',
                    'id_group' => $groupId,
                ]
            )
        );
    }
}
