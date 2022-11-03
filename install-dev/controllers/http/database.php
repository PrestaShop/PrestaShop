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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use PrestaShopBundle\Install\Database;
use Symfony\Component\Yaml\Yaml;

/**
 * Step 3 : configure database
 */
class InstallControllerHttpDatabase extends InstallControllerHttp implements HttpConfigureInterface
{
    /**
     * @var Database
     */
    public $model_database;

    /**
     * @var string
     */
    public $database_server;

    /**
     * @var string
     */
    public $database_name;

    /**
     * @var string
     */
    public $database_login;

    /**
     * @var string
     */
    public $database_password;

    /**
     * @var string
     */
    public $database_engine;

    /**
     * @var string
     */
    public $database_prefix;

    /**
     * @var bool
     */
    public $database_clear;

    /**
     * @var bool
     */
    public $use_smtp;

    /**
     * @var string
     */
    public $smtp_encryption;

    /**
     * @var int
     */
    public $smtp_port;

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        $this->model_database = new Database();
        $this->model_database->setTranslator($this->translator);
    }

    /**
     * {@inheritdoc}
     */
    public function processNextStep(): void
    {
        // Save database config
        $this->session->database_server = trim(Tools::getValue('dbServer'));
        $this->session->database_name = trim(Tools::getValue('dbName'));
        $this->session->database_login = trim(Tools::getValue('dbLogin'));
        $this->session->database_password = trim(Tools::getValue('dbPassword'));
        $this->session->database_prefix = trim(Tools::getValue('db_prefix'));
        $this->session->database_clear = Tools::getValue('database_clear');
        $this->session->rewrite_engine = Tools::getValue('rewrite_engine');
    }

    /**
     * {@inheritdoc}
     */
    public function validate(): bool
    {
        $this->errors = $this->model_database->testDatabaseSettings(
            $this->session->database_server,
            $this->session->database_name,
            $this->session->database_login,
            $this->session->database_password,
            $this->session->database_prefix,
            // We do not want to validate table prefix if we are already in install process
            ($this->session->step == 'process') ? true : $this->session->database_clear
        );
        if (count($this->errors)) {
            return false;
        }

        if (!isset($this->session->database_engine)) {
            $this->session->database_engine = $this->model_database->getBestEngine($this->session->database_server, $this->session->database_name, $this->session->database_login, $this->session->database_password);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function process(): void
    {
        if (Tools::getValue('checkDb')) {
            $this->processCheckDb();
        } elseif (Tools::getValue('createDb')) {
            $this->processCreateDb();
        }
    }

    /**
     * Check if a connection to database is possible with these data
     */
    public function processCheckDb(): void
    {
        $server = Tools::getValue('dbServer');
        $database = Tools::getValue('dbName');
        $login = Tools::getValue('dbLogin');
        $password = Tools::getValue('dbPassword');
        $prefix = Tools::getValue('db_prefix');
        $clear = Tools::getValue('clear');

        $errors = $this->model_database->testDatabaseSettings($server, $database, $login, $password, $prefix, $clear);

        $this->ajaxJsonAnswer(
            (count($errors)) ? false : true,
            (count($errors)) ? implode('<br />', $errors) : $this->translator->trans('Database is connected', [], 'Install')
        );
    }

    /**
     * Attempt to create the database
     */
    public function processCreateDb(): void
    {
        $server = Tools::getValue('dbServer');
        $database = Tools::getValue('dbName');
        $login = Tools::getValue('dbLogin');
        $password = Tools::getValue('dbPassword');

        $success = $this->model_database->createDatabase($server, $database, $login, $password);

        $this->ajaxJsonAnswer(
            $success,
            $success ? $this->translator->trans('Database is created', [], 'Install') : $this->translator->trans('Cannot create the database automatically', [], 'Install')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function display(): void
    {
        if (!$this->session->database_server) {
            if (file_exists(_PS_ROOT_DIR_ . '/app/config/parameters.php')) {
                $parameters = require _PS_ROOT_DIR_ . '/app/config/parameters.php';
            } else {
                $parameters = Yaml::parse(file_get_contents(_PS_ROOT_DIR_ . '/app/config/parameters.yml.dist'));
            }

            $this->database_server = $parameters['parameters']['database_host'];
            if (!empty($parameters['parameters']['database_port'])) {
                $this->database_server .= ':' . $parameters['parameters']['database_port'];
            }
            $this->database_name = $parameters['parameters']['database_name'];
            $this->database_login = $parameters['parameters']['database_user'];
            $this->database_password = $parameters['parameters']['database_password'];
            $this->database_engine = $parameters['parameters']['database_engine'];
            $this->database_prefix = $parameters['parameters']['database_prefix'];

            $this->database_clear = true;
            $this->use_smtp = false;
            $this->smtp_encryption = 'off';
            $this->smtp_port = 25;
        } else {
            $this->database_server = $this->session->database_server;
            $this->database_name = $this->session->database_name;
            $this->database_login = $this->session->database_login;
            $this->database_password = $this->session->database_password;
            $this->database_engine = $this->session->database_engine;
            $this->database_prefix = $this->session->database_prefix;
            $this->database_clear = $this->session->database_clear;

            $this->use_smtp = $this->session->use_smtp;
            $this->smtp_encryption = $this->session->smtp_encryption;
            $this->smtp_port = $this->session->smtp_port;
        }

        $this->displayContent('database');
    }
}
