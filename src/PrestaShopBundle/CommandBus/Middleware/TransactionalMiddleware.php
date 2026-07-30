<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\CommandBus\Middleware;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\HandlerDescriptor;
use Symfony\Component\Messenger\Handler\HandlersLocatorInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

final class TransactionalMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly HandlersLocatorInterface $handlersLocator,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function handle(
        Envelope $envelope,
        StackInterface $stack
    ): Envelope {
        foreach ($this->handlersLocator->getHandlers($envelope) as $handler) {
            if ($this->isTransactional($handler)) {
                return $this->entityManager->wrapInTransaction(
                    fn () => $stack->next()->handle($envelope, $stack)
                );
            }
        }

        return $stack->next()->handle($envelope, $stack);
    }

    private function isTransactional(HandlerDescriptor $handler): bool
    {
        return $handler->getOptions()['transactional'] ?? false;
    }
}
