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
use PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetHook;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineQueryBuilderInterface;
use PrestaShop\PrestaShop\Core\Search\Filters;
use PrestaShopBundle\ApiPlatform\DomainSerializer;
use PrestaShopBundle\ApiPlatform\Exception\NoExtraPropertiesFoundException;
use PrestaShopBundle\ApiPlatform\Resources\Hook;
use ReflectionException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class QueryListProvider implements ProviderInterface
{
    /**
     * @param DomainSerializer $apiPlatformSerializer
     * @param ContainerInterface $container
     */
    public function __construct(
        private readonly DomainSerializer $apiPlatformSerializer,
        private readonly ContainerInterface $container,
    ) {
    }

    /**
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     *
     * @return mixed
     *
     * @throws ExceptionInterface
     * @throws NoExtraPropertiesFoundException
     * @throws ReflectionException
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $queryBuilderDefinition = $operation->getExtraProperties()['query_builder'] ?? null;
        /** @var DoctrineQueryBuilderInterface $queryBuilder **/
        $queryBuilder = $this->container->get($queryBuilderDefinition);

        $queryResult = $queryBuilder->getSearchQueryBuilder(new Filters([
            'limit' => null,
            'sortOrder' => 'asc',
            'filters' => [],
        ]))->fetchAllAssociative();

        $count = $queryBuilder->getCountQueryBuilder(new Filters([
            'limit' => null,
            'sortOrder' => 'asc',
            'filters' => [],
        ]));

        //Handle return type
        $normalizedQueryResult = $this->apiPlatformSerializer->normalize($queryResult);

        if ($operation instanceof CollectionOperationInterface) {
            foreach ($normalizedQueryResult as $key => $result) {
                $normalizedQueryResult[$key] = $this->apiPlatformSerializer->denormalize($result, $operation->getClass());
            }

            return $normalizedQueryResult;
        }

        return $this->apiPlatformSerializer->denormalize($normalizedQueryResult, $operation->getClass());
    }
}
