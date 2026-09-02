<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Exception;

use Throwable;

/**
 * Thrown when minimal product
 */
class MinimalQuantityException extends CartException
{
    /**
     * @var int
     */
    protected $minimalQuantity;

    /**
     * @param string $message
     * @param int $minimalQuantity
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message, int $minimalQuantity, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->minimalQuantity = $minimalQuantity;
    }

    /**
     * @return int
     */
    public function getMinimalQuantity(): int
    {
        return $this->minimalQuantity;
    }
}
