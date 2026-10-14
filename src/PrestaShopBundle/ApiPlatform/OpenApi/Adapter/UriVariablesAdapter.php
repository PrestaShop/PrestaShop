<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\OpenApi\Adapter;

use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Operation;
use ArrayObject;

/**
 * Removes the URI variables (like the identifier of the updated resource) from the documented request body.
 *
 * The input schema is built from the CQRS command, whose constructor usually expects the identifier, but the API reads
 * it from the URL and overrides any value sent in the payload.
 */
class UriVariablesAdapter implements OpenApiSchemaAdapterInterface
{
    public function adapt(string $class, ArrayObject $definition, ?Operation $operation = null): void
    {
        if (!$operation instanceof HttpOperation || empty($definition['properties']) || empty($operation->getUriVariables())) {
            return;
        }

        // Only the request body built from the CQRS command, the resource schema is shared with the responses
        $commandClass = is_array($operation->getInput()) ? ($operation->getInput()['class'] ?? null) : null;
        if (null === $commandClass || $commandClass === $operation->getClass()) {
            return;
        }

        $uriVariables = array_keys($operation->getUriVariables());
        $properties = $definition['properties'];
        foreach ($uriVariables as $uriVariable) {
            if (isset($properties[$uriVariable])) {
                unset($properties[$uriVariable]);
            }
        }
        $definition['properties'] = $properties;

        if (!empty($definition['required'])) {
            $definition['required'] = array_values(array_diff($definition['required'], $uriVariables));
        }
    }
}
