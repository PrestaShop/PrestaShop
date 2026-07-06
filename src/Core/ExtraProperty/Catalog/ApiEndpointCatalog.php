<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Catalog;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use Psr\Log\LoggerInterface;
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
 * The scan is memoized per instance; a cache decorator can later wrap this service for
 * cross-request caching (see the prestashop.extra_property.catalog.filesystem_cache pool).
 */
class ApiEndpointCatalog
{
    /**
     * @var array<string, array{uriTemplate: string, methods: list<string>}>|null indexed and sorted by normalized URI template
     */
    private ?array $entries = null;

    public function __construct(
        private readonly OpenApiFactoryInterface $openApiFactory,
        private readonly LoggerInterface $logger,
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

        $entries = [];

        try {
            $paths = ($this->openApiFactory)()->getPaths()->getPaths();
        } catch (Throwable $e) {
            $this->logger->warning(
                sprintf('Extra property API endpoint catalog: could not generate the OpenApi document: %s', $e->getMessage()),
                ['exception' => $e],
            );

            return $this->entries = [];
        }

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

        return $this->entries = $entries;
    }

    /**
     * Normalizes to a single leading slash and no trailing slash.
     */
    private function normalizeUriTemplate(string $uriTemplate): string
    {
        return '/' . trim(trim($uriTemplate), '/');
    }
}
