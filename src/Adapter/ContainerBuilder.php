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

namespace PrestaShop\PrestaShop\Adapter;

use Doctrine\ORM\Tools\Setup;
use Exception;
use LegacyCompilerPass;
use PrestaShop\PrestaShop\Adapter\Container\ContainerBuilderExtensionInterface;
use PrestaShop\PrestaShop\Adapter\Container\ContainerParametersExtension;
use PrestaShop\PrestaShop\Adapter\Container\DoctrineBuilderExtension;
use PrestaShop\PrestaShop\Adapter\Container\LegacyContainer;
use PrestaShop\PrestaShop\Adapter\Container\LegacyContainerBuilder;
use PrestaShop\PrestaShop\Core\EnvironmentInterface;
use PrestaShopBundle\DependencyInjection\Compiler\LoadServicesFromModulesPass;
use PrestaShopBundle\Exception\ServiceContainerException;
use PrestaShopBundle\PrestaShopBundle;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\DoctrineProvider;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Build the Container for PrestaShop Legacy.
 *
 * @deprecated since 9.0. Please use SymfonyContainer instead.
 */
class ContainerBuilder
{
    /**
     * @var ContainerInterface
     */
    private static $containers;

    /**
     * @var EnvironmentInterface
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
     * @param string $containerName
     * @param bool $isDebug
     *
     * @return LegacyContainerBuilder
     *
     * @throws Exception
     */
    public static function getContainer($containerName, $isDebug)
    {
        if ($containerName === 'admin') {
            throw new ServiceContainerException(
                'You should use `SymfonyContainer::getInstance()` instead of `ContainerBuilder::getContainer(\'admin\')`'
            );
        }
        if (!isset(self::$containers[$containerName])) {
            $builder = new ContainerBuilder(new Environment($isDebug));
            self::$containers[$containerName] = $builder->buildContainer($containerName);
        }

        return self::$containers[$containerName];
    }

    /**
     * @param EnvironmentInterface $environment
     */
    public function __construct(EnvironmentInterface $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @param string $containerName
     *
     * @return ContainerInterface|LegacyContainerBuilder
     *
     * @throws Exception
     */
    public function buildContainer($containerName)
    {
        $this->containerName = $containerName;
        $this->containerClassName = ucfirst($this->containerName) . 'Container';
        $this->dumpFile = $this->environment->getCacheDir() . DIRECTORY_SEPARATOR . $this->containerClassName . '.php';
        $this->containerConfigCache = new ConfigCache($this->dumpFile, $this->environment->isDebug());

        //These methods load required files like autoload or annotation metadata so we need to load
        //them at each container creation, this can't be compiled.
        $this->loadDoctrineAnnotationMetadata();

        $container = $this->loadDumpedContainer();
        if (null === $container) {
            $container = $this->compileContainer();
        } else {
            $this->loadModulesAutoloader($container);
        }

        // synthetic definitions can't be compiled
        $container->set('shop', $container->get('context')->shop);
        $container->set('employee', $container->get('context')->employee);

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
     * @return LegacyContainerBuilder
     *
     * @throws Exception
     */
    private function compileContainer()
    {
        $container = new LegacyContainerBuilder();
        //If the container builder is modified the container logically should be rebuilt
        $container->addResource(new FileResource(__FILE__));

        $container->addCompilerPass(new LoadServicesFromModulesPass($this->containerName), PassConfig::TYPE_BEFORE_OPTIMIZATION, PrestaShopBundle::LOAD_MODULE_SERVICES_PASS_PRIORITY);
        $container->addCompilerPass(new LegacyCompilerPass());

        //Build extensions
        $builderExtensions = [
            new ContainerParametersExtension($this->environment),
            new DoctrineBuilderExtension($this->environment),
        ];
        /** @var ContainerBuilderExtensionInterface $builderExtension */
        foreach ($builderExtensions as $builderExtension) {
            $builderExtension->build($container);
        }

        $this->loadServicesFromConfig($container);
        $this->loadModulesAutoloader($container);
        $container->compile();

        //Dump the container file
        $dumper = new PhpDumper($container);
        $this->containerConfigCache->write(
            $dumper->dump([
                'class' => $this->containerClassName,
                'base_class' => LegacyContainer::class,
            ]),
            $container->getResources()
        );

        return $container;
    }

    /**
     * In symfony context doctrine classes (like Table, Entity, ...) are available thanks to
     * the autoloader. In this specific context we don't have the general autoloader, so we need
     * to include these classes manually. This is performed in Doctrine\ORM\Configuration::newDefaultAnnotationDriver
     * which is called in Setup::createAnnotationMetadataConfiguration.
     */
    private function loadDoctrineAnnotationMetadata()
    {
        //IMPORTANT: we need to provide a cache because doctrine tries to init a connection on redis, memcached, ... on its own
        $cacheProvider = new DoctrineProvider(new ArrayAdapter());
        Setup::createAnnotationMetadataConfiguration([], $this->environment->isDebug(), null, $cacheProvider);
    }

    /**
     * @param LegacyContainerBuilder $container
     *
     * @throws Exception
     */
    private function loadServicesFromConfig(LegacyContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__));
        $servicesPath = sprintf(
            '%sservices/%s/services_%s.yml',
            _PS_CONFIG_DIR_,
            $this->containerName,
            $this->environment->getName()
        );
        $loader->load($servicesPath);
    }

    /**
     * Loops through all active modules and automatically include their autoload (if present).
     * Needs to be done as earlier as possible in application lifecycle. Unfortunately this can't
     * be done in a compiler pass because they are only executed on compilation and this needs to
     * be done at each container instanciation.
     *
     * @param ContainerInterface $container
     *
     * @throws Exception
     */
    private function loadModulesAutoloader(ContainerInterface $container)
    {
        $installedModules = $container->getParameter('prestashop.installed_modules');
        /** @var array<string> $installedModules */
        foreach ($installedModules as $module) {
            $autoloader = _PS_MODULE_DIR_ . $module . '/vendor/autoload.php';

            if (file_exists($autoloader)) {
                include_once $autoloader;
            }
        }
    }
}
