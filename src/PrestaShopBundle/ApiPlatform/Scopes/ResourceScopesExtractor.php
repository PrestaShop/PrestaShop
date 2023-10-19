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

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\AttributesResourceNameCollectionFactory;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;

/**
 * @internal
 */
class ResourceScopesExtractor
{
    public function __construct(
        private readonly ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory,
        private readonly string $moduleDir,
        private readonly array $installedModules,
        private readonly string $projectDir
    ) {
    }

    /**
     * @return ResourceScopes[]
     */
    public function getScopes(): array
    {
        $resourceScopes = [];

        // First extract scopes from the core
        $coreScopes = $this->extractScopes(new AttributesResourceNameCollectionFactory([
            $this->projectDir . '/src/PrestaShopBundle/ApiPlatform/Resources',
        ]));
        if (!empty($coreScopes)) {
            $resourceScopes[] = ResourceScopes::createCoreScopes($coreScopes);
        }

        foreach ($this->installedModules as $moduleName) {
            $moduleScopes = $this->extractScopes(new AttributesResourceNameCollectionFactory(
                $this->getModulePaths($moduleName)
            ));
            if (!empty($moduleScopes)) {
                $resourceScopes[] = ResourceScopes::createModuleScopes($moduleScopes, $moduleName);
            }
        }

        return $resourceScopes;
    }

    private function getModulePaths(string $moduleName): array
    {
        $paths = [];
        $modulePath = $this->moduleDir . $moduleName;
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
            }
        }
        sort($scopes);

        return $scopes;
    }
}
