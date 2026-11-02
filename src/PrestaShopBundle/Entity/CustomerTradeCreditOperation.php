<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * One movement on a customer trade credit balance. The unique reference makes each
 * business event (grant, cart debit, order refund, credit slip refund...) applicable only once.
 *
 * @ORM\Entity()
 *
 * @ORM\Table(
 *     uniqueConstraints={@ORM\UniqueConstraint(name="reference", columns={"reference"})},
 *     indexes={
 *
 *         @ORM\Index(name="id_customer", columns={"id_customer"}),
 *         @ORM\Index(name="id_cart", columns={"id_cart"}),
 *         @ORM\Index(name="id_order", columns={"id_order"})
 *     }
 * )
 */
class CustomerTradeCreditOperation
{
    public const TYPE_GRANT = 'grant';
    public const TYPE_DEBIT = 'debit';
    public const TYPE_RELEASE = 'release';
    public const TYPE_REFUND = 'refund';
    public const TYPE_ADJUSTMENT = 'adjustment';

    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_customer_trade_credit_operation", type="integer", options={"unsigned": true})
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="id_customer", type="integer", options={"unsigned": true})
     */
    private int $customerId;

    /**
     * @ORM\Column(name="type", type="string", length=16)
     */
    private string $type;

    /**
     * Signed amount: negative for debits.
     *
     * @ORM\Column(name="amount", type="decimal", precision=20, scale=6)
     */
    private string $amount;

    /**
     * @ORM\Column(name="balance_after", type="decimal", precision=20, scale=6)
     */
    private string $balanceAfter;

    /**
     * @ORM\Column(name="id_cart", type="integer", nullable=true, options={"unsigned": true})
     */
    private ?int $cartId = null;

    /**
     * @ORM\Column(name="id_order", type="integer", nullable=true, options={"unsigned": true})
     */
    private ?int $orderId = null;

    /**
     * @ORM\Column(name="id_order_slip", type="integer", nullable=true, options={"unsigned": true})
     */
    private ?int $orderSlipId = null;

    /**
     * @ORM\Column(name="id_employee", type="integer", nullable=true, options={"unsigned": true})
     */
    private ?int $employeeId = null;

    /**
     * @ORM\Column(name="reference", type="string", length=64)
     */
    private string $reference;

    /**
     * @ORM\Column(name="date_add", type="datetime_immutable")
     */
    private DateTimeImmutable $dateAdd;

    public function __construct(
        int $customerId,
        string $type,
        string $amount,
        string $balanceAfter,
        string $reference,
        ?int $cartId = null,
        ?int $orderId = null,
        ?int $orderSlipId = null,
        ?int $employeeId = null
    ) {
        $this->customerId = $customerId;
        $this->type = $type;
        $this->amount = $amount;
        $this->balanceAfter = $balanceAfter;
        $this->reference = $reference;
        $this->cartId = $cartId;
        $this->orderId = $orderId;
        $this->orderSlipId = $orderSlipId;
        $this->employeeId = $employeeId;
        $this->dateAdd = new DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getBalanceAfter(): string
    {
        return $this->balanceAfter;
    }

    public function getCartId(): ?int
    {
        return $this->cartId;
    }

    public function getOrderId(): ?int
    {
        return $this->orderId;
    }

    public function getOrderSlipId(): ?int
    {
        return $this->orderSlipId;
    }

    public function getEmployeeId(): ?int
    {
        return $this->employeeId;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getDateAdd(): DateTimeImmutable
    {
        return $this->dateAdd;
    }
}
