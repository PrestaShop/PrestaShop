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

namespace Tests\Integration\ApiPlatform;

use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagManager;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShopBundle\ApiPlatform\Scopes\ApiResourceScopesExtractor;
use RuntimeException;
use Tests\Integration\ApiPlatform\EndPoint\ApiTestCase;
use Tests\Resources\DatabaseDump;

/**
 * These tests muste be executed independently because their variants have impact on the cache,
 * that is also hy the cache must be cleared before, after the tests and in between them.
 *
 * @group isolatedProcess
 */
class DisabledEndpointsTest extends ApiTestCase
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
     * @dataProvider getConfigurations
     *
     * @param bool $isDebug
     * @param bool $expectedEndpointStatus
     * @param bool $forceExperimentalEndpoints
     */
    public function testDisabledEndpoints(bool $isDebug, bool $forceExperimentalEndpoints, bool $expectedEndpointStatus): void
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

        // Scope experimental_scope only exists when the endpoint is enabled
        $bearerToken = $this->getBearerToken($expectedEndpointStatus ? ['experimental_scope'] : [], $kernelOptions, $defaultClientOptions);

        static::createClient($kernelOptions, $defaultClientOptions)->request('GET', '/test/experimental/product/1', [
            'headers' => [
                'Authorization' => 'Bearer ' . $bearerToken,
            ],
        ]);
        self::assertResponseStatusCodeSame($expectedEndpointStatus ? 200 : 404);

        /** @var ApiResourceScopesExtractor $scopesExtractor */
        $scopesExtractor = static::createClient($kernelOptions)->getContainer()->get(ApiResourceScopesExtractor::class);
        $resourceScopes = $scopesExtractor->getAllApiResourceScopes();
        $foundScope = false;
        foreach ($resourceScopes as $resourceScope) {
            if (in_array('experimental_scope', $resourceScope->getScopes())) {
                $foundScope = true;
                break;
            }
        }
        $this->assertEquals($expectedEndpointStatus, $foundScope);
    }

    public function getConfigurations(): iterable
    {
        yield 'debug mode on, force config is off (not relevant anyway), endpoint is enabled' => [
            true,
            false,
            true,
        ];

        yield 'debug mode off, force config is off, endpoint is disabled' => [
            false,
            false,
            false,
        ];

        yield 'debug mode off, force config is on, endpoint is enabled' => [
            false,
            true,
            true,
        ];
    }

    /**
     * @dataProvider getNotFoundEndpoints
     */
    public function testEndpointWithCQRSNotFound(bool $isDebug, bool $forceExperimentalEndpoints, string $endpointMethod, string $endpointUrl, string $endpointScope): void
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

        // When experimental endpoints are enabled, the scope is visible, it is usable when you create a token so it is requested with the token
        // When they are disabled, the scope should be filtered out so trying to use it would result in a 401 (that's why it is not requested in the token)
        $bearerToken = $this->getBearerToken($forceExperimentalEndpoints ? [$endpointScope] : [], $kernelOptions, $defaultClientOptions);

        static::createClient($kernelOptions, $defaultClientOptions)->request($endpointMethod, $endpointUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $bearerToken,
            ],
        ]);

        // When experimental endpoints are enabled, the endpoint exists but is invalid so a 400 http code is returned
        // When experimental endpoints are disabled, the endpoint is filtered out trying to access it results in a 404
        self::assertResponseStatusCodeSame($forceExperimentalEndpoints ? 400 : 404);

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

        // If experimental endpoint is enabled the scope is found, if not it was filtered
        $this->assertEquals($forceExperimentalEndpoints, $foundScope);
    }

    public static function getNotFoundEndpoints(): iterable
    {
        // First test when the experimental endpoints are disabled
        yield 'endpoint with CQRS query not found, experimental feature flag disabled, filtered on prod' => [
            false,
            false,
            'GET',
            '/test/cqrs/query/not_found',
            'filtered_query_scope',
        ];

        yield 'endpoint with CQRS query not found, experimental feature flag disabled, filtered on dev' => [
            true,
            false,
            'GET',
            '/test/cqrs/query/not_found',
            'filtered_query_scope',
        ];

        yield 'endpoint with CQRS command not found, experimental feature flag disabled, filtered on prod' => [
            false,
            false,
            'POST',
            '/test/cqrs/command/not_found',
            'filtered_command_scope',
        ];

        yield 'endpoint with CQRS command not found, experimental feature flag disabled, filtered on dev' => [
            true,
            false,
            'POST',
            '/test/cqrs/command/not_found',
            'filtered_command_scope',
        ];

        yield 'endpoint with CQRS command AND query not found, experimental feature flag disabled, filtered on prod' => [
            false,
            false,
            'PUT',
            '/test/cqrs/query_and_command/not_found',
            'filtered_query_command_scope',
        ];

        yield 'endpoint with CQRS command AND query not found, experimental feature flag disabled, filtered on dev' => [
            true,
            false,
            'PUT',
            '/test/cqrs/query_and_command/not_found',
            'filtered_query_command_scope',
        ];

        yield 'endpoint with grid factory not found, experimental feature flag enabled, filtered on prod' => [
            false,
            false,
            'GET',
            '/test/cqrs/grid_factory/not_found',
            'filtered_grid_factory_scope',
        ];

        yield 'endpoint with grid factory not found, experimental feature flag enabled, filtered on dev' => [
            true,
            false,
            'GET',
            '/test/cqrs/grid_factory/not_found',
            'filtered_grid_factory_scope',
        ];

        // Now the experimental endpoints are enabled
        yield 'endpoint with CQRS query not found, experimental feature flag enabled, still present on prod' => [
            false,
            true,
            'GET',
            '/test/cqrs/query/not_found',
            'filtered_query_scope',
        ];

        yield 'endpoint with CQRS query not found, experimental feature flag enabled, still present on dev' => [
            true,
            true,
            'GET',
            '/test/cqrs/query/not_found',
            'filtered_query_scope',
        ];

        yield 'endpoint with CQRS command not found, experimental feature flag enabled, still present on prod' => [
            false,
            true,
            'POST',
            '/test/cqrs/command/not_found',
            'filtered_command_scope',
        ];

        yield 'endpoint with CQRS command not found, experimental feature flag enabled, still present on dev' => [
            true,
            true,
            'POST',
            '/test/cqrs/command/not_found',
            'filtered_command_scope',
        ];

        yield 'endpoint with CQRS command AND query not found, experimental feature flag enabled, still present on prod' => [
            false,
            true,
            'PUT',
            '/test/cqrs/query_and_command/not_found',
            'filtered_query_command_scope',
        ];

        yield 'endpoint with CQRS command AND query not found, experimental feature flag enabled, still present on dev' => [
            true,
            true,
            'PUT',
            '/test/cqrs/query_and_command/not_found',
            'filtered_query_command_scope',
        ];

        yield 'endpoint with grid factory not found, experimental feature flag enabled, still present on prod' => [
            false,
            true,
            'GET',
            '/test/cqrs/grid_factory/not_found',
            'filtered_grid_factory_scope',
        ];

        yield 'endpoint with grid factory not found, experimental feature flag enabled, still present on dev' => [
            true,
            true,
            'GET',
            '/test/cqrs/grid_factory/not_found',
            'filtered_grid_factory_scope',
        ];
    }
}
