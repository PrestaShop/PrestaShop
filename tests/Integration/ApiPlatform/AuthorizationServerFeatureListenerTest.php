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

class AuthorizationServerFeatureListenerTest extends ApiTestCase
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

    public function testGetBearerTokenWhenAuthorizationServerIsEnabled(): string
    {
        self::createApiClient();
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
     * @depends testGetBearerTokenWhenAuthorizationServerIsEnabled
     *
     * @param string $bearerToken
     *
     * @return string
     */
    public function testAccessTokenNotFoundAfterDisablingAuthorizationServer(string $bearerToken): string
    {
        // Disbale the Admin API feature, we can't even get a token now
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
     * @depends testAccessTokenNotFoundAfterDisablingAuthorizationServer
     *
     * @param string $bearerToken
     *
     * @return string
     */
    public function testAuthorizationServerFeatureDisabled(string $bearerToken): string
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
     * @depends testAuthorizationServerFeatureDisabled
     *
     * @param string $bearerToken
     *
     * @return string
     */
    public function testAuthorizationServerFeatureMultistoreEnabled(string $bearerToken): string
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
     * @depends testAuthorizationServerFeatureMultistoreEnabled
     *
     * @param string $bearerToken
     *
     * @return string
     */
    public function testAuthorizationServerFeatureMultistoreSuccess(string $bearerToken): string
    {
        // Enabled feature flag dedicated for authorization in multistore along with authorization server
        $this->updateConfiguration('PS_ENABLE_ADMIN_API', true);
        $this->featureFlagManager->enable(FeatureFlagSettings::FEATURE_FLAG_AUTHORIZATION_SERVER_MULTISTORE);

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
     * @depends testAuthorizationServerFeatureMultistoreSuccess
     *
     * @param string $bearerToken
     *
     * @return string
     */
    public function testAuthorizationServerWhenMultistoreEnabledButNotDedicatedFeatureFlag(string $bearerToken): string
    {
        // Authorization server enabled, but not with multistore specific feature flag
        $this->updateConfiguration('PS_ENABLE_ADMIN_API', true);
        $this->featureFlagManager->disable(FeatureFlagSettings::FEATURE_FLAG_AUTHORIZATION_SERVER_MULTISTORE);

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
