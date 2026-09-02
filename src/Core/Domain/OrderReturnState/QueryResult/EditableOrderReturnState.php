<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturnState\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\ValueObject\OrderReturnStateId;

/**
 * Stores editable data for order return state
 */
class EditableOrderReturnState
{
    /**
     * @var array<string, string>
     */
    private $localizedNames;

    private OrderReturnStateId $orderReturnStateId;

    public function __construct(
        OrderReturnStateId $orderStateId,
        array $name,
        private string $color,
        private bool $isCancellingReturn,
    ) {
        $this->orderReturnStateId = $orderStateId;
        $this->localizedNames = $name;
    }

    /**
     * @return OrderReturnStateId
     */
    public function getOrderReturnStateId()
    {
        return $this->orderReturnStateId;
    }

    /**
     * @return array
     */
    public function getLocalizedNames()
    {
        return $this->localizedNames;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    public function isCancellingReturn(): bool
    {
        return $this->isCancellingReturn;
    }
}
