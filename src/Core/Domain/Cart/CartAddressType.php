<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Cart;

/**
 * Used to identity which type of address has to be edited
 */
class CartAddressType
{
    public const DELIVERY_ADDRESS_TYPE = 'delivery_address';

    public const INVOICE_ADDRESS_TYPE = 'invoice_address';
}
