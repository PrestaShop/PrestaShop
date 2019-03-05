<?php
/**
 * 2007-2018 PrestaShop.
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

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Is responsible for "Sell > Catalog > Brands & Suppliers" page.
 */
class ManufacturerController extends FrameworkBundleAdminController
{
    /**
     * Show & process address creation.
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))"
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAddressAction(Request $request)
    {
        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/Address/add.html.twig');
    }

    /**
     * Show & process address editing.
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))"
     * )
     *
     * @param int $addressId
     * @param Request $request
     *
     * @return Response
     */
    public function editAddressAction(Request $request, $addressId)
    {
        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/Address/edit.html.twig');
    }
}
