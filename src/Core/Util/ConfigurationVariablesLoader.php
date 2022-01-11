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

namespace PrestaShop\PrestaShop\Core\Util;

use Symfony\Component\Dotenv\Dotenv;

class ConfigurationVariablesLoader
{
    private const PARAMETERS_WITH_ENV_VARS = [
        'database_host' => 'PS_DATABASE_HOST',
        'database_port' => 'PS_DATABASE_PORT',
        'database_user' => 'PS_DATABASE_USER',
        'database_password' => 'PS_DATABASE_PASSWORD',
        'database_name' => 'PS_DATABASE_NAME',
        'database_prefix' => 'PS_DATABASE_PREFIX',
        'database_engine' => 'PS_DATABASE_ENGINE',
        'cookie_key' => 'PS_COOKIE_KEY',
        'cookie_iv' => 'PS_COOKIE_IV',
    ];

    public function __construct(string $envFilePath)
    {
        (new Dotenv(true))->loadEnv($envFilePath);
    }

    public function loadEnvVariables(array $parameters): array
    {
        if (!isset($parameters['parameters'])) {
            $parameters['parameters'] = [];
        }

        foreach (static::PARAMETERS_WITH_ENV_VARS as $parameterKey => $envVarKey) {
            if (array_key_exists($envVarKey, $_ENV)) {
                $parameters['parameters'][$parameterKey] = $_ENV[$envVarKey];
            }
        }

        return $parameters;
    }
}
