<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Tools;

class AddOnsConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('prestashop');

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
                        ->arrayNode('prestatrust')
                            ->children()
                                ->booleanNode('enabled')
                                    ->defaultFalse()
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
