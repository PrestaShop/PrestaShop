<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context;

use Configuration;
use Order;
use OrderReturn;
use OrderReturnState;
use PrestaShopDatabaseException;
use PrestaShopException;

class OrderReturnFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @When I add order return state :orderReturnStateReference
     *
     * @param string $orderReturnStateReference
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function createOrderReturnState(string $orderReturnStateReference): void
    {
        $orderReturnState = new OrderReturnState(null, (int) Configuration::get('PS_LANG_DEFAULT'));
        $orderReturnState->name = 'New order return state';
        $orderReturnState->add();
        SharedStorage::getStorage()->set($orderReturnStateReference, (int) $orderReturnState->id);
    }

    /**
     * @When I add order return :orderReturnReference from order :orderReference
     *
     * @param string $orderReturnReference
     * @param string $orderReference
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function createOrderReturn(string $orderReturnReference, string $orderReference): void
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $order = new Order($orderId);
        $orderReturn = new OrderReturn();
        $orderReturn->id_customer = (int) $order->id_customer;
        $orderReturn->id_order = $order->id;
        $orderReturn->question = 'Why?';
        $orderReturn->state = 1;
        $orderReturn->add();
        $orderDetailIds = $quantities = [];
        foreach ($order->getProducts() as $product) {
            $orderDetailIds[] = $product['id_order_detail'];
            $quantities[] = $product['product_quantity'];
        }
        $orderReturn->addReturnDetail($orderDetailIds, $quantities);
        SharedStorage::getStorage()->set($orderReturnReference, $orderReturn->id);
    }
}
