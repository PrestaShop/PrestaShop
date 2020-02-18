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

namespace PrestaShopBundle\Service\Database;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use PrestaShopBundle\Service\Database\Exception\EntityFolderNotFoundException;
use PrestaShopBundle\Service\Database\Exception\EntityNamespaceNotFoundException;
use Symfony\Component\Finder\Finder;

/**
 * Class DoctrineEntityMapper is able to add entity mapping based on the folder
 * during runtime. It automatically scans the entity folder (annotation only for
 * now) and adds them to the doctrine mapping.
 */
class DoctrineRuntimeEntityMapper
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var MappingDriverChain
     */
    private $mappingDriverChain;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param Reader $reader
     * @param MappingDriverChain $mappingDriverChain
     * @param Configuration $configuration
     */
    public function __construct(
        Reader $reader,
        MappingDriverChain $mappingDriverChain,
        Configuration $configuration
    ) {
        $this->reader = $reader;
        $this->mappingDriverChain = $mappingDriverChain;
        $this->configuration = $configuration;
    }

    /**
     * @param string $entityFolder
     * @param string $entityAlias
     *
     * @throws EntityFolderNotFoundException
     * @throws EntityNamespaceNotFoundException
     */
    public function addDoctrineMapping(string $entityFolder, $entityAlias = '')
    {
        if (!is_dir($entityFolder)) {
            throw new EntityFolderNotFoundException(sprintf(
                'Cannot find entity folder %s',
                $entityFolder
            ));
        }

        $entityNamespace = $this->getEntityNamespace($entityFolder);
        $driver = $this->createAnnotationDriver($entityFolder);
        $this->mappingDriverChain->addDriver($driver, $entityNamespace);
        if (!empty($entityAlias)) {
            $this->configuration->addEntityNamespace($entityAlias, $entityFolder);
        }
    }

    /**
     * This method is derived from DoctrineOrmMappingsPass::createAnnotationMappingDriver, except we only focus on
     * creating the driver here. And (as in ModulesDoctrineCompilerPass) we make sure that the driver will ignore
     * the index.php file that may be present in the entity folder.
     *
     * @param $entityFolder
     *
     * @return AnnotationDriver
     */
    private function createAnnotationDriver($entityFolder)
    {
        $driver = new AnnotationDriver($this->reader, [$entityFolder]);
        $indexFile = $entityFolder . '/index.php';
        if (file_exists($indexFile)) {
            $driver->addExcludePaths([$indexFile]);
        }

        return $driver;
    }

    /**
     * @param string $entityFolder
     *
     * @return string
     *
     * @throws EntityNamespaceNotFoundException
     */
    private function getEntityNamespace(string $entityFolder)
    {
        $finder = new Finder();
        $finder->files()->in($entityFolder)->name('*.php');
        foreach ($finder as $phpFile) {
            $phpContent = file_get_contents($phpFile->getRealPath());
            if (preg_match('~namespace[ \t]+(.+)[ \t]*;~Um', $phpContent, $matches)) {
                return $matches[1];
            }
        }

        throw new EntityNamespaceNotFoundException(sprintf(
            'Cannot find namespace in folder %s',
            $entityFolder
        ));
    }
}
