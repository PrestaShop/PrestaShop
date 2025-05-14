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

use PrestaShopBundle\ApiPlatform\DomainObjectDetector;
use PrestaShopBundle\ApiPlatform\LocalizedValueUpdater;
use PrestaShopBundle\ApiPlatform\Metadata\LocalizedValue;
use PrestaShopBundle\ApiPlatform\NormalizationMapper;
use PrestaShopBundle\ApiPlatform\Validator\CQRSApiValidator;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorResolverInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * This normalizer is based on the Symfony ObjectNormalizer, but it handles some specific normalization for
 * our CQRS <-> ApiPlatform conversion:
 *  - detects if a type is a domain class (either because it is detected as a CQRS command or query) or if it is part of the
 *    PrestaShop\PrestaShop\Core\Domain namespace
 *  - handle CQRS constructor proper types because the constructor types sometimes don't match their properties, since they are
 *    transformed into Value Objects, so the regular ObjectNormalizer triggers a type exception
 *  - handle getters that match the property without starting by get, has, is
 *  - set appropriate context for the ValueObjectNormalizer for when we don't want a ValueObject but the scalar value to be used
 *  - if an API resource is denormalized but has an input class from the domain this serializer detects it and automatically deserialize
 *    the data into the CQRS object, this saves one deserialization process as the command can be passed to the processor directly
 *  - when CQRS input class switching is detected the normalizer performs a validation of the input,then it build a new context for
 *    the next serialization so that mapping and localized values are correctly handled
 *  - handle setter methods that use multiple parameters
 */
