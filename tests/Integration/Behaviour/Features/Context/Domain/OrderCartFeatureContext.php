<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use PHPUnit_Framework_Assert;
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
        PHPUnit_Framework_Assert::assertSame(
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

        PHPUnit_Framework_Assert::assertNotSame($cartInformation->getCartId(), $duplicatedCartInformation->getCartId());

        PHPUnit_Framework_Assert::assertEquals($cartInformation->getCartRules(), $duplicatedCartInformation->getCartRules());
        PHPUnit_Framework_Assert::assertEquals($cartInformation->getAddresses(), $duplicatedCartInformation->getAddresses());
        PHPUnit_Framework_Assert::assertEquals($cartInformation->getCurrencyId(), $duplicatedCartInformation->getCurrencyId());
        PHPUnit_Framework_Assert::assertEquals($cartInformation->getProducts(), $duplicatedCartInformation->getProducts());
        PHPUnit_Framework_Assert::assertEquals($cartInformation->getSummary(), $duplicatedCartInformation->getSummary());
        PHPUnit_Framework_Assert::assertEquals($cartInformation->getLangId(), $duplicatedCartInformation->getLangId());
        // shipping info has to be confirmed and is saved in the db after the order is created
        PHPUnit_Framework_Assert::assertNotEquals(
            $cartInformation->getShipping(),
            $duplicatedCartInformation->getShipping(),
            'shipping info is the same');
    }
}
