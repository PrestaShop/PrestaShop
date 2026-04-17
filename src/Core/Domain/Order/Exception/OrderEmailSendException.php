<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Exception;

/**
 * Thrown when failed to send email associated with order
 */
class OrderEmailSendException extends OrderException
{
    /**
     * When order email resending failed
     */
    public const FAILED_RESEND = 1;

    /**
     * When order process email sending failed
     */
    public const FAILED_SEND_PROCESS_ORDER = 2;
}
