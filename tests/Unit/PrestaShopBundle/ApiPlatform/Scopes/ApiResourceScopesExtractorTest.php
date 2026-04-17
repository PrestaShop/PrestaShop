<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\ApiPlatform\Scopes;

use ApiPlatform\Metadata\Resource\Factory\AttributesResourceMetadataCollectionFactory;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\EnvironmentInterface;
use PrestaShop\PrestaShop\Core\FeatureFlag\DisabledFeatureFlagStateChecker;
use PrestaShopBundle\ApiPlatform\Scopes\ApiResourceScopes;
use PrestaShopBundle\ApiPlatform\Scopes\ApiResourceScopesExtractor;
use Psr\Container\ContainerInterface;

class ApiResourceScopesExtractorTest extends TestCase
{
    private string $moduleDir;

    private string $projectDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->moduleDir = __DIR__ . '/../../../Resources/api_platform/fake_module_resources/';
        $this->projectDir = __DIR__ . '/../../../Resources/api_platform/fake_core_resources';
    }

    public function testGetAllResourceScopes(): void
    {
        $scopesExtractor = $this->buildExtractor();
        $resourceScopes = $scopesExtractor->getAllApiResourceScopes();

        $expectedResourceScopes = [
            ApiResourceScopes::createCoreScopes(['hook_read', 'hook_write']),
            ApiResourceScopes::createModuleScopes(['api_client_read'], 'fake_module'),
            ApiResourceScopes::createModuleScopes(['customer_group_read'], 'disabled_fake_module'),
        ];
        $this->assertEquals($expectedResourceScopes, $resourceScopes);
    }

    public function testGetEnabledResourceScopes(): void
    {
        $scopesExtractor = $this->buildExtractor();
        $resourceScopes = $scopesExtractor->getEnabledApiResourceScopes();

        $expectedResourceScopes = [
            ApiResourceScopes::createCoreScopes(['hook_read', 'hook_write']),
            ApiResourceScopes::createModuleScopes(['api_client_read'], 'fake_module'),
        ];
        $this->assertEquals($expectedResourceScopes, $resourceScopes);
    }

    private function buildExtractor(): ApiResourceScopesExtractor
    {
        $environment = $this->createMock(EnvironmentInterface::class);
        $environment->method('getName')->willReturn('test');

        $container = $this->createMock(ContainerInterface::class);
        $container->method('has')->willReturn(false);

        return new ApiResourceScopesExtractor(
            new AttributesResourceMetadataCollectionFactory(),
            $environment,
            new DisabledFeatureFlagStateChecker(),
            $container,
            $this->moduleDir,
            ['fake_module', 'disabled_fake_module'],
            ['fake_module'],
            $this->projectDir
        );
    }
}
