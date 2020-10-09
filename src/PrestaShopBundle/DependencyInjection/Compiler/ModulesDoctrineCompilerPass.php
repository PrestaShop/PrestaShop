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

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\Common\Util\Inflector;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Load services stored in installed modules.
 */
class ModulesDoctrineCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        //We need the list of active modules to load their config, during install the parameter might no be available
        //if the parameters file has not been generated yet, so we skip this part of the build
        if (!$container->hasParameter('kernel.active_modules')) {
            return;
        }

        $activeModules = $container->getParameter('kernel.active_modules');
        $compilerPassList = $this->getCompilerPassList($activeModules);
        /** @var CompilerPassInterface $compilerPass */
        foreach ($compilerPassList as $compilerResourcePath => $compilerPass) {
            $compilerPass->process($container);
            $container->addResource(
                is_dir($compilerResourcePath) ?
                new DirectoryResource($compilerResourcePath) :
                new FileResource($compilerResourcePath)
            );
        }
    }

    /**
     * Returns a list of CompilerPassInterface indexed with their associated resource.
     *
     * @param array $activeModules
     *
     * @return array
     */
    private function getCompilerPassList(array $activeModules)
    {
        $mappingsPassList = [];
        /** @var SplFileInfo $moduleFolder */
        foreach ($this->getModulesFolders() as $moduleFolder) {
            if (in_array($moduleFolder->getFilename(), $activeModules)
                && is_dir($moduleFolder . '/src/Entity')
            ) {
                $moduleNamespace = $this->getModuleNamespace($moduleFolder);
                if (empty($moduleNamespace)) {
                    continue;
                }
                $modulePrefix = 'Module' . Inflector::camelize($moduleFolder->getFilename());
                $moduleEntityDirectory = realpath($moduleFolder . '/src/Entity');
                $mappingPass = $this->createAnnotationMappingDriver($moduleNamespace, $moduleEntityDirectory, $modulePrefix);
                $mappingsPassList[$moduleEntityDirectory] = $mappingPass;
            }
        }

        return $mappingsPassList;
    }

    /**
     * This method is derived from DoctrineOrmMappingsPass::createAnnotationMappingDriver, sadly the driver includes
     * ALL the files present in the folder and as modules include an index.php file containing an exit statement the
     * whole process was stopped. So we manually create the DoctrineOrmMappingsPass so that AnnotationDriver ignores
     * the index.php file.
     *
     * @param string $moduleNamespace
     * @param string $moduleEntityDirectory
     * @param string $modulePrefix
     *
     * @return DoctrineOrmMappingsPass
     */
    private function createAnnotationMappingDriver($moduleNamespace, $moduleEntityDirectory, $modulePrefix)
    {
        $reader = new Reference('annotation_reader');
        $driverDefinition = new Definition('Doctrine\ORM\Mapping\Driver\AnnotationDriver', [$reader, [$moduleEntityDirectory]]);
        $indexFile = $moduleEntityDirectory . '/index.php';
        if (file_exists($indexFile)) {
            $driverDefinition->addMethodCall('addExcludePaths', [[$indexFile]]);
        }

        return new DoctrineOrmMappingsPass($driverDefinition, [$moduleNamespace], [], false, [$modulePrefix => $moduleNamespace]);
    }

    /**
     * @param SplFileInfo $moduleFolder
     *
     * @return string
     */
    private function getModuleNamespace(SplFileInfo $moduleFolder)
    {
        $finder = new Finder();
        $finder->files()->in($moduleFolder->getRealPath() . '/src/Entity')->name('*.php');
        foreach ($finder as $phpFile) {
            $phpContent = file_get_contents($phpFile->getRealPath());
            if (preg_match('~namespace[ \t]+(.+)[ \t]*;~Um', $phpContent, $matches)) {
                return $matches[1];
            }
        }

        return '';
    }

    /**
     * @return Finder
     */
    private function getModulesFolders()
    {
        return Finder::create()->directories()->in(_PS_MODULE_DIR_)->depth(0);
    }
}
