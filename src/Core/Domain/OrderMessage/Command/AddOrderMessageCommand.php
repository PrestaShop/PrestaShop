<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\OrderMessage\Command;

use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Exception\OrderMessageConstraintException;

/**
 * Add new order message
 */
class AddOrderMessageCommand
{
    /**
     * @var string[]
     */
    private $localizedName;

    /**
     * @var string[]
     */
    private $localizedMessage;

    /**
     * @param string[] $localizedName
     * @param string[] $localizedMessage
     */
    public function __construct(array $localizedName, array $localizedMessage)
    {
        if (empty($localizedName)) {
            throw new OrderMessageConstraintException('OrderMessage name must not be empty');
        }

        if (empty($localizedMessage)) {
            throw new OrderMessageConstraintException('OrderMessage message must not be empty');
        }

        $this->localizedName = $localizedName;
        $this->localizedMessage = $localizedMessage;
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
