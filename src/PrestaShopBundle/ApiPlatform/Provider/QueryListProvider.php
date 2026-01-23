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
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Exception\TypeException;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Builder\FiltersBuilderInterface;
use PrestaShop\PrestaShop\Core\Search\Filters;
use PrestaShopBundle\ApiPlatform\ContextParametersProvider;
use PrestaShopBundle\ApiPlatform\Exception\GridDataFactoryNotFoundException;
use PrestaShopBundle\ApiPlatform\NormalizationMapper;
use PrestaShopBundle\ApiPlatform\Pagination\PaginationElements;
use PrestaShopBundle\ApiPlatform\QueryResultSerializerTrait;
use PrestaShopBundle\ApiPlatform\Serializer\CQRSApiSerializer;
use Psr\Container\ContainerInterface;
use ReflectionException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

class QueryListProvider implements ProviderInterface
{
    use QueryResultSerializerTrait;

    public const DEFAULT_PAGINATED_ITEM_LIMIT = 50;

    public function __construct(
        protected readonly RequestStack $requestStack,
        protected readonly CQRSApiSerializer $domainSerializer,
        protected readonly ContainerInterface $container,
        protected readonly ShopContext $shopContext,
        protected readonly FiltersBuilderInterface $filtersBuilder,
        protected readonly CommandBusInterface $queryBus,
        protected readonly ContextParametersProvider $contextParametersProvider,
        protected readonly PropertyAccessorInterface $propertyAccessor,
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

        $gridDataFactoryDefinition = $operation->getExtraProperties()['gridDataFactory'] ?? null;
        $CQRSQueryClass = $this->getCQRSQueryClass($operation);

        if (null !== $gridDataFactoryDefinition) {
            return $this->paginationByGridDataFactory($gridDataFactoryDefinition, $operation, $context);
        } elseif (null !== $CQRSQueryClass) {
            return $this->paginationByCQRSQuery($CQRSQueryClass, $operation, $uriVariables, $context);
        }

        throw new GridDataFactoryNotFoundException(sprintf('Resource %s has no Grid data factory defined.', $operation->getClass()));
    }

    protected function paginationByGridDataFactory(string $gridDataFactoryDefinition, Operation $operation, array $context): PaginationElements
    {
        if (!$this->container->has($gridDataFactoryDefinition)) {
            // We use UnexpectedValueException as it will be caught by API Platform and interpreted as a 400 http error, similar to the behaviour
            // for CQRS queries and commands not found
            throw new UnexpectedValueException(sprintf('GridDataFactory service %s does not exist.', $gridDataFactoryDefinition));
        }

        /** @var GridDataFactoryInterface $gridDataFactory */
        $gridDataFactory = $this->container->get($gridDataFactoryDefinition);
        if (!$gridDataFactory instanceof GridDataFactoryInterface) {
            throw new UnexpectedValueException(sprintf('Provided service %s is not a GridDataFactory.', $gridDataFactoryDefinition));
        }

        $filter = $this->createFilters($context, $operation);

        $gridData = $gridDataFactory->getData($filter);
        $gridDataItems = $gridData->getRecords()->all();
        $count = $gridData->getRecordsTotal();

        $normalizedQueryResult = [];
        foreach ($gridDataItems as $key => $result) {
            $normalizedQueryResult[$key] = $this->domainSerializer->denormalize(
                $result,
                $operation->getClass(),
                null,
                [
                    NormalizationMapper::NORMALIZATION_MAPPING => $this->getApiResourceMapping($operation),
                    // Query list builders return boolean value as tiny int, so we must cast them
                    CQRSApiSerializer::CAST_BOOL => true,
                ]
            );
        }

        return $this->createPaginationElements(
            $normalizedQueryResult,
            $count,
            $filter,
            $operation,
            $context,
        );
    }

