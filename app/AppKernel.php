<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

use Doctrine\DBAL\DriverManager;
use Symfony\Component\HttpKernel\Kernel;
use PrestaShopBundle\Kernel\ModuleRepository;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AppKernel extends Kernel
{
    /**
     * @{inheritdoc}
     */
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
            // REST API consumer
            new Csa\Bundle\GuzzleBundle\CsaGuzzleBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
        }

        if ('dev' === $this->getEnvironment()) {
            $bundles[] = new Symfony\Bundle\WebServerBundle\WebServerBundle();
        }

        /* Will not work until PrestaShop is installed */
        if ($this->parametersFileExists()) {
            try {
                $this->enableComposerAutoloaderOnModules($this->getActiveModules());
            } catch (\Exception $e) {}
        }

        return $bundles;
    }

    /**
     * @{inheritdoc}
     */
    protected function getKernelParameters()
    {
        $kernelParameters = parent::getKernelParameters();

        $activeModules = array();

        /* Will not work until PrestaShop is installed */
        if ($this->parametersFileExists()) {
            try {
                $this->getConnection()->connect();
                $activeModules = $this->getActiveModules();
            } catch (\Exception $e) {}
        }

        return array_merge(
            $kernelParameters,
            array('kernel.active_modules' => $activeModules)
        );
    }

    /**
     * @{inheritdoc}
     */
    public function getRootDir()
    {
        return __DIR__;
    }

    /**
     * @{inheritdoc}
     */
    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    /**
     * @{inheritdoc}
     */
    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    /**
     * @{inheritdoc}
     * @throws \Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->setParameter('container.autowiring.strict_mode', true);
            $container->setParameter('container.dumper.inline_class_loader', false);
            $container->addObjectResource($this);
        });

        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }

    /**
     * Return all active modules.
     *
     * @return array list of modules names.
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getActiveModules()
    {
        $databasePrefix = $this->getParameters()['database_prefix'];

        $modulesRepository = new ModuleRepository(
            $this->getConnection(),
            $databasePrefix
        );

        return $modulesRepository->getActiveModules();
    }

    /**
     * @return array The root parameters of PrestaShop.
     */
    private function getParameters()
    {
        if ($this->parametersFileExists()) {
            $config = require($this->getParametersFile());

            return $config['parameters'];
        }

        return array();
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
     * @return string file path to PrestaShop configuration parameters.
     */
    private function getParametersFile()
    {
        return $this->getRootDir().'/config/parameters.php';
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
     * Enable auto loading of module Composer autoloader if needed.
     * Need to be done as earlier as possible in application lifecycle.
     *
     * @param array $modules the list of modules
     */
    private function enableComposerAutoloaderOnModules($modules)
    {
        foreach ($modules as $module) {
            $autoloader = __DIR__.'/../modules/'.$module.'/vendor/autoload.php';

            if (file_exists($autoloader)) {
                include_once $autoloader;
            }
        }
    }
}
