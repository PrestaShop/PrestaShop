<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\ApiPlatform;

use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagManager;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShopBundle\ApiPlatform\Scopes\ApiResourceScopesExtractor;
use RuntimeException;
use Symfony\Component\Routing\RouterInterface;
use Tests\Integration\ApiPlatform\EndPoint\ApiTestCase;
use Tests\Resources\DatabaseDump;

/**
 * Tests for CoreVersionCompatibilityMetadataCollectionFactoryDecorator, based on the versioned test
 * operations declared in Tests\Resources\ApiPlatform\Resources\ApiTest which use extreme min/max
 * versions (1.0.0 and 99.99.99) so the expected filtering does not depend on the actual core version.
 *
 * These tests must be executed independently because their variants have impact on the cache,
 * that is also why the cache must be cleared before, after the tests and in between them.
 *
 * @group isolatedProcess
 */
class VersionedEndpointsTest extends ApiTestCase
{
    private FeatureFlagManager $featureFlagManager;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(['feature_flag']);
        self::clearCache();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(['feature_flag']);
        self::clearCache();
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::clearCache();
        $this->featureFlagManager = self::getContainer()->get(FeatureFlagManager::class);
    }

    /**
     * Since the variant configurations influence the cache we MUST clear it between each tests,
     * and clear it again after to avoid impacting the following tests in the suite
     */
    protected static function clearCache(): void
    {
        $baseCommandLine = 'php -d memory_limit=-1 ' . __DIR__ . '/../../../bin/console ';
        $commandLine = $baseCommandLine . 'cache:clear --no-warmup --no-interaction --env=test --app-id=admin-api --quiet';
        $result = 0;
        system($commandLine, $result);
        if ($result !== 0) {
            throw new RuntimeException('Could not clear the cache');
        }
    }

    /**
     * @dataProvider getVersionedEndpoints
     */
    public function testVersionedEndpoints(bool $isDebug, bool $forceExperimentalEndpoints, string $endpointUrl, string $endpointScope, bool $expectedAvailable): void
    {
        // Boot kernel with appropriate configuration, exceptionally we force the environment, so we have
        // distinct cache and adapted data/behaviour for each use case
        $kernelOptions = ['debug' => $isDebug];

        // The purpose in this test is not to check the HTTPS protection so we mimic it (especially for prod environment)
        $defaultClientOptions = [
            'headers' => [
                'X_FORWARDED_PROTO' => 'HTTPS',
            ],
        ];
        static::bootKernel($kernelOptions);

        // Update the configuration
        if ($forceExperimentalEndpoints) {
            $this->featureFlagManager->enable(FeatureFlagSettings::FEATURE_FLAG_ADMIN_API_EXPERIMENTAL_ENDPOINTS);
        } else {
            $this->featureFlagManager->disable(FeatureFlagSettings::FEATURE_FLAG_ADMIN_API_EXPERIMENTAL_ENDPOINTS);
        }

        // The scope of a filtered operation is filtered as well, so it can only be requested with the token when the endpoint is expected available
        $bearerToken = $this->getBearerToken($expectedAvailable ? [$endpointScope] : [], $kernelOptions, $defaultClientOptions);

        static::createClient($kernelOptions, $defaultClientOptions)->request('GET', $endpointUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $bearerToken,
            ],
        ]);

        // When the operation is compatible with the core version the endpoint works, when it is not it is filtered out so a 404 is returned
        self::assertResponseStatusCodeSame($expectedAvailable ? 200 : 404);

        // The scope associated to a filtered operation must be filtered from the extracted scopes as well
        /** @var ApiResourceScopesExtractor $scopesExtractor */
        $scopesExtractor = static::createClient($kernelOptions)->getContainer()->get(ApiResourceScopesExtractor::class);
        $resourceScopes = $scopesExtractor->getAllApiResourceScopes();
        $foundScope = false;
        foreach ($resourceScopes as $resourceScope) {
            if (in_array($endpointScope, $resourceScope->getScopes())) {
                $foundScope = true;
                break;
            }
        }
        $this->assertEquals($expectedAvailable, $foundScope);

        // A filtered operation must not generate any route at all, the 404 comes from the routing level
        // (the OpenApi documentation cannot be asserted from this kernel: enable_docs is false for the
        // admin-api application, the documentation is generated by the admin one based on the same
        // filtered metadata, see CQRSOpenApiFactoryTest)
        /** @var RouterInterface $router */
        $router = static::createClient($kernelOptions)->getContainer()->get('router');
        $endpointPathTemplate = str_replace('/1', '/{productId}', $endpointUrl);
        $routeFound = false;
        foreach ($router->getRouteCollection() as $route) {
            if (str_starts_with($route->getPath(), $endpointPathTemplate)) {
                $routeFound = true;
                break;
            }
        }
        $this->assertEquals($expectedAvailable, $routeFound);
    }

    public static function getVersionedEndpoints(): iterable
    {
        $endpoints = [
            'minVersion lower than the core version' => ['/test/versioned/min-valid/product/1', 'versioned_min_valid_scope', true],
            'minVersion higher than the core version' => ['/test/versioned/min-invalid/product/1', 'versioned_min_invalid_scope', false],
            'maxVersion higher than the core version' => ['/test/versioned/max-valid/product/1', 'versioned_max_valid_scope', true],
            'maxVersion lower than the core version' => ['/test/versioned/max-invalid/product/1', 'versioned_max_invalid_scope', false],
            'version range containing the core version' => ['/test/versioned/range-valid/product/1', 'versioned_range_valid_scope', true],
            'version range not containing the core version' => ['/test/versioned/range-invalid/product/1', 'versioned_range_invalid_scope', false],
        ];

        foreach ($endpoints as $description => [$endpointUrl, $endpointScope, $compatible]) {
            // In prod mode with the experimental feature flag disabled, incompatible operations are filtered
            yield $description . ', debug mode off, force config off => ' . ($compatible ? 'available' : 'filtered') => [
                false,
                false,
                $endpointUrl,
                $endpointScope,
                $compatible,
            ];

            // When the experimental endpoints feature flag is enabled nothing is filtered
            yield $description . ', debug mode off, force config on => available' => [
                false,
                true,
                $endpointUrl,
                $endpointScope,
                true,
            ];

            // Incompatible operations are filtered in debug mode as well (this case must remain the last one:
            // it leaves a debug kernel which supports the cache clearing performed in tearDownAfterClass, a
            // prod kernel would fail on shutdown because its compiled container files have been invalidated
            // by the feature flag update)
            yield $description . ', debug mode on, force config off => ' . ($compatible ? 'available' : 'filtered') => [
                true,
                false,
                $endpointUrl,
                $endpointScope,
                $compatible,
            ];
        }
    }
}
