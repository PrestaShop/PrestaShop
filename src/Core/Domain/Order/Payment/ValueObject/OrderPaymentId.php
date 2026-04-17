<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Payment\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Order\Payment\Exception\PaymentException;

/**
 * Order payment identity
 */
class OrderPaymentId
{
    /**
     * @var int
     */
    private $orderPaymentId;

    /**
     * @param int $orderPaymentId
     */
    public function __construct($orderPaymentId)
    {
        if (!is_int($orderPaymentId) || 0 >= $orderPaymentId) {
            throw new PaymentException('Invalid order payment id supplied.');
        }

        $this->orderPaymentId = $orderPaymentId;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->orderPaymentId;
    }
}
