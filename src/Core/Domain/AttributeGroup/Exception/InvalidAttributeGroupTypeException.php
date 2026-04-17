<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception;

use Throwable;

class InvalidAttributeGroupTypeException extends AttributeGroupConstraintException
{
    public function __construct(string $message = '', int $code = self::INVALID_TYPE, ?Throwable $previous = null)
    {
        parent::__construct($message, $code ?: self::INVALID_TYPE, $previous);
    }
}
