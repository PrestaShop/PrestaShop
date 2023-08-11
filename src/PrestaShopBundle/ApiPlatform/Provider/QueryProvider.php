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

namespace PrestaShopBundle\ApiPlatform\Provider;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShopBundle\ApiPlatform\Converters\ConverterInterface;
use PrestaShopBundle\ApiPlatform\Exception\NoExtraPropertiesFoundException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Serializer;

class QueryProvider implements ProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $queryBus,
        private readonly iterable $converters,
        private readonly Serializer $apiPlatformSerializer
    ) {
    }

    /**
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     *
     * @return \ApiPlatform\State\Pagination\PartialPaginatorInterface|array|\ArrayObject|bool|float|int|mixed|string|null
     *
     * @throws ExceptionInterface
     * @throws NoExtraPropertiesFoundException
     * @throws \ReflectionException
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = [])
    {
        $queryClass = $operation->getExtraProperties()['query'] ?? null;
        $filters = $context['filters'] ?? [];
        $queryParameters = array_merge($uriVariables, $filters);

        if (null === $queryClass) {
            throw new NoExtraPropertiesFoundException('Extra property "query" is not found');
        }

        $query = $this->apiPlatformSerializer->denormalize($queryParameters, $queryClass);

        //Try to call setter on additional query params
        if (count($queryParameters)) {
            foreach ($queryParameters as $param => $value) {
                if ($reflectionMethod = $this->findSetterMethod($param, $queryClass)) {
                    $methodParameter = $reflectionMethod->getParameters()[0];
                    if ($methodParameter->getType() instanceof \ReflectionNamedType && $methodParameter->getType()->getName() !== gettype($value)) {
                        $value = $this->findConverter($methodParameter->getType()->getName())->convert($value);
                    }
                    $reflectionMethod->invoke($query, $value);
                }
            }
        }

        $queryResult = $this->queryBus->handle($query);
        $normalizedQueryResult = $this->apiPlatformSerializer->normalize($queryResult);

        if ($operation instanceof CollectionOperationInterface) {
            foreach ($normalizedQueryResult as $key => $result) {
                $normalizedQueryResult[$key] = $this->apiPlatformSerializer->denormalize($result, $operation->getClass());
            }

            return $normalizedQueryResult;
        }

        return $this->apiPlatformSerializer->denormalize($normalizedQueryResult, $operation->getClass());
    }

    /**
     * @param $type
     *
     * @return ConverterInterface
     *
     * @throws \Exception
     */
    private function findConverter($type): ConverterInterface
    {
        foreach ($this->converters as $converter) {
            if ($converter->supports($type)) {
                return $converter;
            }
        }

        throw new \Exception(sprintf('Converter for type %s not found', $type));
    }

    /**
     * @param $propertyName
     * @param $queryClass
     *
     * @return false|\ReflectionMethod
     */
    private function findSetterMethod($propertyName, $queryClass): bool|\ReflectionMethod
    {
        $reflectionClass = new \ReflectionClass($queryClass);

        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
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
