<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Query\GetManufacturerForViewing;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\QueryResult\ViewableManufacturer;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manages "Sell > Catalog > Brands & Suppliers > Brands" page
 */
class ManufacturerController extends FrameworkBundleAdminController
{
    /**
     * Show information about manufacturer
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param int $manufacturerId
     *
     * @return Response
     */
    public function viewAction($manufacturerId)
    {
        try {
            /** @var ViewableManufacturer $viewableManufacturer */
            $viewableManufacturer = $this->getQueryBus()->handle(new GetManufacturerForViewing(
                (int) $manufacturerId,
                (int) $this->getContextLangId()
            ));
        } catch (ManufacturerException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_manufacturers_index');
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Manufacturer/view.html.twig', [
            'layoutTitle' => $viewableManufacturer->getName(),
            'viewableManufacturer' => $viewableManufacturer,
            'isStockManagementEnabled' => $this->configuration->get('PS_STOCK_MANAGEMENT'),
            'isAllShopContext' => $this->get('prestashop.adapter.shop.context')->isAllShopContext(),
        ]);
    }

    /**
     * @return array
     */
    private function getErrorMessages()
    {
        return [
            ManufacturerNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
        ];
    }
}
