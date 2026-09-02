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
 * Load every flagged Translation providers.
 */
class PopulateTranslationProvidersPass implements CompilerPassInterface
{
    public const DEFINITION = 'prestashop.translation.translations_factory';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::DEFINITION)) {
            return;
        }

        $definition = $container->findDefinition(self::DEFINITION);
        $taggedServices = $container->findTaggedServiceIds('ps.translation_provider');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addProvider', [new Reference($id)]);
        }
    }
}
