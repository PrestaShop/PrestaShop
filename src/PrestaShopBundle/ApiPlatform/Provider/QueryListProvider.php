<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Provider;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Exception\TypeException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Api\ExtraPropertyApiListRecordCollector;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Builder\FiltersBuilderInterface;
use PrestaShop\PrestaShop\Core\Search\Filters;
use PrestaShopBundle\ApiPlatform\ContextParametersProvider;
use PrestaShopBundle\ApiPlatform\DefaultValuesTrait;
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
    use DefaultValuesTrait;
    use QueryResultSerializerTrait;

    public const DEFAULT_PAGINATED_ITEM_LIMIT = 50;

    /**
     * QueryListProvider paginates lists itself, so API Platform's pagination_maximum_items_per_page
     * (unset in this project) never applies. 100 is the maximum API Platform documents as an example
     * and stays next to the default page of 50: a client can still ask for a full back-office page,
     * and a value such as 100000000 is rejected before it reaches the database.
     */
    public const MAX_PAGINATED_ITEM_LIMIT = 100;

    public function __construct(
        protected readonly RequestStack $requestStack,
        protected readonly CQRSApiSerializer $domainSerializer,
        protected readonly ContainerInterface $container,
        protected readonly ShopContext $shopContext,
        protected readonly FiltersBuilderInterface $filtersBuilder,
        protected readonly CommandBusInterface $queryBus,
        protected readonly ContextParametersProvider $contextParametersProvider,
        protected readonly PropertyAccessorInterface $propertyAccessor,
        protected readonly ?ExtraPropertyApiListRecordCollector $extraPropertyListCollector = null,
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

        // The grid query already fetched and cast any grid-associated extra property; hand those records to the
        // collector so the response subscriber can inline them per item instead of re-reading the database.
        if ($operation instanceof HttpOperation) {
            $this->extraPropertyListCollector?->capture(
                $gridDataItems,
                (string) $operation->getUriTemplate(),
                (string) $operation->getMethod(),
            );
        }

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
            // getOffset()/getLimit() turn 0 into null, which would drop a checked offset of 0.
            'offset' => $filter->has('offset') ? $filter->get('offset') : null,
            'limit' => $filter->has('limit') ? $filter->get('limit') : null,
        ];

        // These are GET parameters
        $getParameters = $context['filters'] ?? [];
        $queryParameters = array_merge(
            $paginationParameters,
            $uriVariables,
            $getParameters,
            $this->contextParametersProvider->getContextParameters(),
        );
        // Raw query strings are merged above and would overwrite the integers readOffset()/readLimit() checked.
        if ($filter->has('offset')) {
            $queryParameters['offset'] = $filter->get('offset');
        }
        if ($filter->has('limit')) {
            $queryParameters['limit'] = $filter->get('limit');
        }
        $queryParameters = $this->applyDefaultValues($queryParameters, $operation);
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
                    CQRSApiSerializer::CAST_BOOL => true,
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
            // Filters return a null offset for the first page
            $filter->getOffset() ?? 0,
            // Keep the same filters provided in query parameters
            $context['filters']['filters'] ?? [],
            // The items of a page are always a JSON list, even when the query result indexes them (e.g. by id)
            array_values($items)
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
            'offset' => $this->readOffset($queryParameters),
            'limit' => $this->readLimit($queryParameters),
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
        // Default only when the client sent no limit. A limit already checked above must win even when
        // the filters built from the request report no limit (ParameterBag::getInt rejects values such as "010").
        if (!array_key_exists('limit', $paginationParameters) && $filters->getLimit() === null) {
            $paginationParameters['limit'] = self::DEFAULT_PAGINATED_ITEM_LIMIT;
        }
        $filters->add($paginationParameters);

        return $filters;
    }

    /**
     * A missing offset keeps the filters default. A provided offset must be an integer greater than or equal to zero:
     * Doctrine rejects a negative OFFSET and API Platform would otherwise turn that database error into an HTTP 500.
     *
     * @param array<string, mixed> $queryParameters
     */
    private function readOffset(array $queryParameters): ?int
    {
        if (!array_key_exists('offset', $queryParameters)) {
            return null;
        }

        $raw = $queryParameters['offset'];
        $offset = $this->filterInteger($raw);
        if (false === $offset || $offset < 0) {
            throw new UnexpectedValueException(sprintf('Offset must be an integer greater than or equal to zero, %s given.', $this->stringifyRawQueryValue($raw)));
        }

        return $offset;
    }

    /**
     * A missing limit keeps the filters default (or the provider default of 50). Limit 0 is not a valid page size:
     * Filters::getLimit() treats 0 as "unset", which then reaches PaginationElements as null and raises a TypeError.
     *
     * @param array<string, mixed> $queryParameters
     */
    private function readLimit(array $queryParameters): ?int
    {
        if (!array_key_exists('limit', $queryParameters)) {
            return null;
        }

        $raw = $queryParameters['limit'];
        $limit = $this->filterInteger($raw);
        if (false === $limit || $limit < 1 || $limit > self::MAX_PAGINATED_ITEM_LIMIT) {
            throw new UnexpectedValueException(sprintf('Limit must be an integer between 1 and %d, %s given.', self::MAX_PAGINATED_ITEM_LIMIT, $this->stringifyRawQueryValue($raw)));
        }

        return $limit;
    }

    /**
     * Rejects anything (int) would silently truncate: arrays, empty strings, floats, leading zeros, "1e1".
     */
    private function filterInteger(mixed $raw): int|false
    {
        if (is_int($raw)) {
            return $raw;
        }

        if (!is_string($raw)) {
            return false;
        }

        $filtered = filter_var($raw, FILTER_VALIDATE_INT);
        if (false === $filtered) {
            return false;
        }

        return (int) $filtered;
    }

    private function stringifyRawQueryValue(mixed $raw): string
    {
        if (is_string($raw)) {
            return '' === $raw ? "''" : $raw;
        }

        if (is_int($raw)) {
            return (string) $raw;
        }

        return get_debug_type($raw);
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
