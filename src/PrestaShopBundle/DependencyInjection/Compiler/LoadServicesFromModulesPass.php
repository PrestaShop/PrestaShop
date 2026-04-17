<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\DependencyInjection\Compiler;

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
     * Used to identify which scope of services need to be loaded (front services, admin
     * services or generic ones)
     *
     * @param string $containerName
     */
    public function __construct($containerName = '')
    {
        $this->configPath = '/config/' . (empty($containerName) ? '' : trim($containerName, '/') . '/');
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $installedModules = $container->getParameter('prestashop.installed_modules');
        $moduleDir = $container->getParameter('prestashop.module_dir');

        foreach ($installedModules as $moduleName) {
            $modulePath = $moduleDir . $moduleName;
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
    }
}
