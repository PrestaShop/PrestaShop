<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\DependencyInjection\Compiler;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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
            /*
             * If the src/Entity directory has not already been registered as a resource by PrestaShopExtension,
             * register them here.
             */
            $container->fileExists($compilerResourcePath, '/\.(xml|ya?ml|php)$/');
        }
    }

    /**
     * Returns a list of CompilerPassInterface indexed with their associated resource.
     *
     * @param array $installedModules
     *
     * @return array<string, DoctrineOrmMappingsPass>
     */
    private function getCompilerPassList(array $installedModules, string $moduleDir): array
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
            $mappingsPassList[$moduleEntityDirectory] = $moduleNamespace['uses_attributes'] ?
                DoctrineOrmMappingsPass::createAttributeMappingDriver(
                    [$moduleNamespace['namespace']],
                    [$moduleEntityDirectory]
                ) :
                DoctrineOrmMappingsPass::createAnnotationMappingDriver(
                    [$moduleNamespace['namespace']],
                    [$moduleEntityDirectory]
                );
        }

        return $mappingsPassList;
    }

    /**
     * @param string $moduleEntityDirectory
     *
     * @return array|array{namespace: string, uses_attributes: bool}
     */
    private function getModuleNamespace(string $moduleEntityDirectory): array
    {
        $finder = new Finder();
        $finder->files()->in($moduleEntityDirectory)->name('*.php');

        foreach ($finder as $phpFile) {
            $fileContents = $phpFile->getContents();
            if (preg_match('~namespace[ \t]+(.*)[ \t]*;~Um', $fileContents, $matches)) {
                if (($namespace = trim($matches[1])) === '') {
                    continue;
                }

                $usesAttributes = false;

                foreach (token_get_all($fileContents) as $token) {
                    if (is_array($token) && $token[0] === T_ATTRIBUTE) {
                        $usesAttributes = true;
                        break;
                    }
                }

                // We strip the last part of the namespace to get the namespace matching with the entity folder
                // This is required in case you have sub-folders like src/Entity/Category/Category.php
                // The first matching PHP file would be in a sub namespace and be returned, thus the Entity
                // namespace would not be parsed completely
                if (($pos = strpos($namespace, '\\Entity')) !== false) {
                    return [
                        'namespace' => substr($namespace, 0, $pos + strlen('\\Entity')),
                        'uses_attributes' => $usesAttributes,
                    ];
                }

                // Fallback: if for some reason there's no '\Entity', I'll use what was found anyway
                return [
                    'namespace' => $namespace,
                    'uses_attributes' => $usesAttributes,
                ];
            }
        }

        return [];
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
