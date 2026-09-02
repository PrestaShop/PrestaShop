<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

use PrestaShop\Decimal\DecimalNumber;

class OrderDiscountForViewing
{
    /**
     * @var int
     */
    private $orderCartRuleId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $amountFormatted;

    /**
     * @var DecimalNumber
     */
    private $amountRaw;

    public function __construct(
        int $orderCartRuleId,
        string $name,
        DecimalNumber $amountRaw,
        string $amountFormatted
    ) {
        $this->orderCartRuleId = $orderCartRuleId;
        $this->name = $name;
        $this->amountFormatted = $amountFormatted;
        $this->amountRaw = $amountRaw;
    }

    /**
     * @return int
     */
    public function getOrderCartRuleId(): int
    {
        return $this->orderCartRuleId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAmountFormatted(): string
    {
        return $this->amountFormatted;
    }

    /**
     * @return DecimalNumber
     */
    public function getAmountRaw(): DecimalNumber
    {
        return $this->amountRaw;
    }
}
