<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Catalog;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Throwable;

/**
 * Enumerates the Admin API endpoints an extra property definition can be associated with (the
 * URI templates targeted by ExtraPropertyDefinition::getAssociatedApis()), by reading the
 * OpenApi document API Platform generates: PrestaShopExtension::prepend() registers the core
 * AND active module resource paths in every kernel's api_platform mapping (that is how the
 * back office extracts the OAuth scopes), so the document already aggregates everything —
 * including the decorated additions (CQRSOpenApiFactory, ExtraPropertiesSchemaAdapter).
 *
 * Endpoints of installed-but-DISABLED modules are therefore not listed: their placements are
 * inert anyway and simply trigger the non-blocking "unknown endpoint" warning until the module
 * is enabled. A document that cannot be generated at all is logged and yields an empty catalog
 * — the pickers degrade to free text, never a broken page.
 *
 * KERNEL LIMITATION: API Platform only registers its OpenApi services (the factory injected
 * here) where enable_swagger is on — currently the admin kernel only. This service and its
 * consumers (AssociationExistenceChecker, ExtraPropertyDefinitionAdvancedType) are therefore
 * defined in app/config/admin/services.yml instead of the shared catalog.yml; using them in
 * another kernel requires enabling swagger there first, and will fail loudly until then.
 *
 * The scan is memoized per instance and cached cross-request in the
 * prestashop.extra_property.catalog.filesystem_cache pool — the endpoints are bound to the
 * deployed code and installed modules, whose management already clears the Symfony cache the
 * pool lives in, so no dedicated invalidation is needed. A generation failure is never cached.
 */
class ApiEndpointCatalog
{
    private const CACHE_KEY = 'api_endpoints';

    /**
     * @var array<string, array{uriTemplate: string, methods: list<string>}>|null indexed and sorted by normalized URI template
     */
    private ?array $entries = null;

    public function __construct(
        private readonly OpenApiFactoryInterface $openApiFactory,
        private readonly LoggerInterface $logger,
        private readonly CacheInterface $cache,
    ) {
    }

    /**
     * @return list<array{uriTemplate: string, methods: list<string>}> sorted by URI template
     */
    public function getAll(): array
    {
        return array_values($this->getEntries());
    }

    /**
     * The given path is normalized (single leading slash, no trailing slash) before comparison.
     */
    public function hasUriTemplate(string $path): bool
    {
        return isset($this->getEntries()[$this->normalizeUriTemplate($path)]);
    }

    /**
     * @return array<string, array{uriTemplate: string, methods: list<string>}>
     */
    private function getEntries(): array
    {
        if (null !== $this->entries) {
            return $this->entries;
        }

        try {
            // A throw inside the callback aborts the cache write: a broken document is retried
            // on the next request instead of being cached until the next cache clear.
            return $this->entries = $this->cache->get(self::CACHE_KEY, fn (): array => $this->scanEntries());
        } catch (Throwable $e) {
            $this->logger->warning(
                sprintf('Extra property API endpoint catalog: could not generate the OpenApi document: %s', $e->getMessage()),
                ['exception' => $e],
            );

            return $this->entries = [];
        }
    }

    /**
     * @return array<string, array{uriTemplate: string, methods: list<string>}>
     */
    private function scanEntries(): array
    {
        $entries = [];
        $paths = ($this->openApiFactory)()->getPaths()->getPaths();

        foreach ($paths as $path => $pathItem) {
            $methods = [];
            foreach ([
                'DELETE' => $pathItem->getDelete(),
                'GET' => $pathItem->getGet(),
                'PATCH' => $pathItem->getPatch(),
                'POST' => $pathItem->getPost(),
                'PUT' => $pathItem->getPut(),
            ] as $method => $operation) {
                if (null !== $operation) {
                    $methods[] = $method;
                }
            }

            if ([] === $methods) {
                continue;
            }

            $uriTemplate = $this->normalizeUriTemplate((string) $path);
            $entries[$uriTemplate] = ['uriTemplate' => $uriTemplate, 'methods' => $methods];
        }

        ksort($entries, SORT_STRING);

        return $entries;
    }

    /**
     * Normalizes to a single leading slash and no trailing slash.
     */
    private function normalizeUriTemplate(string $uriTemplate): string
    {
        return '/' . trim(trim($uriTemplate), '/');
    }
}
