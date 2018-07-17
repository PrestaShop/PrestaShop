<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use PrestaShop\PrestaShop\Core\Grid\Presenter\GridPresenter;
use PrestaShop\PrestaShop\Core\Search\Filters\ManufacturerAddressFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\ManufacturerFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ManufacturerController is responsible for "Sell > Catalog > Brands & Suppliers" page
 */
class ManufacturerController extends FrameworkBundleAdminController
{
    /**
     * Show manufacturers listing page
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/listing.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * Render manufacturers grid
     *
     * @param ManufacturerFilters $filters
     *
     * @return Response
     */
    public function renderManufacturersGridAction(ManufacturerFilters $filters)
    {
        $manufacturerGridFactory = $this->get('prestashop.core.grid.manufacturer_factory');
        $manufacturerGrid = $manufacturerGridFactory->createUsingSearchCriteria($filters);

        return $this->render('@PrestaShop/Admin/Common/Grid/grid_panel.html.twig', [
            'grid' => $this->getGridPresenter()->present($manufacturerGrid),
        ]);
    }

    /**
     * Render manufacturer addresses grid
     *
     * @param ManufacturerAddressFilters $filters
     *
     * @return Response
     */
    public function renderAddressesGridAction(ManufacturerAddressFilters $filters)
    {
        $addressesGridFactory = $this->get('prestashop.core.grid.manufacturer_address_factory');
        $addressesGrid = $addressesGridFactory->createUsingSearchCriteria($filters);

        return $this->render('@PrestaShop/Admin/Common/Grid/grid_panel.html.twig', [
            'grid' => $this->getGridPresenter()->present($addressesGrid),
        ]);
    }

    /**
     * Get grid presenter
     *
     * @return GridPresenter
     */
    private function getGridPresenter()
    {
        return $this->get('prestashop.core.grid.presenter.grid_presenter');
    }
}
