<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\OrderMessage;

use OrderMessage;

/**
 * Gets order messages.
 */
class OrderMessageProvider
{
    /**
     * @var int
     */
    private $contextLanguageId;

    /**
     * @param int $contextLanguageId
     */
    public function __construct(int $contextLanguageId)
    {
        $this->contextLanguageId = $contextLanguageId;
    }

    /**
     * @param int|null $langId
     *
     * @return array
     */
    public function getMessages(?int $langId = null): array
    {
        $result = OrderMessage::getOrderMessages($langId ?? $this->contextLanguageId);

        return is_array($result) ? $result : [];
    }
}
