<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception;

use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\ValueObject\Name;

/**
 * Exception is thrown when name which already exists is being used to create or update other order state
 */
class DuplicateOrderReturnStateNameException extends OrderReturnStateException
{
    /**
     * @var Name
     */
    private $name;

    /**
     * @param string $message
     * @param int $code
     * @param null $previous
     */
    public function __construct(Name $name, $message = '', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->name = $name;
    }

    /**
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }
}
