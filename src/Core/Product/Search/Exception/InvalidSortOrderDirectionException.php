<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Product\Search\Exception;

/**
 * Thrown when sort order direction is not valid
 */
class InvalidSortOrderDirectionException extends InvalidSortOrderException
{
    /**
     * @param string $direction the invalid direction
     */
    public function __construct(string $direction)
    {
        $message = sprintf(
            'Invalid SortOrder direction `%s`. Expecting one of: `ASC`, `DESC`, or `RANDOM`.',
            $direction
        );

        parent::__construct($message);
    }
}
