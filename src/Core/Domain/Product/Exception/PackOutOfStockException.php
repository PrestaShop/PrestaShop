<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Exception;

/**
 * Is thrown when using pack (e.g. adding to cart) which is out of stock
 */
class PackOutOfStockException extends ProductOutOfStockException
{
}
