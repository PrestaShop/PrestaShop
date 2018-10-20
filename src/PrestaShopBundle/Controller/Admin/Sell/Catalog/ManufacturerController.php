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
     * @param ManufacturerFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, ManufacturerFilters $filters)
    {
        $manufacturerGridFactory = $this->get('prestashop.core.grid.factory.manufacturer');
        $manufacturerGrid = $manufacturerGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/index.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'manufacturer_grid' => $this->getGridPresenter()->present($manufacturerGrid),
        ]);
    }

    public function createManufacturerAction()
    {
        //todo: implement
        return $this->redirectToRoute('admin_manufacturers_index');
    }

    public function viewManufacturerAction()
    {
        //todo: implement
        return $this->redirectToRoute('admin_manufacturers_index');
    }

    public function editManufacturerAction()
    {
        //todo: implement
        return $this->redirectToRoute('admin_manufacturers_index');
    }

    public function deleteManufacturerAction()
    {
        //todo: implement
        return $this->redirectToRoute('admin_manufacturers_index');
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
