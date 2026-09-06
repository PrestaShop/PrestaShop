<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\GridView\Exception;

use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;

/**
 * Base exception of the GridView domain
 */
class GridViewException extends DomainException
{
    public const MISSING_EMPLOYEE = 1;

    public const UNSUPPORTED_GRID = 2;
}
