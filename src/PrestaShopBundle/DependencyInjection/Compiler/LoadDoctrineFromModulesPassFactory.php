<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\DependencyInjection\Compiler;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\Common\Util\Inflector;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Load services stored in installed modules.
 */
class LoadDoctrineFromModulesPassFactory
{
    /**
     * @param array $installedModules
     *
     * @return CompilerPassInterface[]
     */
    public function buildCompilerPassList(array $installedModules)
    {
        return $this->buildAnnotationMappingsPass($installedModules);
    }

    private function buildAnnotationMappingsPass(array $installedModules)
    {
        $mappingsPassList = [];
        /** @var SplFileInfo $modulePath */
        foreach ($this->getModulesPaths() as $modulePath) {
            if (in_array($modulePath->getFilename(), $installedModules)
                && file_exists($modulePath . '/src/Entity')
            ) {
                $moduleNamespace = $this->getModuleNamespace($modulePath);
                if (empty($moduleNamespace)) {
                    continue;
                }
                $modulePrefix = 'Module'.Inflector::camelize($modulePath->getFilename());
                $moduleEntityDirectory = realpath($modulePath . '/src/Entity');
                $mappingPass = DoctrineOrmMappingsPass::createAnnotationMappingDriver([$moduleNamespace], [$moduleEntityDirectory], [], false, [$modulePrefix => $moduleNamespace]);
                $mappingsPassList[] = $mappingPass;
            }
        }

        return $mappingsPassList;
    }

    /**
     * @param SplFileInfo $modulePath
     *
     * @return string
     */
    private function getModuleNamespace(SplFileInfo $modulePath)
    {
        $finder = new Finder();
        $finder->files()->in($modulePath->getRealPath() . DIRECTORY_SEPARATOR . 'src/Entity')->name('*.php');
        foreach ($finder as $phpFile) {
            $phpContent = file_get_contents($phpFile->getRealPath());
            if (false !== preg_match('~namespace[ \t]+(.+)[ \t]*;~Um', $phpContent, $matches)) {
                return $matches[1];
            }
        }

        return '';
    }

    /**
     * @return Finder
     */
    private function getModulesPaths()
    {
        return Finder::create()->directories()->in(__DIR__ . '/../../../../modules')->depth(0);
    }
}
