<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Class PaymentFree
 * Simple class to allow free order.
 */
class PaymentFree extends PaymentModule
{
    /** @var bool Status */
    public $active = true;
    /** @var string */
    public $name = 'free_order';
    /** @var string */
    public $displayName = 'Free order';
}
