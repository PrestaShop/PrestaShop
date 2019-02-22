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

namespace PrestaShop\PrestaShop\Adapter\Container;


use Doctrine\Bundle\DoctrineBundle\DependencyInjection\DoctrineExtension;
use PrestaShop\PrestaShop\Core\EnvironmentInterface;
use PrestaShopBundle\DependencyInjection\Compiler\ModulesDoctrineCompilerPass;
use PrestaShopBundle\Kernel\ModuleRepositoryFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DoctrineBuilderExtension is used to init the doctrine service in the ContainerBuilder.
 * This is a manual initialisation of Doctrine because we are not in a symfony context, so we need
 * to init a few container's parameters to make the DoctrineExtension work correctly.
 */
class DoctrineBuilderExtension implements ContainerBuilderExtensionInterface
{
    /**
     * @var EnvironmentInterface
     */
    private $environment;

    /**
     * @param EnvironmentInterface $environment
     */
    public function __construct(EnvironmentInterface $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container)
    {
        //Config is mandatory to init doctrine, during install process it might no be available so we skip the build
        $configFile = _PS_ROOT_DIR_ . '/app/config/config.php';
        if (!file_exists($configFile)) {
            return;
        }
        $config = require $configFile;
        $this->initParameters($container);

        $container->registerExtension(new DoctrineExtension());
        $container->loadFromExtension('doctrine', $config['doctrine']);

        //List of active modules necessary to load their config, during install the repository might no be available
        //if the parameters file has not been generated yet, so we skip this part of the build
        $moduleRepository = ModuleRepositoryFactory::getInstance()->getRepository();
        if (null === $moduleRepository) {
            return;
        }
        $activeModules = $moduleRepository->getActiveModules();
        $container->setParameter('kernel.active_modules', $activeModules);
        $container->addCompilerPass(new ModulesDoctrineCompilerPass());
    }

    /**
     * @param ContainerBuilder $container
     */
    private function initParameters(ContainerBuilder $container)
    {
        //We include those parameters mainly for database configuration
        $parameters = require _PS_ROOT_DIR_ . '/app/config/parameters.php';
        foreach ($parameters['parameters'] as $parameter => $value) {
            $container->setParameter($parameter, $value);
        }

        //Most of these parameters are just necessary fro doctrine services definitions
        $container->setParameter('kernel.bundles', []);
        $container->setParameter('kernel.root_dir', _PS_ROOT_DIR_ . '/app/');
        $container->setParameter('kernel.name', 'app');
        $container->setParameter('kernel.debug', $this->environment->isDebug());
        $container->setParameter('kernel.environment', $this->environment->getName());
        $container->setParameter('kernel.cache_dir', _PS_CACHE_DIR_);
    }

}
