<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\EventListener\API;

use PrestaShop\PrestaShop\Adapter\Feature\MultistoreFeature;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagManager;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Check the Admin API configuration
 */
class AdminAPIFeatureListener
{
    public function __construct(
        private readonly FeatureFlagManager $featureFlagManager,
        private readonly MultistoreFeature $multiStoreFeature,
        private readonly ConfigurationInterface $configuration,
    ) {
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $isAdminAPIEnabled = (bool) $this->configuration->get('PS_ENABLE_ADMIN_API');
        $isAdminAPIMultistoreEnabled = $this->featureFlagManager->isEnabled(FeatureFlagSettings::FEATURE_FLAG_ADMIN_API_MULTISTORE);
        $isMultistoreActive = $this->multiStoreFeature->isActive();

        if (!$isAdminAPIEnabled || (!$isAdminAPIMultistoreEnabled && $isMultistoreActive)) {
            throw new NotFoundHttpException();
        }
    }
}
