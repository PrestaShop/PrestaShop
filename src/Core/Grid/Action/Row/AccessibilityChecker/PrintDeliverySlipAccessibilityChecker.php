<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker;

/**
 * Checks if print delivery slip option can be visible in order list.
 */
final class PrintDeliverySlipAccessibilityChecker implements AccessibilityCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isGranted(array $record)
    {
        return $record['delivery_number'] ?? null;
    }
}
