<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * On Symfony 3.x, the parameters like `translator.class` are not used anymore and cannot override the original services.
 * This made the translations unavailable in prod mode, and the module page was crashing.
 * This class replaces the symfony translator with PrestaShop's extended one when in prod mode.
 */
class OverrideTranslatorServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('translator.default');
        $definition->setClass($container->getParameter('translator.class'));

        if (!in_array($container->getParameter('kernel.environment'), ['dev', 'test'])) {
            return;
        }
        $definition = $container->getDefinition('translator.data_collector');
        $definition->setClass($container->getParameter('translator.data_collector'));
    }
}
