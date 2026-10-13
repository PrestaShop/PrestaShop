<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\DependencyInjection\Compiler;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
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
        $compilerPassList = $this->getCompilerPassList(
            $container->getParameter('prestashop.installed_modules'),
            $container->getParameter('prestashop.module_dir')
        );
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
     * @param array $installedModules
     *
     * @return array
     */
    private function getCompilerPassList(array $installedModules, string $moduleDir)
    {
        $modulesDirectories = $this->getModulesFolders($moduleDir);

        /*
         * Filters directories to include only installed modules containing a `/src/Entity` directory.
         */
        $modulesDirectories->filter(
            fn (SplFileInfo $moduleDirectoryWithEntity): bool => in_array($moduleDirectoryWithEntity->getFilename(), $installedModules) && is_dir($moduleDirectoryWithEntity->getRealPath() . '/src/Entity')
        );

        $mappingsPassList = [];

        foreach ($modulesDirectories as $moduleDirectory) {
            $moduleEntityDirectory = realpath($moduleDirectory->getRealPath() . '/src/Entity');
            if ($moduleEntityDirectory === false) {
                continue;
            }
            $moduleNamespace = $this->getModuleNamespace($moduleEntityDirectory);
            if (empty($moduleNamespace)) {
                continue;
            }
            $mappingPass = $this->createAnnotationMappingDriver($moduleNamespace, $moduleEntityDirectory);
            $mappingsPassList[$moduleEntityDirectory] = $mappingPass;
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
     *
     * @return DoctrineOrmMappingsPass
     */
    private function createAnnotationMappingDriver($moduleNamespace, $moduleEntityDirectory)
    {
        $reader = new Reference('annotation_reader');
        $driverDefinition = new Definition('Doctrine\ORM\Mapping\Driver\AnnotationDriver', [$reader, [$moduleEntityDirectory]]);
        $indexFile = $moduleEntityDirectory . '/index.php';
        if (file_exists($indexFile)) {
            $driverDefinition->addMethodCall('addExcludePaths', [[$indexFile]]);
        }

        return new DoctrineOrmMappingsPass($driverDefinition, [$moduleNamespace], [], false, []);
    }

    /**
     * @param string $moduleEntityDirectory
     *
     * @return string
     */
    private function getModuleNamespace(string $moduleEntityDirectory)
    {
        $finder = new Finder();
        $finder->files()->in($moduleEntityDirectory)->name('*.php');

        foreach ($finder as $phpFile) {
            if (preg_match('~namespace[ \t]+(.*)[ \t]*;~Um', $phpFile->getContents(), $matches)) {
                if (($namespace = trim($matches[1])) === '') {
                    continue;
                }

                // We strip the last part of the namespace to get the namespace matching with the entity folder
                // This is required in case you have sub-folders like src/Entity/Category/Category.php
                // The first matching PHP file would be in a sub namespace and be returned, thus the Entity
                // namespace would not be parsed completely
                if (($pos = strpos($namespace, '\\Entity')) !== false) {
                    return substr($namespace, 0, $pos + strlen('\\Entity'));
                }

                // Fallback: if for some reason there's no '\Entity', I'll use what was found anyway
                return $namespace;
            }
        }

        return '';
    }

    /**
     * Returns a list of installed modules.
     *
     * @return Finder
     */
    private function getModulesFolders(string $moduleDir): Finder
    {
        return Finder::create()->directories()->in($moduleDir)->depth(0);
    }
}
