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
