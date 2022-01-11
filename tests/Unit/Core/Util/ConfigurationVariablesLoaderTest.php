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
                ],
            ], $configurationVariablesLoader->loadEnvVariables(['parameters' => []]));
    }

    public function testLoadEnvVariablesReplacedAllowedParameters(): void
    {
        $envFile = $this->createEnvFile('PS_DATABASE_PORT=33123
PS_DATABASE_NAME=test_database
PS_DATABASE_PASSWORD=random_pwd
PS_DATABASE_PREFIX=test
PS_SOMETHING_NOT_ALLOWED=fake_value
');

        $configurationVariablesLoader = new ConfigurationVariablesLoader($envFile);

        $this->assertSame(
            [
                'parameters' => [
                    'database_host' => '192.168.1.1',
                    'database_port' => '33123',
                    'database_user' => 'superadmin',
                    'database_password' => 'random_pwd',
                    'database_name' => 'test_database',
                    'database_prefix' => 'test',
                ],
            ],
            $configurationVariablesLoader->loadEnvVariables(
                [
                    'parameters' => [
                        'database_host' => '192.168.1.1',
                        'database_port' => '32768',
                        'database_user' => 'superadmin',
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
