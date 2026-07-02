<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Catalog;

use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Resource\Factory\AttributesResourceNameCollectionFactory;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\ApiEndpointCatalogInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\ApiEndpointEntry;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Enumerates the Admin API endpoints by scanning the ApiResource classes the same way
 * ApiResourceScopesExtractor does: it builds its own AttributesResourceNameCollectionFactory
 * over the core resources path and over each installed module's ApiPlatform mapping paths.
 *
 * We cannot rely on the ApiPlatform route collection because this service runs in the back
 * office kernel, where the Admin API routes are not mounted; scanning the resource metadata
 * directly also lets us report endpoints of installed-but-disabled modules.
 *
 * Operations sharing a URI template are merged into a single entry with the union of their
 * HTTP methods. A resource class that cannot be introspected is logged and skipped.
 *
 * The scan is memoized per instance; a cache decorator can later wrap this service for
 * cross-request caching (see the prestashop.extra_property.catalog.filesystem_cache pool).
 */
final class ApiEndpointCatalog implements ApiEndpointCatalogInterface
{
    /**
     * @var array<string, ApiEndpointEntry>|null indexed and sorted by normalized URI template
     */
    private ?array $entries = null;

    /**
     * @param list<string> $installedModules
     */
    public function __construct(
        private readonly ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory,
        private readonly string $moduleDir,
        private readonly array $installedModules,
        private readonly string $projectDir,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function getAll(): array
    {
        return array_values($this->getEntries());
    }

    public function hasUriTemplate(string $path): bool
    {
        return isset($this->getEntries()[$this->normalizeUriTemplate($path)]);
    }

    /**
     * @return array<string, ApiEndpointEntry>
     */
    private function getEntries(): array
    {
        if (null !== $this->entries) {
            return $this->entries;
        }

        $entries = [];

        // First scan the core resources
        $coreMappingPaths = [
            rtrim($this->projectDir, '/') . '/src/PrestaShopBundle/ApiPlatform/Resources',
        ];
        $this->collectEndpoints(
            $entries,
            new AttributesResourceNameCollectionFactory($coreMappingPaths),
            ApiEndpointEntry::SOURCE_CORE,
        );

        // Then each installed module (enabled or not, like ApiResourceScopesExtractor::getAllApiResourceScopes)
        foreach ($this->installedModules as $moduleName) {
            $modulePaths = $this->getModulePaths($moduleName);
            if (empty($modulePaths)) {
                continue;
            }

            $this->collectEndpoints(
                $entries,
                new AttributesResourceNameCollectionFactory($modulePaths),
                $moduleName,
            );
        }

        ksort($entries, SORT_STRING);

        return $this->entries = $entries;
    }

    /**
     * @param array<string, ApiEndpointEntry> $entries
     */
    private function collectEndpoints(array &$entries, ResourceNameCollectionFactoryInterface $resourceNameFactory, string $source): void
    {
        foreach ($resourceNameFactory->create() as $resourceClass) {
            try {
                $resourceMetadata = $this->resourceMetadataCollectionFactory->create($resourceClass);
                foreach ($resourceMetadata as $resource) {
                    foreach ($resource->getOperations() ?? [] as $operation) {
                        if (!$operation instanceof HttpOperation) {
                            continue;
                        }

                        $uriTemplate = $operation->getUriTemplate();
                        if (null === $uriTemplate || '' === $uriTemplate) {
                            continue;
                        }

                        $this->addEndpoint($entries, $this->normalizeUriTemplate($uriTemplate), strtoupper($operation->getMethod()), $source);
                    }
                }
            } catch (Throwable $e) {
                $this->logger->warning(
                    sprintf('Extra property API endpoint catalog: skipped resource "%s": %s', $resourceClass, $e->getMessage()),
                    ['exception' => $e],
                );
            }
        }
    }

    /**
     * @param array<string, ApiEndpointEntry> $entries
     */
    private function addEndpoint(array &$entries, string $uriTemplate, string $method, string $source): void
    {
        $existingEntry = $entries[$uriTemplate] ?? null;
        if (null === $existingEntry) {
            $entries[$uriTemplate] = new ApiEndpointEntry($uriTemplate, [$method], $source);

            return;
        }

        if (in_array($method, $existingEntry->methods, true)) {
            return;
        }

        $methods = array_merge($existingEntry->methods, [$method]);
        sort($methods, SORT_STRING);
        // The first declaring source wins, only the methods union is extended
        $entries[$uriTemplate] = new ApiEndpointEntry($uriTemplate, $methods, $existingEntry->source);
    }

    /**
     * Same module mapping paths as ApiResourceScopesExtractor::getModulePaths().
     *
     * @return list<string>
     */
    private function getModulePaths(string $moduleName): array
    {
        $paths = [];
        $modulePath = rtrim($this->moduleDir, '/') . '/' . $moduleName;

        // YAML definitions from the config/api_platform folder in the module
        $moduleConfigPath = sprintf('%s/config/api_platform', $modulePath);
        if (file_exists($moduleConfigPath)) {
            $paths[] = $moduleConfigPath;
        }

        // Folder containing the ApiPlatform resource classes
        $moduleResourcesPath = sprintf('%s/src/ApiPlatform/Resources', $modulePath);
        if (file_exists($moduleResourcesPath)) {
            $paths[] = $moduleResourcesPath;
        }

        return $paths;
    }

    /**
     * Normalizes to a single leading slash and no trailing slash.
     */
    private function normalizeUriTemplate(string $uriTemplate): string
    {
        return '/' . trim(trim($uriTemplate), '/');
    }
}