#[AutoconfigureTag('prestashop.api.normalizers')]
class CQRSApiNormalizer extends ObjectNormalizer
{
    public function __construct(
        protected readonly DomainObjectDetector $domainObjectDetector,
        protected readonly LocalizedValueUpdater $localizedValueUpdater,
        protected readonly CQRSApiValidator $CQRSApiValidator,
        ?ClassMetadataFactoryInterface $classMetadataFactory = null,
        ?NameConverterInterface $nameConverter = null,
        ?PropertyAccessorInterface $propertyAccessor = null,
        ?PropertyTypeExtractorInterface $propertyTypeExtractor = null,
        ?ClassDiscriminatorResolverInterface $classDiscriminatorResolver = null,
        ?callable $objectClassResolver = null,
        array $defaultContext = []
    ) {
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor, $classDiscriminatorResolver, $objectClassResolver, $defaultContext);
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = [])
    {
        if (!empty($context['input']['class']) && $this->isDomainObject($context['input']['class'], [])) {
            $inputType = $context['input']['class'];
            unset($context['input']);

            if (!empty($context['operation']) && $this->CQRSApiValidator->hasConstraints($type)) {
                $apiResource = parent::denormalize($data, $type, $format, $context);
                $this->CQRSApiValidator->validate($apiResource, $context['operation']);
            }

            // Prepare the list of localized values since the LocalizedValue attribute is set on the API Resource class
            // but not on the CQRS input class, we need to pass this information via the context
            $localizedValueParameters = $this->localizedValueUpdater->getLocalizedAttributesContext($type, $context);
            if (!empty($localizedValueParameters)) {
                // CQRS commands are base on Language IDs, so we force the conversion
                foreach ($localizedValueParameters as $parameterName => $localizedValueParameter) {
                    $localizedValueParameters[$parameterName][LocalizedValue::DENORMALIZED_KEY] = LocalizedValue::ID_KEY;
                    $localizedValueParameters[$parameterName][LocalizedValue::NORMALIZED_KEY] = LocalizedValue::ID_KEY;
                }
                $context += [LocalizedValue::LOCALIZED_VALUE_PARAMETERS => $localizedValueParameters];
            }
            if (!empty($context['operation']) && !empty($context['operation']->getExtraProperties()['CQRSCommandMapping'])) {
                $context[NormalizationMapper::NORMALIZATION_MAPPING] = ($context[NormalizationMapper::NORMALIZATION_MAPPING] ?? []) + $context['operation']->getExtraProperties()['CQRSCommandMapping'];
            }

            if (!$this->serializer instanceof DenormalizerInterface) {
                throw new LogicException(sprintf('Cannot create an instance of "%s" from serialized data because the serializer inject in "%s" is not a denormalizer.', $inputType, static::class));
            }

            return $this->serializer->denormalize($data, $inputType, $format, $context);
        }

        return parent::denormalize($data, $type, $format, $context);
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = [])
    {
        return parent::supportsDenormalization($data, $type, $format) && $this->isDomainObject($type, $context);
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = [])
    {
        return parent::supportsNormalization($data, $format) && $this->isDomainObject($data, $context);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            'object' => true,
        ];
    }

    protected function isDomainObject(mixed $objectOrType, array $context): bool
    {
        // Some API Resource operation can define another class as the input, which is defined in the context,
        // if such input class is a domain class then this normalizer should handle it
        if (!empty($context['input']['class'])) {
            $objectOrType = $context['input']['class'];
        }

        return $this->domainObjectDetector->isDomainObject($objectOrType);
    }

    /**
     * This method is overridden because our CQRS objects sometimes have setters with multiple arguments, these are usually used to force specifying arguments that must
     * be defined all together, so they can be validated as a whole. The ObjectNormalizer only deserialize object properties one at a time, so we have to handle this special
     * use case and the best moment to do so is right after the object is instantiated and right before the properties are deserialized.
     */
    protected function instantiateObject(array &$data, string $class, array &$context, ReflectionClass $reflectionClass, bool|array $allowedAttributes, ?string $format = null)
    {
        $object = parent::instantiateObject($data, $class, $context, $reflectionClass, $allowedAttributes, $format);
        $methodsWithMultipleArguments = $this->findMethodsWithMultipleArguments($reflectionClass, $data);
        $this->executeMethodsWithMultipleArguments($data, $object, $methodsWithMultipleArguments, $context, $format);

        return $object;
    }

    /**
     * This method is only used to denormalize the constructor parameters, the CQRS classes usually expect scalar input values that
     * are converted into ValueObject in the constructor, so only in this phase of the denormalization we disable the ValueObjectNormalizer
     * by specifying the context option ValueObjectNormalizer::VALUE_OBJECT_RETURNED_AS_SCALAR.
     *
     * This is also the right moment to update localized values before they are passed in the constructor.
     */
    protected function denormalizeParameter(ReflectionClass $class, ReflectionParameter $parameter, string $parameterName, mixed $parameterData, array $context, ?string $format = null): mixed
    {
        $parameterData = $this->localizedValueUpdater->denormalizeLocalizedValue($parameterData, $parameterName, $context);

        return parent::denormalizeParameter($class, $parameter, $parameterName, $parameterData, $context + [ValueObjectNormalizer::VALUE_OBJECT_RETURNED_AS_SCALAR => true], $format);
    }

    /**
     * This method is used when normalizing nested children, in nested value we don't want the ValueObject to be returned as arrays but as simple
     * values, so we force the ValueObjectNormalizer::VALUE_OBJECT_RETURNED_AS_SCALAR option. So ValueObject are only normalized as array when they
     * are the root object.
     */
    protected function createChildContext(array $parentContext, string $attribute, ?string $format): array
    {
        $childContext = parent::createChildContext($parentContext, $attribute, $format);

        return $childContext + [ValueObjectNormalizer::VALUE_OBJECT_RETURNED_AS_SCALAR => true];
    }

    /**
     * This method is overridden in order to increase the getters used to fetch attributes, by default the ObjectNormalizer
     * searches for getters start with get/is/has/can, but it ignores getters that matches the properties exactly.
     */
    protected function extractAttributes(object $object, ?string $format = null, array $context = []): array
    {
        $attributes = parent::extractAttributes($object, $format, $context);
        if ($this->classMetadataFactory) {
            $metadata = $this->classMetadataFactory->getMetadataFor($object);
            $reflClass = $metadata->getReflectionClass();
        } else {
            $reflClass = new ReflectionClass(\is_object($object) ? $object::class : $object);
        }

        foreach ($reflClass->getMethods(ReflectionMethod::IS_PUBLIC) as $reflMethod) {
            if (
                0 !== $reflMethod->getNumberOfRequiredParameters()
                || $reflMethod->isStatic()
                || $reflMethod->isConstructor()
                || $reflMethod->isDestructor()
            ) {
                continue;
            }

            $methodName = $reflMethod->name;
            // These type of getters have already been handled by the parent
            if (str_starts_with($methodName, 'get') || str_starts_with($methodName, 'has') || str_starts_with($methodName, 'is') || str_starts_with($methodName, 'can')) {
                continue;
            }

            // Add attributes that match the getter method name exactly
            if ($reflClass->hasProperty($methodName) && $this->isAllowedAttribute($object, $methodName, $format, $context)) {
                $attributes[] = $methodName;
            }
        }

        return $attributes;
    }

    /**
     * This method is overridden in order to dynamically change the localized properties identified by a context or the LocalizedValue
     * helper attribute. The used key that are based on Language's locale are automatically converted to rely on Language's database ID.
     */
    protected function getAttributeValue(object $object, string $attribute, ?string $format = null, array $context = []): mixed
    {
        $attributeValue = parent::getAttributeValue($object, $attribute, $format, $context);

        return $this->localizedValueUpdater->denormalizeLocalizedValue($attributeValue, $attribute, $context);
    }

    /**
     * This method is overridden in order to dynamically change the localized properties identified by a context or the LocalizedValue
     *  helper attribute. he used key that are based on Language's database ID are automatically converted to rely on Language's locale.
     */
    protected function setAttributeValue(object $object, string $attribute, mixed $value, ?string $format = null, array $context = [])
    {
        $value = $this->localizedValueUpdater->denormalizeLocalizedValue($value, $attribute, $context);

        parent::setAttributeValue($object, $attribute, $value, $format, $context);
    }

    /**
     * Call all the method with multiple arguments and remove the data from the normalized data since it has already been denormalized into
     * the object.
     *
     * @param array $data
     * @param object $object
     * @param array<string, ReflectionMethod> $methodsWithMultipleArguments
     *
     * @return void
     */
    protected function executeMethodsWithMultipleArguments(array &$data, object $object, array $methodsWithMultipleArguments, array $context, ?string $format = null): void
    {
        foreach ($methodsWithMultipleArguments as $attributeName => $reflectionMethod) {
            $methodParameters = $data[$attributeName];
            // denormalize parameters
            foreach ($reflectionMethod->getParameters() as $parameter) {
                $parameterType = $parameter->getType();
                if ($parameterType instanceof ReflectionNamedType && !$parameterType->isBuiltin()) {
                    $childContext = $this->createChildContext($context, $parameter->getName(), $format);
                    if (!$this->serializer instanceof DenormalizerInterface) {
                        throw new LogicException(sprintf('Cannot denormalize parameter "%s" for method "%s" because injected serializer is not a denormalizer.', $parameter->getName(), $reflectionMethod->getName()));
                    }

                    if ($this->serializer->supportsDenormalization($methodParameters[$parameter->getName()], $parameterType->getName(), $format, $childContext)) {
                        $methodParameters[$parameter->getName()] = $this->serializer->denormalize($methodParameters[$parameter->getName()], $parameterType->getName(), $format, $childContext);
                    }
                }
            }

            $reflectionMethod->invoke($object, ...$methodParameters);
            unset($data[$attributeName]);
        }
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @param array $normalizedData
     *
     * @return array<string, ReflectionMethod>
     */
    protected function findMethodsWithMultipleArguments(ReflectionClass $reflectionClass, array $normalizedData): array
    {
        $methodsWithMultipleArguments = [];
        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            // We only look into public method that can be setters with multiple parameters
            if (
                $reflectionMethod->getNumberOfRequiredParameters() <= 1
                || $reflectionMethod->isStatic()
                || $reflectionMethod->isConstructor()
                || $reflectionMethod->isDestructor()
            ) {
                continue;
            }

            // Remove set/with to get the potential matching property in data (use full method name by default)
            if (str_starts_with($reflectionMethod->getName(), 'set')) {
                $methodPropertyName = lcfirst(substr($reflectionMethod->getName(), 3));
            } elseif (str_starts_with($reflectionMethod->getName(), 'with')) {
                $methodPropertyName = lcfirst(substr($reflectionMethod->getName(), 4));
            } else {
                $methodPropertyName = $reflectionMethod->getName();
            }

            // No data found matching the method so we skip it
            if (empty($normalizedData[$methodPropertyName])) {
                continue;
            }

            $methodParameters = $normalizedData[$methodPropertyName];
            if (!is_array($methodParameters)) {
                throw new InvalidArgumentException(sprintf('Value for method "%s" should be an array', $reflectionMethod->getName()));
            }

            // Now check that all required parameters are present
            foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
                if (!$reflectionParameter->isOptional() && !isset($methodParameters[$reflectionParameter->getName()])) {
                    throw new InvalidArgumentException(sprintf('Missing required parameter "%s" for method "%s"', $reflectionParameter->getName(), $reflectionMethod->getName()));
                }
            }
            $methodsWithMultipleArguments[$methodPropertyName] = $reflectionMethod;
        }

        return $methodsWithMultipleArguments;
    }
}
