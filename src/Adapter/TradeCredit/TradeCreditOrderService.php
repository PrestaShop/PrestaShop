<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\TradeCredit;

use Cart;
use Context;
use Currency;
use Customer;
use Module;
use Order;
use OrderSlip;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\Decimal\Operation\Rounding;
use PrestaShop\PrestaShop\Adapter\TradeCredit\Repository\TradeCreditPaymentModuleRepository;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\Exception\TradeCreditException;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\Exception\TradeCreditInsufficientException;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\TradeCreditAvailability;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\TradeCreditAvailabilityChecker;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\TradeCreditRefundCalculator;
use PrestaShopBundle\Entity\CustomerTradeCreditOperation;
use PrestaShopBundle\Entity\Repository\CustomerTradeCreditRepository;
use Tools;

/**
 * Applies the trade credit rules to carts and orders: availability in checkout, atomic debit when
 * the order is validated, release when the order could not be created, refunds on cancel/refund
 * status and on credit slips.
 *
 * Amounts are converted to the trade credit currency when the cart is charged. Refunds are computed
 * as a share of what was actually charged, so they never depend on later exchange rate changes.
 */
class TradeCreditOrderService
{
    public function __construct(
        private readonly CustomerTradeCreditRepository $repository,
        private readonly TradeCreditPaymentModuleRepository $paymentModuleRepository,
        private readonly TradeCreditAvailabilityChecker $availabilityChecker,
        private readonly TradeCreditRefundCalculator $refundCalculator,
        private readonly ConfigurationInterface $configuration
    ) {
    }

    /**
     * Message explaining why the trade credit cannot be used, for the customer (checkout, order error page).
     */
    public function getUnavailabilityMessage(TradeCreditAvailability $availability): ?string
    {
        // The light front container has no translator service, the legacy context translator works everywhere
        $translator = Context::getContext()->getTranslator();

        return match ($availability->getReason()) {
            null => null,
            TradeCreditAvailability::REASON_GUEST => $translator->trans(
                'Trade credit is available only to registered customers.',
                [],
                'Shop.Notifications.Error'
            ),
            TradeCreditAvailability::REASON_INSUFFICIENT => $translator->trans(
                'The order total %required% exceeds your available trade credit %available%.',
                [
                    '%required%' => $this->formatPrice($availability->getRequired()),
                    '%available%' => $this->formatPrice($availability->getAvailable()),
                ],
                'Shop.Notifications.Error'
            ),
            default => $translator->trans('Trade credit is currently unavailable.', [], 'Shop.Notifications.Error'),
        };
    }

    public function formatPrice(DecimalNumber $amount): string
    {
        $currencyIso = (string) $this->configuration->get('PS_TRADE_CREDIT_CURRENCY');

        // The locale has no price specification for a currency that is not installed
        if (null === $this->getCreditCurrency()) {
            return number_format((float) $this->format($amount), 2, '.', ',') . ' ' . $currencyIso;
        }

        return Tools::getContextLocale(Context::getContext())->formatPrice($this->format($amount), $currencyIso);
    }

    public function isTradeCreditModule(int $moduleId, int $shopId): bool
    {
        return $this->paymentModuleRepository->isTradeCreditModule($moduleId, $shopId);
    }

