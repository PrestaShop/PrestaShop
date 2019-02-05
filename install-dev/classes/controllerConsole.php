<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShopBundle\Install\AbstractInstall;
use PrestaShopBundle\Install\LanguageList;

abstract class InstallControllerConsole
{
    /**
     * @var array List of installer steps
     */
    protected static $steps = array('process');

    protected static $instances = array();

    /**
     * @var string Current step
     */
    public $step;

    /**
     * @var array List of errors
     */
    public $errors = array();

    public $controller;

    /**
     * @var InstallSession
     */
    public $session;

    /**
     * @var LanguageList
     */
    public $language;

    /**
     * @var AbstractInstall
     */
    protected $model_install;

    /**
     * @var \PrestaShopBundle\Install\Database
     */
    protected $model_database;

    /**
     * Validate current step.
     */
    abstract public function validate();

    final public static function execute($argc, $argv)
    {
        if (!($argc - 1)) {
            $available_arguments = Datas::getInstance()->getArgs();
            echo 'Arguments available:'.PHP_EOL;
            foreach ($available_arguments as $key => $arg) {
                $name = isset($arg['name']) ? $arg['name'] : $key;
                echo '--'.$name."\t".(isset($arg['help']) ? $arg['help'] : '').(isset($arg['default']) ? "\t".'(Default: '.$arg['default'].')' : '').PHP_EOL;
            }
            exit;
        }

        $errors = Datas::getInstance()->getAndCheckArgs($argv);
        if (Datas::getInstance()->show_license) {
            echo strip_tags(file_get_contents(_PS_INSTALL_PATH_.'theme/views/license_content.php'));
            exit;
        }

        if ($errors !== true) {
            if (count($errors)) {
                foreach ($errors as $error) {
                    echo $error.PHP_EOL;
                }
            }
            exit;
        }

        if (!file_exists(_PS_INSTALL_CONTROLLERS_PATH_.'console/process.php')) {
            throw new PrestashopInstallerException("Controller file 'console/process.php' not found");
        }

        require_once _PS_INSTALL_CONTROLLERS_PATH_.'console/process.php';
        self::$instances['process'] = new InstallControllerConsoleProcess('process');

        $datas = Datas::getInstance();

        /* redefine HTTP_HOST  */
        $_SERVER['HTTP_HOST'] = $datas->http_host;

        @date_default_timezone_set($datas->timezone);

        self::$instances['process']->process();
    }

    final public function __construct($step)
    {
        $this->step = $step;
        $this->datas = Datas::getInstance();

        // Set current language
        $this->language = LanguageList::getInstance();
        Context::getContext()->language = $this->language->getLanguage($this->datas->language);

        $this->translator = Context::getContext()->getTranslator();

        if (!$this->datas->language) {
            die('No language defined');
        }
        $this->language->setLanguage($this->datas->language);

        $this->init();
    }

    /**
     * Initialize model.
     */
    public function init()
    {
    }

    public function printErrors()
    {
        $errors = array_merge(
            $this->model_database->getErrors(),
            $this->model_install->getErrors()
        );
        if (count($errors)) {
            if (!is_array($errors)) {
                $errors = array($errors);
            }
            echo 'Errors :'. PHP_EOL;
            foreach ($errors as $error_process) {
                if (!is_array($error_process)) {
                    $error_process = [$error_process];
                }
                foreach ($error_process as $error) {
                    echo(is_string($error) ? $error : print_r($error, true)).PHP_EOL;
                }
            }
            die;
        }
    }

    public function process()
    {
    }
}
