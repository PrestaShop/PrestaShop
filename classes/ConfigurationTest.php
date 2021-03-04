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
class ConfigurationTestCore
{
    public static $test_files = [
        '/classes/log/index.php',
        '/classes/cache/index.php',
        '/config/index.php',
        '/controllers/admin/AdminLoginController.php',
        '/download/index.php',
        '/js/tools.js',
        '/js/jquery/plugins/fancybox/jquery.fancybox.js',
        '/localization/fr.xml',
        '/mails/index.php',
        '/modules/index.php',
        '/override/controllers/front/index.php',
        '/pdf/order-return.tpl',
        '/translations/export/index.php',
        '/webservice/dispatcher.php',
        '/index.php',
        '/vendor/autoload.php',
    ];

    /**
     * getDefaultTests return an array of tests to executes.
     * key are method name, value are parameters (false for no parameter)
     * all path are _PS_ROOT_DIR_ related.
     *
     * @return array
     */
    public static function getDefaultTests()
    {
        $tests = [
            'upload' => false,
            'cache_dir' => 'var/cache',
            'log_dir' => 'var/logs',
            'img_dir' => 'img',
            'module_dir' => 'modules',
            'theme_lang_dir' => 'themes/' . _THEME_NAME_ . '/lang/',
            'theme_pdf_lang_dir' => 'themes/' . _THEME_NAME_ . '/pdf/lang/',
            'theme_cache_dir' => 'themes/' . _THEME_NAME_ . '/cache/',
            'translations_dir' => 'translations',
            'customizable_products_dir' => 'upload',
            'virtual_products_dir' => 'download',
            'config_sf2_dir' => 'app/config',
            'translations_sf2' => 'app/Resources/translations',
        ];

        if (!defined('_PS_HOST_MODE_')) {
            $tests = array_merge($tests, [
                'system' => [
                    'fopen', 'fclose', 'fread', 'fwrite',
                    'rename', 'file_exists', 'unlink', 'rmdir', 'mkdir',
                    'getcwd', 'chdir', 'chmod',
                ],
                'phpversion' => false,
                'apache_mod_rewrite' => false,
                'curl' => false,
                'gd' => false,
                'json' => false,
                'pdo_mysql' => false,
                'config_dir' => 'config',
                'files' => false,
                'mails_dir' => 'mails',
                'openssl' => false,
                'simplexml' => false,
                'zip' => false,
                'fileinfo' => false,
                'intl' => false,
                'memory_limit' => false,
                'mbstring' => false,
            ]);
        }

        return $tests;
    }

    /**
     * getDefaultTestsOp return an array of tests to executes.
     * key are method name, value are parameters (false for no parameter).
     *
     * @return array
     */
    public static function getDefaultTestsOp()
    {
        return [
            'new_phpversion' => false,
            'gz' => false,
            'mbstring' => false,
            'dom' => false,
            'pdo_mysql' => false,
            'fopen' => false,
            'intl' => false,
            'memory_limit' => false,
        ];
    }

    /**
     * run all test defined in $tests.
     *
     * @param array $tests
     *
     * @return array results of tests
     */
    public static function check($tests)
    {
        $res = [];
        foreach ($tests as $key => $test) {
            $res[$key] = ConfigurationTest::run($key, $test);
        }

        return $res;
    }

    public static function run($ptr, $arg = 0)
    {
        if (call_user_func(['ConfigurationTest', 'test_' . $ptr], $arg)) {
            return 'ok';
        }

        return 'fail';
    }

    public static function test_phpversion()
    {
        return version_compare(PHP_VERSION, '7.1.3', '>=');
    }

    public static function test_apache_mod_rewrite()
    {
        if (isset($_SERVER['SERVER_SOFTWARE'])
            && strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'apache') === false || !function_exists('apache_get_modules')) {
            return true;
        }

