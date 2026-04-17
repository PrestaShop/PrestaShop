<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker;

/**
 * Checks if print invoice option can be visible in order list.
 */
final class PrintInvoiceAccessibilityChecker implements AccessibilityCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isGranted(array $record)
    {
        return $record['is_invoice_available'] ?? null;
    }
}
