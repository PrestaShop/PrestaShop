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
 * Documents the default values of a write operation: the value is exposed as the default of the property, and the
 * property is removed from the required ones since the API fills it when the payload omits it.
 *
 * It runs after the CommandRequiredPropertiesAdapter, which lists as required every constructor parameter of the
 * command that has no default value: a parameter defaulted by the operation is one of them, and the operation is the
 * one telling the truth about the API contract.
 */
class DefaultValuesAdapter implements OpenApiSchemaAdapterInterface
{
    public function adapt(string $class, ArrayObject $definition, ?Operation $operation = null): void
    {
        if (!$operation || empty($operation->getExtraProperties()['defaultValues'])) {
            return;
        }

        $defaultValues = $operation->getExtraProperties()['defaultValues'];
        foreach ($defaultValues as $propertyName => $defaultValue) {
            $this->documentDefaultValue($definition, (string) $propertyName, $defaultValue);
        }

        if (!empty($definition['required'])) {
            $required = array_values(array_diff($definition['required'], array_keys($defaultValues)));
            if (empty($required)) {
                unset($definition['required']);
            } else {
                $definition['required'] = $required;
            }
        }
    }

    protected function documentDefaultValue(ArrayObject $definition, string $propertyName, mixed $defaultValue): void
    {
        if (empty($definition['properties'])) {
            return;
        }

        $properties = $definition['properties'];
        if (!isset($properties[$propertyName])) {
            return;
        }

        // The property definition is either an ArrayObject modified in place or an array that must be assigned back
        $property = $properties[$propertyName];
        if ($property instanceof ArrayObject) {
            $property['default'] = $defaultValue;

            return;
        }

        if (is_array($property)) {
            $property['default'] = $defaultValue;
            $properties[$propertyName] = $property;
            $definition['properties'] = $properties;
        }
    }
}
