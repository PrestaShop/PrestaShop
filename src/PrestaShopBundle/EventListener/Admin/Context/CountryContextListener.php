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

declare(strict_types=1);

namespace PrestaShopBundle\EventListener\Admin\Context;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\CountryContextBuilder;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Listener dedicated to set up Country context for the Back-Office/Admin application.
 */
class CountryContextListener
{
    public function __construct(
        private readonly CountryContextBuilder $countryContextBuilder,
        private readonly ConfigurationInterface $configuration,
        private readonly FeatureFlagStateCheckerInterface $featureFlagStateChecker,
        private readonly bool $isSymfonyLayout,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if ($this->isSymfonyLayout !== $this->featureFlagStateChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_SYMFONY_LAYOUT)) {
            return;
        }

        $this->countryContextBuilder->setCountryId((int) $this->configuration->get('PS_COUNTRY_DEFAULT'));
    }
}
