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

use ReflectionException;
use ReflectionMethod;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;
use Traversable;

class DomainSerializer implements NormalizerInterface, DenormalizerInterface
{
    public const NORMALIZATION_MAPPING = 'normalization_mapping';

    private SymfonySerializer $serializer;

    private PropertyAccessor $propertyAccessor;

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
        $reflectionClass = new \ReflectionClass($type);
        if ($reflectionClass->getConstructor()) {
            $constructParameters = $reflectionClass->getConstructor()->getParameters();
            foreach ($constructParameters as $constructParameter) {
                if (isset($data[$constructParameter->getName()])) {
                    $dataConstruct[$constructParameter->getName()] = $data[$constructParameter->getName()];
                    unset($data[$constructParameter->getName()]);
                }
            }
        }

        $action = $this->serializer->denormalize($dataConstruct, $type, $format, $context);

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        //Try to call setters
        if (is_iterable($data)) {
            foreach ($data as $param => $value) {
                $parameters = [];
                if ($reflectionMethod = $this->findSetterMethod($param, $type)) {
                    $methodParameters = $reflectionMethod->getParameters();
                    foreach ($methodParameters as $methodParameter) {
                        $paramType = $methodParameter->getType() instanceof \ReflectionNamedType ? $methodParameter->getType()->getName() : null;
                        $parameters[] = $this->getConvertedValue($value, $methodParameter->getName(), $paramType);
                    }

                    $reflectionMethod->invoke($action, ...$parameters);
                } elseif ($propertyAccessor->isWritable($action, $param)) {
                    $propertyAccessor->setValue($action, $param, $this->getConvertedValue($value, $param, null));
                }
            }
        }

        return $action;
    }

    private function getConvertedValue($value, string $paramName, ?string $paramType)
    {
        $convertedValue = is_array($value) && isset($value[$paramName]) ? $value[$paramName] : $value;
        // If converted value is an array with only value it is likely a serialized ValueObject
        $convertedValue = is_array($convertedValue) && isset($convertedValue['value']) ? $convertedValue['value'] : $convertedValue;
        if ($paramType && $paramType !== gettype($convertedValue)) {
            return $this->serializer->denormalize($convertedValue, $paramType);
        } else {
            return $convertedValue;
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

    /**
     * @param $propertyName
     * @param $queryClass
     *
     * @return false|ReflectionMethod
     */
    private function findSetterMethod($propertyName, $queryClass): bool|ReflectionMethod
    {
        $reflectionClass = new \ReflectionClass($queryClass);

        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (str_starts_with($method->getName(), 'set')) {
                $methodName = lcfirst(substr($method->getName(), 3));
                if ($methodName === $propertyName) {
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
    private function mapNormalizedData(&$normalizedData, array $normalizationMapping): void
    {
        foreach ($normalizationMapping as $originPath => $targetPath) {
            if ($this->propertyAccessor->isReadable($normalizedData, $originPath) && $this->propertyAccessor->isWritable($normalizedData, $targetPath)) {
                $this->propertyAccessor->setValue($normalizedData, $targetPath, $this->propertyAccessor->getValue($normalizedData, $originPath));
            }
        }
    }
}
