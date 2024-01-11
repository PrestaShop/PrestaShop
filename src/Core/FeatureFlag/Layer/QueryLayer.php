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

namespace PrestaShop\PrestaShop\Core\FeatureFlag\Layer;

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
        return $this->canBeUsed($featureFlagName) &&
            filter_var(
                $this->requestStack->getMainRequest()?->query->get($this->getVarName($featureFlagName)),
                \FILTER_VALIDATE_BOOLEAN
            );
    }

    /**
     * {@inheritdoc}
     */
    public function enable(string $featureFlagName): void
    {
        throw new \InvalidArgumentException(sprintf('We cannot change status of the query feature flag %s.', $featureFlagName));
    }

    /**
     * {@inheritdoc}
     */
    public function disable(string $featureFlagName): void
    {
        throw new \InvalidArgumentException(sprintf('We cannot change status of the query feature flag %s.', $featureFlagName));
    }
}
