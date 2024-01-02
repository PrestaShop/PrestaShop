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

namespace Tests\Integration\ApiPlatform\EndPoint;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase as ApiPlatformTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\Command\AddApiAccessCommand;
use Tests\Resources\DatabaseDump;

abstract class ApiTestCase extends ApiPlatformTestCase
{
    protected const CLIENT_ID = 'test_client_id';
    protected const CLIENT_NAME = 'test_client_name';

    protected static ?string $clientSecret = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(['api_access']);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(['api_access']);
        self::$clientSecret = null;
    }

    protected static function createClient(array $kernelOptions = [], array $defaultOptions = []): Client
    {
        if (!isset($defaultOptions['headers']['accept'])) {
            $defaultOptions['headers']['accept'] = ['application/json'];
        }

        return parent::createClient($kernelOptions, $defaultOptions);
    }

    protected function getBearerToken(array $scopes = []): string
    {
        if (null === self::$clientSecret) {
            self::createApiAccess($scopes);
        }
        $client = static::createClient();
        $parameters = ['parameters' => [
            'client_id' => static::CLIENT_ID,
            'client_secret' => static::$clientSecret,
            'grant_type' => 'client_credentials',
            'scope' => $scopes,
        ]];
        $options = ['extra' => $parameters];
        $response = $client->request('POST', '/api/oauth2/token', $options);

        return json_decode($response->getContent())->access_token;
    }

    protected static function createApiAccess(array $scopes = [], int $lifetime = 10000): void
    {
        $client = static::createClient();
        $command = new AddApiAccessCommand(
            static::CLIENT_NAME,
            static::CLIENT_ID,
            true,
            '',
            $lifetime,
            $scopes
        );

        $container = $client->getContainer();
        $commandBus = $container->get('prestashop.core.command_bus');
        $createdApiAccess = $commandBus->handle($command);

        self::$clientSecret = $createdApiAccess->getSecret();
    }
}
