<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Title\Exception;

/**
 * Is thrown when Title constraint is violated
 */
class TitleConstraintException extends TitleException
{
    public const INVALID_ID = 1;
    public const INVALID_NAME = 2;
    public const INVALID_TYPE = 3;
}
