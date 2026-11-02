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
 * Trade credit balance of a customer, expressed in the trade credit currency (PS_TRADE_CREDIT_CURRENCY).
 * A missing row means the credit has not been granted yet.
 *
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\CustomerTradeCreditRepository")
 *
 * @ORM\Table()
 */
class CustomerTradeCredit
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_customer", type="integer", options={"unsigned": true})
     */
    private int $customerId;

    /**
     * @ORM\Column(name="amount", type="decimal", precision=20, scale=6)
     */
    private string $amount;

    /**
     * @ORM\Column(name="date_add", type="datetime_immutable")
     */
    private DateTimeImmutable $dateAdd;

    /**
     * @ORM\Column(name="date_upd", type="datetime_immutable")
     */
    private DateTimeImmutable $dateUpd;

    public function __construct(int $customerId, string $amount)
    {
        $this->customerId = $customerId;
        $this->amount = $amount;
        $this->dateAdd = new DateTimeImmutable();
        $this->dateUpd = $this->dateAdd;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getDateAdd(): DateTimeImmutable
    {
        return $this->dateAdd;
    }

    public function getDateUpd(): DateTimeImmutable
    {
        return $this->dateUpd;
    }
}
