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

namespace PrestaShop\PrestaShop\Core\CommandBus;

use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\LogicException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * Class MessengerCommandBusAdapter is the Symfony Messenger CommandsBus implementation for PrestaShop's contract.
 */
final class MessengerCommandBusAdapter implements CommandBusInterface
{
    public function __construct(private readonly MessageBusInterface $commandBus)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function handle($command)
    {
        try {
            $envelope = $this->commandBus->dispatch($command);
            /** @var HandledStamp[] $handledStamps */
            $handledStamps = $envelope->all(HandledStamp::class);

            if (!$handledStamps) {
                throw new LogicException(sprintf('Message of type "%s" was handled zero times. Exactly one handler is expected when using "%s::%s()".', get_debug_type($envelope->getMessage()), MessengerCommandBusAdapter::class, __FUNCTION__));
            }

            if (\count($handledStamps) > 1) {
                $handlers = implode(', ', array_map(function (HandledStamp $stamp): string {
                    return sprintf('"%s"', $stamp->getHandlerName());
                }, $handledStamps));

                throw new LogicException(sprintf('Message of type "%s" was handled multiple times. Only one handler is expected when using "%s::%s()", got %d: %s.', get_debug_type($envelope->getMessage()), MessengerCommandBusAdapter::class, __FUNCTION__, \count($handledStamps), $handlers));
            }

            return $handledStamps[0]->getResult();
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
