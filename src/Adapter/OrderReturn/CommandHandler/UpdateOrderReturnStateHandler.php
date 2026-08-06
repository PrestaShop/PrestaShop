<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderReturn\CommandHandler;

use Configuration;
use Customer;
use Language;
use Mail;
use Order;
use OrderReturn;
use OrderReturnState;
use PrestaShop\PrestaShop\Adapter\OrderReturn\Repository\OrderReturnRepository;
use PrestaShop\PrestaShop\Adapter\OrderReturnState\Repository\OrderReturnStateRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command\UpdateOrderReturnStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\CommandHandler\UpdateOrderReturnStateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Validate;

#[AsCommandHandler]
class UpdateOrderReturnStateHandler implements UpdateOrderReturnStateHandlerInterface
{
    /**
     * @var OrderReturnRepository
     */
    private $orderReturnRepository;

    /**
     * @var OrderReturnStateRepository
     */
    private $orderReturnStateRepository;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * UpdateOrderReturnStateHandler constructor.
     *
     * @param OrderReturnRepository $orderReturnRepository
     * @param OrderReturnStateRepository $orderReturnStateRepository
     * @param TranslatorInterface $translator
     */
    public function __construct(
        OrderReturnRepository $orderReturnRepository,
        OrderReturnStateRepository $orderReturnStateRepository,
        TranslatorInterface $translator
    ) {
        $this->orderReturnRepository = $orderReturnRepository;
        $this->orderReturnStateRepository = $orderReturnStateRepository;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateOrderReturnStateCommand $command): void
    {
        $orderReturn = $this->orderReturnRepository->get($command->getOrderReturnId());
        $previousState = (int) $orderReturn->state;
        $orderReturn = $this->updateOrderReturnWithCommandData($orderReturn, $command);

        $this->orderReturnRepository->update($orderReturn);

        // Notify the customer when the return status actually changes, mirroring the legacy
        // AdminReturnController behaviour (the migration had dropped this email).
        if ((int) $orderReturn->state !== $previousState) {
            $this->sendStateChangeEmail($orderReturn);
        }
    }

    /**
     * Sends the "order return status has changed" email to the customer, in the order's language
     * and shop context.
     *
     * @param OrderReturn $orderReturn
     */
    private function sendStateChangeEmail(OrderReturn $orderReturn): void
    {
        $order = new Order((int) $orderReturn->id_order);
        $customer = new Customer((int) $orderReturn->id_customer);

        if (!Validate::isLoadedObject($order) || !Validate::isLoadedObject($customer)) {
            return;
        }

        $orderReturnState = new OrderReturnState((int) $orderReturn->state);
        $orderLanguage = new Language((int) $order->id_lang);

        $stateName = $orderReturnState->name[(int) $order->id_lang]
            ?? $orderReturnState->name[(int) Configuration::get('PS_LANG_DEFAULT')]
            ?? '';

        Mail::Send(
            (int) $order->id_lang,
            'order_return_state',
            $this->translator->trans(
                'Your order return status has changed',
                [],
                'Emails.Subject',
                $orderLanguage->locale
            ),
            [
                '{lastname}' => $customer->lastname,
                '{firstname}' => $customer->firstname,
                '{id_order_return}' => (int) $orderReturn->id,
                '{state_order_return}' => $stateName,
            ],
            $customer->email,
            $customer->firstname . ' ' . $customer->lastname,
            null,
            null,
            null,
            null,
            _PS_MAIL_DIR_,
            true,
            (int) $order->id_shop
        );
    }

    /**
     * @param OrderReturn $orderReturn
     * @param UpdateOrderReturnStateCommand $command
     *
     * @return OrderReturn
     *
     * @throws OrderReturnException
     */
    private function updateOrderReturnWithCommandData(OrderReturn $orderReturn, UpdateOrderReturnStateCommand $command): OrderReturn
    {
        $orderReturnState = $this->orderReturnStateRepository->get($command->getOrderReturnStateId());
        $orderReturn->state = $orderReturnState->id;

        return $orderReturn;
    }
}
