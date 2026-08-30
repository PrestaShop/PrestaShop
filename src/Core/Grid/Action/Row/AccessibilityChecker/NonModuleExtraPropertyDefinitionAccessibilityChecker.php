<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker;

/**
 * Grants row-level edit/delete access only to extra property definitions
 * that are NOT owned by a module (module_name IS NULL or empty string).
 * Module-owned definitions are registered programmatically and must be
 * managed by the module itself.
 */
final class NonModuleExtraPropertyDefinitionAccessibilityChecker implements AccessibilityCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isGranted(array $record): bool
    {
        return null === $record['module_name'] || '' === (string) $record['module_name'];
    }
}
