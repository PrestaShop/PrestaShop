<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception;

/**
 * Thrown when no StockAvailable associated to a Product is found
 */
class StockAvailableNotFoundException extends ProductStockException
{
}
