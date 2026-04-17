<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\OpenApi\Adapter;

use ApiPlatform\Metadata\Operation;
use ArrayObject;

/**
 * Chain of schema adapters that applies all adaptations in the correct order.
 */
class SchemaAdapterChain implements OpenApiSchemaAdapterInterface
{
    /**
     * @param iterable<OpenApiSchemaAdapterInterface> $adapters
     */
    public function __construct(
        iterable $adapters
    ) {
        $this->adapters = is_array($adapters) ? $adapters : iterator_to_array($adapters);
    }

    /**
     * @var OpenApiSchemaAdapterInterface[]
     */
    protected readonly array $adapters;

    public function adapt(string $class, ArrayObject $definition, ?Operation $operation = null): void
    {
        foreach ($this->adapters as $adapter) {
            $adapter->adapt($class, $definition, $operation);
        }
    }
}
