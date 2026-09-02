<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Security\Exception;

use Throwable;

/**
 * Class CannotBulkDeleteSessionException is a base exception for security sessions context.
 */
class CannotBulkDeleteSessionException extends SessionException
{
    /**
     * @var array<int, int>
     */
    private $sessionIds;

    /**
     * @param array<int, int> $sessionIds
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        array $sessionIds,
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->sessionIds = $sessionIds;
    }

    /**
     * @return array<int, int>
     */
    public function getSessionIds(): array
    {
        return $this->sessionIds;
    }
}
