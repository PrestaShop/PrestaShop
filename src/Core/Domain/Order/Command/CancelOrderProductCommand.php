<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace PrestaShop\PrestaShop\Core\Domain\Order\Command;

use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;

class CancelOrderProductCommand
{
    /**
     * @var array $products
     */
    private $products;

    /**
     * @var array $toBeCanceledProducts
     *
     * key: orderDetailId, value: quantity
     */
    private $toBeCanceledProducts;

    /**
     * @var OrderForViewing $order
     */
    private $order;

    /**
     * CancelOrderProductCommand constructor.
     *
     * @param array $products
     * @param array $toBeCanceledProducts
     * @param OrderForViewing $order
     */
    public function __construct(array $products, array $toBeCanceledProducts, OrderForViewing $order)
    {
        $this->products = $products;
        $this->toBeCanceledProducts = $toBeCanceledProducts;
        $this->order = $order;
    }

    /**
     * @return array
     */
    public function getToBeCanceledProducts()
    {
        return $this->toBeCanceledProducts;
    }

    /**
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @return OrderForViewing
     */
    public function getOrder()
    {
        return $this->order;
    }
}
