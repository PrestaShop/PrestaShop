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

namespace PrestaShop\PrestaShop\Core\FeatureFlag;

use PrestaShopBundle\Entity\Repository\FeatureFlagRepository;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use Symfony\Contracts\Service\ResetInterface;

class FeatureFlagManager implements FeatureFlagStateCheckerInterface, ResetInterface
{
    /**
     * @var array<string, bool>
     */
    private array $featureFlagStates = [];

    public function __construct(
        #[TaggedLocator(TypeLayerInterface::class, defaultIndexMethod: 'getTypeName')]
        private readonly ContainerInterface $locator,
        private readonly FeatureFlagRepository $featureFlagRepository,
    ) {
    }

    /**
     * Get used layer for the feature flag.
     */
    private function getLayer(string $featureFlagName): TypeLayerInterface
    {
        $featureFlag = $this->featureFlagRepository->getByName($featureFlagName);
        if (null !== $featureFlag) {
            foreach ($featureFlag->getOrderedTypes() as $type) {
                if ($this->locator->has($type)) {
                    $handler = $this->locator->get($type);
                    if ($handler->canBeUsed($featureFlagName)) {
                        return $handler;
                    }
                }
            }
            throw new \RuntimeException(sprintf('No handler can be used for feature flag %s.', $featureFlagName));
        }
        throw new \RuntimeException(sprintf('The feature flag %s doesn\'t exist.', $featureFlagName));
    }

    /**
     * Get type of handler used by this feature flag.
     */
    public function getUsedType(string $featureFlagName): string
    {
        return $this->getLayer($featureFlagName)->getTypeName();
    }

    /**
     * Is the handler used by this feature flag read only?
     */
    public function isReadonly(string $featureFlagName): bool
    {
        return $this->getLayer($featureFlagName)->isReadonly();
    }

    /**
     * Is this feature flag enable?
     */
    public function isEnabled(string $featureFlagName): bool
    {
        return $this->getFeatureFlagState($featureFlagName);
    }

    /**
     * Is this feature flag disable?
     */
    public function isDisabled(string $featureFlagName): bool
    {
        return !$this->isEnabled($featureFlagName);
    }

    /**
     * Enable the feature flag by his handler.
     */
    public function enable(string $featureFlagName): void
    {
        $this->getLayer($featureFlagName)->enable($featureFlagName);
    }

    /**
     * Disable the feature flag by his handler.
     */
    public function disable(string $featureFlagName): void
    {
        $this->getLayer($featureFlagName)->disable($featureFlagName);
    }

    public function reset()
    {
        $this->featureFlagStates = [];
    }

    /**
     * Cache each feature flag state to avoid useless multiple queries per request, maybe one day it would be worth
     * adding an actual cache layer over this, which would cache values in filesystem cache.
     *
     * @param string $featureFlagName
     *
     * @return bool
     */
    private function getFeatureFlagState(string $featureFlagName): bool
    {
        if (!isset($this->featureFlagStates[$featureFlagName])) {
            $this->featureFlagStates[$featureFlagName] = $this->getLayer($featureFlagName)->isEnabled($featureFlagName);
        }

        return $this->featureFlagStates[$featureFlagName];
    }
}
