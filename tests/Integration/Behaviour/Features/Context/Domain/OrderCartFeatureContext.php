<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use PHPUnit_Framework_Assert;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartInformation;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartInformation;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\DuplicateOrderCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderCustomerForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class OrderCartFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Given order :orderId has customer :customerId
     *
     * @param int $orderId
     * @param int $customerId
     */
    public function orderHasCustomer(int $orderId, int $customerId)
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
     * @When I duplicate order :orderReference cart :cartReference with reference :duplicatedCartReference
     *
     * @param string $orderReference
     * @param string $cartReference
     * @param string $duplicatedCartReference
     */
    public function duplicateOrderCart(string $orderReference, string $cartReference, string $duplicatedCartReference)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        /** @var CartId $cartIdObject */
        $cartIdObject = $this->getCommandBus()->handle(new DuplicateOrderCartCommand($orderId));
        SharedStorage::getStorage()->set($duplicatedCartReference, $cartIdObject->getValue());
    }

    /**
     * @Then there is duplicated cart :duplicatedCartReference for cart :cartReference
     *
     * @param string $duplicatedCartReference
     * @param string $cartReference
     */
    public function thereIsDuplicatedCartForCart(string $duplicatedCartReference, string $cartReference)
    {
        $duplicatedCartId = SharedStorage::getStorage()->get($duplicatedCartReference);
        $cartId = SharedStorage::getStorage()->get($cartReference);

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
