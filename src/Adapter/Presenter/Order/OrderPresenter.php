<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Presenter\Order;

use Exception;
use Hook;
use Order;
use PrestaShop\PrestaShop\Adapter\Presenter\PresenterInterface;

class OrderPresenter implements PresenterInterface
{
    /**
     * @param Order $order
     *
     * @return OrderLazyArray
     *
     * @throws Exception
     */
    public function present($order)
    {
        if (!($order instanceof Order)) {
            throw new Exception('OrderPresenter can only present instance of Order');
        }

        $orderLazyArray = new OrderLazyArray($order);

        Hook::exec('actionPresentOrder',
            ['presentedOrder' => &$orderLazyArray]
        );

        return $orderLazyArray;
    }
}
