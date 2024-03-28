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

use PrestaShopBundle\ApiPlatform\Scopes\ApiResourceScopesExtractor;
use Tests\Integration\ApiPlatform\EndPoint\ApiTestCase;

/**
 * These tests muste be executed independently because their variants have impact on the cache,
 * that is also hy the cache must be cleared before, after the tests and in between them.
 *
 * @group isolatedProcess
 */
class DisabledEndpointsTest extends ApiTestCase
{
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        self::clearCache();
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::clearCache();
    }

    /**
     * Since the variant configurations influence the cache we MUST clear it between each tests,
     * and clear it again after to avoid impacting the following tests in the suite
     */
    protected static function clearCache(): void
    {
        $kernel = static::bootKernel();
        $baseCommandLine = 'php -d memory_limit=-1 ' . $kernel->getProjectDir() . '/bin/console ';
        $commandLine = $baseCommandLine . 'cache:clear --no-warmup --no-interaction --env=test --app-id=admin-api --quiet';
        $result = 0;
        system($commandLine, $result);
        if ($result !== 0) {
            throw new \RuntimeException('Could not clear the cache');
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
        static::bootKernel($kernelOptions);

        // Update the configuration
        $this->updateConfiguration('PS_ENABLE_EXPERIMENTAL_API_ENDPOINTS', (int) $forceExperimentalEndpoints);

        // Scope experimental_scope only exists when the endpoint is enabled
        $bearerToken = $this->getBearerToken($expectedEndpointStatus ? ['experimental_scope'] : [], $kernelOptions);

        static::createClient($kernelOptions)->request('GET', '/test/experimental/product/1', [
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
}
