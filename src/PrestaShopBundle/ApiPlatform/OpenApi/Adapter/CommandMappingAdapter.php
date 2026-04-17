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
            if ($this->propertyAccessor->isReadable($definition['properties'], $cqrsPath)) {
                if (!str_starts_with($apiPath, '[_context]') && $this->propertyAccessor->isWritable($definition['properties'], $apiPath)) {
                    $this->propertyAccessor->setValue($definition['properties'], $apiPath, $this->propertyAccessor->getValue($definition['properties'], $cqrsPath));
                }
                if ($this->propertyAccessor->isWritable($definition['properties'], $cqrsPath)) {
                    $this->propertyAccessor->setValue($definition['properties'], $cqrsPath, null);
                }
            } elseif (!str_starts_with($apiPath, '[_context]')) {
                $apiPropertyName = trim($apiPath, '[]');
                if (!isset($definition['properties'][$apiPropertyName])) {
                    $definition['properties'][$apiPropertyName] = new ArrayObject();
                }
            }
        }

        foreach ($definition['properties'] as $propertyName => $propertyValue) {
            if (null === $propertyValue) {
                unset($definition['properties'][$propertyName]);
            }
        }
    }
}