    /**
     * Adds the available credit to the label of trade credit payment options and a "trade_credit" entry
     * (available, required, sufficient, message) used to disable the option in checkout.
     *
     * @param array<string, array<int, array<string, mixed>>> $paymentOptions presented options, grouped by module name
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function decoratePaymentOptions(array $paymentOptions, Cart $cart, Customer $customer, Currency $currency, int $shopId): array
    {
        $availability = null;

        foreach ($paymentOptions as $moduleName => $moduleOptions) {
            $moduleId = (int) Module::getModuleIdByName((string) $moduleName);
            if (!$moduleId || !$this->isTradeCreditModule($moduleId, $shopId)) {
                continue;
            }

            $availability ??= $this->getAvailabilityForCart($cart, $customer, $currency);
            // Without the credit currency there is no meaningful amount to show, only the restriction message
            $label = TradeCreditAvailability::REASON_CURRENCY_UNAVAILABLE === $availability->getReason()
                ? ''
                : Context::getContext()->getTranslator()->trans(
                    ', available trade credit: %amount%',
                    ['%amount%' => $this->formatPrice($availability->getAvailable())],
                    'Shop.Theme.Checkout'
                );

            foreach ($moduleOptions as $key => $option) {
                $paymentOptions[$moduleName][$key]['call_to_action_text'] = $option['call_to_action_text'] . $label;
                $paymentOptions[$moduleName][$key]['trade_credit'] = [
                    'available' => $this->formatPrice($availability->getAvailable()),
                    'required' => $this->formatPrice($availability->getRequired()),
                    'sufficient' => $availability->isSufficient(),
                    'message' => $this->getUnavailabilityMessage($availability),
                ];
            }
        }

        return $paymentOptions;
    }

    /**
     * Restrictions exposed to the checkout javascript, by module name.
     *
     * @param array<string, array<int, array<string, mixed>>> $decoratedPaymentOptions
     *
     * @return array<string, array{sufficient: bool, message: string|null}>
     */
    public function getPaymentOptionsRestrictions(array $decoratedPaymentOptions): array
    {
        $restrictions = [];

        foreach ($decoratedPaymentOptions as $moduleName => $moduleOptions) {
            foreach ($moduleOptions as $option) {
                if (isset($option['trade_credit'])) {
                    $restrictions[$moduleName] = [
                        'sufficient' => $option['trade_credit']['sufficient'],
                        'message' => $option['trade_credit']['message'],
                    ];
                }
            }
        }

        return $restrictions;
    }

    /**
     * Availability of the trade credit for the current cart total (tax incl., with shipping).
     */
    public function getAvailabilityForCart(Cart $cart, Customer $customer, Currency $currency): TradeCreditAvailability
    {
        $cartTotal = (float) $cart->getOrderTotal(true, Cart::BOTH);
        $creditCurrency = $this->getCreditCurrency();
        if (null === $creditCurrency) {
            return $this->currencyUnavailable();
        }

        $balance = $this->repository->findBalance((int) $customer->id);

        return $this->availabilityChecker->check(
            null === $balance ? null : new DecimalNumber($balance),
            (bool) $customer->is_guest,
            $this->getDefaultAmount(),
            $this->toCreditCurrency($cartTotal, $currency, $creditCurrency)
        );
    }

    /**
     * Charges the cart total to the customer trade credit, under a lock of the balance row.
     * Charging the same cart twice does nothing.
     *
     * @throws TradeCreditInsufficientException
     */
    public function reserveForCart(Cart $cart, Customer $customer, Currency $currency, float $cartTotal): void
    {
        $creditCurrency = $this->getCreditCurrency();
        if (null === $creditCurrency) {
            throw $this->unavailable($this->currencyUnavailable());
        }

        $customerId = (int) $customer->id;
        $cartId = (int) $cart->id;
        $required = $this->toCreditCurrency($cartTotal, $currency, $creditCurrency);
        if ($customer->is_guest) {
            throw $this->unavailable($this->availabilityChecker->check(null, true, $this->getDefaultAmount(), $required));
        }

        // Granted before (not inside) the debit transaction: two concurrent first orders only meet a duplicate key
        if (null === $this->repository->findBalance($customerId)) {
            $this->repository->grantIfMissing($customerId, $this->format($this->getDefaultAmount()));
        }

        $this->repository->transactional(function () use ($cartId, $customerId, $required): void {
            $balance = $this->lockBalance($customerId);

            // Checked under the lock, so a double submit of the same cart is charged once
            if ($this->getNetCartDebit($cartId)->isGreaterThanZero()) {
                return;
            }

            $availability = $this->availabilityChecker->check($balance, false, $this->getDefaultAmount(), $required);
            if (!$availability->isSufficient()) {
                throw $this->unavailable($availability);
            }

            // Each attempt has its own reference: a cart released after a failed attempt can be charged again
            $attempt = $this->repository->countCartOperations($cartId, CustomerTradeCreditOperation::TYPE_DEBIT) + 1;
            $balanceAfter = $this->format($balance->minus($availability->getRequired()));
            $this->addOperationOrFail([
                'id_customer' => $customerId,
                'type' => CustomerTradeCreditOperation::TYPE_DEBIT,
                'amount' => $this->format($availability->getRequired()->toNegative()),
                'balance_after' => $balanceAfter,
                'id_cart' => $cartId,
                'reference' => sprintf('debit:cart:%d:%d', $cartId, $attempt),
            ]);
            $this->repository->updateBalance($customerId, $balanceAfter);
        });
    }

