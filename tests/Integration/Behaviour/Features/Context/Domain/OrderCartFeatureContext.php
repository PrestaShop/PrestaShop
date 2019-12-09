<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartInformation;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartInformation;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\DuplicateOrderCartCommand;
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
     * @Given there is cart with id :cartId
     *
     * @param int $cartId
     *
     * @throws CartConstraintException
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     */
    public function thereIsCartWithId(int $cartId)
    {
        $this->getQueryBus()->handle(new GetCartInformation($cartId));
    }

    /**
     * @When I duplicate order with id :orderId cart
     *
     * @param int $orderId
     *
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     */
    public function iDuplicateOrderWithIdCart(int $orderId)
    {
        $this->getCommandBus()->handle(new DuplicateOrderCartCommand($orderId));
    }

    /**
     * @Then there is duplicated cart with id :cartId for cart with id :duplicatedCartId
     *
     * @param int $cartId
     * @param int $duplicatedCartId
     *
     * @throws CartConstraintException
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     */
    public function thereIsDuplicatedCartWithIdForCartWithId(int $cartId, int $duplicatedCartId)
    {
        /** @var CartInformation $cartInformation */
        $cartInformation = $this->getQueryBus()->handle(new GetCartInformation($cartId));
        /** @var CartInformation $duplicatedCartInformation */
        $duplicatedCartInformation = $this->getQueryBus()->handle(new GetCartInformation($duplicatedCartId));

        assertNotSame($cartInformation->getCartId(), $duplicatedCartInformation->getCartId());

        assertEquals($cartInformation->getCartRules(), $duplicatedCartInformation->getCartRules());
        assertEquals($cartInformation->getAddresses(), $duplicatedCartInformation->getAddresses());
        assertEquals($cartInformation->getCurrencyId(), $duplicatedCartInformation->getCurrencyId());
        assertEquals($cartInformation->getProducts(), $duplicatedCartInformation->getProducts());
        assertEquals($cartInformation->getSummary(), $duplicatedCartInformation->getSummary());
        assertEquals($cartInformation->getLangId(), $duplicatedCartInformation->getLangId());
        // shipping info has to be confirmed and is saved in the db after the order is created
        assertNotEquals(
            $cartInformation->getShipping(),
            $duplicatedCartInformation->getShipping(),
            'shipping info is the same');
    }
}
