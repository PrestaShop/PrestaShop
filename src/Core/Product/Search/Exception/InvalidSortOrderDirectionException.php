<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Product\Search\Exception;

use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Thrown when sort order direction is not valid
 */
class InvalidSortOrderDirectionException extends CoreException
{
    /**
     * @param string $direction the invalid direction
     */
    public function __construct($direction)
    {
        $message = sprintf(
            'Invalid SortOrder direction `%s`. Expecting one of: `ASC`, `DESC`, or `RANDOM`.',
            $direction
        );

        parent::__construct($message, 0, null);
    }
}
