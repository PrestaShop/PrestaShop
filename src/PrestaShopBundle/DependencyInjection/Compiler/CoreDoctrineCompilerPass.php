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
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Load doctrine entities from the core.
 */
class CoreDoctrineCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $coreEntitiesFolder = $container->getParameter('kernel.root_dir') . '../src/PrestaShopBundle/Entity';
        $coreEntitiesNamespace = 'PrestaShopBundle\Entity';
        $corePrefix = 'PrestaShop';
        $compilerPass = $this->createAnnotationMappingDriver($coreEntitiesNamespace, $coreEntitiesFolder, $corePrefix);

        $compilerPass->process($container);
        $container->addResource(new DirectoryResource($coreEntitiesFolder));
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
}
