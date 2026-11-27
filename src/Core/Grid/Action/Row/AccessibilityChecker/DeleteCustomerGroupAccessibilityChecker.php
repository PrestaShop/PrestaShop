<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker;

final class DeleteCustomerGroupAccessibilityChecker implements AccessibilityCheckerInterface
{
    public function isGranted(array $record): bool
    {
        return empty($record['unremovable']);
    }
}
