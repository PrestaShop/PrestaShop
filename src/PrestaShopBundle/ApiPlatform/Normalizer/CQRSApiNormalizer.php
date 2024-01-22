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

namespace PrestaShopBundle\ApiPlatform\Normalizer;

use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorResolverInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * This normalizer is based on the Symfony ObjectNormalizer but it handles some specific normalization for
 * our CQRS <-> ApiPlatform conversion:
 *  - handle getters that match the property without starting by get, has, is
 *  - normalize ValueObject (when it is the root object), it renames the [value] key based on the ValueObject class name
 *      ex: new ProductId(42); is not normalized as ['value' => 42] but as ['productId' => 42] which most of the time matches
 *          the following DTO object to denormalize and saves adding some extra mapping
 *  - normalize attributes that are ValueObject (so not on the root level) to remove the extra value layer
 *     ex: new CreatedApiAccess(42, 'my_secret') is not normalized as ['apiAccessId' => ['value' => 42], 'secret' => 'my_secret']
 *         but as ['apiAccessId' => 42, 'secret' => 'my_secret']
 *         Again this is useful to help the automatic mapping when denormalizing the following DTO in our workflow
 */
#[AutoconfigureTag('prestashop.api.normalizers')]
class CQRSApiNormalizer extends ObjectNormalizer
{
    protected $protectedObjectClassResolver;

    public function __construct(
        ClassMetadataFactoryInterface $classMetadataFactory = null,
        NameConverterInterface $nameConverter = null,
        PropertyAccessorInterface $propertyAccessor = null,
        PropertyTypeExtractorInterface $propertyTypeExtractor = null,
        ClassDiscriminatorResolverInterface $classDiscriminatorResolver = null,
        callable $objectClassResolver = null,
        array $defaultContext = []
    ) {
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor, $classDiscriminatorResolver, $objectClassResolver, $defaultContext);

        $this->protectedObjectClassResolver = $objectClassResolver ?? function ($class) {
            return \is_object($class) ? \get_class($class) : $class;
        };
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        $normalizedObject = parent::normalize($object, $format, $context);

        if (!$this->isValueObject($object)) {
            return $normalizedObject;
        }

        // Returned normalized ValueObject with array key matching value object class (ex: ProductId => ['productId' => 42])
        $objectValue = $object->getValue();
        $class = ($this->protectedObjectClassResolver)($object);
        $reflClass = new ReflectionClass($class);

        return [
            lcfirst($reflClass->getShortName()) => $objectValue,
        ];
    }

    protected function extractAttributes(object $object, string $format = null, array $context = []): array
    {
        $attributes = parent::extractAttributes($object, $format, $context);

        // Check methods that may have been ignored by the parent, the parent normalizer only checks getter if they start
        // with "is" or "get" we increase this behaviour on other potential getters that don't match this convention
        $class = ($this->protectedObjectClassResolver)($object);
        $reflClass = new ReflectionClass($class);

        foreach ($reflClass->getMethods(ReflectionMethod::IS_PUBLIC) as $reflMethod) {
            if (
                0 !== $reflMethod->getNumberOfRequiredParameters() ||
                $reflMethod->isStatic() ||
                $reflMethod->isConstructor() ||
                $reflMethod->isDestructor()
            ) {
                continue;
            }

            $methodName = $reflMethod->name;
            // These type of getters have already been handled by the parent
            if (str_starts_with($methodName, 'get') || str_starts_with($methodName, 'has') || str_starts_with($methodName, 'is')) {
                continue;
            }

            // Add attributes that match the getter method name exactly
            if ($reflClass->hasProperty($methodName) && $this->isAllowedAttribute($object, $methodName, $format, $context)) {
                $attributes[] = $methodName;
            }
        }

        return $attributes;
    }

    protected function getAttributeValue(object $object, string $attribute, string $format = null, array $context = []): mixed
    {
        $attributeValue = parent::getAttributeValue($object, $attribute, $format, $context);
        // Value objects are not returned as is, the value itself is returned
        if ($this->isValueObject($attributeValue)) {
            $attributeValue = $attributeValue->getValue();
        }

        return $attributeValue;
    }

    protected function isValueObject($object): bool
    {
        return is_object($object)
            && !is_iterable($object)
            && method_exists($object, 'getValue')
            && str_contains(get_class($object), 'ValueObject')
        ;
    }

    /**
     * ObjectNormalizer must be the last normalizer as a fallback.
     *
     * @return int
     */
    public static function getNormalizerPriority(): int
    {
        return -1;
    }
}
