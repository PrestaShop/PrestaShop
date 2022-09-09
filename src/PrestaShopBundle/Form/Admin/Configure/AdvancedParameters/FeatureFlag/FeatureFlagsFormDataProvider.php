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
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShopBundle\Entity\FeatureFlag;

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
     * @var bool
     */
    protected $isMultiShopUsed;

    /**
     * @param EntityManagerInterface $doctrineEntityManager
     * @param string $stability
     * @param bool $isMultiShopUsed
     */
    public function __construct(EntityManagerInterface $doctrineEntityManager, string $stability, bool $isMultiShopUsed)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
        $this->stability = $stability;
        $this->isMultiShopUsed = $isMultiShopUsed;
    }

    public function getData()
    {
        $featureFlags = $this->doctrineEntityManager->getRepository(FeatureFlag::class)->findBy(['stability' => $this->stability]);

        $featureFlagsData = [];
        foreach ($featureFlags as $featureFlag) {
            // We disable product v2 switch based on multishop state and feature name, someday we will need
            // to implement a more generic feature for any feature flag
            $isDisabled = strpos($featureFlag->getName(), '_multi_shop') !== false && !$this->isMultiShopUsed
                || strpos($featureFlag->getName(), '_multi_shop') === false && $this->isMultiShopUsed
            ;
            $featureFlagsData[$featureFlag->getName()] = [
                'enabled' => $featureFlag->isEnabled(),
                'name' => $featureFlag->getName(),
                'label' => $featureFlag->getLabelWording(),
                'label_domain' => $featureFlag->getLabelDomain(),
                'description' => $featureFlag->getDescriptionWording(),
                'description_domain' => $featureFlag->getDescriptionDomain(),
                'disabled' => $isDisabled,
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
