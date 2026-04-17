<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\OrderMessage\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\OrderMessage\ValueObject\OrderMessageId;

/**
 * Transfers current order message data that can be edited
 */
class EditableOrderMessage
{
    /**
     * @var OrderMessageId
     */
    private $orderMessageId;

    /**
     * @var string[]
     */
    private $localizedName;

    /**
     * @var string[]
     */
    private $localizedMessage;

    /**
     * @param OrderMessageId $orderMessageId
     * @param string[] $localizedName
     * @param string[] $localizedMessage
     */
    public function __construct(OrderMessageId $orderMessageId, array $localizedName, array $localizedMessage)
    {
        $this->orderMessageId = $orderMessageId;
        $this->localizedName = $localizedName;
        $this->localizedMessage = $localizedMessage;
    }

    /**
     * @return OrderMessageId
     */
    public function getOrderMessageId(): OrderMessageId
    {
        return $this->orderMessageId;
    }

    /**
     * @return string[]
     */
    public function getLocalizedName(): array
    {
        return $this->localizedName;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMessage(): array
    {
        return $this->localizedMessage;
    }
}
