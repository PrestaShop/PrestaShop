<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use PrestaShop\PrestaShop\Core\Search\Filters\CustomerGroupsFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
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
     * @param Request $request
     * @param CustomerGroupsFilters $filters
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function indexAction(Request $request, CustomerGroupsFilters $filters): Response
    {
        $customerGroupsGridFactory = $this->get('prestashop.core.grid.factory.customer_groups');
        $customerGroupsGrid = $customerGroupsGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/CustomerSettings/Groups/index.html.twig', [
            'customerGroupsGrid' => $this->presentGrid($customerGroupsGrid),
            'layoutTitle' => $this->trans('Groups', 'Admin.Navigation.Menu'),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
        ]);
    }

    /**
     * Displays and handles customer group form.
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_customer_groups_index', message: 'You need permission to create this.')]
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
     * @param int $groupId
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_customer_groups_index', message: 'You need permission to edit this.')]
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
