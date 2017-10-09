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

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpKernel\Kernel;
use PrestaShopBundle\Kernel\ModuleRepository;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            // PrestaShop Core bundle
            new PrestaShopBundle\PrestaShopBundle(),
            // PrestaShop Translation parser
            new PrestaShop\TranslationToolsBundle\TranslationToolsBundle(),
            // Api consumer
            new Csa\Bundle\GuzzleBundle\CsaGuzzleBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
        }

        /**
         * @see https://symfony.com/doc/2.8/configuration/external_parameters.html#environment-variables
         */
        if (extension_loaded('apc')) {
            $_SERVER['SYMFONY__CACHE__DRIVER'] = 'apc';
        } else {
            $_SERVER['SYMFONY__CACHE__DRIVER'] = 'array';
        }

        return $bundles;
    }

    /**
     * @{inheritdoc}
     */
    protected function getKernelParameters()
    {
        $kernelParameters = parent::getKernelParameters();

        $modules = array();
        $modulesPaths = array();

        if ($this->isParametersFile()) {
            try {
                $this->getConnection()->connect();
                $moduleRepository = $this->getModuleRepository();
                $modules = $moduleRepository->getActiveModules();
                $modulesPaths = $moduleRepository->getActiveModulesPaths($modules);
            } catch (\Exception $e) {}
        }

        return array_merge($kernelParameters,
            array(
                'kernel.modules' => $modules,
                'kernel.modules_paths' => $modulesPaths,
            )
        );
    }

    /**
     * @{inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }

    /**
     * Return the module repository.
     *
     * @return ModuleRepository
     */
    private function getModuleRepository()
    {
        $databasePrefix = $this->getParameters()['database_prefix'];

        $modulesRepository = new ModuleRepository(
            $this->getConnection(),
            $databasePrefix
        );

        return $modulesRepository;
    }

    /**
     * @return array The root parameters of PrestaShop
     */
    private function getParameters()
    {
        $parametersFile = $this->getRootDir().'/config/parameters.php';
        if (file_exists($parametersFile)) {
            $config = require($parametersFile);

            return $config['parameters'];
        }

        return array();
    }

    /**
     * @return  bool
     */
    private function isParametersFile()
    {
        $parametersFile = $this->getRootDir().'/config/parameters.php';

        return file_exists($parametersFile);
    }

    /**
     * @return \Doctrine\DBAL\Connection
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

}
