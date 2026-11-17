<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Online withdrawal statement submitted by a consumer for an order (Directive 2011/83/EU, article 11a).
 *
 * The name, email and order reference are copied from the statement rather than read from the customer and
 * the order: the request is a legal record of what the consumer declared, it must not change when the customer
 * account or the order is edited, anonymized or deleted afterwards.
 *
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\WithdrawalRequestRepository")
 *
 * @ORM\Table(
 *     uniqueConstraints={@ORM\UniqueConstraint(name="id_order", columns={"id_order"})},
 *     indexes={
 *
 *         @ORM\Index(name="id_shop_status", columns={"id_shop", "status"}),
 *         @ORM\Index(name="id_customer", columns={"id_customer"})
 *     }
 * )
 *
 * @ORM\HasLifecycleCallbacks
 */
class WithdrawalRequest
{
    public const STATUS_PENDING = 'pending';

    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_withdrawal_request", type="integer", options={"unsigned": true})
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="id_shop", type="integer", options={"unsigned": true})
     */
    private int $shopId;

    /**
     * @ORM\Column(name="id_order", type="integer", options={"unsigned": true})
     */
    private int $orderId;

    /**
     * @ORM\Column(name="id_customer", type="integer", options={"unsigned": true})
     */
    private int $customerId;

    /**
     * @ORM\Column(name="order_reference", type="string", length=64)
     */
    private string $orderReference;

    /**
     * @ORM\Column(name="firstname", type="string", length=255)
     */
    private string $firstname;

    /**
     * @ORM\Column(name="lastname", type="string", length=255)
     */
    private string $lastname;

    /**
     * Electronic means the acknowledgement of receipt is sent to.
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private string $email;

    /**
     * @ORM\Column(name="message", type="text", length=65535, nullable=true)
     */
    private ?string $message = null;

    /**
     * @ORM\Column(name="status", type="string", length=32)
     */
    private string $status = self::STATUS_PENDING;

    /**
     * Legal date and time of the withdrawal, written on the acknowledgement of receipt.
     *
     * @ORM\Column(name="submitted_at", type="datetime")
     */
    private DateTimeInterface $submittedAt;

    /**
     * @ORM\Column(name="acknowledged_at", type="datetime", nullable=true)
     */
    private ?DateTimeInterface $acknowledgedAt = null;

    /**
     * @ORM\Column(name="date_add", type="datetime")
     */
    private DateTimeInterface $dateAdd;

    /**
     * @ORM\Column(name="date_upd", type="datetime")
     */
    private DateTimeInterface $dateUpd;

    public function getId(): int
    {
        return $this->id;
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function setShopId(int $shopId): self
    {
        $this->shopId = $shopId;

        return $this;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function setOrderId(int $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getOrderReference(): string
    {
        return $this->orderReference;
    }

    public function setOrderReference(string $orderReference): self
    {
        $this->orderReference = $orderReference;

        return $this;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getSubmittedAt(): DateTimeImmutable
    {
        return DateTimeImmutable::createFromInterface($this->submittedAt);
    }

    public function setSubmittedAt(DateTimeInterface $submittedAt): self
    {
        $this->submittedAt = DateTime::createFromInterface($submittedAt);

        return $this;
    }

    public function getAcknowledgedAt(): ?DateTimeImmutable
    {
        return null === $this->acknowledgedAt ? null : DateTimeImmutable::createFromInterface($this->acknowledgedAt);
    }

    public function setAcknowledgedAt(?DateTimeInterface $acknowledgedAt): self
    {
        $this->acknowledgedAt = null === $acknowledgedAt ? null : DateTime::createFromInterface($acknowledgedAt);

        return $this;
    }

    public function getDateAdd(): DateTimeInterface
    {
        return $this->dateAdd;
    }

    public function getDateUpd(): DateTimeInterface
    {
        return $this->dateUpd;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist(): void
    {
        $this->dateAdd = new DateTime();
        $this->dateUpd = new DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate(): void
    {
        $this->dateUpd = new DateTime();
    }
}
