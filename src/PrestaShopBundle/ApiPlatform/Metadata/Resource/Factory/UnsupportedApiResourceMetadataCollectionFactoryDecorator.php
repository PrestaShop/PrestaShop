<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Metadata\Resource\Factory;

use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;
use Error;
use Psr\Log\LoggerInterface;

/**
 * Ignores a resource written for a newer core (an operation class or argument this core doesn't have), like a resource
 * whose minVersion is not reached, instead of breaking every other endpoint.
 */
class UnsupportedApiResourceMetadataCollectionFactoryDecorator implements ResourceMetadataCollectionFactoryInterface
{
    use UnsupportedApiResourceTrait;

    public function __construct(
        private readonly ResourceMetadataCollectionFactoryInterface $decorated,
        private readonly ?LoggerInterface $logger = null,
    ) {
    }

    public function create(string $resourceClass): ResourceMetadataCollection
    {
        try {
            return $this->createFromAttributes($resourceClass);
        } catch (Error $error) {
            if (!$this->isUnsupportedApiResource($error)) {
                throw $error;
            }
            $this->logger?->warning(sprintf('API resource %s is ignored, it is not supported by this core: %s', $resourceClass, $error->getMessage()));

            return new ResourceMetadataCollection($resourceClass);
        }
    }

    /**
     * @throws Error when an attribute of the resource uses an operation class or argument this core doesn't have
     */
    private function createFromAttributes(string $resourceClass): ResourceMetadataCollection
    {
        return $this->decorated->create($resourceClass);
    }
}
