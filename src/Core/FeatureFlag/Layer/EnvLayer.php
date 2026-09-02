<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\FeatureFlag\Layer;

use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\TypeLayerInterface;

class EnvLayer implements TypeLayerInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getTypeName(): string
    {
        return FeatureFlagSettings::TYPE_ENV;
    }

    /**
     * {@inheritdoc}
     */
    public function isReadonly(): bool
    {
        // It's always NOT editable via Env layer!
        return true;
    }

    /**
     * Retrieve the const name of this feature flag.
     */
    public function getConstName(string $featureFlagName): string
    {
        return FeatureFlagSettings::PREFIX . strtoupper($featureFlagName);
    }

    /**
     * {@inheritdoc}
     */
    public function canBeUsed(string $featureFlagName): bool
    {
        // Check if PS_FF_{featureFlag's name} is on Env variable.
        return getenv($this->getConstName($featureFlagName)) !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled(string $featureFlagName): bool
    {
        return $this->canBeUsed($featureFlagName)
            && filter_var(getenv($this->getConstName($featureFlagName)), \FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * {@inheritdoc}
     */
    public function enable(string $featureFlagName): void
    {
        throw new InvalidArgumentException(sprintf('We cannot change status of the env feature flag %s.', $featureFlagName));
    }

    /**
     * {@inheritdoc}
     */
    public function disable(string $featureFlagName): void
    {
        throw new InvalidArgumentException(sprintf('We cannot change status of the env feature flag %s.', $featureFlagName));
    }
}
