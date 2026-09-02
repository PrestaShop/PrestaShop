<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Container;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Interface ContainerBuilderExtensionInterface is used to externalize some container
 * building actions from the PrestaShop\PrestaShop\Adapter\ContainerBuilder (register
 * an extension, init some parameters).
 *
 * This builder extension system needs to be used for actions that can't be performed in a
 * CompilerPassInterface due to the compilation workflow (some actions MUST be done before
 * the compilation stars, this is where this system comes in handy).
 */
interface ContainerBuilderExtensionInterface
{
    /**
     * This method is called by the ContainerBuilder before compiling the container. This is where you
     * can add extension, compiler pass, or parameters to the container.
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container);
}
