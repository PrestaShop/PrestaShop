<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Kernel;


use Doctrine\DBAL\DriverManager;

class ModuleRepositoryFactory
{
    /**
     * @var self
     */
    private static $instance;

    /**
     * @var string
     */
    private $parameters;

    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new ModuleRepositoryFactory();
        }

        return self::$instance;
    }

    /**
     * @param array|null $parameters
     */
    public function __construct(array $parameters = null)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return ModuleRepository
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getRepository()
    {
        if (null !== $this->getParameters() && null === $this->moduleRepository) {
            $databasePrefix = $this->parameters['database_prefix'];
            $this->moduleRepository = new ModuleRepository(
                $this->getConnection(),
                $databasePrefix
            );
        }

        return $this->moduleRepository;
    }

    /**
     * @return \Doctrine\DBAL\Connection
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getConnection()
    {
        $parameters = $this->getParameters();

        return DriverManager::getConnection(array(
            'dbname' => $parameters['database_name'],
            'user' => $parameters['database_user'],
            'password' => $parameters['database_password'],
            'host' => $parameters['database_host'],
            'port' => $parameters['database_port'],
            'charset' => 'utf8',
            'driver' => 'pdo_mysql',
        ));
    }

    /**
     * @return string file path to PrestaShop configuration parameters.
     */
    private function getParametersFile()
    {
        return __DIR__ . '/../../../app/config/parameters.php';
    }

    /**
     * @var bool
     * @return bool
     */
    private function parametersFileExists()
    {
        return file_exists($this->getParametersFile());
    }

    /**
     * @return array
     */
    private function getParameters()
    {
        if (null === $this->parameters && $this->parametersFileExists()) {
            $config = require $this->getParametersFile();

            $this->parameters = $config['parameters'];
        }

        return $this->parameters;
    }
}
