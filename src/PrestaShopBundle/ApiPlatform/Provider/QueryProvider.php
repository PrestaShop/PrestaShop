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

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccess;
use PrestaShopBundle\ApiPlatform\Exception\NoExtraPropertiesFoundException;
use Symfony\Component\Serializer\Serializer;

class QueryProvider implements ProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $queryBus,
        private readonly RequestStack $requestStack,
        private readonly Serializer $apiPlatformSerializer
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = [])
    {
        $query = $operation->getExtraProperties()['query'] ?? null;
        $converters = $operation->getExtraProperties()['paramConverters'] ?? [];
        $queryParams = $this->requestStack->getCurrentRequest()->query->all();

        if (null === $query) {
            throw new NoExtraPropertiesFoundException();
        }

        //Convert uri params
        foreach ($uriVariables as $variable => $value) {
            if (array_key_exists($variable, $converters)) {
                $converter = new $converters[$variable]();
                $uriVariables[$variable] = $converter->convert($value);
            }
        }

        //Convert query params
        foreach ($queryParams as $variable => $value) {
            if (array_key_exists($variable, $converters)) {
                $converter = new $converters[$variable]();
                $queryParams[$variable] = $converter->convert($value);
            }
        }

        //Reset param array with orderer array
        $params = array_values($uriVariables);

        //Add optionnal parameters in construct from query params
        $reflectionMethod = new \ReflectionMethod($query, '__construct');
        $constructParameters = $reflectionMethod->getParameters();
        foreach ($constructParameters as $parameter) {
            if (array_key_exists($parameter->name, $queryParams)) {
                $params[$parameter->getPosition()] = $queryParams[$parameter->name];
                unset($queryParams[$parameter->name]);
            }
        }

        $query = new $query(...$params);

        //Try to call setter on additional query params
        if (count($queryParams)) {
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            foreach ($queryParams as $param => $value) {
                $propertyAccessor->setValue($query, $param, $value);
            }
        }

        $queryResult = $this->queryBus->handle($query);
        $normalizedQueryResult = $this->apiPlatformSerializer->normalize($queryResult);

        return $this->apiPlatformSerializer->denormalize($normalizedQueryResult, $operation->getClass());
    }
}
