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

namespace PrestaShopBundle\DependencyInjection\Compiler;

use PrestaShop\PrestaShop\Adapter\Module\Repository\ModuleRepository;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Load services stored in installed modules.
 */
class LoadServicesFromModulesPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    private $configPath;

    /**
     * @var array
     */
    private $activeModulesPaths;

    /**
     * Used to identify which scope of services need to be loaded (front services, admin
     * services or generic ones)
     *
     * @param string $containerName
     */
    public function __construct($containerName = '')
    {
        $this->configPath = '/config/' . (empty($containerName) ? '' : trim($containerName, '/') . '/');
        $this->activeModulesPaths = (new ModuleRepository(_PS_ROOT_DIR_, _PS_MODULE_DIR_))->getActiveModulesPaths();
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (empty($this->activeModulesPaths)) {
            return;
        }

        foreach ($this->activeModulesPaths as $modulePath) {
            $moduleConfigPath = $modulePath . $this->configPath;
            if (file_exists($moduleConfigPath . 'services.yml')) {
                $fileLocator = new FileLocator($moduleConfigPath);
                $loader = new YamlFileLoader($container, $fileLocator);
                $loader->setResolver(new LoaderResolver([
                    new PhpFileLoader($container, $fileLocator),
                    new XmlFileLoader($container, $fileLocator),
                ]));
                $loader->load('services.yml');
            }
        }

        //@todo: POC proposal. If accepted it probably needs to be moved to dedicated pass, but priority is important
        //       (it should probably be after this ModulesPass. e.g. using same in ContainerInjectionPass doesn't work)
        // we find all definitions in order to search all decorated services
        // @todo: find a way to optimize and find all decorated services or all controllers (cannot find decorated controllers by tags)
        $allDefinitions = $container->getDefinitions();
        foreach ($allDefinitions as $definition) {
            $decoratedService = $definition->getDecoratedService();
            if (!$decoratedService || !$container->hasDefinition($decoratedService[0])) {
                // skip service if it is not a decorator
                continue;
            }

            $decoratedServiceDefinition = $container->getDefinition($decoratedService[0]);
            // if the decorator service is controller
            if ($decoratedServiceDefinition->hasTag('controller.service_arguments')) {
                // then we remove controller.service_arguments tag from the decorated class and add it to the decorator
                $decoratedServiceDefinition->clearTag('controller.service_arguments');
                if ($definition->hasTag('controller.service_arguments')) {
                    continue;
                }

                $definition->addTag('controller.service_arguments');
            }
        }
    }
}
