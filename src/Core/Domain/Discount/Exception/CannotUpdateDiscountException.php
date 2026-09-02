<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Discount\Exception;

class CannotUpdateDiscountException extends DiscountException
{
    public const FAILED_UPDATE_DISCOUNT = 1;

    public const FAILED_UPDATE_CONDITIONS = 2;
}
