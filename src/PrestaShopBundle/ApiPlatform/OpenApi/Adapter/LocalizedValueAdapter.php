<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\OpenApi\Adapter;

use ApiPlatform\Metadata\Operation;
use ArrayObject;
use PrestaShopBundle\ApiPlatform\Metadata\LocalizedValue;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;

/**
 * Adapts localized values in OpenAPI schema.
 * Localized values are arrays indexed by locales (or objects with properties matching the locale in JSON),
 * this adapter adapts the expected format along with an example to indicate the user that the key to use is the locale.
 */
class LocalizedValueAdapter implements OpenApiSchemaAdapterInterface
{
    public function __construct(
        protected readonly ClassMetadataFactoryInterface $classMetadataFactory
    ) {
    }

    public function adapt(string $class, ArrayObject $definition, ?Operation $operation = null): void
    {
        if (empty($definition['properties'])) {
            return;
        }

        $resourceClassMetadata = $this->classMetadataFactory->getMetadataFor($class);
        $resourceReflectionClass = $resourceClassMetadata->getReflectionClass();

        foreach ($definition['properties'] as $propertyName => $propertySchema) {
            if (!$resourceReflectionClass->hasProperty($propertyName)) {
                continue;
            }

            $property = $resourceReflectionClass->getProperty($propertyName);
            foreach ($property->getAttributes() as $attribute) {
                if ($attribute->getName() === LocalizedValue::class || is_subclass_of($attribute->getName(), LocalizedValue::class)) {
                    if (!($propertySchema instanceof ArrayObject)) {
                        $definition['properties'][$propertyName] = new ArrayObject();
                    }
                    unset($definition['properties'][$propertyName]['type']);
                    $definition['properties'][$propertyName]['type'] = 'object';
                    if (!isset($definition['properties'][$propertyName]['example'])) {
                        $definition['properties'][$propertyName]['example'] = [
                            'en-US' => 'value',
                            'fr-FR' => 'valeur',
                        ];
                    }
                    unset($definition['properties'][$propertyName]['items']);
                    unset($definition['properties'][$propertyName]['format']);
                    if (isset($definition['properties'][$propertyName]['enum'])) {
                        unset($definition['properties'][$propertyName]['enum']);
                    }
                }
            }
        }
    }
}
