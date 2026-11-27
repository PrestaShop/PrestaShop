<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
use PrestaShop\PrestaShop\Adapter\Presenter\Order\OrderPresenter;
use PrestaShop\PrestaShop\Adapter\WithdrawalRequest\WithdrawalEligibilityChecker;
use PrestaShop\PrestaShop\Adapter\WithdrawalRequest\WithdrawalRequestSubmitter;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Command\SubmitWithdrawalRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Exception\OrderNotEligibleForWithdrawalException;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Exception\WithdrawalRequestConstraintException;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Exception\WithdrawalRequestException;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\QueryResult\OrderWithdrawalEligibility;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\ValueObject\WithdrawalIneligibilityReason;

/**
 * Withdrawal function (Directive 2011/83/EU, article 11a, as amended by Directive (EU) 2023/2673).
 *
 * Lets a consumer withdraw from an order online, in two steps:
 *  1. the "Withdraw from contract here" button (on the account, the order details, the guest tracking page and this
 *     page) opens the online withdrawal statement, prefilled with the name, the order and the email the
 *     acknowledgement of receipt is sent to;
 *  2. the "Confirm withdrawal" button submits it.
 *
 * Logged in customers pick one of their orders. Guests, and customers who do not want to log in, identify the order
 * with its reference and the email used to place it, like on the guest tracking page: the withdrawal function must
 * stay easily accessible, an account cannot be required.
 */
class WithdrawalControllerCore extends FrontController
{
    /** @var bool */
    public $ssl = true;

    /** @var bool */
    public $auth = false;

    /** @var string */
    public $php_self = 'withdrawal';

    /** @var Order|null Order the statement is about, once the visitor proved they can act on it */
    protected $order;

    /** @var OrderWithdrawalEligibility|null */
    protected $eligibility;

    /** @var array<string, string> Values of the statement form */
    protected $statement = [];

