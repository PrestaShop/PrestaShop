<?php


namespace Tests\Integration\Behaviour\Features\Context\Domain;


use Behat\Behat\Tester\Exception\PendingException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartInformation;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartInformation;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderCustomerForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class OrderCartFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Given order with id :orderId has customer with id :customerId
     *
     * @param int $orderId
     * @param int $customerId
     *
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     */
    public function orderWithIdHasCustomerWithId(int $orderId, int $customerId)
    {
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        /** @var OrderCustomerForViewing $orderCustomerForViewing */
        $orderCustomerForViewing = $orderForViewing->getCustomer();
        $customerIdReceived = $orderCustomerForViewing->getId();
        assertSame(
            $customerId,
            $customerIdReceived,
            sprintf('Expected customer with id "%s" but received "%s', $customerId, $customerIdReceived)
        );
    }


    /**
     * @Given there is cart with id :cartId for order with id :orderId
     *
     * @param int $cartId
     * @param int $orderId
     *
     * @throws CartConstraintException
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     */
    public function thereIsCartWithIdForOrderWithId(int $cartId, int $orderId)
    {
        /** @var CartInformation $cartInformation */
        $cartInformation = $this->getQueryBus()->handle(new GetCartInformation($orderId));
        $cartIdReceived = $cartInformation->getCartId();
        assertSame(
            $cartId,
            $cartIdReceived,
            sprintf('Expected cart id "%s" but received "%s"', $cartId, $cartIdReceived)
        );
    }


    /**
     * @When I duplicate order with id :arg1 cart
     *
     * @throws PendingException
     */
    public function iDuplicateOrderWithIdCart($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then customer with id :arg1 has empty cart
     */
    public function customerWithIdHasEmptyCart($arg1)
    {
        throw new PendingException();
    }


}
