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
use PrestaShop\PrestaShop\Core\Multistore\MultistoreConfig;
use Tests\Integration\ApiPlatform\EndPoint\ApiTestCase;
use Tests\Resources\Resetter\ApiClientResetter;
use Tests\Resources\Resetter\ConfigurationResetter;
use Tests\Resources\Resetter\FeatureFlagResetter;
use Tests\Resources\Resetter\FeatureResetter;

class AdminAPIFeatureListenerTest extends ApiTestCase
{
    private FeatureFlagManager $featureFlagManager;

    private array $accessTokenOptions = [];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        ApiClientResetter::resetApiClient();
        FeatureResetter::resetFeatures();

        ConfigurationResetter::resetConfiguration();
        FeatureFlagResetter::resetFeatureFlags();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        ApiClientResetter::resetApiClient();
        FeatureResetter::resetFeatures();

        ConfigurationResetter::resetConfiguration();
        FeatureFlagResetter::resetFeatureFlags();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->featureFlagManager = self::getContainer()->get('PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagManager');
    }

    public function testAPIIsProtectedByDefault(): void
    {
        self::createApiClient();
        $this->accessTokenOptions = [
            'extra' => [
                'parameters' => [
                    'client_id' => static::CLIENT_ID,
                    'client_secret' => static::$clientSecret,
                    'grant_type' => 'client_credentials',
                    'scope' => [],
                ],
            ],
            'headers' => [
                'content-type' => 'application/x-www-form-urlencoded',
            ],
        ];
        static::createClient()->request('POST', '/access_token', $this->accessTokenOptions);
        self::assertResponseStatusCodeSame(401);

        // Try with fake HTTPS request
        static::createClient([], ['headers' => ['X_FORWARDED_PROTO' => 'HTTPS']])->request('POST', '/access_token', $this->accessTokenOptions);
        self::assertResponseStatusCodeSame(200);

        // We now authorize access without HTTPs for the rest of the tests (test environment has debug enabled)
        self::updateConfiguration('PS_ADMIN_API_FORCE_DEBUG_SECURED', 0);

        // Now we can use the API and login correctly
        static::createClient()->request('POST', '/access_token', $this->accessTokenOptions);
        self::assertResponseStatusCodeSame(200);
    }

    /**
     * @depends testAPIIsProtectedByDefault
     *
     * @return string
     */
    public function testGetBearerTokenWhenAdminAPIIsEnabled(): string
    {
        $bearerToken = $this->getBearerToken();

        static::createClient()->request('GET', '/test/unscoped/product/1', [
            'headers' => [
                'Authorization' => 'Bearer ' . $bearerToken,
            ],
        ]);
        self::assertResponseStatusCodeSame(200);

        return $bearerToken;
    }

    /**
     * @depends testGetBearerTokenWhenAdminAPIIsEnabled
     *
     * @param string $bearerToken
     *
     * @return string
     */
    public function testAccessTokenNotFoundAfterDisablingAdminAPI(string $bearerToken): string
    {
        // Disable the Admin API feature, we can't even get a token now
        $this->updateConfiguration('PS_ENABLE_ADMIN_API', false);
        $this->accessTokenOptions = [
            'extra' => [
                'parameters' => [
                    'client_id' => static::CLIENT_ID,
                    'client_secret' => static::$clientSecret,
                    'grant_type' => 'client_credentials',
                    'scope' => [],
                ],
            ],
            'headers' => [
                'content-type' => 'application/x-www-form-urlencoded',
            ],
        ];
        static::createClient()->request('POST', '/access_token', $this->accessTokenOptions);
        self::assertResponseStatusCodeSame(404);

        return $bearerToken;
    }

    /**
     * @depends testAccessTokenNotFoundAfterDisablingAdminAPI
     *
     * @param string $bearerToken
     *
     * @return string
     */
    public function testAdminAPIFeatureDisabled(string $bearerToken): string
    {
        // Endpoint is also no longer accessible
        static::createClient()->request('GET', '/test/unscoped/product/1', [
            'headers' => [
                'Authorization' => 'Bearer ' . $bearerToken,
            ],
        ]);
        self::assertResponseStatusCodeSame(404);

        return $bearerToken;
    }

    /**
     * @depends testAdminAPIFeatureDisabled
     *
     * @param string $bearerToken
     *
     * @return string
     */
    public function testAdminAPIFeatureMultistoreEnabled(string $bearerToken): string
    {
        // Multistore enabled but it is not enough
        self::updateConfiguration(MultistoreConfig::FEATURE_STATUS, 1);

        // Endpoint no longer accessible
        static::createClient()->request('GET', '/test/unscoped/product/1', [
            'headers' => [
                'Authorization' => 'Bearer ' . $bearerToken,
            ],
        ]);
        self::assertResponseStatusCodeSame(404);
        // Access token endpoint no longer accessible either
        static::createClient()->request('POST', '/access_token', $this->accessTokenOptions);
        self::assertResponseStatusCodeSame(404);

        return $bearerToken;
    }

    /**
     * @depends testAdminAPIFeatureMultistoreEnabled
     *
     * @param string $bearerToken
     *
     * @return string
     */
    public function testAdminAPIFeatureMultistoreSuccess(string $bearerToken): string
    {
        // Enabled feature flag dedicated for authorization in multistore along with Admin API configuration
        $this->updateConfiguration('PS_ENABLE_ADMIN_API', true);
        $this->featureFlagManager->enable(FeatureFlagSettings::FEATURE_FLAG_ADMIN_API_MULTISTORE);

        // Endpoint now accessible again
        static::createClient()->request('GET', '/test/unscoped/product/1', [
            'headers' => [
                'Authorization' => 'Bearer ' . $bearerToken,
            ],
        ]);
        self::assertResponseStatusCodeSame(200);

        return $bearerToken;
    }

    /**
     * @depends testAdminAPIFeatureMultistoreSuccess
     *
     * @param string $bearerToken
     *
     * @return string
     */
    public function testAdminAPIWhenMultistoreEnabledButNotDedicatedFeatureFlag(string $bearerToken): string
    {
        // Admin API enabled, but not with multistore specific feature flag
        $this->updateConfiguration('PS_ENABLE_ADMIN_API', true);
        $this->featureFlagManager->disable(FeatureFlagSettings::FEATURE_FLAG_ADMIN_API_MULTISTORE);

        static::createClient()->request('GET', '/test/unscoped/product/1', [
            'headers' => [
                'Authorization' => 'Bearer ' . $bearerToken,
            ],
        ]);
        self::assertResponseStatusCodeSame(404);
        // Access token endpoint no longer accessible either
        static::createClient()->request('POST', '/access_token', $this->accessTokenOptions);
        self::assertResponseStatusCodeSame(404);

        return $bearerToken;
    }
}
