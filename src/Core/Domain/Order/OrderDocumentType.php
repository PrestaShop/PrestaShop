<?php
/**
 * 2007-2020 Friends of PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order;

/**
 * Used to identity which type of document the orders has
 */
class OrderDocumentType
{
    const CREDIT_SLIP = 'credit_slip';

    const DELIVERY_SLIP = 'delivery_slip';

    const INVOICE = 'invoice';
}
