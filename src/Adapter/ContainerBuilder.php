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

use Doctrine\ORM\Tools\Setup;
use LegacyCompilerPass;
use PrestaShop\PrestaShop\Adapter\Container\ContainerBuilderExtensionInterface;
use PrestaShop\PrestaShop\Adapter\Container\DoctrineBuilderExtension;
use PrestaShop\PrestaShop\Core\EnvironmentInterface;
use PrestaShopBundle\Kernel\ModuleRepositoryFactory;
use Psr\Container\ContainerInterface;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
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
    private static $containers;

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
     * @param string $containerName
     * @param bool $isDebug
     *
     * @return SfContainerBuilder
     *
     * @throws \Exception
     */
    public static function getContainer($containerName, $isDebug)
    {
        if (!isset(self::$containers[$containerName]) || null === self::$containers[$containerName]) {
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
     * @return ContainerInterface|SfContainerBuilder
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Exception
     */
    public function buildContainer($containerName)
    {
        $this->containerName = $containerName;
        $this->containerClassName = ucfirst($this->containerName) . 'Container';
        $this->dumpFile = _PS_CACHE_DIR_ . $this->containerClassName . '.php';
        $this->containerConfigCache = new ConfigCache($this->dumpFile, $this->environment->isDebug());

        //These methods load required files like autoload or annotation metadata so we need to load
        //them at each container creation, this can't be compiled.
        $this->loadDoctrineAnnotationMetadata();
        $this->loadModulesAutoloader();

        $container = $this->loadDumpedContainer();
        if (null === $container) {
            $container = $this->compileContainer();
        }

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
    private function compileContainer()
    {
        $container = new SfContainerBuilder();

        $container->addCompilerPass(new LegacyCompilerPass());
        $this->loadServices($container);

        //Build extensions
        $builderExtensions = [
            new DoctrineBuilderExtension($this->environment),
        ];
        /** @var ContainerBuilderExtensionInterface $builderExtension */
        foreach ($builderExtensions as $builderExtension) {
            $builderExtension->build($container);
        }

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
     * In symfony context doctrine classes (like Table, Entity, ...) are available thanks to
     * the autoloader. In this specific context we don't have the general autoloader, so we need
     * to include these classes manually. This is performed in Doctrine\ORM\Configuration::newDefaultAnnotationDriver
     * which is called in Setup::createAnnotationMetadataConfiguration.
     */
    private function loadDoctrineAnnotationMetadata()
    {
        Setup::createAnnotationMetadataConfiguration([]);
    }

    /**
     * @param SfContainerBuilder $container
     *
     * @throws \Exception
     */
    private function loadServices(SfContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__));
        $servicesPath = _PS_CONFIG_DIR_ . sprintf('services/%s/services_%s.yml', $this->containerName, $this->environment->getName());
        $loader->load($servicesPath);
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
                $autoloader = _PS_MODULE_DIR_ . $module . '/vendor/autoload.php';

                if (file_exists($autoloader)) {
                    include_once $autoloader;
                }
            }
        }
    }
}
