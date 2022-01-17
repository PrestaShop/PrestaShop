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

namespace Tests\Unit\Core\Util;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\ConfigurationVariablesLoader;

class ConfigurationVariablesLoaderTest extends TestCase
{
    public function testLoadEnvVariablesSetValuesWhenParametersAreMissing(): void
    {
        $envFile = $this->createBasicEnvFile();

        $configurationVariablesLoader = new ConfigurationVariablesLoader($envFile);

        $this->assertSame(
            [
                'parameters' => [
                    'database_host' => 'localhost',
                    'database_port' => '3306',
                    'database_user' => 'db_user',
                    'database_password' => 'db_password',
                    'database_name' => 'test_db',
                    'database_prefix' => 'ps',
                    'database_engine' => 'mysql',
                    'cookie_key' => 'myCookie',
                    'cookie_iv' => 'myCookieIV',
                    'new_cookie_key' => 'myNewCookieKey',
                    'mailer_transport' => 'smtp',
                    'mailer_host' => 'smtp.gmail.com',
                    'mailer_user' => 'myuser@gmail.com',
                    'mailer_password' => 'thisismymailerpassword',
                    'locale' => 'ar-AR',
                    'secret' => 'this1s4paSspHraS3',
                    'ps_caching' => 'CacheMemcache',
                    'ps_cache_enable' => 'true',
                    'ps_creation_date' => '2021-01-17',
                    'use_debug_toolbar' => 'true',
                ],
            ],
            $configurationVariablesLoader->loadEnvVariables([]));
    }

    public function testLoadEnvVariablesReturnsEmptyParametersWhenParametersAreEmpty(): void
    {
        $envFile = $this->createBasicEnvFile();

        $configurationVariablesLoader = new ConfigurationVariablesLoader($envFile);

        $this->assertSame(
            [
                'parameters' => [
                    'database_host' => 'localhost',
                    'database_port' => '3306',
                    'database_user' => 'db_user',
                    'database_password' => 'db_password',
                    'database_name' => 'test_db',
                    'database_prefix' => 'ps',
                    'database_engine' => 'mysql',
                    'cookie_key' => 'myCookie',
                    'cookie_iv' => 'myCookieIV',
                    'new_cookie_key' => 'myNewCookieKey',
                    'mailer_transport' => 'smtp',
                    'mailer_host' => 'smtp.gmail.com',
                    'mailer_user' => 'myuser@gmail.com',
                    'mailer_password' => 'thisismymailerpassword',
                    'locale' => 'ar-AR',
                    'secret' => 'this1s4paSspHraS3',
                    'ps_caching' => 'CacheMemcache',
                    'ps_cache_enable' => 'true',
                    'ps_creation_date' => '2021-01-17',
                    'use_debug_toolbar' => 'true',
                ],
            ], $configurationVariablesLoader->loadEnvVariables(['parameters' => []]));
    }

    public function testLoadEnvVariablesReplacedAllowedParameters(): void
    {
        $envFile = $this->createEnvFile('PS_DATABASE_PORT=33123
PS_DATABASE_NAME=test_database
PS_DATABASE_PASSWORD=random_pwd
PS_DATABASE_PREFIX=test
PS_DATABASE_ENGINE=postgresql
PS_SOMETHING_NOT_ALLOWED=fake_value
PS_MAILER_TRANSPORT=sendmail
PS_MAILER_HOST=127.0.0.1
PS_MAILER_USER=toto
PS_LOCALE=fr-FR
PS_CACHE_ENABLE=false
PS_CREATION_DATE=2021-01-01
');

        $configurationVariablesLoader = new ConfigurationVariablesLoader($envFile);

        $this->assertSame(
            [
                'parameters' => [
                    'database_host' => '192.168.1.1',
                    'database_port' => '33123',
                    'database_user' => 'superadmin',
                    'locale' => 'fr-FR',
                    'database_password' => 'random_pwd',
                    'database_name' => 'test_database',
                    'database_prefix' => 'test',
                    'database_engine' => 'postgresql',
                    'mailer_transport' => 'sendmail',
                    'mailer_host' => '127.0.0.1',
                    'mailer_user' => 'toto',
                    'ps_cache_enable' => 'false',
                    'ps_creation_date' => '2021-01-01',
                ],
            ],
            $configurationVariablesLoader->loadEnvVariables(
                [
                    'parameters' => [
                        'database_host' => '192.168.1.1',
                        'database_port' => '32768',
                        'database_user' => 'superadmin',
                        'locale' => 'en-US',
                    ],
                ]
            )
        );
    }

    private function createBasicEnvFile()
    {
        return $this->createEnvFile('PS_DATABASE_HOST=localhost
PS_DATABASE_PORT=3306
PS_DATABASE_NAME=test_db
PS_DATABASE_USER=db_user
PS_DATABASE_PASSWORD=db_password
PS_DATABASE_PREFIX=ps
PS_DATABASE_ENGINE=mysql
PS_COOKIE_KEY=myCookie
PS_COOKIE_IV=myCookieIV
PS_NEW_COOKIE_KEY=myNewCookieKey
PS_MAILER_TRANSPORT=smtp
PS_MAILER_HOST=smtp.gmail.com
PS_MAILER_USER=myuser@gmail.com
PS_MAILER_PASSWORD=thisismymailerpassword
PS_LOCALE=ar-AR
PS_SECRET=this1s4paSspHraS3
PS_CACHING=CacheMemcache
PS_CACHE_ENABLE=true
PS_CREATION_DATE=2021-01-17
PS_USE_DEBUG_TOOLBAR=true
');
    }

    private function createEnvFile(string $content): string
    {
        $root = vfsStream::setup();

        $envFile = vfsStream::newFile('.env', 0400)->at($root);
        $envFile->setContent($content);

        return $envFile->url();
    }
}
