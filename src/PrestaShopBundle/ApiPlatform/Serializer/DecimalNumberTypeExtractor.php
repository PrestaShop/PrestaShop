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

namespace PrestaShopBundle\ApiPlatform\Serializer;

use Doctrine\Inflector\Inflector;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Util\Inflector as CoreInflector;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;

/**
 * This extractor is used most for CQRS commands setters on DecimalNumber properties, they
 * usually expect a string value as input and transform them into DecimalNumber. By default,
 * only string values should be accepted but a float input is also a correct type so we allow
 * these two native types (string and float) thanks to this extractor.
 */
class DecimalNumberTypeExtractor implements PropertyTypeExtractorInterface
{
    private array $reflectionClasses = [];
    private Inflector $inflector;

    public function __construct(
        protected ClassMetadataFactoryInterface $classMetadataFactory,
    ) {
        $this->inflector = CoreInflector::getInflector();
    }

    public function getTypes(string $class, string $property, array $context = []): ?array
    {
        $reflectionClass = $this->getReflectionClass($class);
        if (!$reflectionClass) {
            return null;
        }

        $camelCasePropertyName = $this->inflector->camelize($property);
        $setterMethodName = 'set' . ucfirst($camelCasePropertyName);
        $getterMethodName = 'get' . ucfirst($camelCasePropertyName);
        if ($reflectionClass->hasMethod($setterMethodName) && $reflectionClass->hasMethod($getterMethodName)) {
            $setterMethod = $reflectionClass->getMethod($setterMethodName);
            // We only check setters for single field, not multi-parameters setters
            if (!$setterMethod->isPublic() || count($setterMethod->getParameters()) !== 1) {
                return null;
            }

            // We are dealing with a property that is returned as a DecimalNumber
            if ($this->methodReturnBasedOnDecimalNumber($reflectionClass->getMethod($getterMethodName))) {
                // If setter requires a DecimalNumber then it is the only possible type
                if ($this->methodParameterBasedOnDecimalNumber($reflectionClass->getMethod($setterMethodName))) {
                    return [
                        new Type(Type::BUILTIN_TYPE_OBJECT, false, DecimalNumber::class),
                    ];
                }

                // Other types should be string or float but both are acceptable since they can be cast
                return [
                    new Type(Type::BUILTIN_TYPE_FLOAT),
                    new Type(Type::BUILTIN_TYPE_STRING),
                ];
            }
        }

        return null;
    }

    private function methodReturnBasedOnDecimalNumber(ReflectionMethod $reflectionMethod): bool
    {
        if (!$reflectionMethod->hasReturnType() || !$reflectionMethod->getReturnType() instanceof ReflectionNamedType || $reflectionMethod->getReturnType()->isBuiltin()) {
            return false;
        }

        $reflectionType = $reflectionMethod->getReturnType();
        if ($this->isTypeBasedOnDecimalNumber($reflectionType)) {
            return true;
        }

        // Check if the returned type is a ValueObject that relies on DecimalNumber (ex: PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Dimension)
        $returnReflectionClass = $this->getReflectionClass($reflectionType->getName());
        if ($returnReflectionClass) {
            foreach ($returnReflectionClass->getMethods() as $returnReflectionMethod) {
                if (!$returnReflectionMethod->hasReturnType() || !$returnReflectionMethod->getReturnType() instanceof ReflectionNamedType || $returnReflectionMethod->getReturnType()->isBuiltin()) {
                    continue;
                }

                $returnReflectionType = $returnReflectionMethod->getReturnType();
                if ($this->isTypeBasedOnDecimalNumber($returnReflectionType)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function methodParameterBasedOnDecimalNumber(ReflectionMethod $reflectionMethod): bool
    {
        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            if ($reflectionParameter->getType() instanceof ReflectionNamedType && $this->isTypeBasedOnDecimalNumber($reflectionParameter->getType())) {
                return true;
            }
        }

        return false;
    }

    private function isTypeBasedOnDecimalNumber(ReflectionNamedType $reflectionNamedType): bool
    {
        $reflectionType = $reflectionNamedType->getName();
        if ($reflectionType === DecimalNumber::class || is_subclass_of($reflectionType, DecimalNumber::class)) {
            return true;
        }

        return false;
    }

    private function getReflectionClass(string $className): ?ReflectionClass
    {
        if (array_key_exists($className, $this->reflectionClasses)) {
            return $this->reflectionClasses[$className];
        }

        if ($this->classMetadataFactory->hasMetadataFor($className)) {
            $reflectionClass = $this->classMetadataFactory->getMetadataFor($className)->getReflectionClass();
        } else {
            try {
                $reflectionClass = new ReflectionClass($className);
            } catch (ReflectionException) {
                $reflectionClass = null;
            }
        }
        $this->reflectionClasses[$className] = $reflectionClass;

        return $this->reflectionClasses[$className];
    }
}
