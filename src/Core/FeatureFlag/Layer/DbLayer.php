<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\FeatureFlag\Layer;

use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\TypeLayerInterface;
use PrestaShopBundle\Entity\Repository\FeatureFlagRepository;

class DbLayer implements TypeLayerInterface
{
    public function __construct(
        protected readonly FeatureFlagRepository $featureFlagRepository
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function isReadonly(): bool
    {
        // It's always editable via DB layer!
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public static function getTypeName(): string
    {
        return FeatureFlagSettings::TYPE_DB;
    }

    /**
     * {@inheritdoc}
     */
    public function canBeUsed(string $featureFlagName): bool
    {
        // It's always possible via DB!
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled(string $featureFlagName): bool
    {
        return $this->featureFlagRepository->isEnabled($featureFlagName);
    }

    /**
     * {@inheritdoc}
     */
    public function enable(string $featureFlagName): void
    {
        $this->featureFlagRepository->enable($featureFlagName);
    }

    /**
     * {@inheritdoc}
     */
    public function disable(string $featureFlagName): void
    {
        $this->featureFlagRepository->disable($featureFlagName);
    }
}
