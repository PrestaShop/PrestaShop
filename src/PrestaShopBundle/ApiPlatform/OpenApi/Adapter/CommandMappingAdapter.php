<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\OpenApi\Adapter;

use ApiPlatform\Metadata\Operation;
use ArrayObject;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Applies command mapping to OpenAPI schema.
 * Updates the schema property names based on the mapping specified, if for example the CQRS commands has a localizedNames
 * property that was renamed via the mapping into names then the schema won't use localizedNames but names for the final
 * schema output so that it matches the actual expected format.
 *
 * ApiPlatform builds the input schema from the CQRS command, where it detects the properties from the accessors and
 * their writability from the constructor parameters. So three cases must be handled:
 *
 *   - the property was detected under its CQRS name, it is renamed into the API name (localizedDelay becomes delays)
 *   - the property was detected under the API name, but as read only because the matching constructor parameter has a
 *     different name (getMaxWidth against $max_width, isFree against $isFree, hasAdditionalHandlingFee against
 *     $hasAdditionalHandlingFee). The mapping proves it is part of the API input, so the flag is removed.
 *   - the property was detected under neither name, which means the mapping targets nothing on the command: it is
 *     left undocumented rather than documented as an untyped field the command would ignore anyway
 *
 * The context parameters ([_context] paths) are removed from the documented payload since the API injects them from
 * the request context.
 */
class CommandMappingAdapter implements OpenApiSchemaAdapterInterface
{
    public function __construct(
        protected readonly PropertyAccessorInterface $propertyAccessor
    ) {
    }

    public function adapt(string $class, ArrayObject $definition, ?Operation $operation = null): void
    {
        if (!$operation || empty($operation->getExtraProperties()['CQRSCommandMapping'])) {
            return;
        }

        foreach ($operation->getExtraProperties()['CQRSCommandMapping'] as $apiPath => $cqrsPath) {
            $properties = $definition['properties'];
            $cqrsProperty = $this->getRootPropertyName($cqrsPath);

            // The context parameters are not part of the payload, the API fills them from the request context
            if (str_starts_with($apiPath, '[_context]')) {
                if (null !== $cqrsProperty && isset($properties[$cqrsProperty])) {
                    unset($properties[$cqrsProperty]);
                    $definition['properties'] = $properties;
                }

                continue;
            }

            $apiProperty = $this->getRootPropertyName($apiPath);
            if (null === $apiProperty) {
                continue;
            }

            if ($this->isRenaming($apiPath, $cqrsPath) && isset($properties[$cqrsProperty])) {
                // The property was documented under its CQRS name, its definition is the accurate one for the input
                $properties[$apiProperty] = $properties[$cqrsProperty];
                unset($properties[$cqrsProperty]);
                $definition['properties'] = $properties;
            }

            $this->makeApiPropertyWritable($definition, $apiProperty);
        }
    }

    /**
     * Both paths must target a single property for the rename to be unambiguous, a mapping between sub properties
     * (like [ranges][@index][zoneId] to [ranges][@index][id_zone]) applies inside the schema of a property, not to
     * the property itself.
     */
    protected function isRenaming(string $apiPath, string $cqrsPath): bool
    {
        return $apiPath !== $cqrsPath
            && preg_match('/^\[[^\[\]]+\]$/', $apiPath)
            && preg_match('/^\[[^\[\]]+\]$/', $cqrsPath)
        ;
    }

    /**
     * Returns the name of the documented property targeted by a mapping path, so the first level of it since the
     * deeper levels are part of the property own schema.
     */
    protected function getRootPropertyName(string $path): ?string
    {
        if (!preg_match('/^\[([^\[\]]+)\]/', $path, $matches)) {
            return null;
        }

        return $matches[1];
    }

    /**
     * A mapped property is part of the API input by definition, so any readOnly flag inferred from the CQRS class
     * must be removed, else the property is absent from the request body example although the API expects it in the
     * payload (and its absence even breaks the request when the constructor parameter has no default value).
     */
    protected function makeApiPropertyWritable(ArrayObject $definition, string $propertyName): void
    {
        $properties = $definition['properties'];
        if (!isset($properties[$propertyName])) {
            return;
        }

        // The property definition is either an ArrayObject modified in place or an array that must be assigned back
        $property = $properties[$propertyName];
        if ($property instanceof ArrayObject) {
            unset($property['readOnly']);

            return;
        }

        if (is_array($property) && array_key_exists('readOnly', $property)) {
            unset($property['readOnly']);
            $properties[$propertyName] = $property;
            $definition['properties'] = $properties;
        }
    }
}
