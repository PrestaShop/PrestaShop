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
