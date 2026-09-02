<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Order;

/**
 * There are different cancellation action types, they are listed in this class
 *
 * Class CancellationActionType
 */
class CancellationActionType
{
    public const CANCEL_PRODUCT = 0;

    public const STANDARD_REFUND = 1;

    public const PARTIAL_REFUND = 2;

    public const RETURN_PRODUCT = 3;
}
