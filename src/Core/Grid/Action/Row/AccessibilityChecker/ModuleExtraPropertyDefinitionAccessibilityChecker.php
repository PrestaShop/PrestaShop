<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker;

/**
 * Grants row-level access to the read-only "view" action only for extra property definitions
 * that ARE owned by a module (module_name non-empty) — the exact inverse of
 * NonModuleExtraPropertyDefinitionAccessibilityChecker, which grants edit/delete instead. A row
 * is never granted both: the two actions are mutually exclusive on module_name.
 */
final class ModuleExtraPropertyDefinitionAccessibilityChecker implements AccessibilityCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isGranted(array $record): bool
    {
        return null !== $record['module_name'] && '' !== (string) $record['module_name'];
    }
}
