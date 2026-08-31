<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform;

use ApiPlatform\Metadata\Operation;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Applies the default values declared by an operation (defaultValues extra property) to its input.
 *
 * An API resource property cannot carry a default value: the input of an operation is denormalized into the CQRS
 * command or query of that operation, never into the resource class, so the default would be dropped on the way. The
 * defaults are therefore declared by the operation itself and injected into its input, which is the request payload for
 * a command, and the URI variables and filters for a query.
 */
trait DefaultValuesTrait
{
    private ?PropertyAccessorInterface $defaultValuesPropertyAccessor = null;

    /**
     * A value explicitly provided is never replaced, even a null one, since the client did provide it.
     *
     * A default declared as a context property path (a string starting with [_context]) is resolved against the
     * input, where the context parameters are merged before the defaults are applied: the field then defaults to a
     * value of the current context, like the shops the request applies to. An unreadable path injects nothing, so
     * the field remains a missing one and is reported as such.
     */
    protected function applyDefaultValues(mixed $input, ?Operation $operation): mixed
    {
        if (!is_array($input) || null === $operation) {
            return $input;
        }

        foreach ($operation->getExtraProperties()['defaultValues'] ?? [] as $property => $defaultValue) {
            if (array_key_exists($property, $input)) {
                continue;
            }

            if (static::isContextDefaultValue($defaultValue)) {
                if ($this->getDefaultValuesPropertyAccessor()->isReadable($input, $defaultValue)) {
                    $input[$property] = $this->getDefaultValuesPropertyAccessor()->getValue($input, $defaultValue);
                }

                continue;
            }

            $input[$property] = $defaultValue;
        }

        return $input;
    }

    /**
     * A context default is resolved when the values are applied, so it cannot be documented as a literal default
     * value: the OpenAPI adapter relies on this check to document it differently.
     */
    public static function isContextDefaultValue(mixed $defaultValue): bool
    {
        return is_string($defaultValue) && str_starts_with($defaultValue, '[_context]');
    }

    private function getDefaultValuesPropertyAccessor(): PropertyAccessorInterface
    {
        if (null === $this->defaultValuesPropertyAccessor) {
            // Invalid indexes must be detected, so that isReadable tells whether the context path can be resolved
            $this->defaultValuesPropertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
                ->enableExceptionOnInvalidIndex()
                ->enableExceptionOnInvalidPropertyPath()
                ->getPropertyAccessor()
            ;
        }

        return $this->defaultValuesPropertyAccessor;
    }
}
