<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Module\Preloader;

use PrestaShop\PrestaShop\Core\Addon\Module\ModulePreloaderInterface;
use PrestaShopBundle\DependencyInjection\RuntimeContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class RuntimeServiceLoader is able to dynamically load the services defined by a module during
 * runtime, which allows to use them even during installation process (usually only service of installed
 * modules are loaded).
 */
final class ModuleServicePreloader implements ModulePreloaderInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $modulesFolder;

    /**
     * @param ContainerInterface $container
     * @param string $modulesFolder
     */
    public function __construct(
        ContainerInterface $container,
        string $modulesFolder
    ) {
        $this->container = $container;
        $this->modulesFolder = $modulesFolder;
    }

    /**
     * {@inheritdoc}
     */
    public function preload(string $moduleName): void
    {
        // Load module services
        $serviceConfigPaths = [
            $this->modulesFolder . '/' . $moduleName . '/config/admin/',
            $this->modulesFolder . '/' . $moduleName . '/config/',
        ];

        $runtimeBuilder = new RuntimeContainerBuilder($this->container);
        foreach ($serviceConfigPaths as $serviceConfigPath) {
            if (file_exists($serviceConfigPath . 'services.yml')) {
                $loader = new YamlFileLoader($runtimeBuilder, new FileLocator($serviceConfigPath));
                $loader->load('services.yml');
            }
        }
        $runtimeBuilder->compile();
    }
}
