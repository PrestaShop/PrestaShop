<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShopBundle\DependencyInjection\Compiler;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Finder\Finder;

/**
 * Load services stored in installed modules.
 */
class LoadServicesFromModulesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->registerServicesFromModules($container);
    }

    /**
     * Load all services registered in every module.
     *
     * @param ContainerBuilder $container
     */
    private function registerServicesFromModules(ContainerBuilder $container)
    {
        $installedModules = $container->getParameter('kernel.active_modules');

        foreach ($this->getModulesPaths() as $modulePath) {
            if (in_array($modulePath->getFilename(), $installedModules)
                && file_exists($modulePath . '/config/services.yml')
            ) {
                $loader = new YamlFileLoader($container, new FileLocator($modulePath . '/config/'));
                $loader->load('services.yml');
            }
        }
    }

    /**
     * @return \Iterator
     */
    private function getModulesPaths()
    {
        return Finder::create()->directories()->in(__DIR__ . '/../../../../modules')->depth(0);
    }
}