    /**
     * Gives back what is charged for the cart when its order could not be created.
     */
    public function releaseForCart(int $cartId): void
    {
        $customerId = $this->repository->findCartDebitCustomerId($cartId);
        if (null === $customerId) {
            return;
        }

        $this->repository->transactional(function () use ($cartId, $customerId): void {
            $balance = $this->lockBalance($customerId);
            $charged = $this->getNetCartDebit($cartId);
            $attempt = $this->repository->countCartOperations($cartId, CustomerTradeCreditOperation::TYPE_DEBIT);

            $this->applyCredit(
                $customerId,
                $balance,
                $charged,
                CustomerTradeCreditOperation::TYPE_RELEASE,
                sprintf('release:cart:%d:%d', $cartId, $attempt),
                ['id_cart' => $cartId]
            );
        });
    }

    /**
     * Returns what is left of the order share, when the order is cancelled or refunded.
     */
    public function refundOrder(Order $order): void
    {
        $this->refund(
            $order,
            static fn (DecimalNumber $orderShare): DecimalNumber => $orderShare,
            sprintf('refund:order:%d', (int) $order->id),
            []
        );
    }

    /**
     * Returns the credit slip amount (products and shipping, tax incl.), as a share of the order debit.
     */
    public function refundOrderSlip(OrderSlip $orderSlip, Order $order): void
    {
        $orderTotal = new DecimalNumber((string) $order->total_paid_tax_incl);
        if (!$orderTotal->isGreaterThanZero()) {
            return;
        }

        $slipTotal = new DecimalNumber((string) ((float) $orderSlip->total_products_tax_incl + (float) $orderSlip->total_shipping_tax_incl));

        $this->refund(
            $order,
            static fn (DecimalNumber $orderShare): DecimalNumber => $orderShare->times($slipTotal)->dividedBy($orderTotal),
            sprintf('refund:slip:%d', (int) $orderSlip->id),
            ['id_order_slip' => (int) $orderSlip->id]
        );
    }

    /**
     * Part of what is charged for the cart that belongs to the order (a cart creates several orders with
     * multi-shipping); null when nothing is charged, e.g. the order was paid with another payment method.
     */
    private function getOrderShare(Order $order): ?DecimalNumber
    {
        $charged = $this->getNetCartDebit((int) $order->id_cart);
        if (!$charged->isGreaterThanZero()) {
            return null;
        }

        $cartOrdersTotal = new DecimalNumber($this->repository->getCartOrdersTotal((int) $order->id_cart));
        if (!$cartOrdersTotal->isGreaterThanZero()) {
            return $charged;
        }

        return $charged->times(new DecimalNumber((string) $order->total_paid_tax_incl))->dividedBy($cartOrdersTotal);
    }

