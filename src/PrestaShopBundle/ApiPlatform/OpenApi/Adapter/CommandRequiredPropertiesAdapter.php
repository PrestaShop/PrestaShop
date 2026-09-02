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
use ReflectionClass;
use ReflectionParameter;

/**
 * Documents which properties of a write operation are required.
 *
 * The payload is denormalized into the CQRS command, so a constructor parameter without default value must be present
 * in the request: when it is missing the serializer cannot build the command at all and the request fails. Those
 * parameters are therefore listed as required in the schema, using their API name when the mapping renames them.
 *
 * Two kinds of parameters are excluded since the API fills them itself rather than reading them from the payload:
 * the ones mapped from the request context ([_context] paths, like the shop constraint) and the ones provided as URI
 * variables (like the identifier of the updated resource).
 */
class CommandRequiredPropertiesAdapter implements OpenApiSchemaAdapterInterface
{
    public function adapt(string $class, ArrayObject $definition, ?Operation $operation = null): void
    {
        if (!$operation || empty($definition['properties'])) {
            return;
        }

        $commandClass = is_array($operation->getInput()) ? ($operation->getInput()['class'] ?? null) : null;
        if (null === $commandClass || !class_exists($commandClass)) {
            return;
        }

        $constructor = (new ReflectionClass($commandClass))->getConstructor();
        if (null === $constructor) {
            return;
        }

        [$apiNames, $contextParameters] = $this->getMappedParameters($operation);
        // Only the HTTP operations have URI variables, and only those have a request body anyway
        $uriVariables = $operation instanceof HttpOperation ? array_keys($operation->getUriVariables() ?? []) : [];
        $properties = $definition['properties'];
        $required = $definition['required'] ?? [];

        foreach ($constructor->getParameters() as $parameter) {
            if (!$this->isRequired($parameter) || isset($contextParameters[$parameter->getName()])) {
                continue;
            }

            $propertyName = $apiNames[$parameter->getName()] ?? $parameter->getName();
            if (in_array($propertyName, $uriVariables, true) || !isset($properties[$propertyName])) {
                continue;
            }

            if (!in_array($propertyName, $required, true)) {
                $required[] = $propertyName;
            }
        }

        if (!empty($required)) {
            $definition['required'] = array_values($required);
        }
    }

    /**
     * A parameter with a default value can be omitted from the payload, and so can a variadic one.
     */
    protected function isRequired(ReflectionParameter $parameter): bool
    {
        return !$parameter->isOptional();
    }

    /**
     * Reads the CQRSCommandMapping to know the API name of each command parameter, and which ones the API fills from
     * the request context.
     *
     * @return array{array<string, string>, array<string, true>}
     */
    protected function getMappedParameters(Operation $operation): array
    {
        $apiNames = [];
        $contextParameters = [];
        foreach ($operation->getExtraProperties()['CQRSCommandMapping'] ?? [] as $apiPath => $cqrsPath) {
            if (!preg_match('/^\[([^\[\]]+)\]/', $cqrsPath, $cqrsMatches)) {
                continue;
            }
            $cqrsParameter = $cqrsMatches[1];

            if (str_starts_with($apiPath, '[_context]')) {
                $contextParameters[$cqrsParameter] = true;

                continue;
            }

            // Only a mapping between two single properties renames a parameter, deeper paths apply inside its schema
            if (preg_match('/^\[([^\[\]]+)\]$/', $apiPath, $apiMatches) && preg_match('/^\[[^\[\]]+\]$/', $cqrsPath)) {
                $apiNames[$cqrsParameter] = $apiMatches[1];
            }
        }

        return [$apiNames, $contextParameters];
    }
}
