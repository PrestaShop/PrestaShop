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
     * @var string
     */
    private $coreServicesPath;

    /**
     * @param ContainerInterface $container
     * @param string $modulesFolder
     * @param string $coreServicesPath
     */
    public function __construct(
        ContainerInterface $container,
        string $modulesFolder,
        string $coreServicesPath
    ) {
        $this->container = $container;
        $this->modulesFolder = $modulesFolder;
        $this->coreServicesPath = $coreServicesPath;
    }

    /**
     * {@inheritdoc}
     */
    public function preload(string $moduleName): void
    {
        $moduleConfigPaths = $this->getConfigPaths($moduleName);
        if (count($moduleConfigPaths) <= 0) {
            return;
        }

        $runtimeBuilder = new RuntimeContainerBuilder($this->container, [$this->coreServicesPath]);

        // Now load the module configurations
        foreach ($moduleConfigPaths as $moduleConfigPath) {
            $loader = new YamlFileLoader($runtimeBuilder, new FileLocator($moduleConfigPath));
            $loader->load($moduleConfigPath . '/services.yml');
        }

        $runtimeBuilder->compile();
    }

    /**
     * @param string $moduleName
     *
     * @return array
     */
    private function getConfigPaths(string $moduleName): array
    {
        $configPaths = [];
        $serviceConfigPaths = [
            $this->modulesFolder . '/' . $moduleName . '/config/admin/',
            $this->modulesFolder . '/' . $moduleName . '/config/',
        ];

        foreach ($serviceConfigPaths as $serviceConfigPath) {
            if (file_exists($serviceConfigPath . 'services.yml')) {
                $configPaths[] = $serviceConfigPath;
            }
        }

        return $configPaths;
    }
}
