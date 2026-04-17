<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker;

use PrestaShop\PrestaShop\Core\Multistore\MultistoreContextCheckerInterface;

/**
 * This checker is used in multishop view, some actions should not be displayed for products
 * associated to multiple shops. If there is only one it's ok though.
 */
class ProductSingleShopAssociatedAccessibilityChecker implements AccessibilityCheckerInterface
{
    /**
     * @var MultistoreContextCheckerInterface
     */
    private $multiStoreContext;

    public function __construct(
        MultistoreContextCheckerInterface $multiStoreContext
    ) {
        $this->multiStoreContext = $multiStoreContext;
    }

    /**
     * @param array{associated_shops_ids?: array<int>} $record
     *
     * @return bool
     */
    public function isGranted(array $record)
    {
        if ($this->multiStoreContext->isSingleShopContext()) {
            return true;
        }

        return empty($record['associated_shops_ids']) || count($record['associated_shops_ids']) === 1;
    }
}
