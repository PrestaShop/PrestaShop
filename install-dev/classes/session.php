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

/**
 * Manage session for install script
 *
 * @property string $last_step
 * @property string|null $lang
 * @property array $process_validated
 * @property string $install_type
 * @property bool $database_clear
 * @property string $step
 * @property string $database_server
 * @property string $database_login
 * @property string $database_password
 * @property string $database_name
 * @property string $database_prefix
 * @property string $database_engine
 * @property string $shop_name
 * @property array $xml_loader_ids
 * @property int $shop_activity
 * @property string $shop_country
 * @property string $admin_firstname
 * @property string $admin_lastname
 * @property string $admin_password
 * @property string $admin_password_confirm
 * @property string $admin_email
 * @property string $shop_timezone
 * @property bool $configuration_agrement
 * @property bool $licence_agrement
 * @property bool $enable_ssl
 * @property int $rewrite_engine
 * @property bool $use_smtp
 * @property string $smtp_encryption
 * @property int $smtp_port
 * @property array $content_modules
 * @property string $content_theme
 * @property bool $content_install_fixtures
 * @property int $moduleAction
 */
class InstallSession
{
    protected static $_instance;
    protected static $_cookie_mode = false;
    protected static $_cookie = false;

    public static function getInstance()
    {
        if (!static::$_instance) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }

    public function __construct()
    {
        session_name('install_' . substr(md5($_SERVER['HTTP_HOST']), 0, 12));
        $session_started = session_start();
        if (!($session_started)
        || (!isset($_SESSION['session_mode']) && (isset($_GET['_']) || isset($_POST['submitNext']) || isset($_POST['submitPrevious']) || isset($_POST['language'])))) {
            static::$_cookie_mode = true;
            static::$_cookie = new Cookie('ps_install', '', time() + 7200, null, true);
        }
        if ($session_started && !isset($_SESSION['session_mode'])) {
            $_SESSION['session_mode'] = 'session';
            session_write_close();
        }
    }

    public function clean()
    {
        if (static::$_cookie_mode) {
            static::$_cookie->logout();
        } else {
            foreach ($_SESSION as $k => $v) {
                unset($_SESSION[$k]);
            }
        }
    }

    public function &__get($varname)
    {
        if (static::$_cookie_mode) {
            $ref = static::$_cookie->{$varname};
            if (0 === strncmp($ref, 'serialized_array:', strlen('serialized_array:'))) {
                $ref = unserialize(substr($ref, strlen('serialized_array:')));
            }
        } else {
            if (isset($_SESSION[$varname])) {
                $ref = &$_SESSION[$varname];
            } else {
                $null = null;
                $ref = &$null;
            }
        }

        return $ref;
    }

    public function __set($varname, $value)
    {
        if (static::$_cookie_mode) {
            if ($varname == 'xml_loader_ids') {
                return;
            }
            if (is_array($value)) {
                $value = 'serialized_array:' . serialize($value);
            }
            static::$_cookie->{$varname} = $value;
        } else {
            $_SESSION[$varname] = $value;
        }
    }

    public function __isset($varname)
    {
        if (static::$_cookie_mode) {
            return isset(static::$_cookie->{$varname});
        } else {
            return isset($_SESSION[$varname]);
        }
    }

    public function __unset($varname)
    {
        if (static::$_cookie_mode) {
            unset(static::$_cookie->{$varname});
        } else {
            unset($_SESSION[$varname]);
        }
    }
}
