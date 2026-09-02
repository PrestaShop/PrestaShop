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
 * Interface for OpenAPI schema adapters that transform schema definitions.
 */
interface OpenApiSchemaAdapterInterface
{
    /**
     * Adapts the OpenAPI schema definition for a given class.
     *
     * @param string $class The class name to adapt the schema for
     * @param ArrayObject $definition The schema definition to adapt (modified in place)
     * @param Operation|null $operation Optional operation context for the adaptation
     */
    public function adapt(string $class, ArrayObject $definition, ?Operation $operation = null): void;
}
