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

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShopBundle\Entity\FeatureFlag;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * This class is in charge of controlling the state modifications of Feature Flags
 * through the Back Office interface. It fetches the data needed for the Back Office interface
 * and handles payloads from the Back Office page, replicating the changes on the data model.
 */
class FeatureFlagsModifier implements DataConfigurationInterface
{
    /** @var EntityManagerInterface */
    private $doctrineEntityManager;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * @param EntityManagerInterface $doctrineEntityManager
     * @param TranslatorInterface $translator
     */
    public function __construct(EntityManagerInterface $doctrineEntityManager, TranslatorInterface $translator)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(): array
    {
        $configuration = [];

        foreach ($this->getAllFeatureFlags() as $id => $featureFlag) {
            $configuration[$featureFlag->getName()] = $featureFlag->isEnabled();
        }

        return $configuration;
    }

    /**
     * @return FeatureFlag[] $allFlags
     */
    public function getAllFeatureFlags(): array
    {
        return $this->doctrineEntityManager->getRepository('PrestaShopBundle:FeatureFlag')->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration): array
    {
        if (!$this->validateConfiguration($configuration)) {
            throw new InvalidArgumentException('Invalid feature flag configuration submitted');
        }

        /** @var array<string, boolean> $configuration */
        foreach ($configuration as $flagName => $flagState) {
            $featureFlag = $this->getOneFeatureFlagByName($flagName);

            if (null === $featureFlag) {
                throw new InvalidArgumentException(sprintf('Invalid feature flag configuration submitted, flag %s does not exist', $flagName));
            }

            if ($flagState) {
                $featureFlag->enable();
            } else {
                $featureFlag->disable();
            }
        }

        $this->doctrineEntityManager->flush();

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration): bool
    {
        /** @var array<string, boolean> $configuration */
        foreach ($configuration as $flagName => $flagState) {
            if (!is_string($flagName)) {
                return false;
            }

            if (!is_bool($flagState)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $featureFlagName
     *
     * @return FeatureFlag|null return null if feature flag cannot be found
     */
    public function getOneFeatureFlagByName(string $featureFlagName): ?FeatureFlag
    {
        return $this->doctrineEntityManager->getRepository('PrestaShopBundle:FeatureFlag')->findOneBy(['name' => $featureFlagName]);
    }
}
