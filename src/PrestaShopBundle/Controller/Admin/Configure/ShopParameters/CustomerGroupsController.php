<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\CustomerGroupsFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller responsible for "Configure > Shop Parameters > Customer Settings > Groups" page.
 */
class CustomerGroupsController extends PrestaShopAdminController
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
    public function indexAction(
        Request $request,
        CustomerGroupsFilters $filters,
        #[Autowire(service: 'prestashop.core.grid.factory.customer_groups')]
        GridFactoryInterface $customerGroupsGridFactory,
    ): Response {
        $customerGroupsGrid = $customerGroupsGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/CustomerSettings/Groups/index.html.twig', [
            'customerGroupsGrid' => $this->presentGrid($customerGroupsGrid),
            'layoutTitle' => $this->trans('Groups', [], 'Admin.Navigation.Menu'),
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
    public function createAction(LegacyContext $legacyContext): Response
    {
        return $this->redirect(
            $legacyContext->getAdminLink(
                'AdminGroups',
                true,
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
    public function editAction(
        int $groupId,
        LegacyContext $legacyContext,
    ): Response {
        return $this->redirect(
            $legacyContext->getAdminLink(
                'AdminGroups',
                true,
                [
                    'updategroup' => '',
                    'id_group' => $groupId,
                ]
            )
        );
    }
}
