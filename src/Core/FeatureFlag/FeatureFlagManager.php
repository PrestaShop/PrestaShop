<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\FeatureFlag;

use PrestaShopBundle\Entity\Repository\FeatureFlagRepository;
use Psr\Container\ContainerInterface;
use RuntimeException;
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
            throw new RuntimeException(sprintf('No handler can be used for feature flag %s.', $featureFlagName));
        }
        throw new RuntimeException(sprintf('The feature flag %s doesn\'t exist.', $featureFlagName));
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
