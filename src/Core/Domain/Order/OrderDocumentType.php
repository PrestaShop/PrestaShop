<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order;

/**
 * Used to identity which type of document the orders has
 */
class OrderDocumentType
{
    public const CREDIT_SLIP = 'credit_slip';

    public const DELIVERY_SLIP = 'delivery_slip';

    public const INVOICE = 'invoice';
}
