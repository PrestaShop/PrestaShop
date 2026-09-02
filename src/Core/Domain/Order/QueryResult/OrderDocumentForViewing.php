<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

use DateTimeImmutable;

class OrderDocumentForViewing
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var DateTimeImmutable
     */
    private $createdAt;

    /**
     * @var string
     */
    private $referenceNumber;

    /**
     * When eligible, document amount as a number
     *
     * @var float|null
     */
    private $numericalAmount;

    /**
     * When eligible, document amount as a string, ready to be displayed
     *
     * @var string|null
     */
    private $amount;

    /**
     * @var string|null
     */
    private $amountMismatch;

    /**
     * @var string
     */
    private $note;

    /**
     * @var bool
     */
    private $isAddPaymentAllowed;

    public function __construct(
        int $id,
        string $type,
        DateTimeImmutable $createdAt,
        string $referenceNumber,
        ?float $numericalAmount,
        ?string $amount,
        ?string $amountMismatch,
        ?string $note,
        bool $isAddPaymentAllowed
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->createdAt = $createdAt;
        $this->referenceNumber = $referenceNumber;
        $this->numericalAmount = $numericalAmount;
        $this->amount = $amount;
        $this->amountMismatch = $amountMismatch;
        $this->note = $note;
        $this->isAddPaymentAllowed = $isAddPaymentAllowed;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getReferenceNumber(): string
    {
        return $this->referenceNumber;
    }

    /**
     * @return string|null
     */
    public function getAmount(): ?string
    {
        return $this->amount;
    }

    /**
     * @return string|null
     */
    public function getAmountMismatch(): ?string
    {
        return $this->amountMismatch;
    }

    /**
     * @return string
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @return bool
     */
    public function isAddPaymentAllowed(): bool
    {
        return $this->isAddPaymentAllowed;
    }

    /**
     * @return float|null
     */
    public function getNumericalAmount(): ?float
    {
        return $this->numericalAmount;
    }
}
