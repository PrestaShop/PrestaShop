<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\DependencyInjection\Compiler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\AbstractBulkCommandHandler;
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
        $commandsAndQueries = $this->findCommandsAndQueries($container);
        $container->setParameter('prestashop.commands_and_queries', $commandsAndQueries);
        $container->setParameter('prestashop.bulk_commands', $this->findBulkCommands($container));
    }

    /**
     * Gets the commands handled by a bulk handler, they can partially fail (one error per failed item).
     *
     * @return string[]
     */
    private function findBulkCommands(ContainerBuilder $container): array
    {
        $bulkCommands = [];
        foreach ($container->findTaggedServiceIds('messenger.message_handler') as $serviceId => $tags) {
            $handledCommand = current($tags)['handles'] ?? null;
            $className = $container->getDefinition($serviceId)->getClass();
            if (null !== $handledCommand && null !== $className && is_subclass_of($className, AbstractBulkCommandHandler::class)) {
                $bulkCommands[] = $handledCommand;
            }
        }

        return $bulkCommands;
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
