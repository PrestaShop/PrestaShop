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
class Datas
{
    private static $instance = null;
    protected static $available_args = [
        'step' => [
            'name' => 'step',
            'default' => 'all',
            'validate' => 'isGenericName',
            'help' => 'all / database,fixtures,theme,modules,postInstall',
        ],
        'language' => [
            'default' => 'en',
            'validate' => 'isLanguageIsoCode',
            'alias' => 'l',
            'help' => 'language iso code',
        ],
        'all_languages' => [
            'default' => '0',
            'validate' => 'isInt',
            'alias' => 'l',
            'help' => 'install all available languages',
        ],
        'timezone' => [
            'default' => 'Europe/Paris',
            'alias' => 't',
        ],
        'base_uri' => [
            'name' => 'base_uri',
            'validate' => 'isUrl',
            'default' => '/',
        ],
        'http_host' => [
            'name' => 'domain',
            'validate' => 'isGenericName',
            'default' => 'localhost',
        ],
        'database_server' => [
            'name' => 'db_server',
            'default' => 'localhost',
            'validate' => 'isGenericName',
            'alias' => 'h',
        ],
        'database_login' => [
            'name' => 'db_user',
            'alias' => 'u',
            'default' => 'root',
            'validate' => 'isGenericName',
        ],
        'database_password' => [
            'name' => 'db_password',
            'alias' => 'p',
            'default' => '',
        ],
        'database_name' => [
            'name' => 'db_name',
            'alias' => 'd',
            'default' => 'prestashop',
            'validate' => 'isGenericName',
        ],
        'database_clear' => [
            'name' => 'db_clear',
            'default' => '1',
            'validate' => 'isInt',
            'help' => 'Drop existing tables',
        ],
        'database_create' => [
            'name' => 'db_create',
            'default' => '0',
            'validate' => 'isInt',
            'help' => 'Create the database if not exist',
        ],
        'database_prefix' => [
            'name' => 'prefix',
            'default' => 'ps_',
            'validate' => 'isGenericName',
        ],
        'database_engine' => [
            'name' => 'engine',
            'validate' => 'isMySQLEngine',
            'default' => 'InnoDB',
            'help' => 'InnoDB/MyISAM',
        ],
        'shop_name' => [
            'name' => 'name',
            'validate' => 'isGenericName',
            'default' => 'PrestaShop',
        ],
        'shop_activity' => [
            'name' => 'activity',
            'default' => 0,
            'validate' => 'isInt',
        ],
        'shop_country' => [
            'name' => 'country',
            'validate' => 'isLanguageIsoCode',
            'default' => 'fr',
        ],
        'admin_firstname' => [
            'name' => 'firstname',
            'validate' => 'isName',
            'default' => 'John',
        ],
        'admin_lastname' => [
            'name' => 'lastname',
            'validate' => 'isName',
            'default' => 'Doe',
        ],
        'admin_password' => [
            'name' => 'password',
            'validate' => 'isPasswd',
            'default' => '0123456789',
        ],
        'admin_email' => [
            'name' => 'email',
            'validate' => 'isEmail',
            'default' => 'pub@prestashop.com',
        ],
        'show_license' => [
            'name' => 'license',
            'default' => 0,
            'help' => 'show PrestaShop license',
        ],
        'theme' => [
            'name' => 'theme',
            'default' => '',
        ],
        'enable_ssl' => [
            'name' => 'ssl',
            'default' => 0,
            'help' => 'enable SSL for PrestaShop',
        ],
        'rewrite_engine' => [
            'name' => 'rewrite',
            'default' => 1,
            'help' => 'enable rewrite engine for PrestaShop',
        ],
        'fixtures' => [
            'name' => 'fixtures',
            'default' => '1',
            'validate' => 'isInt',
            'help' => 'enable fixtures installation',
        ],
    ];

    protected $datas = [];

    public function __get($key)
    {
        if (isset($this->datas[$key])) {
            return $this->datas[$key];
        }

        return false;
    }

    public function __set($key, $value)
    {
        $this->datas[$key] = $value;
    }

    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public static function getArgs()
    {
        return static::$available_args;
    }

    public function getAndCheckArgs($argv)
    {
        if (!$argv) {
            return false;
        }

        $args_ok = [];
        foreach ($argv as $arg) {
            if (!preg_match('/^--([^=\'"><|`]+)(?:=([^=><|`]+)|(?!license))/i', trim($arg), $res)) {
                continue;
            }

            if ($res[1] == 'license' && !isset($res[2])) {
                $res[2] = 1;
            } elseif ($res[1] == 'prefix' && empty($res[2])) {
                $res[2] = '';
            } elseif (!isset($res[2])) {
                continue;
            }

            $args_ok[$res[1]] = $res[2];
        }

        $errors = [];
        foreach (static::getArgs() as $key => $row) {
            if (isset($row['name'])) {
                $name = $row['name'];
            } else {
                $name = $key;
            }
            if (!isset($args_ok[$name])) {
                if (!isset($row['default'])) {
                    $errors[] = 'Field ' . $row['name'] . ' is empty';
                } else {
                    $this->$key = $row['default'];
                }
            } elseif (isset($row['validate']) && class_exists('Validate') && !call_user_func(['Validate', $row['validate']], $args_ok[$name])) {
                $errors[] = 'Field ' . $key . ' is not valid';
            } else {
                $this->$key = $args_ok[$name];
            }
        }

        return count($errors) ? $errors : true;
    }
}
