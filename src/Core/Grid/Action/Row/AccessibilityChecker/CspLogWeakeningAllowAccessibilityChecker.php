<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker;

/**
 * Shows the confirm-guarded "Allow" action on sources that weaken the policy. The violations grid lists
 * only not-yet-allowed sources, so a weakening source gets this action in place of the plain "Allow".
 */
final class CspLogWeakeningAllowAccessibilityChecker implements AccessibilityCheckerInterface
{
    public function isGranted(array $record): bool
    {
        return 1 === (int) $record['is_weakening'];
    }
}