    /**
     * @param callable(DecimalNumber): DecimalNumber $requestedForShare amount the refund asks for, given the order share
     */
    private function refund(Order $order, callable $requestedForShare, string $reference, array $extra): void
    {
        $customerId = (int) $order->id_customer;
        if (null === $this->repository->findBalance($customerId)) {
            return;
        }

        $this->repository->transactional(function () use ($order, $customerId, $requestedForShare, $reference, $extra): void {
            // Share and previous refunds are read under the lock, so concurrent refunds of the same order see each other
            $balance = $this->lockBalance($customerId);
            $orderShare = $this->getOrderShare($order);
            if (null === $orderShare) {
                return;
            }

            $alreadyRefunded = new DecimalNumber($this->repository->sumOperations(
                $customerId,
                CustomerTradeCreditOperation::TYPE_REFUND,
                ['id_order' => (int) $order->id]
            ));
            $amount = $this->refundCalculator->refundable($orderShare, $alreadyRefunded, $requestedForShare($orderShare));

            $this->applyCredit($customerId, $balance, $amount, CustomerTradeCreditOperation::TYPE_REFUND, $reference, array_merge([
                'id_cart' => (int) $order->id_cart,
                'id_order' => (int) $order->id,
            ], $extra));
        });
    }

    /**
     * Adds the amount to the locked balance, once per reference. Must run inside a transaction holding the lock.
     */
    private function applyCredit(int $customerId, DecimalNumber $balance, DecimalNumber $amount, string $type, string $reference, array $extra): void
    {
        $amount = new DecimalNumber($this->format($amount));
        if (!$amount->isGreaterThanZero() || null !== $this->repository->findOperation($reference)) {
            return;
        }

        $balanceAfter = $this->format($balance->plus($amount));
        $this->addOperationOrFail(array_merge([
            'id_customer' => $customerId,
            'type' => $type,
            'amount' => $this->format($amount),
            'balance_after' => $balanceAfter,
            'reference' => $reference,
        ], $extra));
        $this->repository->updateBalance($customerId, $balanceAfter);
    }

    /**
     * The operation is written before the balance: a duplicate reference rolls the whole change back.
     */
    private function addOperationOrFail(array $operation): void
    {
        if (!$this->repository->addOperation($operation)) {
            throw new TradeCreditException(sprintf('Trade credit operation "%s" was already applied.', $operation['reference']));
        }
    }

    private function lockBalance(int $customerId): DecimalNumber
    {
        $balance = $this->repository->lockBalance($customerId);
        if (null === $balance) {
            throw new TradeCreditException(sprintf('Trade credit of customer %d is not granted.', $customerId));
        }

        return new DecimalNumber($balance);
    }

    private function getNetCartDebit(int $cartId): DecimalNumber
    {
        return new DecimalNumber($this->repository->getCartNetDebit($cartId));
    }

    private function toCreditCurrency(float $amount, Currency $from, Currency $creditCurrency): DecimalNumber
    {
        $converted = (int) $from->id === (int) $creditCurrency->id
            ? $amount
            : (float) Tools::convertPriceFull($amount, $from, $creditCurrency);

        return new DecimalNumber($this->format(new DecimalNumber((string) $converted)));
    }

    private function getCreditCurrency(): ?Currency
    {
        $currencyId = (int) Currency::getIdByIsoCode((string) $this->configuration->get('PS_TRADE_CREDIT_CURRENCY'));

        return $currencyId > 0 ? new Currency($currencyId) : null;
    }

    private function getDefaultAmount(): DecimalNumber
    {
        return new DecimalNumber((string) $this->configuration->get('PS_TRADE_CREDIT_DEFAULT_AMOUNT'));
    }

    private function unavailable(TradeCreditAvailability $availability): TradeCreditInsufficientException
    {
        return new TradeCreditInsufficientException($availability, (string) $this->getUnavailabilityMessage($availability));
    }

    private function currencyUnavailable(): TradeCreditAvailability
    {
        $zero = new DecimalNumber('0');

        return new TradeCreditAvailability($zero, $zero, TradeCreditAvailability::REASON_CURRENCY_UNAVAILABLE);
    }

    private function format(DecimalNumber $amount): string
    {
        return $amount->toPrecision(TradeCreditAvailabilityChecker::PRECISION, Rounding::ROUND_HALF_UP);
    }
}
