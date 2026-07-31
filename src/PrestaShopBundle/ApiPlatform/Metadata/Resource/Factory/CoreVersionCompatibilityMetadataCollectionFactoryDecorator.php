<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Metadata\Resource\Factory;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShop\PrestaShop\Core\Version;
use PrestaShopBundle\ApiPlatform\ExperimentalEndpointsCheckerTrait;

/**
 * This factory decorates the ApiPlatform default resource factory. It looks into each operation and checks
 * if the extra properties minVersion and/or maxVersion are defined, if the running core version is out of the
 * declared bounds the operation is removed. This means the operation is not visible in Swagger, and it's not
 * used to generate the api routing, so it's not usable at all and returns a 404.
 *
 * The purpose is that the ps_apiresources module can contain endpoint definitions that rely on core fixes only
 * available since a specific version (e.g. minVersion 9.2.1), the endpoints are then automatically filtered out
 * on older core versions while the rest of the module's endpoints remain usable.
 *
 * Both bounds are inclusive and compared with version_compare, so its semantics apply as-is (note for example
 * that 9.2.0-beta.1 < 9.2.0). No validation is performed on the version strings.
 *
 * Scope extraction is also impacted by this filtering, meaning if a scope is only associated to operations
 * that are incompatible with the core version it won't be available in both prod mode and debug mode, unless
 * you enable the related feature flag.
 *
 * Note that the core version constant lags behind the actual content of the development branches
 * (Version::VERSION is only bumped at release time), so on a development branch an endpoint gated on the
 * next patch version is filtered even though the branch may already contain its required fix: enable the
 * experimental endpoints feature flag to work with it.
 */
class CoreVersionCompatibilityMetadataCollectionFactoryDecorator implements ResourceMetadataCollectionFactoryInterface
{
    use ExperimentalEndpointsCheckerTrait;

    public function __construct(
        private readonly ResourceMetadataCollectionFactoryInterface $decorated,
        private readonly FeatureFlagStateCheckerInterface $featureFlagStateChecker,
        private readonly string $coreVersion = Version::VERSION,
    ) {
    }

    public function create(string $resourceClass): ResourceMetadataCollection
    {
        // We call the original method since we only want to alter the result of this method.
        $resourceMetadataCollection = $this->decorated->create($resourceClass);

        // In debug and prod mode we always hide the incompatible endpoints, unless the experimental endpoints are forcefully enabled
        if ($this->areExperimentalEndpointsEnabled()) {
            return $resourceMetadataCollection;
        }

        /** @var ApiResource $resourceMetadata */
        foreach ($resourceMetadataCollection as $resourceMetadata) {
            $operations = $resourceMetadata->getOperations();
            /** @var Operation $operation */
            foreach ($operations as $key => $operation) {
                // Single removal criteria, so no need to guard with $operations->has($key) like in
                // CQRSNotFoundMetadataCollectionFactoryDecorator which has several independent ones
                if (!$this->isCompatibleWithCoreVersion($operation)) {
                    $operations->remove($key);
                }
            }
        }

        return $resourceMetadataCollection;
    }

    private function isCompatibleWithCoreVersion(Operation $operation): bool
    {
        $extraProperties = $operation->getExtraProperties();
        if (!empty($extraProperties['minVersion']) && version_compare($this->coreVersion, $extraProperties['minVersion'], '<')) {
            return false;
        }
        if (!empty($extraProperties['maxVersion']) && version_compare($this->coreVersion, $extraProperties['maxVersion'], '>')) {
            return false;
        }

        return true;
    }
}
