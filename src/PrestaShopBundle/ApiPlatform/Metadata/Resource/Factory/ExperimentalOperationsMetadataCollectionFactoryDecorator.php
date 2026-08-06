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
use PrestaShopBundle\ApiPlatform\ExperimentalEndpointsCheckerTrait;

/**
 * This factory decorates the ApiPlatform default resource factory. It looks into each operation and checks
 * if the extra property experimentalOperation is set to true, if its is then the operation should be filtered out
 * in production environment. This means the operation is not visible in Swagger, and it's not used to generate the
 * api routing, so it's not usable at all.
 *
 * Scope extraction is also impacted by this filtering, meaning if a scope is only associated to experimental operations
 * it won't be available in prod mode at all, unless you enable the related feature flag.
 *
 * In dev mode all operations are kept though.
 */
class ExperimentalOperationsMetadataCollectionFactoryDecorator implements ResourceMetadataCollectionFactoryInterface
{
    use ExperimentalEndpointsCheckerTrait;

    public function __construct(
        private readonly ResourceMetadataCollectionFactoryInterface $decorated,
        private readonly bool $isDebug,
        private readonly FeatureFlagStateCheckerInterface $featureFlagStateChecker,
    ) {
    }

    public function create(string $resourceClass): ResourceMetadataCollection
    {
        // We call the original method since we only want to alter the result of this method.
        $resourceMetadataCollection = $this->decorated->create($resourceClass);

        // In debug mode we filter nothing, in prod mode we do unless the forcing configuration is enabled
        if ($this->isDebug || $this->areExperimentalEndpointsEnabled()) {
            return $resourceMetadataCollection;
        }

        /** @var ApiResource $resourceMetadata */
        foreach ($resourceMetadataCollection as $resourceMetadata) {
            $operations = $resourceMetadata->getOperations();
            /** @var Operation $operation */
            foreach ($operations as $key => $operation) {
                $extraProperties = $operation->getExtraProperties();
                if (isset($extraProperties['experimentalOperation']) && $extraProperties['experimentalOperation'] === true) {
                    $operations->remove($key);
                }
            }
        }

        return $resourceMetadataCollection;
    }
}
