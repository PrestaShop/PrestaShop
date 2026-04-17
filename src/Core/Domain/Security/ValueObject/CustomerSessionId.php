<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Security\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Security\Exception\SessionException;

/**
 * Class CustomerSessionId
 */
class CustomerSessionId
{
    /**
     * @var int
     */
    private $sessionId;

    /**
     * @param int $sessionId
     *
     * @throws SessionException
     */
    public function __construct(int $sessionId)
    {
        if (0 >= $sessionId) {
            throw new SessionException('Session id must be greater than zero.');
        }

        $this->sessionId = $sessionId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->sessionId;
    }
}
