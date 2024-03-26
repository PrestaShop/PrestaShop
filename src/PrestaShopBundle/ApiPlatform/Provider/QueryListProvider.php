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
use PrestaShop\PrestaShop\Core\Context\ApiClientContext;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Exception\TypeException;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineQueryBuilderInterface;
use PrestaShop\PrestaShop\Core\Search\Builder\FiltersBuilderInterface;
use PrestaShop\PrestaShop\Core\Search\Filters;
use PrestaShopBundle\ApiPlatform\ContextParametersTrait;
use PrestaShopBundle\ApiPlatform\DomainSerializer;
use PrestaShopBundle\ApiPlatform\Exception\QueryBuilderNotFoundException;
use PrestaShopBundle\ApiPlatform\Pagination\PaginationElements;
use PrestaShopBundle\ApiPlatform\QueryResultSerializerTrait;
use Psr\Container\ContainerInterface;
use ReflectionException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class QueryListProvider implements ProviderInterface
{
    use QueryResultSerializerTrait;
    use ContextParametersTrait;

    public const DEFAULT_PAGINATED_ITEM_LIMIT = 50;

    public function __construct(
        protected readonly RequestStack $requestStack,
        protected readonly DomainSerializer $domainSerializer,
        protected readonly ContainerInterface $container,
        protected readonly ShopContext $shopContext,
        protected readonly LanguageContext $languageContext,
        protected readonly ApiClientContext $apiClientContext,
        protected readonly FiltersBuilderInterface $filtersBuilder
    ) {
    }

    /**
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     *
     * @return object|array|null
     *
     * @throws ExceptionInterface
     * @throws ReflectionException
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!$operation instanceof CollectionOperationInterface) {
            throw new TypeException(
                sprintf(
                    'Expected operation to be %s, %s given ',
                    CollectionOperationInterface::class,
                    $operation->getClass()
                )
            );
        }

        $queryBuilderDefinition = $operation->getExtraProperties()['queryBuilder'] ?? null;

        if (null === $queryBuilderDefinition) {
            throw new QueryBuilderNotFoundException(sprintf('Resource %s has no Query builder defined.', $operation->getClass()));
        }

        /** @var DoctrineQueryBuilderInterface $queryBuilder * */
        $queryBuilder = $this->container->get($queryBuilderDefinition);

        $filter = $this->createFilters($context, $operation);

        $count = (int) $queryBuilder->getCountQueryBuilder($filter)->executeQuery()->fetchOne();

        $queryResult = $queryBuilder->getSearchQueryBuilder($filter)->fetchAllAssociative();

        $normalizedQueryResult = [];

        foreach ($queryResult as $key => $result) {
            $normalizedQueryResult[$key] = $this->domainSerializer->denormalize(
                $result,
                $operation->getClass(),
                null,
                [DomainSerializer::NORMALIZATION_MAPPING => $this->getApiResourceMapping($operation)]
            );
        }

        return new PaginationElements(
            $count,
            $filter->getOrderBy(),
            $filter->getOrderWay(),
            $filter->getLimit(),
            $filter->getOffset(),
            $filter->getFilters(),
            $normalizedQueryResult
        );
    }

    private function createFilters(array $context, Operation $operation): Filters
    {
        $filtersClass = $operation->getExtraProperties()['filtersClass'] ?? Filters::class;
        $filtersMapping = $operation->getExtraProperties()['filtersMapping'] ?? [];

        $queryParameters = $context['filters'] ?? [];
        $paginationFilters = $queryParameters['filters'] ?? [];
        $paginationFilters = $this->domainSerializer->normalize(
            $paginationFilters,
            null,
            [DomainSerializer::NORMALIZATION_MAPPING => $filtersMapping]
        );

        $paginationParameters = [
            'filters' => $paginationFilters,
            'orderBy' => array_key_exists('orderBy', $queryParameters) ? $queryParameters['orderBy'] : null,
            'sortOrder' => array_key_exists('sortOrder', $queryParameters) ? $queryParameters['sortOrder'] : 'asc',
            'offset' => array_key_exists('offset', $queryParameters) ? (int) $queryParameters['offset'] : null,
            'limit' => array_key_exists('limit', $queryParameters)
                ? (int) $queryParameters['limit']
                : self::DEFAULT_PAGINATED_ITEM_LIMIT,
        ];

        /* remove null parameters from the request so the default filter parameters can be used instead of null values. */
        foreach ($paginationParameters as $key => $parameter) {
            if ($parameter === null) {
                unset($paginationParameters[$key]);
            }
        }

        $request = $this->requestStack->getMainRequest();
        // We force filter ID as empty string to avoid using a prefix in the query parameters (eg: we want limit=10 not product[limit]=10)
        $this->filtersBuilder->setConfig([
            'filter_id' => '',
            'filters_class' => $filtersClass,
            'request' => $request,
            'shop_constraint' => $this->shopContext->getShopConstraint(),
        ]);

        $filters = $this->filtersBuilder->buildFilters();
        $filters->add($paginationParameters);

        return $filters;
    }
}
