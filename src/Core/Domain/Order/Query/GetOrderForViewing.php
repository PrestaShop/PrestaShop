<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Query;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Get order for view in Back Office
 */
class GetOrderForViewing
{
    /**
     * @var OrderId
     */
    private $orderId;

    private $productsOrder;

    /**
     * @param int $orderId
     * @param string $productsOrder
     * @throws OrderException
     * @throws Exception
     */
    public function __construct(int $orderId, string $productsOrder = 'ASC')
    {
        $this->orderId = new OrderId($orderId);
        $this->setProductsOrder($productsOrder);
    }

    /**
     * @return OrderId
     */
    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    /**
     * @return mixed
     */
    public function getProductsOrder()
    {
        return $this->productsOrder;
    }

    /**
     * @param mixed $productsOrder
     * @return GetOrderForViewing
     * @throws Exception
     */
    public function setProductsOrder($productsOrder)
    {
        $this->assertProductsOrderSupported($productsOrder);

        $this->productsOrder = $productsOrder;

        return $this;
    }

    /**
     * @param string $productsOrder
     * @throws Exception
     */
    private function assertProductsOrderSupported(string $productsOrder)
    {
        if(!in_array($productsOrder, ['ASC', 'DESC'], true)) {
            throw new Exception('Products order not supported');
        }
    }
}
