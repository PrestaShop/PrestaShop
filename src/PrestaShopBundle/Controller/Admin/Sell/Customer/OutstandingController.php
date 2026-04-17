<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Sell\Customer;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\OutstandingGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\OutstandingFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class OutstandingController manages "Sell > Customers > Outstandings" page.
 */
class OutstandingController extends PrestaShopAdminController
{
    /**
     * Show list of outstandings.
     *
     * @param Request $request
     * @param OutstandingFilters $filters
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.grid.factory.outstanding')]
        GridFactoryInterface $gridFactory,
        OutstandingFilters $filters,
    ) {
        $grid = $gridFactory->getGrid($filters);

        return $this->render(
            '@PrestaShop/Admin/Sell/Outstanding/index.html.twig',
            [
                'layoutHeaderToolbarBtn' => [],
                'outstandingGrid' => $this->presentGrid($grid),
                'layoutTitle' => $this->trans('Outstanding', [], 'Admin.Navigation.Menu'),
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute: 'admin_outstanding_index')]
    public function searchAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.grid.definition.factory.outstanding')]
        GridDefinitionFactoryInterface $definitionFactory,
        #[Autowire(service: 'prestashop.bundle.grid.response_builder')]
        ResponseBuilder $responseBuilder
    ) {
        return $responseBuilder->buildSearchResponse(
            $definitionFactory,
            $request,
            OutstandingGridDefinitionFactory::GRID_ID,
            'admin_outstanding_index'
        );
    }
}
