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
use PrestaShop\PrestaShop\Adapter\Configuration;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class LegacyCompilerPass implements CompilerPassInterface
{
    /**
     * Add legacy services that need to be built using Context::getContext().
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->buildDefinitions([
            'configuration' => Configuration::class,
            'context' => [Context::class, 'getContext'],
            'db' => [Db::class, 'getInstance'],
        ], $container);

        $this->buildSyntheticDefinitions(['shop' => Shop::class, 'employee' => Employee::class], $container);
    }

    private function buildDefinitions(array $keys, ContainerBuilder $container): void
    {
        foreach ($keys as $key => $class) {
            if (is_array($class)) {
                $definition = new Definition($class[0]);
                $definition->setFactory($class);
            } else {
                $definition = new Definition($class);
            }
            $container->setDefinition($key, $definition);
        }
    }

    private function buildSyntheticDefinitions(array $keys, ContainerBuilder $container): void
    {
        foreach ($keys as $key => $class) {
            $definition = new Definition($class);
            $definition->setSynthetic(true);
            $container->setDefinition($key, $definition);
        }
    }
}
