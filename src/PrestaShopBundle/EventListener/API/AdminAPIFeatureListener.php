<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
