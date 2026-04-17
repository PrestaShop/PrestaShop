<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Context;

use ObjectModel;

/**
 * Method shared between builders to check when a legacy object needs to be updated, mostly to update
 * the internal class local field that caches the legacy object model or when we check if the legacy
 * Context fields are in sync with the expected value from the builder.
 */
trait LegacyObjectCheckerTrait
{
    protected function legacyObjectNeedsUpdate(?ObjectModel $objectModel, ?int $expectedId): bool
    {
        return !empty($expectedId) && (empty($objectModel) || (int) $objectModel->id !== $expectedId);
    }
}
