<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CustomerMessage\Exception;

class CustomerMessageConstraintException extends CustomerMessageException
{
    public const MISSING_MESSAGE = 1;
    public const INVALID_MESSAGE = 2;
}
