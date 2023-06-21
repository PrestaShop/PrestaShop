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

namespace PrestaShopBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Aggregates and organizes all Commands & Queries, storing them in a container for future processing,
 * while simultaneously transforming custom tags into Symfony Messenger tags.
 */
class CommandAndQueryCollectorPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!in_array($container->getParameter('kernel.environment'), ['dev', 'test'])) {
            return;
        }

        $handlers = $container->findTaggedServiceIds('messenger.cqrs_handler');
        $commandsAndQueries = $this->findCommandsAndQueries($handlers);
        $this->updateMessengerTags($container, $handlers);

        $container->setParameter('prestashop.commands_and_queries', $commandsAndQueries);
    }

    /**
     * Gets command for each provided handler
     *
     * @return string[]
     */
    private function findCommandsAndQueries(array $handlers): array
    {
        $commands = [];
        foreach ($handlers as $handler) {
            if (isset(current($handler)['command'])) {
                $commands[] = current($handler)['command'];
            }
        }

        return $commands;
    }

    /**
     * update messenger tags allowing the recognition of handlers by symfony
     */
    private function updateMessengerTags(ContainerBuilder $container, array $handlers): void
    {
        foreach ($handlers as $key => $value) {
            $definition = $container->findDefinition($key);
            $definition->addTag('messenger.message_handler', ['method' => 'handle', 'handles' => current($value)['command']]);
            $definition->clearTag('messenger.cqrs_handler');
        }
    }
}
