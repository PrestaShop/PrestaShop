<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Exception;

/**
 * Thrown when product deletion fails in bulk action
 */
class CannotBulkDeleteProductException extends BulkProductException
{
}
