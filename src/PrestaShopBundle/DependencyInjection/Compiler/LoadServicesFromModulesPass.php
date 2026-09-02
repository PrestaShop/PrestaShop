<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\DependencyInjection\Compiler;

use PrestaShop\PrestaShop\Core\Version;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
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
        $servicesFilesList = [
            'services.php',
            sprintf('services-%d.%d.yml', Version::MAJOR_VERSION, Version::MINOR_VERSION),
            sprintf('services-%d.yml', Version::MAJOR_VERSION),
            'services.yml',
        ];

        foreach ($installedModules as $moduleName) {
            $modulePath = $moduleDir . $moduleName;
            $moduleConfigPath = $modulePath . $this->configPath;
            $fileLocator = new FileLocator($moduleConfigPath);
            $resolver = new LoaderResolver([
                new PhpFileLoader($container, $fileLocator),
                new XmlFileLoader($container, $fileLocator),
                new YamlFileLoader($container, $fileLocator),
            ]);
            $loader = new DelegatingLoader($resolver);

            foreach ($servicesFilesList as $servicesFile) {
                if (!is_file($moduleConfigPath . $servicesFile)) {
                    continue;
                }

                $loader->load($servicesFile);
                // Prevent loading less specific services files if one was found
                break;
            }
        }
    }
}
