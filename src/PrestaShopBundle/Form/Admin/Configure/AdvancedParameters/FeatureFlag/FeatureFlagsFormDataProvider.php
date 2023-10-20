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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\FeatureFlag;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShopBundle\Entity\FeatureFlag;
use PrestaShopBundle\Routing\Converter\CacheCleanerInterface;

/**
 * Passes data between the application layer in charge of the feature flags form
 * and the domain layer in charge of the feature flags model
 */
class FeatureFlagsFormDataProvider implements FormDataProviderInterface
{
    /** @var EntityManagerInterface */
    protected $doctrineEntityManager;

    /** @var string */
    protected $stability;

    /**
     * @var CacheCleanerInterface
     */
    private $cacheCleaner;

    /**
     * @param EntityManagerInterface $doctrineEntityManager
     * @param string $stability
     * @param CacheCleanerInterface $cacheCleaner
     */
    public function __construct(
        EntityManagerInterface $doctrineEntityManager,
        string $stability,
        CacheCleanerInterface $cacheCleaner
    ) {
        $this->doctrineEntityManager = $doctrineEntityManager;
        $this->stability = $stability;
        $this->cacheCleaner = $cacheCleaner;
    }

    public function getData()
    {
        $featureFlags = $this->doctrineEntityManager->getRepository(FeatureFlag::class)->findBy(['stability' => $this->stability]);

        $featureFlagsData = [];
        foreach ($featureFlags as $featureFlag) {
            $featureFlagsData[$featureFlag->getName()] = [
                'enabled' => $featureFlag->isEnabled(),
                'name' => $featureFlag->getName(),
                'label' => $featureFlag->getLabelWording(),
                'label_domain' => $featureFlag->getLabelDomain(),
                'description' => $featureFlag->getDescriptionWording(),
                'description_domain' => $featureFlag->getDescriptionDomain(),
                // You can handle specific rules here to indicate if the feature flag should be editable or not, currently
                // no more specific rule but it can evolve in the future
                'disabled' => false,
            ];
        }

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

            $flagState = $flagData['enabled'] ?? false;
            if ($flagState) {
                $featureFlag->enable();
            } else {
                $featureFlag->disable();
            }
        }

        $this->doctrineEntityManager->flush();
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
}
