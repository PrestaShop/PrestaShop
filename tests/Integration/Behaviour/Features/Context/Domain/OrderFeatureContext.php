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

use AdminController;
use Behat\Gherkin\Node\TableNode;
use Cart;
use Context;
use FrontController;
use Order;
use OrderState;
use PHPUnit\Framework\Assert as Assert;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\InvalidCartRuleDiscountValueException;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\AddCartRuleToOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\AddOrderFromBackOfficeCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\BulkChangeOrderStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\DeleteCartRuleFromOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\DuplicateOrderCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\SetInternalOrderNoteCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\UpdateOrderStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidProductQuantityException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\Command\GenerateInvoiceCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\AddProductToOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\DeleteProductFromOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\UpdateProductInOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderDiscountForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderInvoiceAddressForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductOutOfStockException;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\FoundProduct;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\OrderStateByIdChoiceProvider;
use PrestaShopCollection;
use Product;
use RuntimeException;
use stdClass;
use Tax;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class OrderFeatureContext extends AbstractDomainFeatureContext
{
    private const ORDER_CART_RULE_FREE_SHIPPING = 'Free Shipping';

    /**
     * @var array
     */
    private $productStock = [];

    /**
     * @BeforeScenario
     */
    public function before()
    {
        // needed because if no controller defined then CONTEXT_ALL is selected and exception is thrown
        /** @var AdminController|FrontController $adminControllerTestDouble */
        $adminControllerTestDouble = new stdClass();
        $adminControllerTestDouble->controller_type = 'admin';
        $adminControllerTestDouble->php_self = 'dummyTestDouble';
        Context::getContext()->controller = $adminControllerTestDouble;
    }

    /**
     * @Given I add order :orderReference with the following details:
     *
     * @param $orderReference
     * @param TableNode $table
     */
    public function addOrderWithTheFollowingDetails($orderReference, TableNode $table)
    {
        $testCaseData = $table->getRowsHash();

        $data = $this->mapAddOrderFromBackOfficeData($testCaseData);

        /** @var OrderId $orderId */
        $orderId = $this->getCommandBus()->handle(
            new AddOrderFromBackOfficeCommand(
                $data['cartId'],
                $data['employeeId'],
                $data['orderMessage'],
                $data['paymentModuleName'],
                $data['orderStateId']
            )
        );

        SharedStorage::getStorage()->set($orderReference, $orderId->getValue());
    }

    /**
     * @When I add order :orderReference from cart :cartReference with :paymentModuleName payment method and :orderStatus order status
     *
     * @param string $orderReference
     * @param string $cartReference
     * @param string $paymentModuleName
     * @param string $orderStatus
     */
    public function placeOrderWithPaymentMethodAndOrderStatus(
        string $orderReference,
        string $cartReference,
        string $paymentModuleName,
        string $orderStatus
    ) {
        $orderStates = OrderState::getOrderStates(Context::getContext()->language->id);
        $orderStatusId = null;

        foreach ($orderStates as $state) {
            if ($state['name'] === $orderStatus) {
                $orderStatusId = (int) $state['id_order_state'];
            }
        }

        /** @var OrderId $orderId */
        $orderId = $this->getCommandBus()->handle(
            new AddOrderFromBackOfficeCommand(
                (int) SharedStorage::getStorage()->get($cartReference),
                (int) Context::getContext()->employee->id,
                '',
                $paymentModuleName,
                $orderStatusId
            )
        );

        SharedStorage::getStorage()->set($orderReference, $orderId->getValue());
    }

    /**
     * @When I add products to order :orderReference with new invoice and the following products details:
     *
     * @param string $orderReference
     * @param TableNode $table
     */
    public function addProductsToOrderWithNewInvoiceAndTheFollowingDetails(string $orderReference, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);

        $data = $table->getRowsHash();
        $productName = $data['name'];
        $product = $this->getProductByName($productName);
        $productId = $product->getProductId();
        if (isset($data['combination'])) {
            $combinationId = $this->getProductCombinationId($product, $data['combination']);
        } else {
            $combinationId = 0;
        }

        try {
            $this->getCommandBus()->handle(
                AddProductToOrderCommand::withNewInvoice(
                    $orderId,
                    $productId,
                    $combinationId,
                    $data['price'],
                    $data['price'],
                    (int) $data['amount'],
                    PrimitiveUtils::castStringBooleanIntoBoolean($data['free_shipping'] ?? false)
                )
            );
        } catch (InvalidProductQuantityException $e) {
            $this->setLastException($e);
        } catch (ProductOutOfStockException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I remove product :productReference from order :orderReference
     *
     * @param string $productReference
     * @param string $orderReference
     */
    public function removeProductsFromOrder(string $productReference, string $orderReference)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $productId = $this->getProductIdByName($productReference);
        $orderDetailId = null;

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        /** @var OrderProductForViewing[] $products */
        $products = $orderForViewing->getProducts()->getProducts();
        foreach ($products as $product) {
            if ($product->getId() == $productId) {
                $orderDetailId = $product->getOrderDetailId();
                break;
            }
        }
        if (empty($orderDetailId)) {
            throw new RuntimeException(
                sprintf(
                    'Product %s has not been found in order %s',
                    $productReference,
                    $orderReference
                )
            );
        }

        try {
            $this->getCommandBus()->handle(
                new DeleteProductFromOrderCommand($orderId, $orderDetailId)
            );
        } catch (OrderException | OrderNotFoundException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I add products to order :orderReference to last invoice and the following products details:
     *
     * @param string $orderReference
     * @param TableNode $table
     */
    public function addProductsToOrderWithExistingInvoiceAndTheFollowingDetails(string $orderReference, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $order = new Order($orderId);
        $invoicesCollection = $order->getInvoicesCollection();
        $lastInvoice = $invoicesCollection->getLast();

        $data = $table->getRowsHash();
        $productName = $data['name'];
        $productId = $this->getProductIdByName($productName);

        try {
            $this->getCommandBus()->handle(
                AddProductToOrderCommand::toExistingInvoice(
                    (int) $orderId,
                    (int) $lastInvoice->id,
                    (int) $productId,
                    0,
                    $data['price'],
                    $data['price'],
                    (int) $data['amount']
                )
            );
        } catch (InvalidProductQuantityException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then order :orderReference should have :expectedCount cart rule(s)
     *
     * @param string$orderReference
     * @param int $expectedCount
     */
    public function checkOrderCartRulesCount(string $orderReference, int $expectedCount)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);

        /** @var OrderProductForViewing[] $orderProducts */
        $orderDiscounts = $this->getOrderDiscounts($orderId);

        if (count($orderDiscounts) == $expectedCount) {
            return;
        }
        throw new RuntimeException(
            sprintf(
                'Invalid number of cart rules for order %s, expected %s but got %s instead',
                $orderReference,
                $expectedCount,
                count($orderDiscounts)
            )
        );
    }

    /**
     * @When I remove cart rule :cartRuleName from order :reference
     *
     * @param string $cartRuleName
     * @param string $orderReference
     */
    public function deleteCartRuleFromOrder(string $cartRuleName, string $orderReference)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        /** @var OrderDiscountForViewing $discount */
        $discount = $this->getOrderDiscountByName($orderId, $cartRuleName);
        if (null === $discount) {
            throw new RuntimeException(
                sprintf(
                    'Cannot delete cart rule "%s" from Order "%s" because it does not have it',
                    $cartRuleName,
                    $orderReference
                )
            );
        }

        $this->getCommandBus()->handle(
            new DeleteCartRuleFromOrderCommand($orderId, $discount->getOrderCartRuleId())
        );
    }

    /**
     * @Then order :reference should have cart rule :cartRuleName with amount :cartRuleAmount
     *
     * @param string $orderReference
     * @param string $cartRuleName
     * @param string $cartRuleAmount
     */
    public function createdOrderShouldHaveCartRule(string $orderReference, string $cartRuleName, string $cartRuleAmount)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);

        /** @var OrderDiscountForViewing $discount */
        $discount = $this->getOrderDiscountByName($orderId, $cartRuleName);
        if (null === $discount) {
            throw new RuntimeException(
                sprintf(
                    'Order "%s" should have cart rule "%s".',
                    $orderReference,
                    $cartRuleName
                )
            );
        }

        if ($cartRuleAmount !== $discount->getAmountFormatted()) {
            throw new RuntimeException(
                sprintf(
                    'Order "%s" has cart rule "%s" but amount is %s whereas %s was expected',
                    $orderReference,
                    $cartRuleName,
                    $discount->getAmountFormatted(),
                    $cartRuleAmount
                )
            );
        }
    }

    /**
     * @Then order :reference should not have cart rule :cartRuleName
     *
     * @param string $orderReference
     * @param string $cartRuleName
     */
    public function createdOrderShouldNotHaveCartRule(string $orderReference, string $cartRuleName)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);

        /** @var OrderDiscountForViewing $discount */
        $discount = $this->getOrderDiscountByName($orderId, $cartRuleName);
        if (null !== $discount) {
            throw new RuntimeException(
                sprintf(
                    'Order "%s" should not have cart rule "%s".',
                    $orderReference,
                    $cartRuleName
                )
            );
        }
    }

    /**
     * @Then order :orderReference should have :expectedCount invoices
     */
    public function checkOrderInvoicesCount(string $orderReference, int $expectedCount)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $order = new Order($orderId);
        $invoicesCollection = $order->getInvoicesCollection();

        if ($expectedCount !== $invoicesCollection->count()) {
            throw new RuntimeException(sprintf(
                'Invalid number of invoices for order %s, expected %s but got %s instead',
                $orderReference,
                $expectedCount,
                $invoicesCollection->count()
            ));
        }
    }

    /**
     * @When I edit product :productName to order :orderReference with following products details:
     *
     * @param string $productName
     * @param string $orderReference
     * @param TableNode $table
     */
    public function editProductsToOrderWithFollowingDetails(string $productName, string $orderReference, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $productOrderDetail = $this->getOrderDetailFromOrder($productName, $orderReference);
        $data = $table->getRowsHash();

        $this->updateProductInOrder($orderId, $productOrderDetail, $data);
    }

    /**
     * @When I edit combination :combinationName of product :productName to order :orderReference with following products details:
     *
     * @param string $combinationName
     * @param string $productName
     * @param string $orderReference
     * @param TableNode $table
     */
    public function editCombinationToOrderWithFollowingDetails(string $combinationName, string $productName, string $orderReference, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $productOrderDetail = $this->getOrderDetailFromOrder($productName, $orderReference, $combinationName);
        $data = $table->getRowsHash();

        $this->updateProductInOrder($orderId, $productOrderDetail, $data);
    }

    /**
     * @param int $orderId
     * @param array $productOrderDetail
     * @param array $data
     *
     * @throws \PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidAmountException
     */
    private function updateProductInOrder(int $orderId, array $productOrderDetail, array $data)
    {
        try {
            $this->getCommandBus()->handle(
                new UpdateProductInOrderCommand(
                    (int) $orderId,
                    (int) $productOrderDetail['id_order_detail'],
                    $data['price'],
                    $data['price'],
                    (int) $data['amount']
                )
            );
        } catch (InvalidProductQuantityException $e) {
            $this->setLastException($e);
        } catch (ProductOutOfStockException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then I should get error that product quantity is invalid for order
     */
    public function assertLastErrorIsNegativeProductQuantity()
    {
        $this->assertLastErrorIs(InvalidProductQuantityException::class);
    }

    /**
     * @Then I should get error that product is out of stock
     */
    public function assertLastErrorIsProductOutOfStock()
    {
        $this->assertLastErrorIs(ProductOutOfStockException::class);
    }

    /**
     * @When I generate invoice for :invoiceReference order
     *
     * @param string $orderReference
     */
    public function generateOrderInvoice(string $orderReference)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $this->getCommandBus()->handle(
            new GenerateInvoiceCommand($orderId)
        );
    }

    /**
     * @When I update orders :orderIdsString statuses to :status
     *
     * @param string $orderReferencesString
     * @param string $status
     */
    public function updateOrdersToStatuses(string $orderReferencesString, string $status)
    {
        /** @var string[] $orderReferencesString */
        $orderReferencesString = explode(',', $orderReferencesString);
        $ordersIds = [];
        foreach ($orderReferencesString as $orderReference) {
            $ordersIds[] = SharedStorage::getStorage()->get($orderReference);
        }

        /** @var OrderStateByIdChoiceProvider $orderStateChoiceProvider */
        $orderStateChoiceProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.order_state_by_id');
        $availableOrderStates = $orderStateChoiceProvider->getChoices();
        $statusId = (int) $availableOrderStates[$status];

        $this->getCommandBus()->handle(
            new BulkChangeOrderStatusCommand(
                $ordersIds, $statusId
            )
        );
    }

    /**
     * @Then order :orderReference has status :status
     *
     * @param string $orderReference
     * @param string $status
     *
     * @throws RuntimeException
     */
    public function orderHasStatus(string $orderReference, string $status)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        /** @var OrderState $currentOrderState */
        $currentOrderStateId = $orderForViewing->getHistory()->getCurrentOrderStatusId();

        /** @var OrderStateByIdChoiceProvider $orderStateChoiceProvider */
        $orderStateChoiceProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.order_state_by_id');
        $availableOrderStates = $orderStateChoiceProvider->getChoices();
        $expectedStatusId = (int) $availableOrderStates[$status];

        if ($currentOrderStateId !== $expectedStatusId) {
            throw new RuntimeException('After changing order status id should be [' . $expectedStatusId . '] but received [' . $currentOrderStateId . ']');
        }
    }

    /**
     * @When I update order :orderReference status to :status
     *
     * @param string $orderReference
     * @param string $status
     */
    public function updateOrderStatusTo(string $orderReference, string $status)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);

        /** @var OrderStateByIdChoiceProvider $orderStateChoiceProvider */
        $orderStateChoiceProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.order_state_by_id');
        $availableOrderStates = $orderStateChoiceProvider->getChoices();
        $statusId = (int) $availableOrderStates[$status];

        $this->getCommandBus()->handle(
            new UpdateOrderStatusCommand(
                $orderId,
                $statusId
            )
        );
    }

    /**
     * @Then order :reference should have :quantity products in total
     *
     * @param string $reference
     * @param int $quantity
     */
    public function assertOrderProductsQuantity(string $reference, int $quantity)
    {
        $orderId = SharedStorage::getStorage()->get($reference);

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        /** @var OrderProductForViewing[] $orderProducts */
        $orderProducts = $orderForViewing->getProducts()->getProducts();

        $totalQuantity = 0;
        foreach ($orderProducts as $orderProduct) {
            $totalQuantity += $orderProduct->getQuantity();
        }

        if ($totalQuantity !== $quantity) {
            throw new RuntimeException(sprintf('Order should have "%d" products, but has "%d".', $totalQuantity, $quantity));
        }
    }

    /**
     * @Then order :reference should have free shipping
     *
     * @param string $reference
     */
    public function createdOrderShouldHaveFreeShipping(string $reference)
    {
        $orderId = SharedStorage::getStorage()->get($reference);

        /** @var OrderDiscountForViewing $discount */
        $discount = $this->getOrderDiscountByName($orderId, self::ORDER_CART_RULE_FREE_SHIPPING);
        if (null === $discount) {
            throw new RuntimeException('Order should have free shipping.');
        }
    }

    /**
     * @Then order :orderReference should have invoice
     *
     * @param string $orderReference
     */
    public function orderShouldHaveInvoice(string $orderReference)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        /* @var OrderInvoiceAddressForViewing $invoiceAddress */
        $orderForViewing->getInvoiceAddress();
    }

    /**
     * @Given order :orderReference does not have any invoices
     *
     * @param string $orderReference
     */
    public function orderDoesNotHaveAnyInvoices(string $orderReference)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        /** @var OrderInvoiceAddressForViewing $invoiceAddress */
        $invoiceAddress = $orderForViewing->getInvoiceAddress();
        Assert::assertNotNull($invoiceAddress);
    }

    /**
     * @Given order with reference :orderReference does not contain product :productName
     *
     * @param string $orderReference
     * @param string $productName
     */
    public function orderDoesNotContainProduct(string $orderReference, string $productName)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $productId = $this->getProductIdByName($productName);

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));

        /** @var OrderProductForViewing[] $orderProducts */
        $orderProducts = $orderForViewing->getProducts()->getProducts();

        foreach ($orderProducts as $orderProduct) {
            if ($orderProduct->getId() == $productId) {
                throw new RuntimeException(sprintf('Order with reference "%s" contains product with reference "%s".', $orderReference, $productName));
            }
        }
    }

    /**
     * @When I duplicate :orderReference to create cart :duplicatedCartReference
     */
    public function duplicateOrder($orderReference, $duplicatedCartReference)
    {
        $orderId = (int) SharedStorage::getStorage()->get($orderReference);

        /** @var CartId $cartId */
        $cartId = $this->getCommandBus()->handle(
            new DuplicateOrderCartCommand($orderId)
        );

        SharedStorage::getStorage()->set($duplicatedCartReference, new Cart($cartId->getValue()));
    }

    /**
     * @Then order :orderReference should contain :quantity product(s) :productName
     * @Then order :orderReference should contain :quantity combination(s) :combinationName of product :productName
     *
     * @param string $orderReference
     * @param int $quantity
     * @param string $productName
     * @param string|null $combinationName
     */
    public function orderContainsProductWithReference(string $orderReference, int $quantity, string $productName, ?string $combinationName = null)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);

        $product = $this->getProductByName($productName);
        $productId = $product->getProductId();
        $combinationId = null !== $combinationName ? $this->getProductCombinationId($product, $combinationName) : null;

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        /** @var OrderProductForViewing[] $orderProducts */
        $orderProducts = $orderForViewing->getProducts()->getProducts();

        $productQuantity = 0;
        foreach ($orderProducts as $orderProduct) {
            if ($orderProduct->getId() === $productId
                && (null === $combinationId || $orderProduct->getCombinationId() === $combinationId)) {
                $productQuantity += $orderProduct->getQuantity();
            }
        }

        if ($productQuantity == $quantity) {
            return;
        }
        throw new RuntimeException(
            sprintf(
                'Order was expected to have "%d" products "%s" in it. Instead got "%d"',
                $quantity,
                $productName,
                $productQuantity
            )
        );
    }

    /**
     * @Then order :orderReference should contain :quantity refunded products :productName
     *
     * @param string $orderReference
     * @param int $quantity
     * @param string $productName
     */
    public function orderContainsRefundedProductWithReference(string $orderReference, int $quantity, string $productName)
    {
        $productQuantities = $this->getProductQuantitiesByReference($orderReference, $productName);

        if ((int) $productQuantities['refunded_quantity'] === (int) $quantity) {
            return;
        }

        throw new RuntimeException(sprintf('Order was expected to have "%d" refunded products "%s" in it. Instead got "%s"', $quantity, $productName, $productQuantities['refunded_quantity']));
    }

    /**
     * @Then product :productName in order :orderReference has following details:
     * @Then product :productReference named :productName in order :orderReference has following details:
     *
     * @param string $orderReference
     * @param string $productName
     * @param TableNode $table
     * @param string|null $productReference saves product reference to shared storage if provided
     */
    public function checkProductDetailsWithReference(string $orderReference, string $productName, TableNode $table, ?string $productReference = null)
    {
        $productOrderDetail = $this->getOrderDetailFromOrder($productName, $orderReference);
        $expectedDetails = $table->getRowsHash();
        foreach ($expectedDetails as $detailName => $expectedDetailValue) {
            Assert::assertEquals(
                (float) $expectedDetailValue,
                $productOrderDetail[$detailName],
                sprintf(
                    'Invalid product detail field %s for product %s, expected %s instead of %s',
                    $detailName,
                    $productName,
                    $expectedDetailValue,
                    $productOrderDetail[$detailName]
                )
            );
        }

        if ($productReference) {
            $this->getSharedStorage()->set($productReference, $this->getProductIdByName($productName));
        }
    }

    /**
     * @Then /^I watch the stock of product "(.+)"$/
     *
     * This statement must be called to store an initial stock for a product which then
     * allows you to simply check the expected differences in stock (1 less, 2 more, ...).
     *
     * @param string $productName
     */
    public function storeProductInStock(string $productName)
    {
        $productId = $this->getProductIdByName($productName);
        $nbProduct = Product::getQuantity($productId);
        $this->productStock[$productName] = $nbProduct;
    }

    /**
     * @Then /^there are ([\-\d]+) (less|more) "(.+)" in stock$/
     *
     * @param int $productDifference
     * @param string $factor
     * @param string $productName
     */
    public function checkProductStockDifference(int $productDifference, string $factor, string $productName)
    {
        if (!isset($this->productStock[$productName])) {
            throw new RuntimeException('Cannot compare a stock that has not been checked initially');
        }
        $initialQuantity = $this->productStock[$productName];
        $factor = 'less' === $factor ? -1 : 1;
        $expectedDifference = $factor * $productDifference;
        $expectedQuantity = $initialQuantity + $expectedDifference;

        $productId = $this->getProductIdByName($productName);
        $nbProduct = Product::getQuantity($productId);
        if ($expectedQuantity != $nbProduct) {
            throw new RuntimeException(sprintf('Invalid difference for product %s expected %s, got %s instead', $productName, $expectedDifference, $nbProduct - $initialQuantity));
        }
        $this->productStock[$productName] = $expectedQuantity;
    }

    /**
     * @Given Order :orderReference has following prices:
     * @Then Order :orderReference should have following prices:
     */
    public function assertOrderPrices(string $orderReference, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $data = $table->getRowsHash();

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));

        $totalProducts = $orderForViewing->getPrices()->getProductsPriceFormatted();
        $totalDiscounts = $orderForViewing->getPrices()->getDiscountsAmountFormatted();
        $totalShipping = $orderForViewing->getPrices()->getShippingPriceFormatted();
        $totalTaxes = $orderForViewing->getPrices()->getTaxesAmountFormatted();
        $totalPrice = $orderForViewing->getPrices()->getTotalAmountFormatted();

        Assert::assertEquals($data['products'], $totalProducts);
        Assert::assertEquals($data['discounts'], $totalDiscounts);
        Assert::assertEquals($data['shipping'], $totalShipping);
        Assert::assertEquals($data['taxes'], $totalTaxes);
        Assert::assertEquals($data['total'], $totalPrice);
    }

    /**
     * @Then order :orderReference should have the following details:
     *
     * @param string $orderReference
     * @param TableNode $table
     */
    public function queryOrderToGetTheFollowingProperties(string $orderReference, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $this->assertOrderPropertiesEquals(new Order($orderId), $table->getRowsHash());
    }

    /**
     * @When I add discount to order :orderReference with following details:
     *
     * @param string $orderReference
     * @param TableNode $data
     */
    public function addAmountTypeCartRuleToOrder(string $orderReference, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $data = $table->getRowsHash();

        try {
            $this->getQueryBus()->handle(new AddCartRuleToOrderCommand(
                $orderId,
                $data['name'],
                $data['type'],
                $data['value']
            ));
        } catch (InvalidCartRuleDiscountValueException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I add discount to order :orderReference on last invoice and following details:
     *
     * @param string $orderReference
     * @param TableNode $table
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function addAmountTypeCartRuleAndUpdateSingleInvoice(string $orderReference, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $data = $table->getRowsHash();

        $invoices = $this->getOrderInvoices($orderId);
        Assert::assertGreaterThanOrEqual(1, $invoices->count());

        $this->getQueryBus()->handle(new AddCartRuleToOrderCommand(
            $orderId,
            $data['name'],
            $data['type'],
            $data['value'],
            (int) $invoices->getLast()->id
        ));
    }

    /**
     * @Then I should get error that cart rule has invalid min percent
     */
    public function assertInvalidMinPercentCartRule(): void
    {
        $this->assertLastErrorIs(
            InvalidCartRuleDiscountValueException::class,
            InvalidCartRuleDiscountValueException::INVALID_MIN_PERCENT
        );
    }

    /**
     * @Then I should get error that cart rule has invalid max percent
     */
    public function assertInvalidMaxPercentCartRule(): void
    {
        $this->assertLastErrorIs(
            InvalidCartRuleDiscountValueException::class,
            InvalidCartRuleDiscountValueException::INVALID_MAX_PERCENT
        );
    }

    /**
     * @Then I should get error that cart rule has invalid max amount
     */
    public function assertInvalidMaxAmountCartRule(): void
    {
        $this->assertLastErrorIs(
            InvalidCartRuleDiscountValueException::class,
            InvalidCartRuleDiscountValueException::INVALID_MAX_AMOUNT
        );
    }

    /**
     * @Then I should get error that cart rule has invalid min amount
     */
    public function assertInvalidMinAmountCartRule(): void
    {
        $this->assertLastErrorIs(
            InvalidCartRuleDiscountValueException::class,
            InvalidCartRuleDiscountValueException::INVALID_MIN_AMOUNT
        );
    }

    /**
     * @Then last invoice for order :orderReference should have following prices:
     */
    public function assertLastInvoicePrices(string $orderReference, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $data = $table->getRowsHash();

        $invoices = $this->getOrderInvoices($orderId);
        Assert::assertGreaterThanOrEqual(1, $invoices->count());

        $invoice = $invoices->getLast();
        Assert::assertEquals((float) $data['products'], $invoice->total_products);
        Assert::assertEquals((float) $data['discounts tax excluded'], $invoice->total_discount_tax_excl);
        Assert::assertEquals((float) $data['discounts tax included'], $invoice->total_discount_tax_incl);
        Assert::assertEquals((float) $data['shipping tax excluded'], $invoice->total_shipping_tax_excl);
        Assert::assertEquals((float) $data['shipping tax included'], $invoice->total_shipping_tax_incl);
        Assert::assertEquals((float) $data['total paid tax excluded'], $invoice->total_paid_tax_excl);
        Assert::assertEquals((float) $data['total paid tax included'], $invoice->total_paid_tax_incl);
    }

    /**
     * @When I change order :orderReference note to :internalNote
     *
     * @param string $orderReference
     * @param string $internalNote
     */
    public function changeOrderInternalNoteTo(string $orderReference, string $internalNote)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $this->getCommandBus()->handle(new SetInternalOrderNoteCommand($orderId, $internalNote));
    }

    /**
     * @Then order :orderReference note should be :internalNote
     *
     * @param string $orderReference
     * @param string $internalNote
     */
    public function internalNoteShouldBe(string $orderReference, string $internalNote)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        $expectedInternalNote = $orderForViewing->getNote();
        Assert::assertSame($expectedInternalNote, $internalNote);
    }

    /**
     * Sales-taxes US-FL 6%
     *
     * @Given tax :taxName is applied to order :ordeReference
     */
    public function assertTaxIsAppliedToOrder(string $taxName, string $orderReference)
    {
        $orderId = $this->getSharedStorage()->get($orderReference);
        $order = new Order($orderId);
        $expectedTaxId = (int) Tax::getTaxIdByName($taxName);

        if (!$expectedTaxId) {
            throw new RuntimeException(sprintf(
                'Tax "%s" does not exist',
                $taxName
            ));
        }

        $taxDetails = $order->getOrderDetailTaxes();

        foreach ($taxDetails as $taxDetail) {
            if (!empty($taxDetail['id_tax']) && (int) $taxDetail['id_tax'] === $expectedTaxId) {
                return;
            }
        }

        throw new RuntimeException(sprintf(
            'Tax "%s" is not applied to order "%s"',
            $taxName,
            $orderReference
        ));
    }

    /**
     * @param string $productName
     *
     * @return int
     */
    private function getProductByName(string $productName)
    {
        $products = $this->getQueryBus()->handle(new SearchProducts($productName, 1, Context::getContext()->currency->iso_code));

        if (empty($products)) {
            throw new RuntimeException(sprintf('Product with name "%s" was not found', $productName));
        }

        /** @var FoundProduct $product */
        $product = reset($products);

        return $product;
    }

    /**
     * @param string $productName
     *
     * @return int
     */
    private function getProductIdByName(string $productName)
    {
        $product = $this->getProductByName($productName);

        return (int) $product->getProductId();
    }

    /**
     * @param string $orderReference
     * @param string $productName
     *
     * @return array
     */
    private function getProductQuantitiesByReference(string $orderReference, string $productName)
    {
        $productId = $this->getProductIdByName($productName);
        $orderId = SharedStorage::getStorage()->get($orderReference);

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        /** @var OrderProductForViewing[] $products */
        $products = $orderForViewing->getProducts()->getProducts();

        $productQuantities = [
            'quantity' => 0,
            'refunded_quantity' => 0,
        ];
        foreach ($products as $product) {
            if ($product->getId() === $productId) {
                $productQuantities['quantity'] += $product->getQuantity();
                $productQuantities['refunded_quantity'] += $product->getQuantityRefunded();
            }
        }

        return $productQuantities;
    }

    /**
     * @param Order $order
     * @param array $data
     */
    private function assertOrderPropertiesEquals(Order $order, array $data): void
    {
        foreach (array_keys($data) as $property) {
            if (!property_exists($order, $property) || $data[$property] !== $order->$property) {
                throw new RuntimeException(
                    sprintf(
                        'Expected %s value to be equal to %s, got %s instead',
                        $property,
                        $data[$property],
                        $order->$property
                    )
                );
            }
        }
    }

    /**
     * @deprecated
     *
     * @param TableNode $table
     *
     * @return array
     *
     * @throws RuntimeException
     */
    private function extractFirstRowFromProperties(TableNode $table): array
    {
        $hash = $table->getHash();
        if (count($hash) != 1) {
            throw new RuntimeException('Properties are invalid');
        }
        /** @var array $data */
        $data = $hash[0];

        return $data;
    }

    /**
     * @param array $testCaseData
     *
     * @return array
     */
    private function mapAddOrderFromBackOfficeData(array $testCaseData)
    {
        $data = [];
        $cartId = SharedStorage::getStorage()->get($testCaseData['cart']);
        $data['cartId'] = $cartId;
        $data['employeeId'] = Context::getContext()->employee->id;
        $data['orderMessage'] = $testCaseData['message'];
        $data['paymentModuleName'] = $testCaseData['payment module name'];

        /** @var OrderStateByIdChoiceProvider $orderStateChoiceProvider */
        $orderStateChoiceProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.order_state_by_id');
        $availableOrderStates = $orderStateChoiceProvider->getChoices();
        $data['orderStateId'] = (int) $availableOrderStates[$testCaseData['status']];

        return $data;
    }

    /**
     * @param string $productName
     * @param string $orderReference
     * @param string|null $combinationName
     *
     * @return array
     */
    private function getOrderDetailFromOrder(string $productName, string $orderReference, string $combinationName = null): array
    {
        $product = $this->getProductByName($productName);
        $productId = $product->getProductId();
        $combinationId = null !== $combinationName ? $this->getProductCombinationId($product, $combinationName) : null;
        $order = new Order(SharedStorage::getStorage()->get($orderReference));
        $orderDetails = $order->getProducts();
        $productOrderDetail = null;
        foreach ($orderDetails as $orderDetail) {
            if ((int) $orderDetail['product_id'] === $productId
                && (null === $combinationId || (int) $orderDetail['product_attribute_id'] === $combinationId)) {
                $productOrderDetail = $orderDetail;
                break;
            }
        }

        if (null === $productOrderDetail) {
            throw new RuntimeException(sprintf('Cannot find product details for product %s in order %s', $productName, $orderReference));
        }

        return $productOrderDetail;
    }

    /**
     * @param FoundProduct $product
     * @param string $combinationName
     *
     * @return int
     */
    private function getProductCombinationId(FoundProduct $product, string $combinationName)
    {
        $combinationId = null;
        foreach ($product->getCombinations() as $productCombination) {
            if ($productCombination->getReference() == $combinationName) {
                $combinationId = $productCombination->getAttributeCombinationId();
                break;
            }
        }
        if (null === $combinationId) {
            throw new RuntimeException(sprintf('Could not find combination %s of product %s', $product->getName(), $combinationName));
        }

        return $combinationId;
    }

    /**
     * Gets order invoices collection
     *
     * @param int $orderId
     *
     * @return PrestaShopCollection
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function getOrderInvoices(int $orderId): PrestaShopCollection
    {
        $order = new \Order($orderId);

        return $order->getInvoicesCollection();
    }

    /**
     * @param int $orderId
     *
     * @return OrderProductForViewing[]
     */
    private function getOrderDiscounts(int $orderId): array
    {
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));

        return $orderForViewing->getDiscounts()->getDiscounts();
    }

    /**
     * @param int $orderId
     * @param string $cartRuleName
     *
     * @return OrderDiscountForViewing|null
     */
    private function getOrderDiscountByName(int $orderId, string $cartRuleName): ?OrderDiscountForViewing
    {
        $cartRuleName = CommonFeatureContext::getContainer()->get('translator')->trans(
            $cartRuleName,
            [],
            'Admin.Orderscustomers.Feature'
        );

        /** @var OrderDiscountForViewing[] $orderDiscountsForViewing */
        $orderDiscountsForViewing = $this->getOrderDiscounts($orderId);

        foreach ($orderDiscountsForViewing as $discount) {
            if ($discount->getName() == $cartRuleName) {
                return $discount;
            }
        }

        return null;
    }
}
