<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;

/**
 * Admin controller for the Stock pages.
 */
class StockController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function overviewAction()
    {
        return $this->render('@PrestaShop/Admin/Sell/Catalog/Stock/overview.html.twig', [
            'is_shop_context' => (new Context())->isShopContext(),
            'layoutTitle' => $this->trans('Stock', [], 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('stock'),
        ]);
    }
}
