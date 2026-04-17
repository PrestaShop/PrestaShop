<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\ApiPlatform\Scopes;

use ApiPlatform\Metadata\Resource\Factory\AttributesResourceMetadataCollectionFactory;
use PrestaShop\PrestaShop\Adapter\Environment;
use PrestaShop\PrestaShop\Core\FeatureFlag\DisabledFeatureFlagStateChecker;
use Psr\Container\ContainerInterface;

class ApiResourceScopesExtractorFactory
{
    public static function build(ContainerInterface $container, string $environmentName, string $moduleDir, array $installedModules, array $enabledModules, string $projectDir): ApiResourceScopesExtractor
    {
        return new ApiResourceScopesExtractor(
            new AttributesResourceMetadataCollectionFactory(),
            new Environment('dev' === $environmentName, $environmentName),
            new DisabledFeatureFlagStateChecker(),
            $container,
            $moduleDir,
            $installedModules,
            $enabledModules,
            $projectDir,
        );
    }
}
