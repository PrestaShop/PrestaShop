<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

class OrderMessagesForViewing
{
    /** @var OrderMessageForViewing[] */
    private $messages = [];

    /**
     * @var int
     */
    private $total;

    /**
     * @param OrderMessageForViewing[] $messages
     * @param int $total
     */
    public function __construct(array $messages, int $total)
    {
        foreach ($messages as $message) {
            $this->add($message);
        }

        $this->total = $total;
    }

    /**
     * @return OrderMessageForViewing[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @param OrderMessageForViewing $message
     */
    private function add(OrderMessageForViewing $message): void
    {
        $this->messages[] = $message;
    }
}
