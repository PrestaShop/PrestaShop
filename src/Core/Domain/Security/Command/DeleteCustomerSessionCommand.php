<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Security\Command;

use PrestaShop\PrestaShop\Core\Domain\Security\ValueObject\CustomerSessionId;

/**
 * Class DeleteCustomerSessionCommand is a command to delete customer session by given id.
 */
class DeleteCustomerSessionCommand
{
    /**
     * @var CustomerSessionId
     */
    private $sessionId;

    /**
     * @param int $sessionId
     */
    public function __construct(int $sessionId)
    {
        $this->sessionId = new CustomerSessionId($sessionId);
    }

    /**
     * @return CustomerSessionId
     */
    public function getCustomerSessionId(): CustomerSessionId
    {
        return $this->sessionId;
    }
}
