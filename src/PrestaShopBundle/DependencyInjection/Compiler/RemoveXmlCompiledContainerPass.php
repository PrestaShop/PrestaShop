<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Security Compiler pass: removed app{env}{..}.xml files from cache.
 */
class RemoveXmlCompiledContainerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->getParameter('kernel.debug')) {
            $filename = $container->getParameter('debug.container.dump');
            $filesystem = new Filesystem();

            try {
                $filesystem->remove($filename);
            } catch (IOException) {
                // discard chmod failure (some filesystem may not support it)
            }
        }
    }
}