    protected function paginationByCQRSQuery(string $CQRSQueryClass, Operation $operation, array $uriVariables, array $context): PaginationElements
    {
        $filter = $this->createFilters($context, $operation);
        $paginationParameters = [
            'filterId' => $filter->getFilterId(),
            'filters' => $filter->getFilters(),
            'orderBy' => $filter->getOrderBy(),
            'sortOrder' => $filter->getOrderWay(),
            'offset' => $filter->getOffset(),
            'limit' => $filter->getLimit(),
        ];

        // These are GET parameters
        $getParameters = $context['filters'] ?? [];
        $queryParameters = array_merge(
            $paginationParameters,
            $uriVariables,
            $getParameters,
            $this->contextParametersProvider->getContextParameters(),
        );
        $CQRSQuery = $this->domainSerializer->denormalize($queryParameters, $CQRSQueryClass, null, [NormalizationMapper::NORMALIZATION_MAPPING => $this->getCQRSQueryMapping($operation)]);
        $CQRSQueryResult = $this->queryBus->handle($CQRSQuery);

        // Query result must be a structure that contains paginated elements (so a list of items and a count)
        $normalizedQueryResult = $this->domainSerializer->normalize($CQRSQueryResult, null, [NormalizationMapper::NORMALIZATION_MAPPING => $this->getCQRSQueryMapping($operation)]);

        $itemsField = $operation->getExtraProperties()['itemsField'] ?? 'items';
        $countField = $operation->getExtraProperties()['countField'] ?? 'count';
        $queryItems = $normalizedQueryResult[$itemsField];
        $queryCount = $normalizedQueryResult[$countField];

        $denormalizedItems = [];
        foreach ($queryItems as $key => $result) {
            $denormalizedItems[$key] = $this->domainSerializer->denormalize(
                array_merge($result, $queryParameters),
                $operation->getClass(),
                null,
                [
                    NormalizationMapper::NORMALIZATION_MAPPING => $this->getApiResourceMapping($operation),
                ]
            );
        }

        return $this->createPaginationElements(
            $denormalizedItems,
            $queryCount,
            $filter,
            $operation,
            $context,
        );
    }

    protected function createPaginationElements(array $items, int $count, Filters $filter, Operation $operation, array $context): PaginationElements
    {
        // Apply mapping the other way around, because filters may have default orderBy values named for Grid
        // that need to be mapped to match the API naming
        $filtersMapping = $operation->getExtraProperties()['filtersMapping'] ?? [];
        $orderBy = $this->mapOrderByFieldForAPI($filter->getOrderBy(), $filtersMapping);

        return new PaginationElements(
            $count,
            $orderBy,
            $filter->getOrderWay(),
            $filter->getLimit(),
            $filter->getOffset(),
            // Keep the same filters provided in query parameters
            $context['filters']['filters'] ?? [],
            $items
        );
    }

    protected function createFilters(array $context, Operation $operation): Filters
    {
        $filtersClass = $operation->getExtraProperties()['filtersClass'] ?? Filters::class;
        $filtersMapping = $operation->getExtraProperties()['filtersMapping'] ?? [];

        $queryParameters = $context['filters'] ?? [];
        $paginationFilters = $queryParameters['filters'] ?? [];
        $paginationFilters = $this->domainSerializer->normalize(
            $paginationFilters,
            null,
            [NormalizationMapper::NORMALIZATION_MAPPING => $filtersMapping]
        );

        $orderBy = $this->mapOrderByFieldForGrid($queryParameters['orderBy'] ?? null, $filtersMapping);
        $paginationParameters = [
            'filters' => $paginationFilters,
            'orderBy' => $orderBy,
            'sortOrder' => $queryParameters['sortOrder'] ?? 'asc',
            'offset' => array_key_exists('offset', $queryParameters) ? (int) $queryParameters['offset'] : null,
            'limit' => array_key_exists('limit', $queryParameters) ? (int) $queryParameters['limit'] : null,
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
        if ($filters->getLimit() === null) {
            $paginationParameters['limit'] = self::DEFAULT_PAGINATED_ITEM_LIMIT;
        }
        $filters->add($paginationParameters);

        return $filters;
    }

    protected function mapOrderByFieldForGrid(?string $apiOrderBy, array $filtersMapping): ?string
    {
        if (empty($apiOrderBy) || empty($filtersMapping)) {
            return $apiOrderBy;
        }

        // The orderBy parameter also needs to be mapped (the APIResource field should be used but may not match the SQL query one)
        foreach ($filtersMapping as $apiKey => $gridKey) {
            if ('[' . $apiOrderBy . ']' === $apiKey) {
                $apiOrderBy = str_replace(['[', ']'], '', $gridKey);
            }
        }

        return $apiOrderBy;
    }

    protected function mapOrderByFieldForAPI(?string $gridOrderBy, array $filtersMapping): ?string
    {
        if (empty($gridOrderBy)) {
            return $gridOrderBy;
        }

        // The orderBy parameter also needs to be mapped (the APIResource field should be used but may not match the SQL query one)
        foreach ($filtersMapping as $apiKey => $gridKey) {
            if ('[' . $gridOrderBy . ']' === $gridKey) {
                $gridOrderBy = str_replace(['[', ']'], '', $apiKey);
            }
        }

        return $gridOrderBy;
    }
}
