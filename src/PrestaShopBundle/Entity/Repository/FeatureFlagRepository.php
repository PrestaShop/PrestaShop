<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use PrestaShopBundle\Entity\FeatureFlag;

class FeatureFlagRepository extends EntityRepository
{
    /**
     * Get a feature flag entity by its name.
     *
     * @param string $featureFlagName
     *
     * @return FeatureFlag|null return null if feature flag cannot be found
     */
    public function getByName(string $featureFlagName): ?FeatureFlag
    {
        return $this->findOneBy(['name' => $featureFlagName]);
    }

    /**
     * Check if a feature flag is enabled based on its name (if it doesn't exist false is returned).
     *
     * @param string $featureFlagName
     *
     * @return bool
     */
    public function isEnabled(string $featureFlagName): bool
    {
        $featureFlag = $this->getByName($featureFlagName);

        return null !== $featureFlag && $featureFlag->isEnabled();
    }

    /**
     * Check if a feature flag is disabled based on its name (if it doesn't exist true is returned).
     *
     * @param string $featureFlagName
     *
     * @return bool
     */
    public function isDisabled(string $featureFlagName): bool
    {
        $featureFlag = $this->getByName($featureFlagName);

        return null === $featureFlag || !$featureFlag->isEnabled();
    }

    /**
     * Enable a feature flag by its flag name.
     *
     * @param string $featureFlagName
     */
    public function enable(string $featureFlagName): void
    {
        $featureFlag = $this->getByName($featureFlagName);
        if (null !== $featureFlag) {
            $featureFlag->enable();
            $this->getEntityManager()->persist($featureFlag);
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Disable a feature flag by its flag name.
     *
     * @param string $featureFlagName
     */
    public function disable(string $featureFlagName): void
    {
        $featureFlag = $this->getByName($featureFlagName);
        if (null !== $featureFlag) {
            $featureFlag->disable();
            $this->getEntityManager()->persist($featureFlag);
            $this->getEntityManager()->flush();
        }
    }
}
