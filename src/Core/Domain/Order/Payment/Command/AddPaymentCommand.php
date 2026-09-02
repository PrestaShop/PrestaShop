<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Payment\Command;

use DateTimeImmutable;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\NegativePaymentAmountException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Adds payment for given order.
 */
class AddPaymentCommand
{
    /**
     * @var string
     */
    public const INVALID_CHARACTERS_NAME = '<>{}';

    /**
     * @var string
     */
    private const PATTERN_PAYMENT_METHOD_NAME = '/^[^' . self::INVALID_CHARACTERS_NAME . ']*$/u';

    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var DateTimeImmutable
     */
    private $paymentDate;

    /**
     * @var string
     */
    private $paymentMethod;

    /**
     * @var DecimalNumber
     */
    private $paymentAmount;

    /**
     * @var CurrencyId
     */
    private $paymentCurrencyId;

    /**
     * @var string|null
     */
    private $transactionId;

    /**
     * @var int|null
     */
    private $orderInvoiceId;

    /**
     * @var EmployeeId
     */
    protected $employeeId;

    /**
     * @param int $orderId
     * @param string $paymentDate
     * @param string $paymentMethod
     * @param string $paymentAmount
     * @param int $paymentCurrencyId
     * @param int $employeeId
     * @param int|null $orderInvoiceId
     * @param string|null $transactionId transaction ID, usually payment ID from payment gateway
     */
    public function __construct(
        int $orderId,
        string $paymentDate,
        string $paymentMethod,
        string $paymentAmount,
        int $paymentCurrencyId,
        int $employeeId,
        ?int $orderInvoiceId = null,
        ?string $transactionId = null
    ) {
        $amount = new DecimalNumber($paymentAmount);
        $this->assertAmountIsPositive($amount);
        $this->assertPaymentMethodIsGenericName($paymentMethod);

        $this->orderId = new OrderId($orderId);
        $this->paymentDate = new DateTimeImmutable($paymentDate);
        $this->paymentMethod = $paymentMethod;
        $this->paymentAmount = $amount;
        $this->paymentCurrencyId = new CurrencyId($paymentCurrencyId);
        $this->employeeId = new EmployeeId($employeeId);
        $this->orderInvoiceId = $orderInvoiceId;
        $this->transactionId = $transactionId;
    }

    /**
     * @return OrderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getPaymentDate()
    {
        return $this->paymentDate;
    }

    /**
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @return DecimalNumber
     */
    public function getPaymentAmount()
    {
        return $this->paymentAmount;
    }

    /**
     * @return CurrencyId
     */
    public function getPaymentCurrencyId()
    {
        return $this->paymentCurrencyId;
    }

    /**
     * @return int|null
     */
    public function getOrderInvoiceId()
    {
        return $this->orderInvoiceId;
    }

    /**
     * @return EmployeeId
     */
    public function getEmployeeId(): EmployeeId
    {
        return $this->employeeId;
    }

    /**
     * @return string|null
     */
    public function getPaymentTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @param string $paymentMethod
     *
     * @return void
     *
     * @throws OrderConstraintException
     */
    private function assertPaymentMethodIsGenericName(string $paymentMethod): void
    {
        if (empty($paymentMethod) || !preg_match(self::PATTERN_PAYMENT_METHOD_NAME, $paymentMethod)) {
            throw new OrderConstraintException(
                'The selected payment method is invalid.',
                OrderConstraintException::INVALID_PAYMENT_METHOD
            );
        }
    }

    /**
     * @param DecimalNumber $amount
     *
     * @return void
     *
     * @throws NegativePaymentAmountException
     */
    private function assertAmountIsPositive(DecimalNumber $amount): void
    {
        if ($amount->isNegative()) {
            throw new NegativePaymentAmountException('The amount should be greater than 0.');
        }
    }
}
