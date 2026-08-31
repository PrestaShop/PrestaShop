<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\GridView\Exception;

/**
 * Thrown when a grid view field does not satisfy its constraints
 */
class GridViewConstraintException extends GridViewException
{
    public const INVALID_ID = 1;

    public const INVALID_NAME = 2;

    public const INVALID_GRID_ID = 3;

    public const INVALID_FILTER_ID = 4;

    public const UNKNOWN_ROUTE = 5;
}
