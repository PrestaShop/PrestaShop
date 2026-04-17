<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\OrderMessage\ValueObject;

/**
 * Order message identity
 */
class OrderMessageId
{
    /**
     * @var int
     */
    private $orderMessageId;

    /**
     * @param int $orderMessageId
     */
    public function __construct(int $orderMessageId)
    {
        $this->orderMessageId = $orderMessageId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->orderMessageId;
    }
}