    /** @var array<string, string>|null Submitted statement, once recorded */
    protected $submittedStatement;

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        if (!Configuration::get('PS_WITHDRAWAL_FUNCTION') || Configuration::isCatalogMode()) {
            $this->redirect_after = '404';
            $this->redirect();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function postProcess(): void
    {
        $this->loadOrder();
        if (!Validate::isLoadedObject($this->order)) {
            return;
        }

        $this->eligibility = $this->get(WithdrawalEligibilityChecker::class)->check(
            new OrderId((int) $this->order->id),
            new DateTimeImmutable()
        );

        $customer = new Customer((int) $this->order->id_customer);
        $this->statement = [
            'firstname' => (string) Tools::getValue('firstname', $customer->firstname),
            'lastname' => (string) Tools::getValue('lastname', $customer->lastname),
            'email' => (string) Tools::getValue('withdrawal_email', $customer->email),
            'message' => (string) Tools::getValue('message', ''),
        ];

        if (Tools::isSubmit('submitWithdrawal')) {
            $this->processSubmitWithdrawal();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function initContent(): void
    {
        parent::initContent();

        if (null !== $this->submittedStatement) {
            $this->context->smarty->assign(['withdrawal' => $this->submittedStatement]);
            $this->setTemplate('customer/withdrawal-confirmation');

            return;
        }

        if (!Validate::isLoadedObject($this->order)) {
            $this->context->smarty->assign([
                'withdrawal_orders' => $this->getCustomerOrdersForWithdrawal(),
            ]);
            $this->setTemplate('customer/withdrawal-order');

            return;
        }

        if (!$this->eligibility->isEligible()) {
            $this->warning[] = $this->getIneligibilityMessage($this->eligibility);
        }

        $this->context->smarty->assign([
            'order' => (new OrderPresenter())->present($this->order),
            'withdrawal' => [
                'is_eligible' => $this->eligibility->isEligible(),
                'deadline' => $this->eligibility->getDeadline() ? Tools::displayDate($this->eligibility->getDeadline()->format('Y-m-d H:i:s')) : null,
                'statement' => $this->statement,
                'is_guest_lookup' => !$this->isLoggedOwner($this->order),
                'order_reference' => $this->order->reference,
                'lookup_email' => (string) Tools::getValue('email'),
                'message_max_length' => SubmitWithdrawalRequestCommand::MESSAGE_MAX_LENGTH,
            ],
        ]);
        $this->setTemplate('customer/withdrawal-statement');
    }

    /**
     * {@inheritdoc}
     */
    public function getBreadcrumbLinks(): array
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        if ($this->context->customer->isLogged()) {
            $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();
        }

        $breadcrumb['links'][] = [
            'title' => $this->trans('Withdraw from a contract', [], 'Shop.Theme.Customeraccount'),
            'url' => $this->context->link->getPageLink('withdrawal'),
        ];

        return $breadcrumb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCanonicalURL(): string
    {
        return $this->context->link->getPageLink('withdrawal');
    }

    /**
     * Finds the order the visitor wants to withdraw from: one of their own orders when they are logged in, or the
     * order matching the reference and email they typed, like on the guest tracking page.
     */
    protected function loadOrder(): void
    {
        $orderId = (int) Tools::getValue('id_order');
        if ($orderId && $this->context->customer->isLogged()) {
            $order = new Order($orderId);
            if (Validate::isLoadedObject($order) && $this->isLoggedOwner($order)) {
                $this->order = $order;

                return;
            }

            $this->errors[] = $this->trans('We couldn\'t find your order with the information provided, please try again', [], 'Shop.Notifications.Error');

            return;
        }

        $orderReference = current(explode('#', (string) Tools::getValue('order_reference')));
        $email = (string) Tools::getValue('email');
        if ('' === $orderReference && '' === $email) {
            return;
        }

        if ('' === $orderReference || '' === $email || !Validate::isEmail($email)) {
            $this->errors[] = $this->trans('Please provide the required information', [], 'Shop.Notifications.Error');

            return;
        }

        $order = Order::getByReferenceAndEmail($orderReference, $email);
        if (!Validate::isLoadedObject($order) || (int) $order->id_shop !== (int) $this->context->shop->id) {
            $this->errors[] = $this->trans('We couldn\'t find your order with the information provided, please try again', [], 'Shop.Notifications.Error');

            return;
        }

        $this->order = $order;
    }

    protected function processSubmitWithdrawal(): void
    {
        if (!$this->isTokenValid()) {
            $this->errors[] = $this->trans('Invalid token', [], 'Shop.Notifications.Error');

            return;
        }

        if (!$this->eligibility->isEligible()) {
            return;
        }

        if (!Validate::isName($this->statement['firstname']) || !Validate::isName($this->statement['lastname'])) {
            $this->errors[] = $this->trans('Please enter a valid first name and last name.', [], 'Shop.Notifications.Error');
        }
        if (!Validate::isEmail($this->statement['email'])) {
            $this->errors[] = $this->trans('Invalid email address.', [], 'Shop.Notifications.Error');
        }
        if (!Validate::isCleanHtml($this->statement['message'])) {
            $this->errors[] = $this->trans('Invalid message', [], 'Shop.Notifications.Error');
        }
        if (!empty($this->errors)) {
            return;
        }

        $submittedAt = new DateTimeImmutable();

        try {
            $this->get(WithdrawalRequestSubmitter::class)->submit(new SubmitWithdrawalRequestCommand(
                (int) $this->order->id,
                $this->statement['firstname'],
                $this->statement['lastname'],
                $this->statement['email'],
                $this->statement['message'],
                $submittedAt
            ));
        } catch (OrderNotEligibleForWithdrawalException $e) {
            $this->errors[] = $this->getIneligibilityMessage(
                new OrderWithdrawalEligibility((int) $this->order->id, $e->getReason(), null)
            );

            return;
        } catch (WithdrawalRequestConstraintException $e) {
            $this->errors[] = $this->trans('Please provide the required information', [], 'Shop.Notifications.Error');

            return;
        } catch (WithdrawalRequestException $e) {
            PrestaShopLogger::addLog(
                sprintf('Withdrawal request of order #%d could not be saved: %s', (int) $this->order->id, $e->getMessage()),
                PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR,
                null,
                'Order',
                (int) $this->order->id
            );
            $this->errors[] = $this->trans('An error occurred while sending your withdrawal, please try again or contact us.', [], 'Shop.Notifications.Error');

            return;
        }

        $this->submittedStatement = [
            'order_reference' => $this->order->reference,
            'firstname' => $this->statement['firstname'],
            'lastname' => $this->statement['lastname'],
            'email' => $this->statement['email'],
            'message' => $this->statement['message'],
            'submitted_at' => Tools::displayDate($submittedAt->format('Y-m-d H:i:s'), true),
        ];
    }

    /**
     * Orders of the logged in customer, with whether they can still be withdrawn from.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getCustomerOrdersForWithdrawal(): array
    {
        if (!$this->context->customer->isLogged()) {
            return [];
        }

        $checker = $this->get(WithdrawalEligibilityChecker::class);
        $now = new DateTimeImmutable();
        $orders = [];
        foreach (Order::getCustomerOrders((int) $this->context->customer->id) as $customerOrder) {
            $eligibility = $checker->check(new OrderId((int) $customerOrder['id_order']), $now);
            if (WithdrawalIneligibilityReason::ORDER_CANCELLED === $eligibility->getIneligibilityReason()) {
                continue;
            }

            $orders[] = [
                'id_order' => (int) $customerOrder['id_order'],
                'reference' => $customerOrder['reference'],
                'date' => Tools::displayDate($customerOrder['date_add']),
                'is_eligible' => $eligibility->isEligible(),
                'status' => $this->getIneligibilityMessage($eligibility),
                'url' => $this->context->link->getPageLink('withdrawal', null, null, ['id_order' => (int) $customerOrder['id_order']]),
            ];
        }

        return $orders;
    }

    protected function isLoggedOwner(Order $order): bool
    {
        return $this->context->customer->isLogged()
            && (int) $order->id_customer === (int) $this->context->customer->id;
    }

    protected function getIneligibilityMessage(OrderWithdrawalEligibility $eligibility): string
    {
        switch ($eligibility->getIneligibilityReason()) {
            case null:
                if (null !== $eligibility->getDeadline()) {
                    return $this->trans(
                        'You can withdraw until %date%',
                        ['%date%' => Tools::displayDate($eligibility->getDeadline()->format('Y-m-d H:i:s'))],
                        'Shop.Theme.Customeraccount'
                    );
                }

                return $this->trans('You can withdraw from this order', [], 'Shop.Theme.Customeraccount');
            case WithdrawalIneligibilityReason::ALREADY_REQUESTED:
                return $this->trans(
                    'You withdrew from this order on %date%',
                    ['%date%' => Tools::displayDate((string) $eligibility->getWithdrawalRequestedAt()?->format('Y-m-d H:i:s'), true)],
                    'Shop.Theme.Customeraccount'
                );
            case WithdrawalIneligibilityReason::PERIOD_EXPIRED:
                return $this->trans('The withdrawal period of this order has ended.', [], 'Shop.Theme.Customeraccount');
            default:
                return $this->trans('You cannot withdraw from this order online, please contact us.', [], 'Shop.Theme.Customeraccount');
        }
    }
}
