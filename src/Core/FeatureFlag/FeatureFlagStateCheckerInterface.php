<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\FeatureFlag;

interface FeatureFlagStateCheckerInterface
{
    /**
     * Retrieve if the feature flag is enabled.
     */
    public function isEnabled(string $featureFlagName): bool;

    /**
     * Retrieve if the feature flag is enabled.
     */
    public function isDisabled(string $featureFlagName): bool;
}
