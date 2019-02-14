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
use PrestaShopBundle\DependencyInjection\Compiler\LoadDoctrineFromModulesPassFactory;
use PrestaShopBundle\Kernel\ModuleRepositoryFactory;
use Symfony\Component\Config\FileLocator;
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
     * @param string $name
     * @param bool $isDebug
     *
     * @return SfContainerBuilder
     *
     * @throws \Exception
     */
    public static function getContainer($name, $isDebug)
    {
        if ( isset($_SERVER['APP_ENV'])) {
            $environment = $_SERVER['APP_ENV'];
        } elseif (defined('_PS_IN_TEST_')) {
            $environment = 'test';
        } else {
            $environment = $isDebug ? 'dev' : 'prod';
        }

        $containerName = ucfirst($name) . 'Container';
        $file = _PS_CACHE_DIR_ . "${containerName}.php";

        if (!$isDebug && file_exists($file)) {
            require_once $file;

            return new $containerName();
        }

        $container = new SfContainerBuilder();

        $parameters = require _PS_ROOT_DIR_ . '/app/config/parameters.php';
        foreach ($parameters['parameters'] as $parameter => $value) {
            $container->setParameter($parameter, $value);
        }
        $container->setParameter('kernel.bundles', []);
        $container->setParameter('kernel.root_dir', _PS_ROOT_DIR_ . '/app/');
        $container->setParameter('kernel.name', 'app');
        $container->setParameter('kernel.debug', $isDebug);
        $container->setParameter('kernel.environment', $environment);
        $container->setParameter('kernel.cache_dir', _PS_CACHE_DIR_);

        $container->addCompilerPass(new LegacyCompilerPass());

        $moduleRepository = ModuleRepositoryFactory::getInstance()->getRepository();
        if (null !== $moduleRepository) {
            $activeModules = $moduleRepository->getActiveModules();
            self::addDoctrine($container, $activeModules);
            self::enableComposerAutoloaderOnModules($activeModules);
        }

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__));
        $servicesPath = _PS_CONFIG_DIR_ . "services/${name}/services_${$environment}.yml";
        $loader->load($servicesPath);

        $container->compile();

        $dumper = new PhpDumper($container);
        file_put_contents($file, $dumper->dump(array('class' => $containerName)));

        return $container;
    }

    /**
     * Enable auto loading of module Composer autoloader if needed.
     * Need to be done as earlier as possible in application lifecycle.
     *
     * @param array $modules the list of modules
     */
    private static function enableComposerAutoloaderOnModules($modules)
    {
        foreach ($modules as $module) {
            $autoloader = _PS_ROOT_DIR_.'/modules/'.$module.'/vendor/autoload.php';

            if (file_exists($autoloader)) {
                include_once $autoloader;
            }
        }
    }

    private static function addDoctrine(SfContainerBuilder $container, array $activeModules)
    {
        $configFile = _PS_ROOT_DIR_ . '/app/config/config.php';
        if (!file_exists($configFile)) {
            return;
        }
        $config = require $configFile;

        //Necessary to require all annotation classes from Doctrine
        Setup::createAnnotationMetadataConfiguration([]);

        $container->registerExtension(new DoctrineExtension());
        $container->loadFromExtension('doctrine', $config['doctrine']);

        $doctrinePassFactory = new LoadDoctrineFromModulesPassFactory();
        $compilerPassList = $doctrinePassFactory->buildCompilerPassList($activeModules);
        /** @var CompilerPassInterface $compilerPass */
        foreach ($compilerPassList as $compilerPass) {
            $container->addCompilerPass($compilerPass);
        }
    }
}
