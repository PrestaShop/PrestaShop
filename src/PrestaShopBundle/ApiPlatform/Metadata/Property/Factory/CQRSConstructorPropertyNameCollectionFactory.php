<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Metadata\Property\Factory;

use ApiPlatform\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use ApiPlatform\Metadata\Property\PropertyNameCollection;
use ReflectionClass;

/**
 * Adds the constructor parameters of the CQRS commands to their properties.
 *
 * The properties are detected from the accessors, so a parameter whose getter has no get/is/has prefix (like
 * AddCustomerGroupCommand::showPrice()) was missing from the documented input, although the command requires it.
 */
class CQRSConstructorPropertyNameCollectionFactory implements PropertyNameCollectionFactoryInterface
{
    public function __construct(
        protected readonly PropertyNameCollectionFactoryInterface $decorated,
        protected readonly array $commandsAndQueries,
    ) {
    }

    public function create(string $resourceClass, array $options = []): PropertyNameCollection
    {
        $propertyNames = $this->decorated->create($resourceClass, $options);
        if (!in_array($resourceClass, $this->commandsAndQueries, true) || !class_exists($resourceClass)) {
            return $propertyNames;
        }

        $constructor = (new ReflectionClass($resourceClass))->getConstructor();
        if (null === $constructor) {
            return $propertyNames;
        }

        $names = iterator_to_array($propertyNames);
        foreach ($constructor->getParameters() as $parameter) {
            if (!in_array($parameter->getName(), $names, true)) {
                $names[] = $parameter->getName();
            }
        }

        return new PropertyNameCollection($names);
    }
}
