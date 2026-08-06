<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Exception;

use Exception;

class GridViewException extends Exception implements ExceptionInterface
{
    public const UNKNOWN_ROUTE = 2;
    public const MISSING_EMPLOYEE = 3;
    public const UNSUPPORTED_GRID = 4;
    public const INVALID_FILTER_ID = 5;
    public const VIEW_LIMIT_REACHED = 6;
}
