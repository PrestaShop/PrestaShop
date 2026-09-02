<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Exception;

use Throwable;

/**
 * Is thrown when product type is not suitable for certain operation
 * e.g. if product with combination is expected but got standard or pack product
 */
class InvalidProductTypeException extends ProductException
{
    /**
     * Code used when expected type was standard
     */
    public const EXPECTED_STANDARD_TYPE = 10;

    /**
     * Code used when expected type was pack
     */
    public const EXPECTED_PACK_TYPE = 20;

    /**
     * Code used when expected type was virtual
     */
    public const EXPECTED_VIRTUAL_TYPE = 30;

    /**
     * Code used when expected type was combinations
     */
    public const EXPECTED_COMBINATIONS_TYPE = 40;

    /**
     * Code used when expected type was standard
     */
    public const EXPECTED_NO_COMBINATIONS_TYPE = 50;

    /**
     * Code used when trying to change a product into a pack while it is already associated with another.
     */
    public const EXPECTED_NO_EXISTING_PACK_ASSOCIATIONS = 60;

    /**
     * @param int $code
     * @param string $message
     * @param Throwable|null $previous
     */
    public function __construct($code, $message = '', ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
