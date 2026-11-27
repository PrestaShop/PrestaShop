<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker;

/**
 * Shows the plain "Allow" action on non-weakening sources. The violations grid already lists only
 * not-yet-allowed sources, so the action split is decided by whether the source weakens the policy.
 */
final class CspLogAllowAccessibilityChecker implements AccessibilityCheckerInterface
{
    public function isGranted(array $record): bool
    {
        return 0 === (int) $record['is_weakening'];
    }
}
