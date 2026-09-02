<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Tools;

class AddOnsConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('prestashop');
        $rootNode = $treeBuilder->getRootNode();

        Tools::refreshCACertFile();

        $rootNode
            ->children()
            ->arrayNode('addons')
            ->children()
            ->arrayNode('categories')
            ->arrayPrototype()
            ->children()
            ->scalarNode('id_category')->isRequired()->end()
            ->scalarNode('name')->isRequired()->end()
            ->scalarNode('order')->isRequired()->end()
            ->scalarNode('link')->isRequired()->end()
            ->scalarNode('id_parent')->isRequired()->end()
            ->scalarNode('parent_link')->isRequired()->end()
            ->scalarNode('tab')->isRequired()->end()
            ->arrayNode('categories')
            ->arrayPrototype()
            ->children()
            ->scalarNode('id_category')->isRequired()->end()
            ->scalarNode('name')->isRequired()->end()
            ->scalarNode('link')->isRequired()->end()
            ->scalarNode('id_parent')->isRequired()->end()
            ->scalarNode('link_rewrite')->isRequired()->end()
            ->scalarNode('tab')->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->arrayNode('api_client')
            ->children()
            ->integerNode('ttl')
            ->defaultValue(0)
            ->end()
            ->scalarNode('verify_ssl')
            ->defaultValue(_PS_CACHE_CA_CERT_FILE_)
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
