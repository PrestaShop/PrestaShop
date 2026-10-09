<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\DependencyInjection\Compiler;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
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
        $compilerPassList = self::getCompilerPassList(
            $container->getParameter('prestashop.installed_modules'),
            $container->getParameter('prestashop.module_dir')
        );

        foreach ($compilerPassList as $compilerResource => $compilerPass) {
            $compilerPass->process($container);
            /*
             * Because this method gets installed_modules and api_platform does only with active_modules
             * if src/Entity DirectoryResource is not added previously by PrestaShopExtension
             * add it here if required.
             *
             */
            $container->fileExists($compilerResource, '/\.(xml|ya?ml|php)$/');
        }
    }

    /**
     * Returns a list of DoctrineOrmMappingsPass instances, indexed by their associated /src/Entity directory resource.
     *
     * @param array $installedModules
     * @param string $modulesDir
     *
     * @return array<string, DoctrineOrmMappingsPass>
     */
    private function getCompilerPassList(array $installedModules, string $modulesDir): array
    {
        /*
         * Retrieves a list of paths to all `/src/Entity` directories found across installed modules.
         */
        $modulesDirectoriesWithEntities = Finder::create()->directories()->in($modulesDir)->depth(0)->filter(
            fn (SplFileInfo $moduleDirectoryWithEntity): bool => in_array($moduleDirectoryWithEntity->getFilename(), $installedModules) && is_dir($moduleDirectoryWithEntity->getRealPath() . '/src/Entity')
        );

        $mappingsPassList = [];

        foreach ($modulesDirectoriesWithEntities as $moduleDirectoryWithEntity) {
            $moduleEntityDirectory = $moduleDirectoryWithEntity->getRealPath() . '/src/Entity';

            /*
             * Recursively removes index.php files from '/src/Entity' directories.
             *
             * Note: `PrestaShopBundle\DependencyInjection\PrestaShopExtension::prependApiConfig()`
             * already handles the deletion of all index files.
             *
             */
            (new Filesystem())->remove(Finder::create()->files()->in($moduleEntityDirectory)->name('index.php'));

            $moduleNamespace = self::parseEntityDirectory($moduleEntityDirectory);

            if (empty($moduleNamespace)) {
                continue;
            }

            $mappingsPassList[$moduleEntityDirectory] = $moduleNamespace['has_attributes'] ?
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
     * @return array|array{namespace: string, has_attributes: bool}
     */
    private function parseEntityDirectory(string $moduleEntityDirectory): array
    {
        foreach ((new Finder())->files()->in($moduleEntityDirectory)->name('*.php') as $phpFile) {
            $content = $phpFile->getContents();

            if (preg_match('~namespace[ \t]+(.*)[ \t]*;~Um', $content, $matches)) {
                if (($namespace = trim($matches[1])) === '') {
                    continue;
                }

                $hasAttributes = str_contains($content, '#[ORM\\') || str_contains($content, '#[\\Doctrine\\ORM\\');

                // We strip the last part of the namespace to get the namespace matching with the entity folder
                // This is required in case you have sub-folders like src/Entity/Category/Category.php
                // The first matching PHP file would be in a sub namespace and be returned, thus the Entity
                // namespace would not be parsed completely
                if (($pos = strpos($namespace, '\\Entity')) !== false) {
                    return [
                        'namespace' => substr($namespace, 0, $pos + strlen('\\Entity')),
                        'has_attributes' => $hasAttributes,
                    ];
                }

                // Fallback: if for some reason there's no '\Entity', I'll use what was found anyway
                return [
                    'namespace' => $namespace,
                    'has_attributes' => $hasAttributes,
                ];
            }
        }

        return [];
    }
}
