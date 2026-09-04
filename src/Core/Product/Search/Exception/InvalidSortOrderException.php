<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Product\Search\Exception;

use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Thrown when sort order format is not valid
 */
class InvalidSortOrderException extends CoreException
{
    public function __construct(string $message = '')
    {
        parent::__construct($message ?: 'Invalid SortOrder format, expection {entity}.{field}.{direction}');
    }
}
