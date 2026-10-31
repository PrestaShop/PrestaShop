<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker;

use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

final class DeleteShopAccessibilityChecker implements AccessibilityCheckerInterface
{
    public function __construct(
        private readonly ShopConfigurationInterface $configuration,
    ) {
    }

    public function isGranted(array $record): bool
    {
        return (int) $record['id_shop'] !== (int) $this->configuration->get('PS_SHOP_DEFAULT', null, ShopConstraint::allShops())
            && !$record['has_dependencies'];
    }
}
