<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\DependencyInjection\Compiler;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use RegexIterator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * This class is responsible for retrieving controllers in the modules that inherit from FrameworkBundleAdminController
 * and declare them as services, also adding the autoconfigure and autowire tags.
 *
 * This modification is necessary to initialize the globalContainer and thus be able to retrieve all services
 * via the $this->get function in the modules.
 */
class ModuleControllerRegisterPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $installedModules = $container->getParameter('prestashop.installed_modules');
        $moduleDir = $container->getParameter('prestashop.module_dir');

        foreach ($installedModules as $moduleName) {
            $fileIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($moduleDir . $moduleName));
            $phpFiles = new RegexIterator($fileIterator, '/\.php$/');

            foreach ($phpFiles as $file) {
                $className = $this->getFrameworkAdminControllerClassNameFromFile($file->getRealPath());

                if ($className !== null) {
                    $reflector = new ReflectionClass($className);

                    if ($reflector->isSubclassOf(FrameworkBundleAdminController::class)) {
                        $definition = $this->getServiceDefinition($container, $className);
                        $definition->addTag('controller.service_arguments');
                        $definition->setAutoconfigured(true);
                        $definition->setAutowired(true);
                    }
                }
            }
        }
    }

    private function getServiceDefinition(ContainerBuilder $container, $className): Definition
    {
        if ($container->has($className)) {
            return $container->getDefinition($className);
        }

        /** @var Definition $definition */
        foreach ($container->getDefinitions() as $definition) {
            if ($definition->getClass() === $className) {
                return $definition;
            }
        }

        return $container->register($className, $className);
    }

    private function getFrameworkAdminControllerClassNameFromFile(string $filePath): ?string
    {
        $contents = file_get_contents($filePath);
        $namespace = $className = '';

        if (preg_match('/namespace\s+(.+?);/s', $contents, $matches)) {
            $namespace = $matches[1];
        }

        if (preg_match('/class\s+(\w+)\s+extends\s+FrameworkBundleAdminController/', $contents, $matches)) {
            $className = $matches[1];
        }

        if (!empty($namespace) && !empty($className)) {
            $className = $namespace . '\\' . $className;

            if (class_exists($className)) {
                return $className;
            }
        }

        return null;
    }
}
