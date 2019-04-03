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
use PrestaShop\PrestaShop\Adapter\Environment;

/**
 * Class ModuleRepositoryFactory is used to build the ModuleRepository in context where symfony container is not
 * available or not yet initialised (ex: AppKernel, PrestaShop\PrestaShop\Adapter\ContainerBuilder).
 * This factory is able to fetch the necessary parameters itself and builds the database connection for ModuleRepository.
 *
 * WARNING: this factory is only to be used in the specific cases mentioned above, for any other case please use the
 * 'prestashop.module_kernel.repository' or 'prestashop.bundle.repository.module' depending on your needs.
 */
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
     * @var string
     */
    private $environment;

    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

    /**
     * @var string
     */
    private $parametersFile = '';

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new ModuleRepositoryFactory();
        }

        return self::$instance;
    }

    /**
     * @param array|null $parameters
     * @param string|null $environment
     */
    public function __construct(array $parameters = null, $environment = null)
    {
        $this->parameters = $parameters;
        $this->environment = $environment;
        if (null === $environment) {
            $this->environment = (new Environment())->getName();
        }
    }

    /**
     * @return ModuleRepository
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getRepository()
    {
        $parameters = $this->getParameters();
        if (null !== $parameters && null === $this->moduleRepository) {
            $databasePrefix = $parameters['database_prefix'];
            $this->moduleRepository = new ModuleRepository(
                $this->getConnection($parameters),
                $databasePrefix
            );
        }

        return $this->moduleRepository;
    }

    /**
     * @param array $parameters
     *
     * @return \Doctrine\DBAL\Connection
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getConnection(array $parameters)
    {
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
     * @return string file path to PrestaShop configuration parameters
     */
    private function getParametersFile()
    {
        if (empty($this->parametersFile) && file_exists(__DIR__ . '/../../../app/config/parameters.php')) {
            $this->parametersFile = realpath(__DIR__ . '/../../../app/config/parameters.php');
        }

        return $this->parametersFile;
    }

    /**
     * @return array
     */
    private function getParameters()
    {
        if (null === $this->parameters && !empty($this->getParametersFile())) {
            $config = require $this->getParametersFile();
            $this->parameters = $config['parameters'];
        }

        return $this->parameters;
    }
}
