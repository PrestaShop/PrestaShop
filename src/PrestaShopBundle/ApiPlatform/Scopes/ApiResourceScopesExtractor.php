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

namespace PrestaShopBundle\ApiPlatform\Scopes;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\AttributesResourceNameCollectionFactory;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use PrestaShop\PrestaShop\Core\EnvironmentInterface;

/**
 * This service manually extracts data from the ApiResource classes to get the scopes associated
 * to them, following our internal convention to set the scopes via extra parameters.
 *
 * We cannot use the ApiPlatform metadata collection because it only contains resources for enabled modules,
 * as it should, that were set in our PrestaShopExtension. Since in forms we need all the installed scopes,
 * not just the enabled ones, we need this service to extract them.
 *
 * @internal
 */
class ApiResourceScopesExtractor implements ApiResourceScopesExtractorInterface
{
    public function __construct(
        private readonly ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory,
        private readonly EnvironmentInterface $environment,
        private readonly string $moduleDir,
        private readonly array $installedModules,
        private readonly array $enabledModules,
        private readonly string $projectDir
    ) {
    }

    /**
     * Returns all installed resource scopes even the ones that are not enabled for now.
     *
     * @return ApiResourceScopes[]
     */
    public function getAllApiResourceScopes(): array
    {
        return $this->getCoreAndModulesResources($this->installedModules);
    }

    /**
     * Returns resource scopes for core and ENABLED modules.
     *
     * @return ApiResourceScopes[]
     */
    public function getEnabledApiResourceScopes(): array
    {
        return $this->getCoreAndModulesResources($this->enabledModules);
    }

    private function getCoreAndModulesResources(array $modules): array
    {
        $resourceScopes = [];

        // First extract scopes from the core
        $coreMappingPaths = [
            rtrim($this->projectDir, '/') . '/src/PrestaShopBundle/ApiPlatform/Resources',
        ];

        // In test environment an additional mapping folder is added, but we can't inject api_platform configuration
        // in this service easily to make it fully dynamic so the extra path is hard-coded here but only in test environment
        // This could be refactored if we find a ay to properly inject the api_platform config (at least the mapping.paths part)
        if ($this->environment->getName() === 'test') {
            $testResources = rtrim($this->projectDir, '/') . '/tests/Resources/ApiPlatform/Resources';
            if (is_dir($testResources)) {
                $coreMappingPaths[] = $testResources;
            }
        }

        $coreScopes = $this->extractScopes(new AttributesResourceNameCollectionFactory($coreMappingPaths));
        if (!empty($coreScopes)) {
            $resourceScopes[] = ApiResourceScopes::createCoreScopes($coreScopes);
        }

        foreach ($modules as $moduleName) {
            $moduleScopes = $this->extractScopes(new AttributesResourceNameCollectionFactory(
                $this->getModulePaths($moduleName)
            ));
            if (!empty($moduleScopes)) {
                $resourceScopes[] = ApiResourceScopes::createModuleScopes($moduleScopes, $moduleName);
            }
        }

        return $resourceScopes;
    }

    private function getModulePaths(string $moduleName): array
    {
        $paths = [];
        $modulePath = rtrim($this->moduleDir, '/') . '/' . $moduleName;
        // Load YAML definition from the config/api_platform folder in the module
        $moduleConfigPath = sprintf('%s/config/api_platform', $modulePath);
        if (file_exists($moduleConfigPath)) {
            $paths[] = $moduleConfigPath;
        }

        // Folder containing ApiPlatform resources classes
        $moduleRessourcesPath = sprintf('%s/src/ApiPlatform/Resources', $modulePath);
        if (file_exists($moduleRessourcesPath)) {
            $paths[] = $moduleRessourcesPath;
        }

        return $paths;
    }

    private function extractScopes(ResourceNameCollectionFactoryInterface $resourceExtractor): array
    {
        $scopes = [];
        foreach ($resourceExtractor->create() as $resourceName) {
            $resourceMetadata = $this->resourceMetadataCollectionFactory->create($resourceName);
            foreach ($resourceMetadata as $resource) {
                $scopes = array_merge($scopes, $this->getScopesByResources($resource));
            }
        }

        // We want unique scopes in the list
        $scopes = array_unique($scopes);
        // array_unique can change the keys, so we create a new array with values only
        $scopes = array_values($scopes);
        // And finally sorted alphabetically
        sort($scopes);

        return $scopes;
    }

    private function getScopesByResources(ApiResource $resource): array
    {
        $scopes = [];
        /** @var Operation $operation */
        foreach ($resource->getOperations() as $operation) {
            $extraProperties = $operation->getExtraProperties();
            if (array_key_exists('scopes', $extraProperties)) {
                $operationScopes = $extraProperties['scopes'];
                foreach ($operationScopes as $operationScope) {
                    if (!in_array($operationScope, $scopes)) {
                        $scopes[] = $operationScope;
                    }
                }
            }
        }

        return $scopes;
    }
}
