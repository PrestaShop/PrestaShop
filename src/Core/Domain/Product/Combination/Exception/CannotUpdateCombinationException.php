<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception;

/**
 * Is thrown when combination update fails
 */
class CannotUpdateCombinationException extends CombinationException
{
    /**
     * When generic product update fails
     */
    public const FAILED_UPDATE_COMBINATION = 1;

    /**
     * When fails to update options of single combination
     */
    public const FAILED_UPDATE_DETAILS = 10;

    /**
     * When fails to update prices information of single combination
     */
    public const FAILED_UPDATE_PRICES = 20;

    /**
     * When fails to update stock information of single combination
     */
    public const FAILED_UPDATE_STOCK = 30;

    /**
     * When fails to update combination in combinations list
     */
    public const FAILED_UPDATE_LISTED_COMBINATION = 40;

    /**
     * When fails to update default combination
     */
    public const FAILED_UPDATE_DEFAULT_COMBINATION = 50;

    /**
     * When fails to update default supplier data
     */
    public const FAILED_UPDATE_DEFAULT_SUPPLIER_DATA = 60;
}
