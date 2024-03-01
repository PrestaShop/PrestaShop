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

    protected function setUp(): void
    {
        parent::setUp();
        $this->featureFlagManager = self::getContainer()->get('PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagManager');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        ApiClientResetter::resetApiClient();
        FeatureResetter::resetFeatures();

        ConfigurationResetter::resetConfiguration();
        FeatureFlagResetter::resetFeatureFlags();
    }

    public function testAuthorizationServerFeatureDisabled()
    {
        $this->featureFlagManager->disable(FeatureFlagSettings::FEATURE_FLAG_AUTHORIZATION_SERVER);
        self::createApiClient();
        $bearerToken = $this->getBearerToken();
        static::createClient()->request('GET', '/api/test/unscoped/product/1', [
            'headers' => [
                'Authorization' => 'Bearer ' . $bearerToken,
            ],
        ]);

        self::assertResponseStatusCodeSame(404);
    }

    public function testAuthorizationServerFeatureMultistoreEnabled()
    {
        self::updateConfiguration(MultistoreConfig::FEATURE_STATUS, 1);
        self::createApiClient();
        $bearerToken = $this->getBearerToken();
        static::createClient()->request('GET', '/api/test/unscoped/product/1', [
            'headers' => [
                'Authorization' => 'Bearer ' . $bearerToken,
            ],
        ]);

        self::assertResponseStatusCodeSame(404);
    }

    public function testAuthorizationServerFeatureMultistoreSuccess()
    {
        self::updateConfiguration(MultistoreConfig::FEATURE_STATUS, 1);
        $this->featureFlagManager->enable(FeatureFlagSettings::FEATURE_FLAG_AUTHORIZATION_SERVER_MULTISTORE);
        self::createApiClient();
        $bearerToken = $this->getBearerToken();
        static::createClient()->request('GET', '/api/test/unscoped/product/1', [
            'headers' => [
                'Authorization' => 'Bearer ' . $bearerToken,
            ],
        ]);

        self::assertResponseStatusCodeSame(200);
    }
}
