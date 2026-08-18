<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform;

use ApiPlatform\Metadata\Operation;

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
    /**
     * A value explicitly provided is never replaced, even a null one, since the client did provide it.
     */
    protected function applyDefaultValues(mixed $input, ?Operation $operation): mixed
    {
        if (!is_array($input) || null === $operation) {
            return $input;
        }

        foreach ($operation->getExtraProperties()['defaultValues'] ?? [] as $property => $defaultValue) {
            if (!array_key_exists($property, $input)) {
                $input[$property] = $defaultValue;
            }
        }

        return $input;
    }
}
