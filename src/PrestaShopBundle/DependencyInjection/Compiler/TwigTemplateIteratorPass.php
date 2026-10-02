<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\DependencyInjection\Compiler;

use PrestaShopBundle\Twig\TemplateIterator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Replaces the templates list used by the Twig cache warmer, see TemplateIterator.
 */
class TwigTemplateIteratorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('twig.template_iterator')) {
            return;
        }

        $container->getDefinition('twig.template_iterator')->setClass(TemplateIterator::class);
    }
}
