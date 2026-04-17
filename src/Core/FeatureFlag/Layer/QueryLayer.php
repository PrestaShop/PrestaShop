<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\FeatureFlag\Layer;

use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\EnvironmentInterface;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\TypeLayerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class QueryLayer implements TypeLayerInterface
{
    public function __construct(
        private EnvironmentInterface $environment,
        private RequestStack $requestStack,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function getTypeName(): string
    {
        return FeatureFlagSettings::TYPE_QUERY;
    }

    /**
     * {@inheritdoc}
     */
    public function isReadonly(): bool
    {
        // It's always NOT editable via Query layer!
        return true;
    }

    /**
     * Retrieve the var name of this feature flag.
     */
    public function getVarName(string $featureFlagName): string
    {
        return FeatureFlagSettings::PREFIX . strtoupper($featureFlagName);
    }

    /**
     * {@inheritdoc}
     */
    public function canBeUsed(string $featureFlagName): bool
    {
        // Only for debug environment.
        if (!$this->environment->isDebug()) {
            return false;
        }

        // Check if PS_FF_{featureFlag's name} is on query variable.
        return null !== $this->requestStack->getMainRequest()?->query->get($this->getVarName($featureFlagName));
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled(string $featureFlagName): bool
    {
        return $this->canBeUsed($featureFlagName)
            && filter_var(
                $this->requestStack->getMainRequest()?->query->get($this->getVarName($featureFlagName)),
                \FILTER_VALIDATE_BOOLEAN
            );
    }

    /**
     * {@inheritdoc}
     */
    public function enable(string $featureFlagName): void
    {
        throw new InvalidArgumentException(sprintf('We cannot change status of the query feature flag %s.', $featureFlagName));
    }

    /**
     * {@inheritdoc}
     */
    public function disable(string $featureFlagName): void
    {
        throw new InvalidArgumentException(sprintf('We cannot change status of the query feature flag %s.', $featureFlagName));
    }
}
