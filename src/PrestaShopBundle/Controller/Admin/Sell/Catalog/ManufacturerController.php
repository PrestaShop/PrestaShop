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

use PrestaShop\PrestaShop\Core\Search\Filters\ManufacturerAddressFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\ManufacturerFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;

class ManufacturerController extends FrameworkBundleAdminController
{
    public function indexAction(
        Request $request,
        ManufacturerFilters $manufacturerFilters,
        ManufacturerAddressFilters $addressFilters
    ) {
        $manufacturerGridFactory = $this->get('prestashop.core.grid.manufacturer_factory');
        $manufacturerGrid = $manufacturerGridFactory->createUsingSearchCriteria($manufacturerFilters);

        $addressesGridFactory = $this->get('prestashop.core.grid.manufacturer_address_factory');
        $addressesGrid = $addressesGridFactory->createUsingSearchCriteria($addressFilters);

        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/listing.html.twig', [
            'manufacturersGrid' => $gridPresenter->present($manufacturerGrid),
            'addressesGrid' => $gridPresenter->present($addressesGrid),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }
}
