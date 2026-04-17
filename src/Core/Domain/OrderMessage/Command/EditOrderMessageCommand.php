<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\OrderMessage\Command;

use PrestaShop\PrestaShop\Core\Domain\OrderMessage\ValueObject\OrderMessageId;

/**
 * Edit given order message
 */
class EditOrderMessageCommand
{
    /**
     * @var OrderMessageId
     */
    private $orderMessageId;

    /**
     * @var string[]|null
     */
    private $localizedName;

    /**
     * @var string[]|null
     */
    private $localizedMessage;

    /**
     * @param int $orderMessageId
     * @param string[]|null $localizedName Array of localized name or null if name should not be edited
     * @param string[]|null $localizedMessage Array of localized message or null if message should not be edited
     */
    public function __construct(int $orderMessageId, ?array $localizedName = null, ?array $localizedMessage = null)
    {
        $this->orderMessageId = new OrderMessageId($orderMessageId);
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
     * @return string[]|null
     */
    public function getLocalizedName(): ?array
    {
        return $this->localizedName;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedMessage(): ?array
    {
        return $this->localizedMessage;
    }
}
