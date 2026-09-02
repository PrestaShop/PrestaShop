<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\CommandBus;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Class MessengerCommandBus is the Symfony Messenger CommandsBus implementation for PrestaShop's contract.
 */
final class MessengerCommandBus implements CommandBusInterface
{
    use HandleTrait {
        HandleTrait::handle as process;
    }

    public function __construct(MessageBusInterface $messageBus)
    {
        // used in HandleTrait
        $this->messageBus = $messageBus;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($command)
    {
        try {
            return $this->process($command);
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
