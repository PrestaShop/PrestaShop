<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\CommandBus\Middleware;

use PrestaShop\PrestaShop\Core\CommandBus\ExecutedCommandRegistry;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\HandlersLocatorInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

/**
 * Registers every command that was executed in system
 */
final class CommandRegisterMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly HandlersLocatorInterface $handlersLocator,
        private readonly ExecutedCommandRegistry $executedCommandRegistry
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $command = $envelope->getMessage();
        $handlers = $this->handlersLocator->getHandlers($envelope);

        foreach ($handlers as $handler) {
            $this->executedCommandRegistry->register($command, $handler);
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
