<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Cache\Clearer\Symfony;

use AdminAPIKernel;
use FrontKernel;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShopBundle\Entity\Repository\ApiClientRepository;
use Throwable;

/**
 * @internal
 */
final class KernelUsageChecker
{
    public function __construct(
        private readonly FeatureFlagStateCheckerInterface $featureFlagStateChecker,
        private readonly ConfigurationInterface $configuration,
        private readonly ApiClientRepository $apiClientRepository,
    ) {
    }

    public function isUsed(string $appId): bool
    {
        try {
            return match ($appId) {
                FrontKernel::APP_ID => $this->featureFlagStateChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_FRONT_CONTAINER_V2),
                AdminAPIKernel::APP_ID => $this->configuration->get('PS_ENABLE_ADMIN_API') && $this->apiClientRepository->count([]) > 0,
                default => true,
            };
        } catch (Throwable) {
            return true;
        }
    }
}