        return in_array('mod_rewrite', apache_get_modules());
    }

    public static function test_new_phpversion()
    {
        return static::test_phpversion();
    }

    public static function test_mysql_support()
    {
        return extension_loaded('mysql') || extension_loaded('mysqli') || extension_loaded('pdo_mysql');
    }

    public static function test_intl()
    {
        return extension_loaded('intl');
    }

    public static function test_memory_limit()
    {
        $memoryLimit = Tools::getMemoryLimit();

        return $memoryLimit === '-1' || $memoryLimit >= Tools::getOctets('256M');
    }

    public static function test_pdo_mysql()
    {
        return extension_loaded('pdo_mysql');
    }

    public static function test_upload()
    {
        return ini_get('file_uploads');
    }

    public static function test_fopen()
    {
        return in_array(ini_get('allow_url_fopen'), ['On', 'on', '1']);
    }

    public static function test_system($funcs)
    {
        foreach ($funcs as $func) {
            if (!function_exists($func)) {
                return false;
            }
        }

        return true;
    }

    public static function test_curl()
    {
        return extension_loaded('curl');
    }

    public static function test_gd()
    {
        return function_exists('imagecreatetruecolor');
    }

    public static function test_json()
    {
        return extension_loaded('json');
    }

    public static function test_gz()
    {
        if (function_exists('gzencode')) {
            return @gzencode('dd') !== false;
        }

        return false;
    }

    public static function test_simplexml()
    {
        return extension_loaded('SimpleXML');
    }

    public static function test_zip()
    {
        return extension_loaded('zip');
    }

    public static function test_fileinfo()
    {
        return extension_loaded('fileinfo');
    }

    public static function test_dir($relative_dir, $recursive = false, &$full_report = null)
    {
        $dir = rtrim(_PS_ROOT_DIR_, '\\/') . DIRECTORY_SEPARATOR . trim($relative_dir, '\\/');
        if (!file_exists($dir) || !$dh = @opendir($dir)) {
            $full_report = sprintf('Directory %s does not exist or is not writable', $dir); // sprintf for future translation

            return false;
        }
        closedir($dh);
        $dummy = rtrim($dir, '\\/') . DIRECTORY_SEPARATOR . uniqid();
        if (@file_put_contents($dummy, 'test')) {
            @unlink($dummy);
            if (!$recursive) {
                return true;
            }
        } elseif (!is_writable($dir)) {
            $full_report = sprintf('Directory %s is not writable', $dir); // sprintf for future translation

            return false;
        }

        if ($recursive) {
            foreach (Tools::getDirectories($dir) as $file) {
                if (!ConfigurationTest::test_dir($relative_dir . DIRECTORY_SEPARATOR . $file, $recursive, $full_report)) {
                    return false;
                }
            }
        }

        return true;
    }

    public static function test_file($file_relative)
    {
        $file = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . $file_relative;

        return file_exists($file) && is_writable($file);
    }

    public static function test_config_dir($dir)
    {
        return ConfigurationTest::test_dir($dir);
    }

    public static function test_sitemap($dir)
    {
        return ConfigurationTest::test_file($dir);
    }

    public static function test_root_dir($dir)
    {
        return ConfigurationTest::test_dir($dir);
    }

    public static function test_log_dir($dir)
    {
        return ConfigurationTest::test_dir($dir);
    }

    public static function test_admin_dir($dir)
    {
        return ConfigurationTest::test_dir($dir);
    }

    public static function test_img_dir($dir)
    {
        return ConfigurationTest::test_dir($dir, true);
    }

    public static function test_module_dir($dir)
    {
        return ConfigurationTest::test_dir($dir, true);
    }

    public static function test_cache_dir($dir)
    {
        return ConfigurationTest::test_dir($dir, true);
    }

    public static function test_tools_v2_dir($dir)
    {
        return ConfigurationTest::test_dir($dir);
    }

    public static function test_cache_v2_dir($dir)
    {
        return ConfigurationTest::test_dir($dir);
    }

    public static function test_download_dir($dir)
    {
        return ConfigurationTest::test_dir($dir);
    }

    public static function test_mails_dir($dir)
    {
        return ConfigurationTest::test_dir($dir, true);
    }

    public static function test_translations_dir($dir)
    {
        return ConfigurationTest::test_dir($dir, true);
    }

    public static function test_config_sf2_dir($dir)
    {
        return ConfigurationTest::test_dir($dir, true);
    }

    public static function test_theme_lang_dir($dir)
    {
        $absoluteDir = rtrim(_PS_ROOT_DIR_, '\\/') . DIRECTORY_SEPARATOR . trim($dir, '\\/');
        if (!file_exists($absoluteDir)) {
            return true;
        }

        return ConfigurationTest::test_dir($dir, true);
    }

    public static function test_theme_pdf_lang_dir($dir)
    {
        $absoluteDir = rtrim(_PS_ROOT_DIR_, '\\/') . DIRECTORY_SEPARATOR . trim($dir, '\\/');
        if (!file_exists($absoluteDir)) {
            return true;
        }

        return ConfigurationTest::test_dir($dir, true);
    }

    public static function test_theme_cache_dir($dir)
    {
        $absoluteDir = rtrim(_PS_ROOT_DIR_, '\\/') . DIRECTORY_SEPARATOR . trim($dir, '\\/');
        if (!file_exists($absoluteDir)) {
            return true;
        }

        return ConfigurationTest::test_dir($dir, true);
    }

    public static function test_customizable_products_dir($dir)
    {
        return ConfigurationTest::test_dir($dir);
    }

    public static function test_virtual_products_dir($dir)
    {
        return ConfigurationTest::test_dir($dir);
    }

    public static function test_mbstring()
    {
        return extension_loaded('mbstring');
    }

    public static function test_openssl()
    {
        return function_exists('openssl_encrypt');
    }

    public static function test_sessions()
    {
        if (!$path = @ini_get('session.save_path')) {
            return true;
        }

        return is_writable($path);
    }

    public static function test_dom()
    {
        return extension_loaded('Dom');
    }

    public static function test_files($full = false)
    {
        $return = [];
        foreach (ConfigurationTest::$test_files as $file) {
            if (!file_exists(rtrim(_PS_ROOT_DIR_, DIRECTORY_SEPARATOR) . str_replace('/', DIRECTORY_SEPARATOR, $file))) {
                if ($full) {
                    $return[] = $file;
                } else {
                    return false;
                }
            }
        }

        if ($full) {
            return $return;
        }

        return true;
    }

    public static function test_translations_sf2($dir)
    {
        return ConfigurationTest::test_dir($dir);
    }
}
