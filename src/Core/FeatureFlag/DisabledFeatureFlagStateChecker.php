<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\FeatureFlag;

/**
 * This checker is used in conditions when no DB or container is accessible so we
 * simulate that all the feature flags are disabled.
 */
class DisabledFeatureFlagStateChecker implements FeatureFlagStateCheckerInterface
{
    public function isEnabled(string $featureFlagName): bool
    {
        return false;
    }

    public function isDisabled(string $featureFlagName): bool
    {
        return true;
    }
}
