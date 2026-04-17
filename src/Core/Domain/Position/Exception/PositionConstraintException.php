<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Position\Exception;

use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;

/**
 * Exception thrown when invalid data is used to create a RowPosition value object.
 */
class PositionConstraintException extends DomainException
{
    public const INVALID_ROW_ID = 10;

    public const INVALID_OLD_POSITION = 20;

    public const INVALID_NEW_POSITION = 30;
}
