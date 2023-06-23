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
use ReflectionClass;
use RuntimeException;
use Symfony\Component\DependencyInjection\ChildDefinition;
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
        $this->registerMessengerTags($container);

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
        $handlers = $container->findTaggedServiceIds('messenger.cqrs_handler');
        $commands = [];
        foreach ($handlers as $handler) {
            if (isset(current($handler)['command'])) {
                $commands[] = current($handler)['command'];
            }
        }

        return $commands;
    }

    /**
     * register messenger tags allowing the recognition of handlers by symfony
     */
    private function registerMessengerTags(ContainerBuilder $container): void
    {
        $container->registerAttributeForAutoconfiguration(AsCommandHandler::class, static function (ChildDefinition $definition, AsCommandHandler $attribute, ReflectionClass $reflector): void {
            $definition->addTag('messenger.message_handler', ['method' => $attribute->method, 'handles' => self::guessHandledClasses($reflector, $attribute->method)]);
        });

        $container->registerAttributeForAutoconfiguration(AsQueryHandler::class, static function (ChildDefinition $definition, AsQueryHandler $attribute, ReflectionClass $reflector): void {
            $definition->addTag('messenger.message_handler', ['method' => $attribute->method, 'handles' => self::guessHandledClasses($reflector, $attribute->method)]);
        });
    }

    private static function guessHandledClasses(ReflectionClass $class, string $method): ?string
    {
        $reflectionMethod = $class->getMethod($method);
        $parameters = $reflectionMethod->getParameters();

        if (count($parameters) != 1) {
            throw new RuntimeException(sprintf('Invalid handler service "%s": number of argument "$%s" in method "%s" must be 1 , "%s" given.', $class->getName(), $parameters[0]->getName(), $method, count($parameters)));
        }

        $firstParameter = $parameters[0];

        return $firstParameter->getType()->getName();
    }
}
