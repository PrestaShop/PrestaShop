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

namespace PrestaShopBundle\Bridge;

use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShopBundle\Entity\Repository\FeatureFlagRepository;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * This service is used to detect if Symfony layout is enabled, it can be globally enabled via a feature flag
 * or punctually via a query parameter.
 */
class SymfonyLayoutFeature implements SymfonyLayoutFeatureInterface
{
    /**
     * @var FeatureFlagRepository
     */
    private $featureFlagRepository;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var bool
     */
    private $enabledByEnv;

    public function __construct(
        FeatureFlagRepository $featureFlagRepository,
        RequestStack $requestStack,
        bool $enabledByEnv
    ) {
        $this->featureFlagRepository = $featureFlagRepository;
        $this->requestStack = $requestStack;
        $this->enabledByEnv = $enabledByEnv;
    }

    public function isEnabled(): bool
    {
        // When parameter is specified in the url it overrides the other settings, this helps developer debug more easily
        // by forcing the layout via a query parameter
        if ($this->requestStack->getCurrentRequest() &&
            $this->requestStack->getCurrentRequest()->query->has('use_symfony_layout')) {
            return $this->requestStack->getCurrentRequest()->query->getBoolean('use_symfony_layout');
        }

        // In case USE_SYMFONY_LAYOUT has been set to true/on/yes/1 then it has priority over the feature flag from DB
        if ($this->enabledByEnv) {
            return true;
        }

        return $this->featureFlagRepository->isEnabled(FeatureFlagSettings::SYMFONY_LAYOUT);
    }
}
