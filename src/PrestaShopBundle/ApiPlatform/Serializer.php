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
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;
use Traversable;

class Serializer implements NormalizerInterface, DenormalizerInterface
{
    private SymfonySerializer $serializer;

    /**
     * @param Traversable $normalizers
     */
    public function __construct(Traversable $normalizers)
    {
        $this->serializer = new SymfonySerializer(iterator_to_array($normalizers));
    }

    /**
     * {@inheritdoc}
     *
     * @throws ReflectionException
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): mixed
    {
        $action = $this->serializer->denormalize($data, $type, $format, $context);

        //Try to call setters
        foreach ($data as $param => $value) {
            $parameters = [];
            if ($reflectionMethod = $this->findSetterMethod($param, $type)) {
                $methodParameters = $reflectionMethod->getParameters();
                foreach ($methodParameters as $methodParameter) {
                    $requestValue = is_array($value) ? $value[$methodParameter->getName()] : $value;
                    if ($methodParameter->getType() instanceof \ReflectionNamedType && $methodParameter->getType()->getName() !== gettype($requestValue)) {
                        $parameters[] = $this->serializer->denormalize($requestValue, $methodParameter->getType()->getName());
                    }
                }

                $reflectionMethod->invoke($action, ...$parameters);
            }
        }

        return $action;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        return $this->serializer->normalize($object, $format, $context);
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
}
