<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception;

class MovementReasonConstraintException extends ProductStockException
{
    /**
     * Thrown when invalid movement reason id is provided
     */
    public const INVALID_ID = 10;
}
