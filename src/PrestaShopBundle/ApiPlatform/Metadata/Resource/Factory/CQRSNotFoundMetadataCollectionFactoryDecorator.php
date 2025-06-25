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

namespace PrestaShopBundle\ApiPlatform\Metadata\Resource\Factory;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use Psr\Container\ContainerInterface;
use Throwable;

/**
 * This factory decorates the ApiPlatform default resource factory. It looks into each operation and checks
 * if the extra property contains some CQRS commands and/or queries. If they are not found the operation/endpoint
 * is removed.
 *
 * The purpose for this clean is that we can have a single ps_apiresources module that contains definitions for
 * endpoints based on 9.1 commands for example, the endpoints would not work on 9.0 so they are filtered out.
 *
 * Scope extraction is also impacted by this filtering, meaning if a scope is only associated to invalid operations
 * it won't be available in both prod mode and debug mode, unless you enable the related feature flag.
 */
class CQRSNotFoundMetadataCollectionFactoryDecorator implements ResourceMetadataCollectionFactoryInterface
{
    public function __construct(
        private readonly ResourceMetadataCollectionFactoryInterface $decorated,
        private readonly FeatureFlagStateCheckerInterface $featureFlagStateChecker,
        private readonly ContainerInterface $container,
    ) {
    }

    public function create(string $resourceClass): ResourceMetadataCollection
    {
        // We call the original method since we only want to alter the result of this method.
        $resourceMetadataCollection = $this->decorated->create($resourceClass);

        // In debug and prod mode we always hide the invalid endpoints, unless the experimental endpoints are forcefully enabled
        if ($this->areInvalidEndpointsEnabled()) {
            return $resourceMetadataCollection;
        }

        /** @var ApiResource $resourceMetadata */
        foreach ($resourceMetadataCollection as $resourceMetadata) {
            $operations = $resourceMetadata->getOperations();
            /** @var Operation $operation */
            foreach ($operations as $key => $operation) {
                $extraProperties = $operation->getExtraProperties();

                if ($operations->has($key) && !empty($extraProperties['CQRSQuery']) && !class_exists($extraProperties['CQRSQuery'])) {
                    $operations->remove($key);
                }
                if ($operations->has($key) && !empty($extraProperties['CQRSCommand']) && !class_exists($extraProperties['CQRSCommand'])) {
                    $operations->remove($key);
                }
                if ($operations->has($key) && !empty($extraProperties['gridDataFactory']) && !$this->container->has($extraProperties['gridDataFactory'])) {
                    $operations->remove($key);
                }
            }
        }

        return $resourceMetadataCollection;
    }

    /**
     * This decorator is implied during cache clearing which would fail when the shop is not installed
     * because the DB config is not set up yet. So we protected the feature flag fetching in a try/catch
     * and return false (default value) in case of an error.
     *
     * @return bool
     */
    private function areInvalidEndpointsEnabled(): bool
    {
        try {
            return $this->featureFlagStateChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_ADMIN_API_EXPERIMENTAL_ENDPOINTS);
        } catch (Throwable) {
            return false;
        }
    }
}
