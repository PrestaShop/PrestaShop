<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class AdminInformationControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function initContent()
    {
        $this->show_toolbar = false;
        $this->display = 'view';
        parent::initContent();
    }

    public function initToolbarTitle()
    {
        $this->toolbar_title = array_unique($this->breadcrumbs);
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        unset($this->page_header_toolbar_btn['back']);
    }

    public function renderView()
    {
        $this->initPageHeaderToolbar();

        $hosting_vars = array();
        if (!defined('_PS_HOST_MODE_')) {
            $hosting_vars = array(
                'version' => array(
                    'php' => phpversion(),
                    'server' => $_SERVER['SERVER_SOFTWARE'],
                    'memory_limit' => ini_get('memory_limit'),
                    'max_execution_time' => ini_get('max_execution_time'),
                    'upload_max_filesize' => ini_get('upload_max_filesize')
                ),
                'database' => array(
                    'version' => Db::getInstance()->getVersion(),
                    'server' => _DB_SERVER_,
                    'name' => _DB_NAME_,
                    'user' => _DB_USER_,
                    'prefix' => _DB_PREFIX_,
                    'engine' => _MYSQL_ENGINE_,
                    'driver' => Db::getClass(),
                ),
                'uname' => function_exists('php_uname') ? php_uname('s').' '.php_uname('v').' '.php_uname('m') : '',
                'apache_instaweb' => Tools::apacheModExists('mod_instaweb')
            );
        }

        $shop_vars = array(
            'shop' => array(
                'ps' => _PS_VERSION_,
                'url' => $this->context->shop->getBaseURL(),
                'theme' => $this->context->shop->theme->getName(),
            ),
            'mail' => Configuration::get('PS_MAIL_METHOD') == 1,
            'smtp' => array(
                'server' => Configuration::get('PS_MAIL_SERVER'),
                'user' => Configuration::get('PS_MAIL_USER'),
                'password' => Configuration::get('PS_MAIL_PASSWD'),
                'encryption' => Configuration::get('PS_MAIL_SMTP_ENCRYPTION'),
                'port' => Configuration::get('PS_MAIL_SMTP_PORT'),
            ),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        );

        $this->tpl_view_vars = array_merge($this->getTestResult(), array_merge($hosting_vars, $shop_vars));

        return parent::renderView();
    }

    /**
     * get all tests
     *
     * @return array of test results
     */
    public function getTestResult()
    {
        $tests_errors = array(
            'phpversion' => $this->trans('Update your PHP version.', array(), 'Admin.Advparameters.Notification'),
            'upload' => $this->trans('Configure your server to allow file uploads.', array(), 'Admin.Advparameters.Notification'),
            'system' => $this->trans('Configure your server to allow the creation of directories and files with write permissions.', array(), 'Admin.Advparameters.Notification'),
            'gd' => $this->trans('Enable the GD library on your server.', array(), 'Admin.Advparameters.Notification'),
            'mysql_support' => $this->trans('Enable the MySQL support on your server.', array(), 'Admin.Advparameters.Notification'),
            'config_dir' => $this->trans('Set write permissions for the "config" folder.', array(), 'Admin.Advparameters.Notification'),
            'cache_dir' => $this->trans('Set write permissions for the "cache" folder.', array(), 'Admin.Advparameters.Notification'),
            'sitemap' => $this->trans('Set write permissions for the "sitemap.xml" file.', array(), 'Admin.Advparameters.Notification'),
            'img_dir' => $this->trans('Set write permissions for the "img" folder and subfolders.', array(), 'Admin.Advparameters.Notification'),
            'log_dir' => $this->trans('Set write permissions for the "log" folder and subfolders.', array(), 'Admin.Advparameters.Notification'),
            'mails_dir' => $this->trans('Set write permissions for the "mails" folder and subfolders.', array(), 'Admin.Advparameters.Notification'),
            'module_dir' => $this->trans('Set write permissions for the "modules" folder and subfolders.', array(), 'Admin.Advparameters.Notification'),
            'theme_lang_dir' => $this->trans('Set the write permissions for the "themes%s/lang/" folder and subfolders, recursively.', array('%s' => _THEME_NAME_), 'Admin.Advparameters.Notification'),
            'translations_dir' => $this->trans('Set write permissions for the "translations" folder and subfolders.', array(), 'Admin.Advparameters.Notification'),
            'customizable_products_dir' => $this->trans('Set write permissions for the "upload" folder and subfolders.', array(), 'Admin.Advparameters.Notification'),
            'virtual_products_dir' => $this->trans('Set write permissions for the "download" folder and subfolders.', array(), 'Admin.Advparameters.Notification'),
            'fopen' => $this->trans('Allow the PHP fopen() function on your server.', array(), 'Admin.Advparameters.Notification'),
            'gz' => $this->trans('Enable GZIP compression on your server.', array(), 'Admin.Advparameters.Notification'),
            'files' => $this->trans('Some PrestaShop files are missing from your server.', array(), 'Admin.Advparameters.Notification'),
            'new_phpversion' => $this->trans('You are using PHP %s version. Soon, the latest PHP version supported by PrestaShop will be PHP 5.4. To make sure you’re ready for the future, we recommend you to upgrade to PHP 5.4 now!', array('%s' => phpversion()), 'Admin.Advparameters.Notification'),
            'apache_mod_rewrite' => $this->trans('Enable the Apache mod_rewrite module', array(), 'Admin.Advparameters.Notification')
        );

        // Functions list to test with 'test_system'
        // Test to execute (function/args): lets uses the default test
        $params_required_results = ConfigurationTest::check(ConfigurationTest::getDefaultTests());

        if (!defined('_PS_HOST_MODE_')) {
            $params_optional_results = ConfigurationTest::check(ConfigurationTest::getDefaultTestsOp());
        }

        $fail_required = in_array('fail', $params_required_results);

        if ($fail_required && $params_required_results['files'] != 'ok') {
            $tmp = ConfigurationTest::test_files(true);
            if (is_array($tmp) && count($tmp)) {
                $tests_errors['files'] = $tests_errors['files'].'<br/>('.implode(', ', $tmp).')';
            }
        }

        $results = array(
            'failRequired' => $fail_required,
            'testsErrors' => $tests_errors,
            'testsRequired' => $params_required_results,
        );

        if (!defined('_PS_HOST_MODE_')) {
            $results = array_merge($results, array(
                'failOptional' => in_array('fail', $params_optional_results),
                'testsOptional' => $params_optional_results,
            ));
        }

        return $results;
    }

    public function displayAjaxCheckFiles()
    {
        $this->file_list = array('missing' => array(), 'updated' => array());
        $xml = @simplexml_load_file(_PS_API_URL_.'/xml/md5/'._PS_VERSION_.'.xml');
        if (!$xml) {
            die(json_encode($this->file_list));
        }

        $this->getListOfUpdatedFiles($xml->ps_root_dir[0]);
        die(json_encode($this->file_list));
    }

    public function getListOfUpdatedFiles(SimpleXMLElement $dir, $path = '')
    {
        $exclude_regexp = '(install(-dev|-new)?|themes|tools|cache|docs|download|img|localization|log|mails|translations|upload|modules|override/(:?.*)index.php$)';
        $admin_dir = basename(_PS_ADMIN_DIR_);

        foreach ($dir->md5file as $file) {
            $filename = preg_replace('#^admin/#', $admin_dir.'/', $path.$file['name']);
            if (preg_match('#^'.$exclude_regexp.'#', $filename)) {
                continue;
            }

            if (!file_exists(_PS_ROOT_DIR_.'/'.$filename)) {
                $this->file_list['missing'][] = $filename;
            } else {
                $md5_local = md5_file(_PS_ROOT_DIR_.'/'.$filename);
                if ($md5_local != (string)$file) {
                    $this->file_list['updated'][] = $filename;
                }
            }
        }

        foreach ($dir->dir as $subdir) {
            $this->getListOfUpdatedFiles($subdir, $path.$subdir['name'].'/');
        }
    }
}
