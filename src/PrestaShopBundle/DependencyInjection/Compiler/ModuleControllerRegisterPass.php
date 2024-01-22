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
        $activeModules = $container->getParameter('prestashop.active_modules');
        $moduleDir = $container->getParameter('prestashop.module_dir');

        foreach ($activeModules as $moduleName) {
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
