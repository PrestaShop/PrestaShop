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

namespace PrestaShop\PrestaShop\Adapter\Container;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\DoctrineExtension;
use PrestaShop\PrestaShop\Core\Util\File\YamlParser;
use PrestaShopBundle\DependencyInjection\Compiler\ModulesDoctrineCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DoctrineBuilderExtension is used to init the doctrine service in the ContainerBuilder.
 * This is a manual initialisation of Doctrine because we are not in a symfony context, so we need
 * add the extension manually (required parameters are managed by ContainerParametersExtension).
 *
 * Note: this can't be done as a CompilerPassInterface because extensions need to be registered before compilation.
 */
class DoctrineBuilderExtension implements ContainerBuilderExtensionInterface
{
    /** @var string */
    private $cacheDir;

    /**
     * @param string $cacheDir
     */
    public function __construct($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $yamlParser = new YamlParser($this->cacheDir);

        $config = $yamlParser->parse(_PS_ROOT_DIR_ . '/app/config/config.yml');
        $container->registerExtension(new DoctrineExtension());
        $container->loadFromExtension('doctrine', $config['doctrine']);
        $container->addCompilerPass(new ModulesDoctrineCompilerPass());
    }
}
