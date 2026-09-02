<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker;

/**
 * Class DeleteOrderStatesAccessibilityChecker
 */
class DeleteOrderStatesAccessibilityChecker implements AccessibilityCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isGranted(array $record)
    {
        if (isset($record['unremovable'])) {
            return !$record['unremovable'];
        }

        return false;
    }
}
