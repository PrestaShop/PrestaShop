<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Status;

/**
 * Defines colors for order statuses
 */
class OrderStatusColor
{
    /**
     * Used for statuses that are waiting for customer actions.
     * Example statuses: Awaiting bank wire payment, Awaiting check payment, On backorder (not paid).
     */
    public const AWAITING_PAYMENT = '#34209E';

    /**
     * Used for statuses when further merchant action is required.
     * Example statuses: Processing in progress, On backorder (paid), Payment accepted.
     */
    public const ACCEPTED_PAYMENT = '#3498D8';

    /**
     * Used for statuses when no actions are required anymore.
     * Example statuses: Shipped, Refunded, Delivered.
     */
    public const COMPLETED = '#01b887';

    /**
     * Used for error statuses.
     * Example statuses: Payment error.
     */
    public const ERROR = '#E74C3C';

    /**
     * Used for statuses with special cases.
     * Example statuses: Canceled.
     */
    public const SPECIAL = '#2C3E50';

    /**
     * Class is not meant to be initialized.
     */
    private function __construct()
    {
    }
}
