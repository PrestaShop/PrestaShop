<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use PHPUnit\Framework\Assert as Assert;
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
        Assert::assertSame(
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

        Assert::assertNotSame($cartInformation->getCartId(), $duplicatedCartInformation->getCartId());

        Assert::assertEquals($cartInformation->getCartRules(), $duplicatedCartInformation->getCartRules());
        Assert::assertEquals($cartInformation->getAddresses(), $duplicatedCartInformation->getAddresses());
        Assert::assertEquals($cartInformation->getCurrencyId(), $duplicatedCartInformation->getCurrencyId());
        Assert::assertEquals($cartInformation->getProducts(), $duplicatedCartInformation->getProducts());
        Assert::assertEquals($cartInformation->getLangId(), $duplicatedCartInformation->getLangId());
        Assert::assertEquals(
            $this->convertSummaryToArray($cartInformation->getSummary()),
            $this->convertSummaryToArray($duplicatedCartInformation->getSummary())
        );
    }

    /**
     * We convert the summary to an array to filter data that won't be eaquals:
     * - order message
     * - process order link
     *
     * @param CartInformation\CartSummary $cartSummary
     *
     * @return array
     */
    private function convertSummaryToArray(CartInformation\CartSummary $cartSummary): array
    {
        return [
            $cartSummary->getTotalDiscount(),
            $cartSummary->getTotalPriceWithoutTaxes(),
            $cartSummary->getTotalPriceWithTaxes(),
            $cartSummary->getTotalProductsPrice(),
            $cartSummary->getTotalShippingPrice(),
            $cartSummary->getTotalTaxes(),
        ];
    }
}
