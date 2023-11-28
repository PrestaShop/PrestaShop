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

namespace PrestaShopBundle\ApiPlatform;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;
use Traversable;

class DomainSerializer implements NormalizerInterface, DenormalizerInterface
{
    public const NORMALIZATION_MAPPING = 'normalization_mapping';

    protected SymfonySerializer $serializer;

    protected PropertyAccessor $propertyAccessor;

    /**
     * @var array<string, ReflectionClass>
     */
    protected $cachedReflectionClasses = [];

    /**
     * @param Traversable $denormalizers
     */
    public function __construct(Traversable $denormalizers)
    {
        $this->serializer = new SymfonySerializer(iterator_to_array($denormalizers));
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     *
     * @throws ReflectionException
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): mixed
    {
        $dataConstruct = [];
        $reflectionClass = $this->getReflectionClass($type);
        if ($reflectionClass->getConstructor()) {
            $constructParameters = $reflectionClass->getConstructor()->getParameters();
            foreach ($constructParameters as $constructParameter) {
                if (isset($data[$constructParameter->getName()])) {
                    $dataConstruct[$constructParameter->getName()] = $data[$constructParameter->getName()];
                    unset($data[$constructParameter->getName()]);
                }
            }
        }

        $denormalizedObject = $this->serializer->denormalize($dataConstruct, $type, $format, $context);

        //Try to call setters
        if (is_iterable($data)) {
            foreach ($data as $propertyName => $value) {
                $parameters = [];
                if ($reflectionMethod = $this->findSetterMethod($propertyName, $type)) {
                    $methodParameters = $reflectionMethod->getParameters();
                    // Using setter method with multiple parameters, use parameters by name
                    if (count($methodParameters) > 1 && is_array($value)) {
                        foreach ($methodParameters as $methodParameter) {
                            $parameters[$methodParameter->getName()] = $this->getConvertedValue($value[$methodParameter->getName()], $methodParameter);
                        }
                    } else {
                        // Single parameter use positioned parameter
                        $parameters[] = $this->getConvertedValue($value, $methodParameters[0]);
                    }

                    $reflectionMethod->invoke($denormalizedObject, ...$parameters);
                } elseif ($this->propertyAccessor->isWritable($denormalizedObject, $propertyName)) {
                    $reflectionClass = $this->getReflectionClass($type);
                    $reflectionProperty = $reflectionClass->hasProperty($propertyName) ? $reflectionClass->getProperty($propertyName) : null;
                    $this->propertyAccessor->setValue($denormalizedObject, $propertyName, $this->getConvertedValue($value, $reflectionProperty));
                }
            }
        }

        return $denormalizedObject;
    }

    private function getConvertedValue($value, ReflectionParameter|ReflectionProperty $parameter = null)
    {
        $paramType = $parameter->getType() instanceof ReflectionNamedType ? $parameter->getType()->getName() : null;
        if ($paramType && $paramType !== gettype($value)) {
            return $this->serializer->denormalize($value, $paramType);
        } else {
            return $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        $normalizedData = $this->serializer->normalize($object, $format, $context);
        if (!empty($context[self::NORMALIZATION_MAPPING])) {
            $this->mapNormalizedData($normalizedData, $context[self::NORMALIZATION_MAPPING]);
        }

        return $normalizedData;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $this->serializer->supportsNormalization($data, $format);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return $this->serializer->supportsDenormalization($data, $type, $format);
    }

    protected function findSetterMethod(string $propertyName, string $queryClass): bool|ReflectionMethod
    {
        $reflectionClass = $this->getReflectionClass($queryClass);

        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (str_starts_with($method->getName(), 'set')) {
                $methodName = substr($method->getName(), 3);
                if ($methodName === $propertyName || lcfirst($methodName) === $propertyName) {
                    return $method;
                }
            }

            if (str_starts_with($method->getName(), 'with')) {
                $methodName = substr($method->getName(), 4);
                if ($methodName === $propertyName || lcfirst($methodName) === $propertyName) {
                    return $method;
                }
            }
        }

        return false;
    }

    /**
     * Modify the normalized data based on a mapping, basically it copies some values from a path to another, the original
     * path is not modified.
     *
     * @param $normalizedData
     * @param array $normalizationMapping
     */
    protected function mapNormalizedData(&$normalizedData, array $normalizationMapping): void
    {
        foreach ($normalizationMapping as $originPath => $targetPath) {
            if ($this->propertyAccessor->isReadable($normalizedData, $originPath) && $this->propertyAccessor->isWritable($normalizedData, $targetPath)) {
                $this->propertyAccessor->setValue($normalizedData, $targetPath, $this->propertyAccessor->getValue($normalizedData, $originPath));
            }
        }
    }

    protected function getReflectionClass(string $type): ReflectionClass
    {
        if (!isset($this->cachedReflectionClasses[$type])) {
            $this->cachedReflectionClasses[$type] = new ReflectionClass($type);
        }

        return $this->cachedReflectionClasses[$type];
    }
}
