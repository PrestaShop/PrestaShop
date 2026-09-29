<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker;

/** Shows the plain "Allow" action only on not-yet-allowed, non-weakening sources. */
final class CspLogAllowAccessibilityChecker implements AccessibilityCheckerInterface
{
    public function isGranted(array $record): bool
    {
        return 0 === (int) $record['is_allowed'] && 0 === (int) $record['is_weakening'];
    }
}
