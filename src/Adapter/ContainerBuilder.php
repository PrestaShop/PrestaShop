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

namespace PrestaShop\PrestaShop\Adapter;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\DoctrineExtension;
use Doctrine\ORM\Tools\Setup;
use LegacyCompilerPass;
use PrestaShopBundle\DependencyInjection\Compiler\ModulesDoctrinePassListBuilder;
use PrestaShopBundle\Kernel\ModuleRepositoryFactory;
use Psr\Container\ContainerInterface;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder as SfContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Build the Container for PrestaShop Legacy.
 */
class ContainerBuilder
{
    /**
     * @var ContainerInterface
     */
    private static $container;

    /**
     * @var bool
     */
    private $isDebug;

    /**
     * @var string
     */
    private $environment;

    /**
     * @var string
     */
    private $containerName;

    /**
     * @var string
     */
    private $containerClassName;

    /**
     * @var string
     */
    private $dumpFile;

    /**
     * @var ConfigCache
     */
    private $containerConfigCache;

    /**
     * @param string $name
     * @param bool $isDebug
     *
     * @return SfContainerBuilder
     *
     * @throws \Exception
     */
    public static function getContainer($name, $isDebug)
    {
        if (null === self::$container) {
            $builder = new ContainerBuilder();
            self::$container = $builder->initContainer($name, $isDebug);
        }

        return self::$container;
    }

    /**
     * @param string $name
     * @param bool $isDebug
     *
     * @return ContainerInterface|SfContainerBuilder
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function initContainer($name, $isDebug)
    {
        $this->containerName = $name;
        $this->isDebug = $isDebug;

        $environment = new Environment($isDebug);
        $this->environment = $environment->getEnvironment();
        $this->containerClassName = ucfirst($this->containerName) . 'Container';
        $this->dumpFile = _PS_CACHE_DIR_ . $this->containerClassName . '.php';
        $this->containerConfigCache = new ConfigCache($this->dumpFile, $this->isDebug);

        //Necessary to require all annotation classes from Doctrine
        Setup::createAnnotationMetadataConfiguration([]);

        $container = $this->loadDumpedContainer();
        if (null === $container) {
            $container = $this->buildContainer();
        }
        $this->loadModulesAutoloader();

        return $container;
    }

    /**
     * @return ContainerInterface|null
     */
    private function loadDumpedContainer()
    {
        $container = null;
        if ($this->containerConfigCache->isFresh()) {
            require_once $this->dumpFile;
            $container = new $this->containerClassName();
        }

        return $container;
    }

    /**
     * @return SfContainerBuilder
     *
     * @throws \Exception
     */
    private function buildContainer()
    {
        $container = new SfContainerBuilder();

        $this->initParameters($container);
        $container->addCompilerPass(new LegacyCompilerPass());
        $this->initDoctrine($container);
        $this->loadServices($container);

        $container->compile();

        //Dump the container file
        $dumper = new PhpDumper($container);
        $this->containerConfigCache->write(
            $dumper->dump(['class' => $this->containerClassName]),
            $container->getResources()
        );

        return $container;
    }

    /**
     * @param SfContainerBuilder $container
     */
    private function initParameters(SfContainerBuilder $container)
    {
        $parameters = require _PS_ROOT_DIR_ . '/app/config/parameters.php';
        foreach ($parameters['parameters'] as $parameter => $value) {
            $container->setParameter($parameter, $value);
        }
        $container->setParameter('kernel.bundles', []);
        $container->setParameter('kernel.root_dir', _PS_ROOT_DIR_ . '/app/');
        $container->setParameter('kernel.name', 'app');
        $container->setParameter('kernel.debug', $this->isDebug);
        $container->setParameter('kernel.environment', $this->environment);
        $container->setParameter('kernel.cache_dir', _PS_CACHE_DIR_);
    }

    /**
     * @param SfContainerBuilder $container
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function initDoctrine(SfContainerBuilder $container)
    {
        $configFile = _PS_ROOT_DIR_ . '/app/config/config.php';
        if (!file_exists($configFile)) {
            return;
        }
        $moduleRepository = ModuleRepositoryFactory::getInstance()->getRepository();
        if (null === $moduleRepository) {
            return;
        }
        $config = require $configFile;
        $activeModules = $moduleRepository->getActiveModules();

        $container->registerExtension(new DoctrineExtension());
        $container->loadFromExtension('doctrine', $config['doctrine']);

        $doctrinePassFactory = new ModulesDoctrinePassListBuilder($activeModules);
        $compilerPassList = $doctrinePassFactory->getCompilerPassList($activeModules);
        /** @var CompilerPassInterface $compilerPass */
        foreach ($compilerPassList as $compilerResourcePath => $compilerPass) {
            $container->addCompilerPass($compilerPass);
            if (is_dir($compilerResourcePath)) {
                $container->addResource(new DirectoryResource($compilerResourcePath));
            } elseif (is_file($compilerResourcePath)) {
                $container->addResource(new FileResource($compilerResourcePath));
            }
        }
    }

    /**
     * @param SfContainerBuilder $container
     *
     * @throws \Exception
     */
    private function loadServices(SfContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__));
        $servicesPath = _PS_CONFIG_DIR_ . sprintf('services/%s/services_%s.yml', $this->containerName, $this->environment);
        if (file_exists($servicesPath)) {
            $loader->load($servicesPath);
        }
    }

    /**
     * Loops through all active modules and automatically include their autoload (if present).
     * Needs to be done as earlier as possible in application lifecycle.
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function loadModulesAutoloader()
    {
        $moduleRepository = ModuleRepositoryFactory::getInstance()->getRepository();
        if (null !== $moduleRepository) {
            $activeModules = $moduleRepository->getActiveModules();
            foreach ($activeModules as $module) {
                $autoloader = _PS_ROOT_DIR_ . '/modules/' . $module . '/vendor/autoload.php';

                if (file_exists($autoloader)) {
                    include_once $autoloader;
                }
            }
        }
    }
}
