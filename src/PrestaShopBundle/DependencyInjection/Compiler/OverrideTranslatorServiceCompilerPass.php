<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

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
        // Feed the extra property registry wordings into the back-office translator so their domains
        // exist in its catalogue (makes Module::isUsingNewTranslationSystem() detect a module whose
        // only new-system wordings come from extra properties).
        $definition->addMethodCall('setExtraPropertyTranslationLoader', [new Reference('prestashop.translation.loader.extra_property')]);

        if (!in_array($container->getParameter('kernel.environment'), ['dev', 'test'])) {
            return;
        }
        $definition = $container->getDefinition('translator.data_collector');
        $definition->setClass($container->getParameter('translator.data_collector'));
    }
}
