<?php
/**
 * 2007-2020 Friends of PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Exception;

/**
 * Thrown when the order state is incompatible with an action (ex: standard
 * refund on an order not paid yet).
 */
class InvalidOrderStateException extends OrderException
{
}
