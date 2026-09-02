<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Exception;

/**
 * Exception thrown when a bulk update has been tried, it stores the exception for each failing product.
 */
class CannotBulkUpdateProductException extends BulkProductException
{
}
