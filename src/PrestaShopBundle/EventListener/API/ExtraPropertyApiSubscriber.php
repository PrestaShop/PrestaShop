<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\EventListener\API;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use ObjectModelCore;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\ExtraProperty\Api\ExtraPropertyApiListRecordCollector;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionCollection;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyReaderInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyWriterInterface;
use PrestaShop\PrestaShop\Core\Util\Inflector;
use PrestaShopBundle\ApiPlatform\Exception\LocaleNotFoundException;
use PrestaShopBundle\ApiPlatform\LocalizedValueUpdater;
use PrestaShopBundle\ApiPlatform\Metadata\LocalizedValue;
use PrestaShopBundle\ApiPlatform\Provider\QueryListProvider;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;

/**
 * Bridges Admin API responses with the extra property system, and is the single API-Platform-coupled entry point of
 * the feature: it resolves the entity id and converts LANG values between locale and id_lang (LocalizedValueUpdater),
 * so the pure-Core services it drives — the reader (single item), the grid-record collector (list) and the writer
 * — never depend on API Platform. Validation lives in the dedicated CQRSApiValidator decorator.
 *
 * On kernel.response (after API Platform produced the JSON body) it:
 *  - persists the submitted extraProperties payload of a write, once the entity id is in the response body,
 *  - enriches the response: a single item gets the nested `extraProperties` object (all locales), each item of a
 *    paginated list gets the grid-fetched values inline at its root (single context locale).
 *
 * Registered only in the Admin API kernel.
 */
class ExtraPropertyApiSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected readonly ExtraPropertyDefinitionRepositoryInterface $repository,
        protected readonly ExtraPropertyReaderInterface $reader,
        protected readonly ExtraPropertyApiListRecordCollector $listRecordCollector,
        protected readonly ExtraPropertyWriterInterface $writer,
        protected readonly ShopContext $shopContext,
        protected readonly LanguageContext $languageContext,
        protected readonly LocalizedValueUpdater $localizedValueUpdater,
        protected readonly ?PropertyNameCollectionFactoryInterface $propertyNameCollectionFactory = null,
        protected readonly ?PropertyMetadataFactoryInterface $propertyMetadataFactory = null,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // We run on kernel.response, after API Platform has serialized the result and built the Response on
            // kernel.view (SerializeListener/RespondListener). EventPriorities::* constants are kernel.view
            // priorities, so they don't apply here; -10 simply keeps us after any other kernel.response listener.
            KernelEvents::RESPONSE => [['onKernelResponse', -10]],
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $operation = $request->attributes->get('_api_operation');
        if (!$operation instanceof HttpOperation) {
            return;
        }

        $response = $event->getResponse();
        if (!$this->isJsonEntityResponse($response)) {
            return;
        }

        $content = $response->getContent();
        if (false === $content || '' === $content) {
            return;
        }

        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            return;
        }

        $uriTemplate = (string) $operation->getUriTemplate();
        $method = (string) $operation->getMethod();

        // Single match: the definitions targeting this operation. When none match there is nothing to do.
        $definitions = $this->repository->getAllDefinitions()->filterByApi($uriTemplate, $method);
        if ($definitions->isEmpty()) {
            return;
        }

        $resourceClass = (string) $operation->getClass();
        $entityName = $definitions->first()->getEntityName();
        $langScopedFields = $this->langScopedFields($definitions);

        if ($this->isCollection($operation, $decoded)) {
            if (isset($decoded['items']) && is_array($decoded['items'])) {
                $decoded['items'] = $this->enrichListItems($decoded['items'], $operation, $definitions, $entityName, $resourceClass);
            }
        } else {
            $entityId = $this->resolveId($decoded, $entityName, $resourceClass);
            if ($entityId > 0) {
                // Persist the submitted payload first (write operations), so the read-back below reflects it. A 2xx
                // response means the merging validator already accepted the payload.
                if ($this->isWriteMethod($method)) {
                    $payload = $this->extractRequestPayload($request, $definitions);
                    if ([] !== $payload) {
                        // The payload is already in writeAll() shape (LANG values keyed by id_lang). Shop scoping
                        // comes from the ShopContext, built by the Admin API kernel's shop-context listener — a
                        // multi-shop value is a single value identified by that context, like the form integration.
                        $this->writer->writeAll(
                            $entityName,
                            $definitions->first()->getPrimaryKeyName(),
                            $entityId,
                            $this->localesToIds($payload, $langScopedFields),
                            $this->shopContext->getShopConstraint(),
                        );
                    }
                }

                $values = $this->idsToLocales(
                    $this->reader->getExtraProperties(
                        $entityName,
                        $definitions->first()->getPrimaryKeyName(),
                        $entityId,
                        null,
                        $this->shopContext->getShopConstraint(),
                        $this->isLangMultishop($entityName),
                        $definitions,
                    ),
                    $langScopedFields
                );
                if ([] !== $values) {
                    $decoded['extraProperties'] = array_merge(
                        is_array($decoded['extraProperties'] ?? null) ? $decoded['extraProperties'] : [],
                        $values
                    );
                }
            }
        }

        $response->setContent((string) json_encode($decoded));
        // Content length is recomputed when the response is sent; drop any stale value.
        $response->headers->remove('Content-Length');
    }

    /**
     * Enriches each item of a collection response with its extra properties, inline at the item root under the field
     * name (single context-locale value), combining two sources:
     *  - grid-associated properties on a grid-backed list (QueryListProvider + grid data factory) are reused from the
     *    collector — the grid query already fetched them, so no extra read;
     *  - the rest are read from the database in a single batched query: on a grid-backed list that is the
     *    API-associated-but-not-grid properties (exposed on the API yet absent from the grid); on a CQRS-paginated
     *    list (e.g. /products/{id}/combinations) the grid never runs, so it is every property.
     *
     * @param array<int, mixed> $items
     *
     * @return array<int, mixed>
     */
    protected function enrichListItems(array $items, HttpOperation $operation, ExtraPropertyDefinitionCollection $definitions, string $entityName, string $resourceClass): array
    {
        $gridBacked = $this->isGridBackedCollection($operation);

        // On a grid-backed list the grid already provided the grid-associated properties (read back from the
        // collector); only the API-associated properties it did not fetch still need a database read. On any other
        // list the grid never runs, so every property is read.
        $readerDefinitions = $gridBacked ? $this->apiOnlyDefinitions($definitions) : $definitions;

        // Resolve every item's entity id once.
        $entityIdByIndex = [];
        foreach ($items as $index => $item) {
            if (!is_array($item)) {
                continue;
            }
            $entityId = $this->resolveId($item, $entityName, $resourceClass);
            if ($entityId > 0) {
                $entityIdByIndex[$index] = $entityId;
            }
        }
        if ([] === $entityIdByIndex) {
            return $items;
        }

        // Batch-read the properties the grid did not provide, in one query, for the current display language.
        $valuesByEntity = [];
        if (!$readerDefinitions->isEmpty()) {
            $valuesByEntity = $this->reader->getMultipleExtraProperties(
                $entityName,
                $readerDefinitions->first()->getPrimaryKeyName(),
                array_values($entityIdByIndex),
                $this->languageContext->getId(),
                $this->shopContext->getShopConstraint(),
                $this->isLangMultishop($entityName),
                $readerDefinitions,
            );
        }

        foreach ($entityIdByIndex as $index => $entityId) {
            $item = $items[$index];

            if ($gridBacked) {
                // Grid-associated properties: reuse exactly the (single context-locale) values the grid fetched.
                $captured = $this->listRecordCollector->find($entityName, $entityId);
                if (null !== $captured) {
                    $item = array_merge($item, $captured);
                }
            }

            // Reader-fetched properties, flattened to the same inline shape: field name => value.
            $entityValues = $valuesByEntity[$entityId] ?? null;
            if (null !== $entityValues) {
                foreach ($readerDefinitions as $definition) {
                    $moduleValues = $entityValues[$definition->getNormalizedModuleKey()] ?? null;
                    if (null !== $moduleValues && array_key_exists($definition->getPropertyName(), $moduleValues)) {
                        $item[$definition->getFieldName()] = $moduleValues[$definition->getPropertyName()];
                    }
                }
            }

            $items[$index] = $item;
        }

        return $items;
    }

    /**
     * The subset of $definitions NOT associated with any grid — exposed on the API (and possibly a form) but never
     * fetched by a grid query, so they are absent from the grid-record collector and must be read from the database.
     */
    protected function apiOnlyDefinitions(ExtraPropertyDefinitionCollection $definitions): ExtraPropertyDefinitionCollection
    {
        $apiOnly = [];
        foreach ($definitions as $definition) {
            if (empty($definition->getAssociatedGrids())) {
                $apiOnly[] = $definition;
            }
        }

        return new ExtraPropertyDefinitionCollection($apiOnly);
    }

    /**
     * A list is grid-backed when it is served by QueryListProvider through a grid data factory — the only case
     * where ExtraPropertyApiListRecordCollector has captured the values. Everything else (CQRS-paginated lists)
     * must be read from the database.
     */
    protected function isGridBackedCollection(HttpOperation $operation): bool
    {
        return QueryListProvider::class === $operation->getProvider()
            && null !== ($operation->getExtraProperties()['gridDataFactory'] ?? null);
    }

    /**
     * Whether the entity stores LANG values per shop (its ObjectModel definition is multilang_shop). The class name
     * is the StudlyCase of the (tableized) entity name; isClassLangMultishop safely returns false for any unknown
     * class, so a wrong guess never breaks the read.
     */
    protected function isLangMultishop(string $entityName): bool
    {
        return ObjectModelCore::isClassLangMultishop(Inflector::getInflector()->classify($entityName));
    }

    protected function isJsonEntityResponse(Response $response): bool
    {
        if (Response::HTTP_OK !== $response->getStatusCode() && Response::HTTP_CREATED !== $response->getStatusCode()) {
            return false;
        }

        return str_contains((string) $response->headers->get('Content-Type', ''), 'json');
    }

    /**
     * @param array<string, mixed> $decoded
     */
    protected function isCollection(HttpOperation $operation, array $decoded): bool
    {
        return $operation instanceof CollectionOperationInterface || array_key_exists('items', $decoded);
    }

    protected function isWriteMethod(string $method): bool
    {
        return in_array(strtoupper($method), ['POST', 'PUT', 'PATCH'], true);
    }

    /**
     * Resolves the integer entity identifier from a normalized API item. Resolution order: the
     * #[ApiProperty(identifier: true)] property (via metadata) → the generic 'id' field → the camelCase
     * "{entity}Id" pattern (e.g. product → productId). Returns 0 when none is found.
     *
     * @param array<string, mixed> $normalizedData
     */
    protected function resolveId(array $normalizedData, string $entityName, string $resourceClass): int
    {
        if (null !== $this->propertyNameCollectionFactory && null !== $this->propertyMetadataFactory) {
            try {
                foreach ($this->propertyNameCollectionFactory->create($resourceClass) as $propertyName) {
                    $metadata = $this->propertyMetadataFactory->create($resourceClass, $propertyName);
                    if ($metadata->isIdentifier() && isset($normalizedData[$propertyName]) && is_int($normalizedData[$propertyName])) {
                        return $normalizedData[$propertyName];
                    }
                }
            } catch (Throwable) {
                // Fall through to heuristic resolution below.
            }
        }

        if (isset($normalizedData['id']) && is_int($normalizedData['id'])) {
            return $normalizedData['id'];
        }

        $idPropertyName = lcfirst(Container::camelize($entityName)) . 'Id';
        if (isset($normalizedData[$idPropertyName]) && is_int($normalizedData[$idPropertyName])) {
            return $normalizedData[$idPropertyName];
        }

        return 0;
    }

    /**
     * Reads the submitted extraProperties sub-object from the initial request body and keeps only the properties
     * actually associated with this operation — those in $definitions, already narrowed by filterByApi. Without this
     * filtering, a write to any endpoint-associated property would let the payload smuggle in unrelated extra
     * properties (no definition of theirs matched the operation). Request::getContent() is cached, so this does not
     * consume the input stream a second time.
     *
     * @return array<string, array<string, mixed>>
     */
    protected function extractRequestPayload(Request $request, ExtraPropertyDefinitionCollection $definitions): array
    {
        $content = $request->getContent();
        if ('' === $content) {
            return [];
        }

        $decoded = json_decode($content, true);
        if (!is_array($decoded) || !isset($decoded['extraProperties']) || !is_array($decoded['extraProperties'])) {
            return [];
        }

        $submitted = $decoded['extraProperties'];
        $filtered = [];
        foreach ($definitions as $definition) {
            $moduleKey = $definition->getNormalizedModuleKey();
            $propertyName = $definition->getPropertyName();
            // array_key_exists (not isset): an explicit null is a valid submitted value and must be kept.
            if (isset($submitted[$moduleKey]) && is_array($submitted[$moduleKey]) && array_key_exists($propertyName, $submitted[$moduleKey])) {
                $filtered[$moduleKey][$propertyName] = $submitted[$moduleKey][$propertyName];
            }
        }

        return $filtered;
    }

    /**
     * @return array<string, array<string, true>> [moduleKey][propertyName] => true for LANG-scoped definitions
     */
    protected function langScopedFields(ExtraPropertyDefinitionCollection $definitions): array
    {
        $fields = [];
        foreach ($definitions as $definition) {
            if (ExtraPropertyScope::LANG === $definition->getScope()) {
                $fields[$definition->getNormalizedModuleKey()][$definition->getPropertyName()] = true;
            }
        }

        return $fields;
    }

    /**
     * Converts the LANG fields of a submitted payload from locale keys to id_lang keys; other scopes pass through.
     *
     * @param array<string, array<string, mixed>> $payload
     * @param array<string, array<string, true>> $langScopedFields
     *
     * @return array<string, array<string, mixed>>
     */
    protected function localesToIds(array $payload, array $langScopedFields): array
    {
        foreach ($payload as $moduleKey => $fields) {
            if (!is_array($fields)) {
                continue;
            }
            foreach ($fields as $fieldName => $value) {
                if (!is_array($value) || empty($langScopedFields[$moduleKey][$fieldName])) {
                    continue;
                }
                try {
                    $payload[$moduleKey][$fieldName] = $this->localizedValueUpdater->denormalizeLocalizedValue(
                        $value,
                        $fieldName,
                        [LocalizedValue::IS_LOCALIZED_VALUE => true, LocalizedValue::DENORMALIZED_KEY => LocalizedValue::ID_KEY],
                    );
                } catch (LocaleNotFoundException) {
                    // Unknown locale (already reported by validation) — drop the field rather than fail the write.
                    unset($payload[$moduleKey][$fieldName]);
                }
            }
        }

        return $payload;
    }

    /**
     * Converts the LANG fields of loaded values from id_lang keys to locale keys; other scopes pass through.
     *
     * @param array<string, array<string, mixed>> $valuesByModule
     * @param array<string, array<string, true>> $langScopedFields
     *
     * @return array<string, array<string, mixed>>
     */
    protected function idsToLocales(array $valuesByModule, array $langScopedFields): array
    {
        foreach ($valuesByModule as $moduleKey => $fields) {
            if (!is_array($fields)) {
                continue;
            }
            foreach ($fields as $fieldName => $value) {
                if (!is_array($value) || empty($langScopedFields[$moduleKey][$fieldName])) {
                    continue;
                }
                try {
                    $valuesByModule[$moduleKey][$fieldName] = $this->localizedValueUpdater->normalizeLocalizedValue(
                        $value,
                        $fieldName,
                        [LocalizedValue::IS_LOCALIZED_VALUE => true],
                    );
                } catch (Throwable) {
                    // Unknown id_lang in stored data — keep the raw value rather than fail the response.
                }
            }
        }

        return $valuesByModule;
    }
}
