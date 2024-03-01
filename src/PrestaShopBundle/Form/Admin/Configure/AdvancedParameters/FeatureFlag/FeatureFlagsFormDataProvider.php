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

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\FeatureFlag;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagManager;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShopBundle\Entity\FeatureFlag;
use PrestaShopBundle\Routing\Converter\CacheCleanerInterface;

/**
 * Passes data between the application layer in charge of the feature flags form
 * and the domain layer in charge of the feature flags model
 */
class FeatureFlagsFormDataProvider implements FormDataProviderInterface
{
    /**
     * @param EntityManagerInterface $doctrineEntityManager
     * @param string $stability
     * @param CacheCleanerInterface $cacheCleaner
     */
    public function __construct(
        protected EntityManagerInterface $doctrineEntityManager,
        protected readonly string $stability,
        private CacheCleanerInterface $cacheCleaner,
        private FeatureFlagManager $featureFlagManager,
        private readonly FeatureInterface $multiStoreFeature,
    ) {
    }

    public function getData()
    {
        $featureFlags = $this->doctrineEntityManager->getRepository(FeatureFlag::class)->findBy(['stability' => $this->stability]);

        $featureFlagsData = [];
        foreach ($featureFlags as $featureFlag) {
            $flagName = $featureFlag->getName();
            $featureFlagsData[$flagName] = [
                'enabled' => $this->featureFlagManager->isEnabled($flagName),
                'name' => $featureFlag->getName(),
                'label' => $featureFlag->getLabelWording(),
                'label_domain' => $featureFlag->getLabelDomain(),
                'description' => $featureFlag->getDescriptionWording(),
                'description_domain' => $featureFlag->getDescriptionDomain(),
                'type' => $featureFlag->getOrderedTypes(),
                'type_used' => $this->featureFlagManager->getUsedType($flagName),
                'disabled' => $this->featureFlagManager->isReadOnly($flagName),
                'forced_by_env' => $this->featureFlagManager->getUsedType($flagName) === FeatureFlagSettings::TYPE_ENV,
            ];
        }

        $featureFlagsData = $this->checkAuthorizationServerMultistore($featureFlagsData);

        return ['feature_flags' => $featureFlagsData];
    }

    public function setData(array $flagsData)
    {
        $featureFlags = $flagsData['feature_flags'];
        if (!$this->validateFlagsData($featureFlags)) {
            throw new InvalidArgumentException('Invalid feature flag configuration submitted');
        }

        foreach ($featureFlags as $flagName => $flagData) {
            $featureFlag = $this->getOneFeatureFlagByName($flagName);

            if (null === $featureFlag) {
                throw new InvalidArgumentException(sprintf('Invalid feature flag configuration submitted, flag %s does not exist', $flagName));
            }

            if ($this->featureFlagManager->isReadonly($flagName)) {
                continue;
            }

            $flagState = $flagData['enabled'] ?? false;
            if ($flagState) {
                $this->featureFlagManager->enable($flagName);
            } else {
                $this->featureFlagManager->disable($flagName);
            }
        }

        // Clear cache of legacy routes since they can depend on an associated feature flag
        // when the attribute _legacy_feature_flag is used
        $this->cacheCleaner->clearCache();

        return [];
    }

    protected function validateFlagsData(array $flagsData): bool
    {
        foreach ($flagsData as $flagName => $flagData) {
            if (!is_string($flagName)) {
                return false;
            }

            if ($flagData['enabled'] !== null && !is_bool($flagData['enabled'])) {
                return false;
            }
        }

        return true;
    }

    protected function getOneFeatureFlagByName(string $featureFlagName): ?FeatureFlag
    {
        return $this->doctrineEntityManager->getRepository(FeatureFlag::class)->findOneBy(['name' => $featureFlagName]);
    }

    // conditions the display of the AuthorizationServerMultistore feature fag only if AuthorizationServer is activated, regardless of its stability
    private function checkAuthorizationServerMultistore(array $featureFlagsData): array
    {
        $authorizationServerMultistoreKey = FeatureFlagSettings::FEATURE_FLAG_AUTHORIZATION_SERVER_MULTISTORE;
        $authorizationServerKey = FeatureFlagSettings::FEATURE_FLAG_AUTHORIZATION_SERVER;
        $isMultistoreActive = $this->multiStoreFeature->isActive();

        if (array_key_exists($authorizationServerMultistoreKey, $featureFlagsData)) {
            if (!$isMultistoreActive) {
                unset($featureFlagsData[$authorizationServerMultistoreKey]);
            } elseif (array_key_exists($authorizationServerKey, $featureFlagsData)
                && !$featureFlagsData[$authorizationServerKey]['enabled']) {
                unset($featureFlagsData[$authorizationServerMultistoreKey]);
            } else {
                $featureFlag = $this->doctrineEntityManager->getRepository(FeatureFlag::class)->findOneBy(['name' => $authorizationServerKey]);
                if (!$this->featureFlagManager->isEnabled($featureFlag->getName())) {
                    unset($featureFlagsData[$authorizationServerMultistoreKey]);
                }
            }
        }

        return $featureFlagsData;
    }
}
