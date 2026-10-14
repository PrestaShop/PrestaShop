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
 * Turns the enum of the properties into lists.
 *
 * ApiPlatform documents a Choice constraint with its choices as they are, so the choices of an associative constant
 * (like ProductVisibility::AVAILABLE_VISIBILITY_VALUES) became a JSON object, while OpenAPI expects an array.
 */
class EnumListAdapter implements OpenApiSchemaAdapterInterface
{
    public function adapt(string $class, ArrayObject $definition, ?Operation $operation = null): void
    {
        if (empty($definition['properties'])) {
            return;
        }

        $properties = $definition['properties'];
        foreach ($properties as $propertyName => $property) {
            $properties[$propertyName] = $this->listEnums($property);
        }
        $definition['properties'] = $properties;
    }

    protected function listEnums(mixed $schema): mixed
    {
        if (!is_array($schema) && !$schema instanceof ArrayObject) {
            return $schema;
        }

        if (isset($schema['enum']) && is_array($schema['enum'])) {
            $schema['enum'] = array_values($schema['enum']);
        }
        if (isset($schema['items'])) {
            $schema['items'] = $this->listEnums($schema['items']);
        }

        return $schema;
    }
}
