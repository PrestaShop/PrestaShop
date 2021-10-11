<?php

namespace PrestaShop\PrestaShop\Core\Util;

use Symfony\Component\Dotenv\Dotenv;

class ConfigurationVariablesLoader
{
    private $loaded = false;

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

    public function loadEnvVariables(array $parameters): array
    {
        if (!$this->loaded) {
            (new Dotenv(true))->loadEnv(_PS_ROOT_DIR_ . '/.env');
        }

        if (!array_key_exists('parameters', $parameters)) {
            return $parameters;
        }

        foreach ($parameters['parameters'] as $parameterKey => $parameterValue) {
            if (isset(self::PARAMETERS_WITH_ENV_VARS[$parameterKey]) && false !== $envValue = getenv(self::PARAMETERS_WITH_ENV_VARS[$parameterKey])) {
                $parameters['parameters'][$parameterKey] = $envValue;
            }
        }

        return $parameters;
    }

}