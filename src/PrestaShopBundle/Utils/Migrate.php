<?php

namespace PrestaShopBundle\Utils;

use Symfony\Component\Yaml\Yaml;
use RandomLib;
use Composer\Script\Event;

class Migrate
{
    const SETTINGS_FILE = 'config/settings.inc.php';

    public static function migrateSettingsFile(Event $event = null)
    {
        if ($event !== null) {
            $event->getIO()->write("Migrating old setting file...");
        }

        if ($event) {
            $root_dir = realpath('');
        } else {
            $root_dir = realpath('../../');
        }

        if (file_exists($root_dir.'/app/config/parameters.yml')) {
            return false;
        }

        if (file_exists($root_dir.'/'.self::SETTINGS_FILE)) {
            $tmp_settings = file_get_contents($root_dir.'/'.self::SETTINGS_FILE);
            if (strpos($tmp_settings, '_DB_SERVER_') !== false) {
                $tmp_settings = preg_replace('/(\'|")\_/', '$1_LEGACY_', $tmp_settings);
                file_put_contents($root_dir.'/'.self::SETTINGS_FILE, $tmp_settings);
                include $root_dir.'/'.self::SETTINGS_FILE;

                $factory   = new RandomLib\Factory;
                $generator = $factory->getLowStrengthGenerator();
                $secret    = $generator->generateString(56);

                $default_parameters = Yaml::parse($root_dir.'/app/config/parameters.yml.dist');

                $parameters = array(
                    'parameters' => array(
                        'database_host'     => _LEGACY_DB_SERVER_,
                        'database_port'     => '~',
                        'database_user'     => _LEGACY_DB_USER_,
                        'database_password' => _LEGACY_DB_PASSWD_,
                        'database_name'     => _LEGACY_DB_NAME_,
                        'database_prefix'   => _LEGACY_DB_PREFIX_,
                        'database_engine'   => _LEGACY_MYSQL_ENGINE_,
                        'cookie_key'        => _LEGACY_COOKIE_KEY_,
                        'cookie_iv'         => _LEGACY_COOKIE_IV_,
                        'ps_caching'        => _LEGACY_PS_CACHING_SYSTEM_,
                        'ps_cache_enable'   => _LEGACY_PS_CACHE_ENABLED_,
                        'ps_creation_date'  => _LEGACY_PS_CREATION_DATE_,
                        'secret'            => $secret,
                        'mailer_transport'  => 'smtp',
                        'mailer_host'       => '127.0.0.1',
                        'mailer_user'       => '~',
                        'mailer_password'   => '~',
                    ) + $default_parameters['parameters']
                );

                if (file_put_contents($root_dir.'/app/config/parameters.yml', Yaml::dump($parameters))) {
                    $settings_content = "<?php\n";
                    $settings_content .= "//@deprecated 1.7";

                    file_put_contents($root_dir.'/'.self::SETTINGS_FILE, $settings_content);
                }
            }
        }
        if ($event !== null) {
            $event->getIO()->write("Finished...");
        }
    }
}
