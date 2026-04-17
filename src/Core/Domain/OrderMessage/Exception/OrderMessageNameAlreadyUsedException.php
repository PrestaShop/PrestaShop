<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderMessage\Exception;

use Exception;

/**
 * Thrown when order message's name is already used by another order message.
 */
class OrderMessageNameAlreadyUsedException extends OrderMessageException
{
    /**
     * @var int
     */
    private $langId;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name the email that's being used
     * @param int $langId
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(string $name, int $langId, string $message = '', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->name = $name;
        $this->langId = $langId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getLangId(): int
    {
        return $this->langId;
    }
}
