<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\Status;

/**
 * Defines colors for order statuses
 */
class CustomerThreadStatusColor
{
    /**
     * Used for status when customer thread is open.
     */
    public const OPENED = '#01B887';

    /**
     * Used for statuses when customer thread is closed.
     * Example statuses: Processing in progress, On backorder (paid), Payment accepted.
     */
    public const CLOSED = '#2C3E50';

    /**
     * Used for status when customer thread is pending_1.
     */
    public const PENDING_1 = '#3498D8';

    /**
     * Used for status when customer thread is pending_2.
     */
    public const PENDING_2 = '#34209E';

    public const CUSTOMER_THREAD_STATUSES = [
        'open' => self::OPENED,
        'closed' => self::CLOSED,
        'pending1' => self::PENDING_1,
        'pending2' => self::PENDING_2,
    ];

    /**
     * Class is not meant to be initialized.
     */
    private function __construct()
    {
    }
}
