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
     * @var OrderReturnStateId
     */
    private $orderReturnStateId;
    /**
     * @var array
     */
    private $localizedNames;
    /**
     * @var string
     */
    private $color;

    public function __construct(
        OrderReturnStateId $orderStateId,
        array $name,
        string $color
    ) {
        $this->orderReturnStateId = $orderStateId;
        $this->localizedNames = $name;
        $this->color = $color;
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
}
