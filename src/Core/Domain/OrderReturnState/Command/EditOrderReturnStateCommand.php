<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Command;

use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\ValueObject\Name;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\ValueObject\OrderReturnStateId;

/**
 * Edits provided order return state.
 * It can edit either all or partial data.
 *
 * Only not-null values are considered when editing order return state.
 * For example, if the name is null, then the original value is not modified,
 * however, if name is set, then the original value will be overwritten.
 */
class EditOrderReturnStateCommand
{
    /**
     * @var OrderReturnStateId
     */
    private $orderReturnStateId;

    /**
     * @var array<string>|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $color;

    /**
     * @param int $orderReturnStateId
     */
    public function __construct($orderReturnStateId)
    {
        $this->orderReturnStateId = new OrderReturnStateId($orderReturnStateId);
    }

    /**
     * @return OrderReturnStateId
     */
    public function getOrderReturnStateId()
    {
        return $this->orderReturnStateId;
    }

    /**
     * @return array<string>|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param array<string> $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @return self
     */
    public function setColor(?string $color)
    {
        $this->color = $color;

        return $this;
    }
}
