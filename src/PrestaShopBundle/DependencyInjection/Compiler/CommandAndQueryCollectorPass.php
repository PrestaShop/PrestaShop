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

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use ReflectionAttribute;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Aggregates and organizes all Commands & Queries and storing them in a container for future processing
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

        $commandsAndQueries = $this->findCommandsAndQueries($container);
        $container->setParameter('prestashop.commands_and_queries', $commandsAndQueries);
    }

    /**
     * Gets command for each provided handler
     *
     * @return string[]
     */
    private function findCommandsAndQueries(ContainerBuilder $container): array
    {
        $handlers = $container->findTaggedServiceIds('messenger.message_handler');
        $commands = [];
        foreach ($handlers as $key => $value) {
            if (count(current($value)) == 0) {
                continue;
            }

            $className = $container->getDefinition($key)->getClass();
            $handlerAttributes = $this->getHandlerAttributes($className);
            $this->processHandlerAttributes($handlerAttributes, $value, $commands);
        }

        return $commands;
    }

    /**
     * Get the attributes of a message handler using reflection.
     *
     * @return ReflectionAttribute[]
     */
    private function getHandlerAttributes(string $handlerClassName): array
    {
        $handler = new ReflectionClass($handlerClassName);

        return $handler->getAttributes();
    }

    /**
     * Process the handler attributes and add commands and queries to the result.
     *
     * @param ReflectionAttribute[] $handlerAttributes
     * @param array $value
     * @param string[] $commands
     *
     * @return void
     */
    private function processHandlerAttributes(array $handlerAttributes, array $value, array &$commands): void
    {
        foreach ($handlerAttributes as $handlerAttribute) {
            $isCommandHandler = $handlerAttribute->getName() === AsCommandHandler::class;
            $isQueryHandler = $handlerAttribute->getName() === AsQueryHandler::class;

            if (($isCommandHandler || $isQueryHandler) && isset(current($value)['handles'])) {
                $commands[] = current($value)['handles'];
            }
        }
    }
}
