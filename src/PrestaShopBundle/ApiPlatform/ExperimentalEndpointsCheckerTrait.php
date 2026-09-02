<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform;

use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use Throwable;

/**
 * Shared check for the experimental endpoints feature flag, used by the metadata decorators and the
 * scopes extractor that filter Admin API operations. The classes using this trait must have a
 * $featureFlagStateChecker field.
 */
trait ExperimentalEndpointsCheckerTrait
{
    /**
     * The services using this check are implied during cache clearing which would fail when the shop is
     * not installed because the DB config is not set up yet. So we protected the feature flag fetching
     * in a try/catch and return false (default value) in case of an error.
     *
     * @return bool
     */
    protected function areExperimentalEndpointsEnabled(): bool
    {
        try {
            return $this->featureFlagStateChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_ADMIN_API_EXPERIMENTAL_ENDPOINTS);
        } catch (Throwable) {
            return false;
        }
    }
}
