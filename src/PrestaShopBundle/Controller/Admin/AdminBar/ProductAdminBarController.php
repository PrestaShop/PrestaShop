<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\AdminBar;

use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class ProductAdminBarController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('update', 'AdminProducts')", redirectRoute: 'admin_product_catalog')]
    public function editAction(int $productId): RedirectResponse
    {
        if (!$this->getFeatureFlagStateChecker()->isEnabled(FeatureFlagSettings::FEATURE_FLAG_FRONT_OFFICE_ADMIN_BAR)) {
            throw $this->createNotFoundException();
        }

        return $this->redirectToRoute('admin_product_form', ['id' => $productId]);
    }
}
