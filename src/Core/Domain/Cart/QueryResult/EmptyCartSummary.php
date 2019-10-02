<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult;

class EmptyCartSummary
{
    /**
     * @var array
     */
    private $carts;

    /**
     * @var array
     */
    private $orders;

    /**
     * @var array
     */
    private $cart;

    /**
     * @var array
     */
    private $addresses;

    /**
     * EmptyCartSummary constructor.
     *
     * @param array $carts
     * @param array $orders
     * @param array $cart
     * @param array $addresses
     */
    public function __construct(array $carts, array $orders, array $cart, array $addresses)
    {
        $this->carts = $carts;
        $this->orders = $orders;
        $this->cart = $cart;
        $this->addresses = $addresses;
    }

    /**
     * @return array
     */
    public function getCarts(): array
    {
        return $this->carts;
    }

    /**
     * @return array
     */
    public function getOrders(): array
    {
        return $this->orders;
    }

    /**
     * @return array
     */
    public function getCart(): array
    {
        return $this->cart;
    }

    /**
     * @return array
     */
    public function getAddresses(): array
    {
        return $this->addresses;
    }
}
