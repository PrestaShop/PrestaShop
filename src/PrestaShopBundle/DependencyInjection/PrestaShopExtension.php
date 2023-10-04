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

namespace PrestaShopBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Adds main PrestaShop core services to the Symfony container.
 */
class PrestaShopExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__) . '/Resources/config'));
        $env = $container->getParameter('kernel.environment');
        $loader->load('services_' . $env . '.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new AddOnsConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'prestashop';
    }

    public function prepend(ContainerBuilder $container)
    {
        $this->preprendApiConfig($container);
    }

    public function preprendApiConfig(ContainerBuilder $container)
    {
        $paths = [];
        $installedModules = $container->getParameter('prestashop.installed_modules');
        $moduleDir = $container->getParameter('prestashop.module_dir');

        foreach ($installedModules as $moduleName) {
            $modulePath = $moduleDir . $moduleName;
            // Load YAML definition from the config/api_platform folder in the module
            $moduleConfigPath = sprintf('%s/config/api_platform', $modulePath);
            if (file_exists($moduleConfigPath)) {
                $paths[] = $moduleConfigPath;
            }

            /**
             * TODO: Understand why this crashes PrestaShop and redirects to Front Office - no support of entities until then
             * // Load Doctrine entities that could be used as ApiPlatform DTO resources as well in the src/Entity folder
             * $entitiesRessourcesPath = sprintf('%s/src/Entity', $modulePath);
             * if (file_exists($entitiesRessourcesPath)) {
             *   $paths[] = $entitiesRessourcesPath;
             * }
             */

            // Load ApiPLatform DTOs from the src/ApiPlatform/Resources folder
            $moduleRessourcesPath = sprintf('%s/src/ApiPlatform/Resources', $modulePath);
            if (file_exists($moduleRessourcesPath)) {
                $paths[] = $moduleRessourcesPath;
            }
        }

        if (!empty($paths)) {
            $container->prependExtensionConfig('api_platform', ['mapping' => ['paths' => $paths]]);
        }
    }
}
