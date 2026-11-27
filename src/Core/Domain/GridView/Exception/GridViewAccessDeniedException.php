<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\GridView\Exception;

/**
 * Thrown when the current employee is not allowed to read or modify a grid view
 */
class GridViewAccessDeniedException extends GridViewException
{
}
