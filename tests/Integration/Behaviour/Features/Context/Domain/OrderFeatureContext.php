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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Address;
use AdminController;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Cart;
use Configuration;
use Context;
use FrontController;
use Order;
use OrderInvoice;
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
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\CannotFindProductInOrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\DuplicateProductInOrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\DuplicateProductInOrderInvoiceException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidProductQuantityException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\Command\GenerateInvoiceCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\AddProductToOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\DeleteProductFromOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\UpdateProductInOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderPreview;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderDiscountForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderInvoiceAddressForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPreview;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPreviewInvoiceDetails;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPreviewShippingDetails;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderShippingAddressForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductOutOfStockException;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\FoundProduct;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\OrderStateByIdChoiceProvider;
use PrestaShopCollection;
use Product;
use RuntimeException;
use SpecificPrice;
use stdClass;
use Tax;
use TaxCalculator;
use TaxManagerFactory;
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
     * @param string $orderReference
     * @param TableNode $table
     */
    public function addOrderWithTheFollowingDetails(string $orderReference, TableNode $table): void
    {
        $testCaseData = $table->getRowsHash();

        $data = $this->mapAddOrderFromBackOfficeData($testCaseData);

        /** @var OrderId $orderId */
        $orderId = $this->getCommandBus()->handle(
            new AddOrderFromBackOfficeCommand(
                $data['cartId'],
                (int) $data['employeeId'],
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
     * @When I add products to order :orderReference without invoice and the following products details:
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
        $productId = (int) $product->getProductId();

        $combinationId = isset($data['combination']) ? $this->getProductCombinationId($product, $data['combination']) : 0;

        if (empty($data['price_tax_incl'])) {
            $data['price_tax_incl'] = (string) $this->getProductTaxCalculator((int) $orderId, $productId)
                ->addTaxes($data['price']);
        }

        try {
            $hasFreeShipping = null;
            if (isset($data['free_shipping'])) {
                $hasFreeShipping = PrimitiveUtils::castStringBooleanIntoBoolean($data['free_shipping']);
            }
            $this->getCommandBus()->handle(
                AddProductToOrderCommand::withNewInvoice(
                    $orderId,
                    $productId,
                    $combinationId,
                    $data['price_tax_incl'],
                    $data['price'],
                    (int) $data['amount'],
                    $hasFreeShipping
                )
            );
        } catch (InvalidProductQuantityException $e) {
            $this->setLastException($e);
        } catch (ProductOutOfStockException $e) {
            $this->setLastException($e);
        } catch (DuplicateProductInOrderException $e) {
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
        } catch (OrderException|OrderNotFoundException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When /^I add products to order "(.+)" to the (.+) invoice and the following products details:$/
     *
     * @param string $orderReference
     * @param string $invoicePosition
     * @param TableNode $table
     */
    public function addProductsToOrderWithExistingInvoiceAndTheFollowingDetails(string $orderReference, string $invoicePosition, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $order = new Order($orderId);
        $orderInvoice = $this->getInvoiceFromOrder($order, $invoicePosition);

        $data = $table->getRowsHash();
        $productName = $data['name'];
        $product = $this->getProductByName($productName);

        if (isset($data['combination'])) {
            $combinationId = $this->getProductCombinationId($product, $data['combination']);
        } else {
            $combinationId = 0;
        }

        if (empty($data['price_tax_incl'])) {
            $data['price_tax_incl'] = (string) $this->getProductTaxCalculator((int) $orderId, $product->getProductId())
                ->addTaxes($data['price']);
        }

        try {
            $this->getCommandBus()->handle(
                AddProductToOrderCommand::toExistingInvoice(
                    (int) $orderId,
                    (int) $orderInvoice->id,
                    (int) $product->getProductId(),
                    (int) $combinationId,
                    $data['price_tax_incl'],
                    $data['price'],
                    (int) $data['amount']
                )
            );
        } catch (InvalidProductQuantityException $e) {
            $this->setLastException($e);
        } catch (DuplicateProductInOrderException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then the :invoicePosition invoice from order :orderReference should have following details:
     *
     * @param string $invoicePosition
     * @param string $orderReference
     * @param TableNode $table
     */
    public function checkInvoiceDetails(string $invoicePosition, string $orderReference, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $invoiceData = $table->getRowsHash();

        $order = new Order($orderId);
        $orderInvoice = $this->getInvoiceFromOrder($order, $invoicePosition);

        foreach ($invoiceData as $invoiceField => $invoiceValue) {
            Assert::assertEquals(
                (float) $invoiceValue,
                $orderInvoice->{$invoiceField},
                sprintf(
                    'Invalid order invoice field %s, expected %s instead of %s',
                    $invoiceField,
                    $invoiceValue,
                    $orderInvoice->{$invoiceField}
                )
            );
        }
    }

    /**
     * @Then the :invoicePosition invoice from order :orderReference should have following shipping tax details:
     *
     * @param string $invoicePosition
     * @param string $orderReference
     * @param TableNode $table
     */
    public function checkInvoiceShippingTaxDetails(string $invoicePosition, string $orderReference, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $invoiceShippingData = $table->getColumnsHash();

        $order = new Order($orderId);
        $orderInvoice = $this->getInvoiceFromOrder($order, $invoicePosition);
        $invoiceShippingTaxDetails = $orderInvoice->getShippingTaxesBreakdown($order);

        Assert::assertLessThanOrEqual(
            count($invoiceShippingTaxDetails),
            count($invoiceShippingData),
            sprintf(
                'Invalid number of tax details, expected at least %d instead of %d',
                count($invoiceShippingData),
                count($invoiceShippingTaxDetails)
            )
        );

        foreach ($invoiceShippingData as $invoiceShippingIndex => $invoiceShippingDetails) {
            $shippingTaxDetails = $invoiceShippingTaxDetails[$invoiceShippingIndex];
            foreach ($invoiceShippingDetails as $shippingField => $shippingValue) {
                Assert::assertEquals(
                    (float) $shippingValue,
                    (float) $shippingTaxDetails[$shippingField],
                    sprintf(
                        'Invalid order tax field %s, expected %s instead of %s',
                        $shippingField,
                        $shippingValue,
                        (float) $shippingTaxDetails[$shippingField]
                    )
                );
            }
        }
    }

    /**
     * @Then the :invoicePosition invoice from order :orderReference should have following product tax details:
     *
     * @param string $invoicePosition
     * @param string $orderReference
     * @param TableNode $table
     */
    public function checkInvoiceProductTaxDetails(string $invoicePosition, string $orderReference, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $invoiceProductData = $table->getColumnsHash();

        $order = new Order($orderId);
        $orderInvoice = $this->getInvoiceFromOrder($order, $invoicePosition);
        $invoiceProductTaxDetails = array_values($orderInvoice->getProductTaxesBreakdown($order));

        Assert::assertLessThanOrEqual(
            count($invoiceProductTaxDetails),
            count($invoiceProductData),
            sprintf(
                'Invalid number of product tax details, expected at least %d instead of %d',
                count($invoiceProductData),
                count($invoiceProductTaxDetails)
            )
        );

        foreach ($invoiceProductData as $invoiceProductIndex => $invoiceProductDetails) {
            $productTaxDetails = $invoiceProductTaxDetails[$invoiceProductIndex];
            foreach ($invoiceProductDetails as $taxField => $taxValue) {
                Assert::assertEquals(
                    (float) $taxValue,
                    (float) $productTaxDetails[$taxField],
                    sprintf(
                        'Invalid order tax field %s, expected %s instead of %s',
                        $taxField,
                        $taxValue,
                        (float) $productTaxDetails[$taxField]
                    )
                );
            }
        }
    }

    /**
     * @Then order :orderReference should have :expectedCount cart rule(s)
     *
     * @param string $orderReference
     * @param int $expectedCount
     */
    public function checkOrderCartRulesCount(string $orderReference, int $expectedCount)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);

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
     * @Then order :orderReference should have :expectedCount invoice(s)
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
     * @When I edit product :productName in :invoicePosition invoice from order :orderReference with following products details:
     *
     * @param string $productName
     * @param string $orderReference
     * @param TableNode $table
     */
    public function editProductsFromInvoiceWithFollowingDetails(string $productName, string $orderReference, string $invoicePosition, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $order = new Order($orderId);
        $invoice = $this->getInvoiceFromOrder($order, $invoicePosition);
        $productOrderDetail = $this->getOrderDetailFromOrder($productName, $orderReference, null, $invoice->id);
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
     * @param Order $order
     * @param string $invoicePosition
     *
     * @return OrderInvoice
     */
    private function getInvoiceFromOrder(Order $order, string $invoicePosition)
    {
        $invoicesCollection = $order->getInvoicesCollection();
        Assert::assertGreaterThanOrEqual(1, $invoicesCollection->count());

        $invoiceIndexes = [
            'first' => 0,
            'second' => 1,
            'third' => 2,
            'fourth' => 3,
            'last' => $invoicesCollection->count() - 1,
        ];
        if (!isset($invoiceIndexes[$invoicePosition])) {
            throw new RuntimeException(sprintf('Cannot interpret this invoice position %s', $invoicePosition));
        }
        /** @var OrderInvoice $orderInvoice */
        $orderInvoice = $invoicesCollection->offsetGet($invoiceIndexes[$invoicePosition]);

        return $orderInvoice;
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
        $invoiceId = null;
        if (isset($data['invoice'])) {
            $order = new Order($orderId);
            $invoice = $this->getInvoiceFromOrder($order, $data['invoice']);
            $invoiceId = (int) $invoice->id;
        }

        // If tax included price is not given, it is calculated
        if (!isset($data['price_tax_incl'])) {
            $data['price_tax_incl'] = (string) $this->getProductTaxCalculator($orderId, (int) $productOrderDetail['product_id'])
                ->addTaxes($data['price']);
        }

        try {
            $this->getCommandBus()->handle(
                new UpdateProductInOrderCommand(
                    (int) $orderId,
                    (int) $productOrderDetail['id_order_detail'],
                    $data['price_tax_incl'],
                    $data['price'],
                    (int) $data['amount'],
                    $invoiceId
                )
            );
        } catch (InvalidProductQuantityException $e) {
            $this->setLastException($e);
        } catch (ProductOutOfStockException $e) {
            $this->setLastException($e);
        } catch (DuplicateProductInOrderInvoiceException $e) {
            $this->setLastException($e);
        } catch (CannotFindProductInOrderException $e) {
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
     * @Then I should get error that adding duplicate product is forbidden
     */
    public function assertDuplicateProductIsForbidden()
    {
        $this->assertLastErrorIs(DuplicateProductInOrderException::class);
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
        $orderReferences = explode(',', $orderReferencesString);
        $ordersIds = [];
        foreach ($orderReferences as $orderReference) {
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
     * @Then order :orderReference has :statusNb status(es) in history
     *
     * @param string $orderReference
     * @param int $statusNb
     *
     * @throws RuntimeException
     */
    public function countOrderStatus(string $orderReference, int $statusNb)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        $actualStatusNb = count($orderForViewing->getHistory()->getStatuses());
        if ($statusNb !== $actualStatusNb) {
            throw new RuntimeException(sprintf('Incorrect number of statuses in history expected %d but got %d instead', $statusNb, $actualStatusNb));
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
            throw new RuntimeException(sprintf('Order should have "%d" products, but has "%d".', $quantity, $totalQuantity));
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

        $discount = $this->getOrderDiscountByName($orderId, self::ORDER_CART_RULE_FREE_SHIPPING);
        if (null === $discount) {
            throw new RuntimeException('Order should have free shipping.');
        }
    }

    /**
     * @Then order :reference should have a cart rule with name :cartRuleName
     *
     * @param string $reference
     * @param string $cartRuleName
     *
     * @throws RuntimeException
     */
    public function createdOrderShouldHaveNamedCartRule(string $reference, string $cartRuleName): void
    {
        $orderId = SharedStorage::getStorage()->get($reference);

        $discount = $this->getOrderDiscountByName($orderId, $cartRuleName);
        if (null === $discount) {
            throw new RuntimeException(sprintf(
                'Order should have a cart rule with name "%s"',
                $cartRuleName
            ));
        }
    }

    /**
     * @Then order :reference should be a gift with message :message
     *
     * @param string $reference
     * @param string $message
     */
    public function createdOrderShouldBeAGift(string $reference, string $message)
    {
        $orderId = SharedStorage::getStorage()->get($reference);

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        Assert::assertTrue($orderForViewing->getShipping()->isGiftWrapping());
        Assert::assertEquals($message, $orderForViewing->getShipping()->getGiftMessage());
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
        $this->assertProductCounts($orderReference, $quantity, $productName, $combinationName);
    }

    /**
     * @Then the :invoicePosition invoice from order :orderReference should contain :quantity product(s) :productName
     * @Then the :invoicePosition invoice from order :orderReference should contain :quantity combination(s) :combinationName of product :productName
     *
     * @param string $invoicePosition
     * @param string $orderReference
     * @param int $quantity
     * @param string $productName
     * @param string|null $combinationName
     */
    public function orderInvoiceContainsProductWithReference(string $invoicePosition, string $orderReference, int $quantity, string $productName, ?string $combinationName = null)
    {
        $this->assertProductCounts($orderReference, $quantity, $productName, $combinationName, $invoicePosition);
    }

    /**
     * @param string $orderReference
     * @param int $quantity
     * @param string $productName
     * @param string|null $combinationName
     * @param string|null $invoicePosition
     */
    private function assertProductCounts(string $orderReference, int $quantity, string $productName, ?string $combinationName = null, ?string $invoicePosition = null)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);

        $product = $this->getProductByName($productName);
        $productId = $product->getProductId();
        $combinationId = null !== $combinationName ? $this->getProductCombinationId($product, $combinationName) : null;

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        /** @var OrderProductForViewing[] $orderProducts */
        $orderProducts = $orderForViewing->getProducts()->getProducts();

        $orderInvoice = null;
        if (null !== $invoicePosition) {
            $order = new Order($orderId);
            $orderInvoice = $this->getInvoiceFromOrder($order, $invoicePosition);
        }

        $productQuantity = 0;
        foreach ($orderProducts as $orderProduct) {
            if ($orderProduct->getId() === $productId
                && (null === $combinationId || $orderProduct->getCombinationId() === $combinationId)
                && (null === $orderInvoice || $orderProduct->getOrderInvoiceId() === (int) $orderInvoice->id)) {
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
     * @Then cart of order :orderReference should contain :quantity product(s) :productName
     *
     * @param string $orderReference
     * @param int $quantity
     * @param string $productName
     * @param string|null $combinationName
     */
    public function cartOrderContainsProductWithReference(string $orderReference, int $quantity, string $productName, ?string $combinationName = null)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $order = new Order($orderId);
        $cart = new Cart($order->id_cart);

        $product = $this->getProductByName($productName);
        $productId = $product->getProductId();
        $combinationId = null !== $combinationName ? $this->getProductCombinationId($product, $combinationName) : 0;

        $cartQuantities = $cart->getProductQuantity($productId, $combinationId);
        $productQuantity = (int) $cartQuantities['quantity'];
        if ($productQuantity === $quantity) {
            return;
        }
        throw new RuntimeException(
            sprintf(
                'Cart of order was expected to have "%d" products "%s" in it. Instead got "%d"',
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
     * @Then the product :productName in the :invoicePosition invoice from the order :orderReference should have the following details:
     *
     * @param string $orderReference
     * @param string $productName
     */
    public function checkProductDetailsInInvoiceWithReference(string $productName, string $invoicePosition, string $orderReference, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $order = new Order($orderId);
        $orderInvoice = $this->getInvoiceFromOrder($order, $invoicePosition);

        $productOrderDetail = $this->getOrderDetailFromOrder($productName, $orderReference, null, $orderInvoice->id);
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
     * @Then /^there is ([\-\d]+) (less|more) "(.+)" in stock$/
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
     * @param TableNode $table
     */
    public function addCartRuleToOrder(string $orderReference, TableNode $table): void
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $data = $table->getRowsHash();

        try {
            $this->getQueryBus()->handle(new AddCartRuleToOrderCommand(
                $orderId,
                $data['name'],
                $data['type'],
                $data['value'] ?? null
            ));
        } catch (InvalidCartRuleDiscountValueException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I add discount to order :orderReference on :invoicePosition invoice and following details:
     *
     * @param string $orderReference
     * @param string $invoicePosition
     * @param TableNode $table
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function addCartRuleAndUpdateSingleInvoice(string $orderReference, string $invoicePosition, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $data = $table->getRowsHash();

        $order = new Order($orderId);
        $orderInvoice = $this->getInvoiceFromOrder($order, $invoicePosition);

        $this->getQueryBus()->handle(new AddCartRuleToOrderCommand(
            $orderId,
            $data['name'],
            $data['type'],
            $data['value'] ?? null,
            (int) $orderInvoice->id
        ));
    }

    /**
     * @Then I should get error that adding duplicate product in invoice is forbidden
     */
    public function assertDuplicateProductInInvoiceIsForbidden()
    {
        $this->assertLastErrorIs(DuplicateProductInOrderInvoiceException::class);
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
     * @Then the last invoice for order :orderReference should have following prices:
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
     * @When /^I change order "(.*)" note to "(.*)"$/
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
     * @Then /^order "(.*)" note should be "(.*)"$/
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
     * @Then product :productName in order :orderReference should have no specific price
     *
     * @param string $productName
     * @param string $orderReference
     */
    public function assertNoSpecificPrice(string $productName, string $orderReference)
    {
        $productId = $this->getProductIdByName($productName);
        // @todo: maybe manage combination as well
        $combinationId = 0;
        $orderId = $this->getSharedStorage()->get($orderReference);

        $specificPriceId = $this->getSpecificPriceId($productId, $combinationId, $orderId);
        Assert::assertNull(
            $specificPriceId,
            sprintf(
                'Product %s from order %s should have no specific price',
                $productName,
                $orderReference
            )
        );
    }

    /**
     * @Then /^product "(.*)" in order "(.*)" should have specific price (\d+\.\d+)$/
     *
     * @param string $productName
     * @param string $orderReference
     * @param float $expectedPrice
     */
    public function assertSpecificPrice(string $productName, string $orderReference, float $expectedPrice)
    {
        $productId = $this->getProductIdByName($productName);
        // @todo: maybe manage combination as well
        $combinationId = 0;
        $orderId = $this->getSharedStorage()->get($orderReference);

        $specificPriceId = $this->getSpecificPriceId($productId, $combinationId, $orderId);
        Assert::assertNotNull(
            $specificPriceId,
            sprintf(
                'Product %s from order %s should have specific price',
                $productName,
                $orderReference
            )
        );

        $specificPrice = new SpecificPrice($specificPriceId);
        Assert::assertEquals(
            $expectedPrice,
            $specificPrice->price
        );
        Assert::assertEquals('amount', $specificPrice->reduction_type);
        Assert::assertTrue((bool) $specificPrice->reduction_tax);
    }

    /**
     * @Then order :orderReference preview shipping address should have the following details:
     */
    public function getOrderPreviewShippingAddress(string $orderReference, TableNode $table): void
    {
        $orderId = $this->getSharedStorage()->get($orderReference);
        $orderPreview = $this->getQueryBus()->handle(new GetOrderPreview($orderId));
        $shippingAddress = $orderPreview->getShippingDetails();

        $address = [
            'firstName' => $shippingAddress->getFirstName(),
            'lastName' => $shippingAddress->getLastName(),
            'company' => $shippingAddress->getCompany(),
            'vatNumber' => $shippingAddress->getVatNumber(),
            'address1' => $shippingAddress->getAddress1(),
            'address2' => $shippingAddress->getAddress2(),
            'city' => $shippingAddress->getCity(),
            'postalCode' => $shippingAddress->getPostalCode(),
            'stateName' => $shippingAddress->getStateName(),
            'country' => $shippingAddress->getCountry(),
            'phone' => $shippingAddress->getPhone(),
            'carrierName' => $shippingAddress->getCarrierName(),
            'trackingNumber' => $shippingAddress->getTrackingNumber(),
            'trackingUrl' => $shippingAddress->getTrackingUrl(),
        ];

        $expectedDetails = $table->getRowsHash();
        foreach ($expectedDetails as $key => $value) {
            Assert::assertEquals(
                $value,
                $address[$key]
            );
        }
    }

    /**
     * @Then /^the order "(.+)" preview has the following formatted (shipping|invoice) address$/
     */
    public function getOrderPreviewFormattedAddress(
        string $orderReference,
        string $addressType,
        PyStringNode $pyStringNode
    ): void {
        $orderId = $this->getSharedStorage()->get($orderReference);
        /** @var OrderPreview $orderPreview */
        $orderPreview = $this->getQueryBus()->handle(new GetOrderPreview($orderId));

        if ($addressType == 'shipping') {
            $address = $orderPreview->getShippingAddressFormatted();
        } elseif ($addressType == 'invoice') {
            $address = $orderPreview->getInvoiceAddressFormatted();
        }

        if (!isset($address)) {
            return;
        }

        Assert::assertEquals(
            $address,
            $pyStringNode->getRaw(),
            sprintf(
                'Invalid formatted address for preview order %s, expected %s instead of %s',
                $orderReference,
                $address,
                $pyStringNode->getRaw()
            )
        );
    }

    /**
     * @param int $productId
     * @param int $combinationId
     * @param int $orderId
     *
     * @return int|null
     */
    private function getSpecificPriceId(int $productId, int $combinationId, int $orderId): ?int
    {
        $order = new Order($orderId);

        $specificPriceId = $order->getProductSpecificPriceId($productId, $combinationId);

        return $specificPriceId ? (int) $specificPriceId : null;
    }

    /**
     * @param string $productName
     *
     * @throws RuntimeException
     *
     * @return FoundProduct
     */
    private function getProductByName(string $productName): FoundProduct
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
     * @param int|null $orderInvoiceId
     *
     * @return array
     */
    private function getOrderDetailFromOrder(
        string $productName,
        string $orderReference,
        ?string $combinationName = null,
        ?int $orderInvoiceId = null
    ): array {
        $product = $this->getProductByName($productName);
        $productId = $product->getProductId();
        $combinationId = null !== $combinationName ? $this->getProductCombinationId($product, $combinationName) : null;
        $order = new Order(SharedStorage::getStorage()->get($orderReference));
        $orderDetails = $order->getProducts();
        $productOrderDetail = null;
        foreach ($orderDetails as $orderDetail) {
            if ((int) $orderDetail['product_id'] === $productId
                && (null === $combinationId || (int) $orderDetail['product_attribute_id'] === $combinationId)
                && (null === $orderInvoiceId || (int) $orderDetail['id_order_invoice'] === $orderInvoiceId)) {
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
     * @return OrderDiscountForViewing[]
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

    /**
     * @When I delete product :productReference from catalogue
     *
     * @param string $productReference
     */
    public function removeProductFromCatalogue(string $productReference)
    {
        $foundProduct = $this->getProductByName($productReference);
        $product = new Product($foundProduct->getProductId());
        $product->delete();
    }

    /**
     * @When I update deleted product :productReference in order :orderReference
     *
     * @param string $productReference
     * @param string $orderReference
     */
    public function tryUpdatingProductDeletedFromCatalogue(string $productReference, string $orderReference)
    {
        // get order detail
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $order = new Order($orderId);
        $orderDetailList = $order->getOrderDetailList();

        foreach ($orderDetailList as $orderDetail) {
            if ($orderDetail['product_name'] === $productReference) {
                $productOrderDetail = $orderDetail;
                break;
            }
        }

        if (!isset($productOrderDetail)) {
            throw new RuntimeException(sprintf('Product %s has not been found in order %s', $productReference, $orderReference));
        }

        // update product price/quantity in order
        $this->updateProductInOrder($orderId, $productOrderDetail, ['price' => '10', 'amount' => '3']);
    }

    /**
     * @Then I should get error that the product being edited was not found
     */
    public function assertLastErrorIsRefundQuantityTooHigh()
    {
        $this->assertLastErrorIs(
            CannotFindProductInOrderException::class
        );
    }

    /**
     * @param int $orderId
     * @param int $productId
     *
     * @return TaxCalculator
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function getProductTaxCalculator(int $orderId, int $productId): TaxCalculator
    {
        $order = new Order($orderId);
        $taxAddress = new Address($order->{Configuration::get('PS_TAX_ADDRESS_TYPE', null, null, $order->id_shop)});
        $taxManager = TaxManagerFactory::getManager($taxAddress, Product::getIdTaxRulesGroupByIdProduct((int) $productId, Context::getContext()));

        return $taxManager->getTaxCalculator();
    }

    /**
     * @Then product :productReference in order :orderReference has following prices for viewing in BO:
     *
     * @param string $orderReference
     * @param TableNode $table
     */
    public function checkOrderForViewingWithReference(string $productReference, string $orderReference, TableNode $table): void
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $productId = $this->getProductIdByName($productReference);
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));

        $productList = $orderForViewing->getProducts()->getProducts();
        $expectedDetails = $table->getRowsHash();
        foreach ($productList as $product) {
            if ($product->getId() == $productId) {
                Assert::assertEquals(
                    $expectedDetails['unit_price_tax_excl_raw'],
                    $product->getUnitPriceTaxExclRaw()
                );
                Assert::assertEquals(
                    $expectedDetails['unit_price_tax_incl_raw'],
                    $product->getUnitPriceTaxInclRaw()
                );
                Assert::assertEquals(
                    $expectedDetails['unit_price'],
                    $product->getUnitPrice()
                );
                Assert::assertEquals(
                    $expectedDetails['total_price'],
                    $product->getTotalPrice()
                );
            }
        }
    }

    /**
     * @Then I should get no order error
     */
    public function assertNoOrderError()
    {
        $this->assertLastErrorIsNull();
    }

    /**
     * @Then /^the order "(.+)" has following (shipping|invoice) address$/
     *
     * @param string $orderReference
     * @param string $addressType
     * @param TableNode $table
     */
    public function orderCheckAddress(string $orderReference, string $addressType, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));

        if ($addressType == 'shipping') {
            /** @var OrderShippingAddressForViewing $address */
            $address = $orderForViewing->getShippingAddress();
        } elseif ($addressType == 'invoice') {
            /** @var OrderInvoiceAddressForViewing $address */
            $address = $orderForViewing->getInvoiceAddress();
        }

        if (!isset($address)) {
            return;
        }

        $expectedDetails = $table->getRowsHash();
        $arrayActual = [
            'Address' => $address->getAddress1(),
            'City' => $address->getCityName(),
            'Country' => $address->getCountryName(),
            'DNI' => $address->getDni(),
            'Fullname' => $address->getFullName(),
            'Postal code' => $address->getPostCode(),
        ];
        foreach ($expectedDetails as $detailName => $expectedDetailValue) {
            if (!array_key_exists($detailName, $arrayActual)) {
                throw new RuntimeException(sprintf('Invalid check for address field %s', $detailName));
            }

            Assert::assertEquals(
                $expectedDetailValue,
                $arrayActual[$detailName],
                sprintf(
                    'Invalid address field %s for order %s, expected %s instead of %s',
                    $detailName,
                    $orderReference,
                    $expectedDetailValue,
                    $arrayActual[$detailName]
                )
            );
        }
    }

    /**
     * @Then /^the order "(.+)" has the following formatted (shipping|invoice) address$/
     *
     * @param string $orderReference
     * @param string $addressType
     * @param PyStringNode $pyStringNode
     */
    public function orderCheckAddressFormatted(string $orderReference, string $addressType, PyStringNode $pyStringNode): void
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));

        if ($addressType == 'shipping') {
            $address = $orderForViewing->getShippingAddressFormatted();
        } elseif ($addressType == 'invoice') {
            $address = $orderForViewing->getInvoiceAddressFormatted();
        }

        if (!isset($address)) {
            return;
        }

        Assert::assertEquals(
            $address,
            $pyStringNode->getRaw(),
            sprintf(
                'Invalid formatted address for order %s, expected %s instead of %s',
                $orderReference,
                $address,
                $pyStringNode->getRaw()
            )
        );
    }

    /**
     * @Then /^the preview order "(.+)" has following (shipping|invoice) address$/
     * @Then /^the preview order "(.+)" has following (shipping) details$/
     *
     * @param string $orderReference
     * @param string $addressType
     * @param TableNode $table
     */
    public function previewOrderCheckAddress(string $orderReference, string $addressType, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        /** @var OrderPreview $orderPreview */
        $orderPreview = $this->getQueryBus()->handle(new GetOrderPreview($orderId));
        switch ($addressType) {
            case 'shipping':
                /** @var OrderPreviewShippingDetails $address */
                $address = $orderPreview->getShippingDetails();
                break;
            case 'invoice':
                /** @var OrderPreviewInvoiceDetails $address */
                $address = $orderPreview->getInvoiceDetails();
                break;
            default:
                throw new RuntimeException('Address Type is invalid');
        }

        $expectedDetails = $table->getRowsHash();
        $arrayActual = [
            'Address' => $address->getAddress1(),
            'City' => $address->getCity(),
            'Country' => $address->getCountry(),
            'DNI' => $address->getDni(),
            'Fullname' => $address->getFirstName() . ' ' . $address->getLastname(),
            'Postal code' => $address->getPostalCode(),
        ];
        if ('shipping' === $addressType) {
            $arrayActual += [
                'Tracking number' => $address->getTrackingNumber(),
                'Tracking URL' => $address->getTrackingUrl(),
            ];
        }
        foreach ($expectedDetails as $detailName => $expectedDetailValue) {
            if (!array_key_exists($detailName, $arrayActual)) {
                throw new RuntimeException(sprintf('Invalid check for address field %s', $detailName));
            }

            Assert::assertEquals(
                $expectedDetailValue,
                $arrayActual[$detailName],
                sprintf(
                    'Invalid address field %s for order %s, expected %s instead of %s',
                    $detailName,
                    $orderReference,
                    $expectedDetailValue,
                    $arrayActual[$detailName]
                )
            );
        }
    }

    /**
     * @Then /^the order "(.+)" should have (\d+) document(s?)$/
     *
     * @param string $orderReference
     * @param int $numDocuments
     */
    public function orderHasNumDocuments(string $orderReference, int $numDocuments): void
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));

        Assert::assertEquals(
            $numDocuments,
            count($orderForViewing->getDocuments()->getDocuments()),
            sprintf(
                'Invalid number of order documents, expected %s but got %s instead',
                $numDocuments,
                count($orderForViewing->getDocuments()->getDocuments())
            )
        );
    }

    /**
     * @Then /^the order "(.+)" should have following documents:$/
     *
     * @param string $orderReference
     * @param TableNode $table
     */
    public function orderHasFollowingDocuments(string $orderReference, TableNode $table): void
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));

        $expectedDetails = $table->getHash();
        foreach ($expectedDetails as $expectedDetail) {
            $hasDocument = $document = false;
            foreach ($orderForViewing->getDocuments()->getDocuments() as $document) {
                if ($expectedDetail['referenceNumber'] === $document->getReferenceNumber()) {
                    $hasDocument = true;
                    break;
                }
            }
            if (!$hasDocument) {
                throw new RuntimeException(sprintf(
                    'Document not found : %s',
                    $expectedDetail['referenceNumber']
                ));
            }

            Assert::assertEquals(
                $expectedDetail['type'],
                $document->getType(),
                sprintf(
                    'Invalid document type for order %s, expected %s instead of %s',
                    $orderReference,
                    $expectedDetail['type'],
                    $document->getType()
                )
            );
            Assert::assertEquals(
                $expectedDetail['amount'],
                $document->getAmount(),
                sprintf(
                    'Invalid document type for order %s, expected %s instead of %s',
                    $orderReference,
                    $expectedDetail['amount'],
                    $document->getAmount()
                )
            );
        }
    }

    /**
     * @Then /^the order "(.+)" should have following customizations:$/
     *
     * @param string $orderReference
     * @param TableNode $table
     */
    public function orderHasFollowingCustomizations(string $orderReference, TableNode $table): void
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));

        $expectedDetails = $table->getHash();
        foreach ($expectedDetails as $expectedDetail) {
            $hasProduct = $product = false;
            foreach ($orderForViewing->getProducts()->getProducts() as $product) {
                if ($expectedDetail['productReference'] === $product->getReference()) {
                    $hasProduct = true;
                    break;
                }
            }
            if (!$hasProduct) {
                throw new RuntimeException(sprintf(
                    'Product not found : %s',
                    $expectedDetail['productReference']
                ));
            }

            if ($product->getCustomizations() === null) {
                throw new RuntimeException(sprintf(
                    'No customizations found for Product %s',
                    $expectedDetail['productReference']
                ));
            }

            $customizations = [];
            if ($expectedDetail['type'] === 'text') {
                $customizations = $product->getCustomizations()->getTextCustomizations();
            }
            if ($expectedDetail['type'] === 'file') {
                $customizations = $product->getCustomizations()->getFileCustomizations();
            }

            $hasCustomization = $customization = false;
            foreach ($customizations as $customization) {
                if ($expectedDetail['name'] === $customization->getName()) {
                    $hasCustomization = true;
                    break;
                }
            }
            if (!$hasCustomization) {
                throw new RuntimeException(sprintf(
                    'Customization not found : %s (for Product %s)',
                    $expectedDetail['name'],
                    $expectedDetail['productReference']
                ));
            }

            Assert::assertEquals(
                $expectedDetail['value'],
                $customization->getValue(),
                sprintf(
                    'Invalid value for the customization %s in product %s for order %s, expected %s instead of %s',
                    $expectedDetail['name'],
                    $expectedDetail['productReference'],
                    $orderReference,
                    $expectedDetail['value'],
                    $customization->getValue()
                )
            );
        }
    }
}
