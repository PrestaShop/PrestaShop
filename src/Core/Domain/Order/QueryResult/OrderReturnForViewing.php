<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

use DateTimeImmutable;

class OrderReturnForViewing
{
    /**
     * @var int
     */
    private $orderInvoiceId;

    /**
     * @var DateTimeImmutable
     */
    private $date;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $stateName;

    /**
     * @var string
     */
    private $orderReturnNumber;

    /**
     * @var int
     */
    private $idOrderReturn;

    /**
     * @param int $idOrderReturn
     * @param int $orderInvoiceId
     * @param DateTimeImmutable $date
     * @param string $type
     * @param string $stateName
     * @param string $orderReturnNumber
     */
    public function __construct(
        int $idOrderReturn,
        int $orderInvoiceId,
        DateTimeImmutable $date,
        string $type,
        string $stateName,
        string $orderReturnNumber
    ) {
        $this->orderInvoiceId = $orderInvoiceId;
        $this->date = $date;
        $this->type = $type;
        $this->stateName = $stateName;
        $this->idOrderReturn = $idOrderReturn;
        $this->orderReturnNumber = $orderReturnNumber;
    }

    /**
     * @return int
     */
    public function getOrderInvoiceId(): int
    {
        return $this->orderInvoiceId;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getStateName(): string
    {
        return $this->stateName;
    }

    /**
     * @return string
     */
    public function getOrderReturnNumber(): string
    {
        return $this->orderReturnNumber;
    }

    /**
     * @return int
     */
    public function getIdOrderReturn(): int
    {
        return $this->idOrderReturn;
    }
}
