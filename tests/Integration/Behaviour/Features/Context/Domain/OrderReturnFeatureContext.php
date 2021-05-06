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

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Configuration;
use Order;
use OrderReturn;
use OrderReturnState;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command\UpdateOrderReturnStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnConstraintException;
use PrestaShopDatabaseException;
use PrestaShopException;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class OrderReturnFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I add order return state :orderReturnStateReference
     *
     * @param string $orderReturnStateReference
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function createOrderReturnState(string $orderReturnStateReference): void
    {
        $orderReturnState = new OrderReturnState(null, Configuration::get('PS_LANG_DEFAULT'));
        $orderReturnState->name = 'New order return state';
        $orderReturnState->add();
        SharedStorage::getStorage()->set($orderReturnStateReference, $orderReturnState->id);
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
        $orderDetailIds = $quantities = $customizationIds = $customizationQuantityIds = [];
        foreach ($order->getProducts() as $product) {
            $orderDetailIds[] = $product['id_order_detail'];
            $quantities[] = $product['product_quantity'];
        }
        $orderReturn->addReturnDetail($orderDetailIds, $quantities, $customizationIds, $customizationQuantityIds);
        SharedStorage::getStorage()->set($orderReturnReference, $orderReturn->id);
    }

    /**
     * @When I change order return :orderReturnReference state to :orderReturnStateReference
     *
     * @param string $orderReturnReference
     * @param string $orderRetunStateReference
     *
     * @throws OrderReturnConstraintException
     */
    public function updateOrderReturnState(string $orderReturnReference, string $orderRetunStateReference): void
    {
        $orderReturnId = SharedStorage::getStorage()->get($orderReturnReference);
        $orderReturnStateId = SharedStorage::getStorage()->get($orderRetunStateReference);

        $this->getCommandBus()->handle(
            new UpdateOrderReturnStateCommand(
                (int) $orderReturnId,
                (int) $orderReturnStateId
            )
        );
    }

    /**
     * @Given :orderReturnReference has state :orderReturnStateReference
     *
     * @param string $orderReturnReference
     * @param string $orderReturnStateReference
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function checkOrderReturnState(string $orderReturnReference, string $orderReturnStateReference): void
    {
        $orderReturnId = SharedStorage::getStorage()->get($orderReturnReference);
        $orderReturn = new OrderReturn($orderReturnId);
        $orderReturnStateId = SharedStorage::getStorage()->get($orderReturnStateReference);
        if ($orderReturn->state !== $orderReturnStateId) {
            $errorMessage = sprintf('Invalid order state for  %s, expected %s but got %s', $orderReturnReference, $orderReturnStateReference, $orderReturn->state);
            throw new RuntimeException($errorMessage);
        }
    }
}
