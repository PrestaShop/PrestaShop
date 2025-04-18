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

namespace PrestaShopBundle\ApiPlatform\Metadata\Property\Factory;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

/**
 * This service is used so that the parameters of the CQRS command constructor are used in priority over
 * their class fields. This is mostly because CQRS commands often change their initial scalar inputs into
 * ValueObjects:
 *   ex: input is inr $productId turned into a protected ProductId $productId;
 *
 * In the JSON schema we don't want to document the ValueObject but the actual input used to create the command
 * that should be used in the JSON body content.
 *
 * To do so we integrate this service in the decoration chain used by SchemaPropertyMetadataFactory, so we can replace
 * the types of property fields with their associated constructor types when present.
 */
class CQRSConstructorPropertyMetadataFactory implements PropertyMetadataFactoryInterface
{
    /**
     * Define the priority so this decorator is the last one in the chain right before SchemaPropertyMetadataFactory,
     * this is the last opportunity to change the extracted types before the JSON schema is generated based on those types.
     */
    public const DECORATION_PRIORITY = 11;

    public function __construct(
        protected readonly PropertyMetadataFactoryInterface $decorated,
        protected readonly PropertyTypeExtractorInterface $constructorExtractor,
        protected readonly array $commandsAndQueries,
    ) {
    }

    public function create(string $resourceClass, string $property, array $options = []): ApiProperty
    {
        $parentApiProperty = $this->decorated->create($resourceClass, $property, $options);

        // We only handle parameters in the constructor
        if (!$parentApiProperty->isInitializable()) {
            return $parentApiProperty;
        }

        // This service only targets CQRS commands schemas, we don't want it to impact other classes
        if (!in_array($resourceClass, $this->commandsAndQueries, true)) {
            return $parentApiProperty;
        }

        return $this->overrideConstructorTypes($parentApiProperty, $resourceClass, $property, $options);
    }

    /**
     * This method code is mostly copied from the PropertyInfoPropertyMetadataFactory except it extracts the type from the constructor
     */
    protected function overrideConstructorTypes(ApiProperty $propertyMetadata, string $resourceClass, string $property, array $options = []): ApiProperty
    {
        $types = $this->constructorExtractor->getTypes($resourceClass, $property, $options) ?? [];
        if (empty($types)) {
            return $propertyMetadata;
        }

        foreach ($types as $i => $type) {
            // Temp fix for https://github.com/symfony/symfony/pull/52699
            if (ArrayCollection::class === $type->getClassName()) {
                $types[$i] = new Type($type->getBuiltinType(), $type->isNullable(), $type->getClassName(), true, $type->getCollectionKeyTypes(), $type->getCollectionValueTypes());
            }
        }

        return $propertyMetadata->withBuiltinTypes($types)->withSchema([]);
    }
}
