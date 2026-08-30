<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\QuickAccess\Exception;

class QuickAccessConstraintException extends QuickAccessException
{
    public const INVALID_ID = 1;
    public const INVALID_NAME = 2;
    public const INVALID_LINK = 3;
    public const LINK_ALREADY_EXISTS = 4;
}
