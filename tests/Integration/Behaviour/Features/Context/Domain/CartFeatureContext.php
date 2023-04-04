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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Cart;
use CartRule;
use Configuration;
use Context;
use Country;
use Currency;
use Customer;
use DateInterval;
use DateTime;
use Exception;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Adapter\Cart\Repository\CartRepository;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\AddCartRuleToCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\AddCustomizationCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\AddProductToCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\BulkDeleteCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\CreateEmptyCustomerCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\DeleteCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\RemoveCartRuleFromCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\RemoveProductFromCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartAddressesCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartDeliverySettingsCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateProductPriceInCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateProductQuantityInCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CannotDeleteOrderedCartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\MinimalQuantityException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartForOrderCreation;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\PackOutOfStockException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductCustomizationNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\FoundProduct;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use Product;
use RuntimeException;
use SpecificPrice;
use State;
use Tests\Integration\Behaviour\Features\Context\LegacyProductFeatureContext;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class CartFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @var LegacyProductFeatureContext
     */
    protected $productFeatureContext;

    /** @BeforeScenario */
    public function before(BeforeScenarioScope $scope)
    {
        /** @var InitializedContextEnvironment $environment */
        $environment = $scope->getEnvironment();
        /** @var LegacyProductFeatureContext $productFeatureContext */
        $productFeatureContext = $environment->getContext(LegacyProductFeatureContext::class);

        $this->productFeatureContext = $productFeatureContext;
    }

    /**
     * @Given the current currency is :currencyIsoCode
     */
    public function addCurrencyToContext($currencyIsoCode)
    {
        $currencyId = (int) Currency::getIdByIsoCode($currencyIsoCode);

        if ($currencyId) {
            $currency = new Currency($currencyId);
        } else {
            $currency = new Currency();
            $currency->name = $currencyIsoCode;
            $currency->precision = 2;
            $currency->iso_code = $currencyIsoCode;
            $currency->active = 1;
            $currency->conversion_rate = 1;
        }

        Context::getContext()->currency = $currency;
        SharedStorage::getStorage()->set($currencyIsoCode, (int) $currency->id);
    }

    /**
     * @When I create an empty cart :cartReference for customer :customerReference
     * @Given customer :customerReference has an empty cart :cartReference
     *
     * @param string $cartReference
     * @param string $customerReference
     */
    public function createEmptyCartForCustomer(string $cartReference, string $customerReference)
    {
        // Clear static cache each time you create a cart
        Cart::resetStaticCache();
        $customerId = SharedStorage::getStorage()->get($customerReference);

        /** @var CartId $cartIdObject */
        $cartIdObject = $this->getCommandBus()->handle(
            new CreateEmptyCustomerCartCommand(
                (int) $customerId
            )
        );
        // Reset context's cart to avoid one from former tests to be used with invalid values (like non existent addresses)
        Context::getContext()->cart = new Cart($cartIdObject->getValue());

        SharedStorage::getStorage()->set($cartReference, $cartIdObject->getValue());
    }

    /**
     * @When I update the cart :cartReference currency to :currencyReference
     *
     * @param string $cartReference
     * @param string $currencyReference
     */
    public function updateCartCurrency(string $cartReference, string $currencyReference)
    {
        $currency = $this->getCurrency($currencyReference);
        $cartId = SharedStorage::getStorage()->get($cartReference);

        $this->getCommandBus()->handle(
            new UpdateCartCurrencyCommand(
                $cartId,
                (int) $currency->id
            )
        );

        Cart::resetStaticCache();
    }

    /**
     * @When /^I add (\d+) product(?:s)? "(.+)" to the cart "(.+)"$/
     *
     * @param int $quantity
     * @param string $productName
     * @param string $cartReference
     */
    public function addProductsToCart(int $quantity, string $productName, string $cartReference)
    {
        $productId = $this->getProductIdByName($productName);

        try {
            $this->getCommandBus()->handle(
                new AddProductToCartCommand(
                    $this->getSharedStorage()->get($cartReference),
                    $productId,
                    $quantity
                )
            );
            $this->getSharedStorage()->set($productName, $productId);

            // Clear cart static cache or it will have no products in next calls
            Cart::resetStaticCache();
        } catch (MinimalQuantityException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I update product :productName in the cart :cartReference to :price
     *
     * @param string $productName
     * @param string $cartReference
     * @param float $price
     */
    public function updateProductPriceInCart(string $productName, string $cartReference, float $price): void
    {
        $productId = $this->getProductIdByName($productName);
        $cartId = SharedStorage::getStorage()->get($cartReference);

        $command = new UpdateProductPriceInCartCommand($cartId, $productId, 0, $price
        );

        $this->getCommandBus()->handle($command);
    }

    /**
     * @Then product :productName in cart :cartReference should have specific price :price
     *
     * @param string $productName
     * @param string $cartReference
     * @param float $price
     */
    public function checkCartProductSpecificPrice(string $productName, string $cartReference, float $price): void
    {
        $productId = $this->getProductIdByName($productName);
        $cartId = SharedStorage::getStorage()->get($cartReference);
        $cart = new Cart($cartId);

        $specificPriceId = SpecificPrice::exists(
            $productId,
            0,
            0,
            0,
            0,
            0,
            $cart->id_customer,
            SpecificPrice::ORDER_DEFAULT_FROM_QUANTITY,
            SpecificPrice::ORDER_DEFAULT_DATE,
            SpecificPrice::ORDER_DEFAULT_DATE,
            false,
            $cartId
        );

        if (!$specificPriceId) {
            throw new RuntimeException(sprintf(
                'Could not find specific price for product %s in car %s',
                $productName,
                $cartReference
            ));
        }

        $specificPrice = new SpecificPrice($specificPriceId);
        Assert::assertEquals(
            $price,
            $specificPrice->price
        );
        Assert::assertEquals('amount', $specificPrice->reduction_type);
        Assert::assertTrue((bool) $specificPrice->reduction_tax);
    }

    /**
     * @When I update quantity of product :productName in the cart :cartReference to :quantity
     *
     * @param int $quantity
     * @param string $productName
     * @param string $cartReference
     */
    public function updateProductQuantityInCart(int $quantity, string $productName, string $cartReference)
    {
        $productId = $this->getProductIdByName($productName);

        try {
            $this->getCommandBus()->handle(
                new UpdateProductQuantityInCartCommand(
                    SharedStorage::getStorage()->get($cartReference),
                    $productId,
                    $quantity
                )
            );
            SharedStorage::getStorage()->set($productName, $productId);

            // Clear cart static cache or it will have no products in next calls
            Cart::resetStaticCache();
        } catch (MinimalQuantityException $e) {
            $this->setLastException($e);
        } catch (PackOutOfStockException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When /^(?:I )?add (\d+) customized products? with reference "(.+)" (with|without)? all its customizations to the cart "(.+)"$/
     */
    public function addCustomizedProductToCartsWithCustomization(
        int $quantity,
        string $productReference,
        string $withCombinations,
        string $reference
    ) {
        $hasCombinations = ($withCombinations === 'with');
        $cartId = (int) SharedStorage::getStorage()->get($reference);
        $productId = (int) Product::getIdByReference($productReference);
        $product = new Product($productId);
        $customizationFields = $product->getCustomizationFieldIds();
        if (empty($customizationFields)) {
            throw new Exception('The product has no customizables fields');
        }

        $customizationId = null;
        if ($hasCombinations) {
            $customizations = [];
            foreach ($customizationFields as $customizationField) {
                $customizationFieldId = (int) $customizationField['id_customization_field'];
                if (Product::CUSTOMIZE_TEXTFIELD == $customizationField['type']) {
                    $customizations[$customizationFieldId] = 'Toto';
                }
            }

            /** @var CustomizationId $customizationId */
            $customizationId = $this->getCommandBus()->handle(new AddCustomizationCommand(
                $cartId,
                $productId,
                $customizations
            ));
            $customizationId = $customizationId->getValue();
        }

        try {
            $this->getCommandBus()->handle(
                new UpdateProductQuantityInCartCommand(
                    $cartId,
                    $productId,
                    $quantity,
                    null,
                    $customizationId
                )
            );
        } catch (Exception $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then I should get an error that the product is customizable and the customization is not provided
     */
    public function assertLastErrorIsProductCustomizationNotFoundException()
    {
        $this->assertLastErrorIs(ProductCustomizationNotFoundException::class);
    }

    /**
     * @When I add :quantity items of combination :combinationName of the product :productName to the cart :cartReference
     */
    public function addProductsCombinationsToCart(
        int $quantity,
        string $combinationName,
        string $productName,
        string $cartReference
    ) {
        // Use product reference from shared storage if available, or from legacy context otherwise
        if ($this->getSharedStorage()->exists($productName)) {
            $productId = (int) $this->getSharedStorage()->get($productName);
        } else {
            $this->productFeatureContext->checkProductWithNameExists($productName);
            $productId = (int) $this->productFeatureContext->getProductWithName($productName)->id;
        }
        // Use combination reference from shared storage if available, or from legacy context otherwise
        if ($this->getSharedStorage()->exists($combinationName)) {
            $combinationId = $this->getSharedStorage()->get($combinationName);
        } else {
            $this->productFeatureContext->checkCombinationWithNameExists($productName, $combinationName);
            $combinationId = (int) $this->productFeatureContext->getCombinationWithName($productName, $combinationName)->id;
        }
        try {
            $this->getCommandBus()->handle(
                new UpdateProductQuantityInCartCommand(
                    (int) SharedStorage::getStorage()->get($cartReference),
                    $productId,
                    $quantity,
                    $combinationId
                )
            );
            // Clear cart static cache or it will have no products in next calls
            Cart::resetStaticCache();
        } catch (MinimalQuantityException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I select :countryIsoCode address as delivery and invoice address for customer :customerReference in cart :cartReference
     * @Given cart :cartReference delivery and invoice address country for customer :customerReference is :countryIsoCode
     *
     * @param string $countryIsoCode
     * @param string $customerReference
     * @param string $cartReference
     */
    public function selectAddressAsDeliveryAndInvoiceAddress(string $countryIsoCode, string $customerReference, string $cartReference)
    {
        $customerId = SharedStorage::getStorage()->get($customerReference);
        $customer = new Customer($customerId);

        $getAddressByCountryIsoCode = static function ($isoCode) use ($customer) {
            $customerAddresses = $customer->getAddresses((int) Configuration::get('PS_LANG_DEFAULT'));

            foreach ($customerAddresses as $address) {
                $country = new Country($address['id_country']);

                if ($country->iso_code === $isoCode) {
                    return (int) $address['id_address'];
                }
            }

            throw new Exception(sprintf('Customer does not have address in "%s" country.', $isoCode));
        };

        $addressId = $getAddressByCountryIsoCode($countryIsoCode);

        $this->getCommandBus()->handle(
            new UpdateCartAddressesCommand(
                (int) SharedStorage::getStorage()->get($cartReference),
                $addressId,
                $addressId
            )
        );
    }

    /**
     * @Given cart :cartReference delivery and invoice address for customer :customeReferenceis is in :stateName state of :countryIsoCode country
     *
     * @param string $cartReference
     * @param string $customerReference
     * @param string $countryIsoCode
     * @param string $stateName
     */
    public function selectDeliveryAndInvoiceAddressWithState(
        string $cartReference,
        string $customerReference,
        string $countryIsoCode,
        string $stateName
    ) {
        $customerId = SharedStorage::getStorage()->get($customerReference);
        $customer = new Customer($customerId);

        $getAddressByCountryIsoCode = static function ($isoCode) use ($customer, $stateName) {
            $customerAddresses = $customer->getAddresses((int) Configuration::get('PS_LANG_DEFAULT'));

            foreach ($customerAddresses as $address) {
                $country = new Country($address['id_country']);
                $state = new State($address['id_state']);

                if ($country->iso_code === $isoCode && $state->name === $stateName) {
                    return (int) $address['id_address'];
                }
            }

            throw new RuntimeException(sprintf(
                'Customer does not have address in "%s" state of "%s" country.',
                $stateName,
                $isoCode
            ));
        };

        $addressId = $getAddressByCountryIsoCode($countryIsoCode);

        $this->getCommandBus()->handle(
            new UpdateCartAddressesCommand(
                (int) SharedStorage::getStorage()->get($cartReference),
                $addressId,
                $addressId
            )
        );
    }

    /**
     * @When I select carrier :carrierReference for cart :cartReference
     *
     * @param string $cartReference
     * @param string $carrierReference
     */
    public function selectCarrierForCart(string $cartReference, string $carrierReference)
    {
        $cartId = (int) SharedStorage::getStorage()->get($cartReference);
        $carrierId = (int) SharedStorage::getStorage()->get($carrierReference);

        try {
            $this->getCommandBus()->handle(
                new UpdateCartCarrierCommand(
                    $cartId,
                    $carrierId
                )
            );
        } catch (CartConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then cart :cartReference should have :carrierReference as a carrier
     *
     * @param string $cartReference
     * @param string $carrierReference
     */
    public function checkCartCarrier(string $cartReference, string $carrierReference)
    {
        $cartId = (int) SharedStorage::getStorage()->get($cartReference);
        $carrierId = (int) SharedStorage::getStorage()->get($carrierReference);

        $cart = new Cart($cartId);

        if ((int) $cart->id_carrier === 0) {
            throw new RuntimeException(sprintf(
                'Cart %s has no carrier defined',
                $cartReference
            ));
        }
        if ((int) $cart->id_carrier !== $carrierId) {
            throw new RuntimeException(sprintf(
                'Cart %s should have %s as a carrier, expected id_carrier to be %d but is %d instead',
                $cartReference,
                $carrierReference,
                $carrierId,
                (int) $cart->id_carrier
            ));
        }
    }

    /**
     * @When I set Free shipping to the cart :cartReference
     *
     * @param string $cartReference
     */
    public function setFreeShippingToCart(string $cartReference)
    {
        $this->getCommandBus()->handle(
            new UpdateCartDeliverySettingsCommand(
                SharedStorage::getStorage()->get($cartReference),
                true
            )
        );
    }

    /**
     * @When I declare cart :cartReference is a gift with message :message
     *
     * @param string $cartReference
     * @param string $message
     */
    public function sendAsAGift(string $cartReference, string $message)
    {
        $cartId = SharedStorage::getStorage()->get($cartReference);

        $this->getCommandBus()->handle(
            new UpdateCartDeliverySettingsCommand(
                $cartId,
                true,
                true,
                false,
                $message
            )
        );
    }

    /**
     * @When I use a voucher :voucherCode for a discount of :discountAmount on the cart :cartReference
     *
     * @param string $voucherCode
     * @param float $discountAmount
     * @param string $cartReference
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function useDiscountVoucherOnCart(string $voucherCode, float $discountAmount, string $cartReference)
    {
        $cartRule = $this->createCommonCartRule($voucherCode);
        $cartRule->reduction_amount = $discountAmount;

        $this->addCartRule($cartRule);

        $this->getCommandBus()->handle(
            new AddCartRuleToCartCommand(
                SharedStorage::getStorage()->get($cartReference),
                $cartRule->id
            )
        );
    }

    /**
     * @When I use a voucher :voucherCode on the cart :cartReference
     *
     * @param string $voucherCode
     * @param string $cartReference
     */
    public function useDiscountByCodeOnCart(string $voucherCode, string $cartReference)
    {
        $cartId = $this->getSharedStorage()->get($cartReference);
        $cartRuleId = $this->getSharedStorage()->get($voucherCode);

        $this->getCommandBus()->handle(
            new AddCartRuleToCartCommand(
                $cartId,
                $cartRuleId
            )
        );
    }

    /**
     * @When I use a voucher :voucherCode which provides a gift product :productName on the cart :cartReference
     *
     * @param string $voucherCode
     * @param string $giftProductName
     * @param string $cartReference
     */
    public function useGiftProductVoucherOnCart(string $voucherCode, string $giftProductName, string $cartReference)
    {
        $productId = $this->getProductIdByName($giftProductName);
        $cartRule = $this->createCommonCartRule($voucherCode);
        $cartRule->gift_product = $productId;

        $this->addCartRule($cartRule);
        $cartRuleId = (int) $cartRule->id;

        $this->getCommandBus()->handle(
            new AddCartRuleToCartCommand(
                $this->getSharedStorage()->get($cartReference),
                $cartRuleId
            )
        );

        $this->getSharedStorage()->set($voucherCode, $cartRuleId);
        $this->getSharedStorage()->set($giftProductName, $productId);
    }

    /**
     * @When I delete product :productName from cart :cartReference
     */
    public function deleteProduct(string $productName, string $cartReference)
    {
        $productId = (int) $this->getSharedStorage()->get($productName);
        $cartId = (int) $this->getSharedStorage()->get($cartReference);

        try {
            $this->getCommandBus()->handle(new RemoveProductFromCartCommand(
                $cartId,
                $productId
            ));
        } catch (CartException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I delete voucher :voucherCode from cart :cartReference
     *
     * @param string $voucherCode
     * @param string $cartReference
     */
    public function deleteGiftCartRule(string $voucherCode, string $cartReference)
    {
        $cartId = (int) $this->getSharedStorage()->get($cartReference);
        $cartRuleId = $this->getSharedStorage()->get($voucherCode);

        $this->getCommandBus()->handle(new RemoveCartRuleFromCartCommand($cartId, $cartRuleId));
    }

    /**
     * @Then cart :cartReference should not contain product :productName
     */
    public function assertCartDoesNotContainProduct(string $cartReference, string $productName)
    {
        if ($this->productIsInCart($cartReference, $productName)) {
            throw new RuntimeException(sprintf(
                'Expected cart not to contain product %s, but it was found in cart',
                $productName
            ));
        }
    }

    /**
     * @Then cart :cartReference should not contain product :productName unless it is a gift
     *
     * @param string $cartReference
     * @param string $productName
     */
    public function assertCartContainsOnlyGiftProduct(string $cartReference, string $productName)
    {
        $productId = (int) $this->getSharedStorage()->get($productName);
        $cartInfo = $this->getCartForOrderCreationByReference($cartReference);

        foreach ($cartInfo->getProducts() as $cartProduct) {
            if ($cartProduct->getProductId() !== $productId) {
                continue;
            }

            if (!$cartProduct->isGift()) {
                throw new RuntimeException(sprintf(
                    'Cart contains product "%s", but it is not a gift',
                    $productName
                ));
            }
        }
    }

    /**
     * @Then voucher :voucherCode should not be applied to cart :cartReference
     */
    public function assertCartRuleIsNotAppliedToCart(string $voucherCode, string $cartReference)
    {
        $cartInfo = $this->getCartForOrderCreationByReference($cartReference);
        $cartRuleId = $this->getSharedStorage()->get($voucherCode);

        foreach ($cartInfo->getCartRules() as $cartRule) {
            if ($cartRule->getCartRuleId() === $cartRuleId) {
                throw new RuntimeException(sprintf(
                    'Voucher %s is applied to cart',
                    $voucherCode
                ));
            }
        }
    }

    /**
     * @Then voucher :voucherCode should still be applied to cart :cartReference
     */
    public function assertCartRuleIsAppliedToCart(string $voucherCode, string $cartReference)
    {
        $cartInfo = $this->getCartForOrderCreationByReference($cartReference);
        $cartRuleId = $this->getSharedStorage()->get($voucherCode);

        foreach ($cartInfo->getCartRules() as $cartRule) {
            if ($cartRule->getCartRuleId() === $cartRuleId) {
                return;
            }
        }

        throw new RuntimeException(sprintf(
            'Voucher %s is not applied to cart',
            $voucherCode
        ));
    }

    /**
     * @Then cart :cartReference should contain product :productName
     * @Then cart :cartReference contains product :productName
     *
     * @param string $cartReference
     * @param string $productName
     */
    public function assertCartContainsProduct(string $cartReference, string $productName)
    {
        if (!$this->productIsInCart($cartReference, $productName)) {
            throw new RuntimeException(sprintf(
                'Expected cart to contain product %s, but it was not found',
                $productName
            ));
        }
    }

    /**
     * @Then product :productName quantity in cart :cartReference should be :quantity excluding gift products
     *
     * @param string $cartReference
     * @param string $productName
     * @param int $quantity
     */
    public function assertPaidProductQuantity(string $cartReference, string $productName, int $quantity)
    {
        $this->assertProductQuantity($cartReference, $productName, $quantity, false);
    }

    /**
     * @Then gifted product :productName quantity in cart :cartReference should be :quantity
     *
     * @param string $cartReference
     * @param string $productName
     * @param int $quantity
     */
    public function assertGiftedProductQuantity(string $cartReference, string $productName, int $quantity)
    {
        $this->assertProductQuantity($cartReference, $productName, $quantity, true);
    }

    /**
     * @param string $cartReference
     * @param string $productName
     * @param int $quantity
     * @param bool $isGift
     */
    public function assertProductQuantity(string $cartReference, string $productName, int $quantity, bool $isGift)
    {
        $productId = $this->getProductIdByName($productName);
        $cartInfo = $this->getCartForOrderCreationByReference($cartReference);

        foreach ($cartInfo->getProducts() as $product) {
            if ($productId === $product->getProductId() && (bool) $product->isGift() === $isGift) {
                Assert::assertEquals($quantity, $product->getQuantity());

                return;
            }
        }

        throw new RuntimeException(sprintf('Product %s was not found in cart', $productName));
    }

    /**
     * @Then cart :cartReference should contain gift product :productName
     * @Given cart :cartReference contains gift product :productName
     *
     * @param string $cartReference
     * @param string $productName
     */
    public function assertCartContainsGiftProduct(string $cartReference, string $productName)
    {
        $productId = (int) $this->getSharedStorage()->get($productName);
        $cartInfo = $this->getCartForOrderCreationByReference($cartReference);

        $matchingProducts = [];

        foreach ($cartInfo->getProducts() as $cartProduct) {
            if ($cartProduct->getProductId() !== $productId) {
                continue;
            }

            $matchingProducts[] = $cartProduct;
        }

        if (!empty($matchingProducts)) {
            /** @var CartForOrderCreation\CartProduct $cartProduct */
            foreach ($matchingProducts as $cartProduct) {
                if ($cartProduct->isGift()) {
                    return;
                }
            }
        }

        throw new RuntimeException(sprintf(
            'Cart does not contain gift product "%s"',
            $productName
        ));
    }

    /**
     * @Then cart :cartReference should not contain gift product :productName
     * @Given cart :cartReference does not contain gift product :productName
     *
     * @param string $cartReference
     * @param string $productName
     */
    public function assertCartDoesNotContainGiftProduct(string $cartReference, string $productName)
    {
        $productId = (int) $this->getSharedStorage()->get($productName);
        $cartInfo = $this->getCartForOrderCreationByReference($cartReference);

        $matchingProducts = [];

        foreach ($cartInfo->getProducts() as $cartProduct) {
            if ($cartProduct->getProductId() === $productId) {
                $matchingProducts[] = $cartProduct;
            }
        }

        if (!empty($matchingProducts)) {
            /** @var CartForOrderCreation\CartProduct $cartProduct */
            foreach ($matchingProducts as $cartProduct) {
                if ($cartProduct->isGift()) {
                    throw new RuntimeException(sprintf(
                        'Cart contains gift product "%s"',
                        $productName
                    ));
                }
            }
        }
    }

    /**
     * @Then cart :cartReference should have free shipping
     */
    public function assertCartShippingIsFree(string $cartReference)
    {
        $cartInfo = $this->getCartForOrderCreationByReference($cartReference);
        Assert::assertTrue($cartInfo->getShipping()->isFreeShipping());
        Assert::assertEquals('0', $cartInfo->getShipping()->getShippingPrice());
    }

    /**
     * @Given /^I use a voucher "(.+)" which provides a gift product "(.+)" and free shipping on the cart "(.+)"$/
     */
    public function addGiftPlusFreeShippingCartRule(string $voucherCode, string $giftProductName, string $cartReference)
    {
        $productId = $this->getProductIdByName($giftProductName);

        if (!(new Product($productId))->id) {
            throw new RuntimeException(sprintf('Product %d was not found', $productId));
        }
        $cartRule = $this->createCommonCartRule($voucherCode);
        $cartRule->free_shipping = true;
        $cartRule->gift_product = $productId;

        $this->addCartRule($cartRule);
        $cartRuleId = (int) $cartRule->id;

        $this->getCommandBus()->handle(
            new AddCartRuleToCartCommand(
                SharedStorage::getStorage()->get($cartReference),
                $cartRuleId
            )
        );

        $this->getSharedStorage()->set($voucherCode, $cartRuleId);
        $this->getSharedStorage()->set($giftProductName, $productId);
    }

    /**
     * @Then /^reduction value of voucher "(.+)" in cart "(.+)" should be "(.+)"$/
     */
    public function assertReductionValueOfCartCartRule(
        string $voucherCode,
        string $cartReference,
        string $value
    ) {
        $cartInfo = $this->getCartForOrderCreationByReference($cartReference);
        $cartRuleId = $this->getSharedStorage()->get($voucherCode);

        foreach ($cartInfo->getCartRules() as $cartRule) {
            if ($cartRuleId === $cartRule->getCartRuleId()) {
                Assert::assertEquals($value, $cartRule->getValue());

                return;
            }
        }

        throw new RuntimeException(sprintf('Voucher was %s not found in cart', $voucherCode));
    }

    /**
     * @Then cart :cartReference should contain :quantity products
     *
     * @param string $cartReference
     * @param int $quantity
     */
    public function assertCartNumberOfProducts(string $cartReference, int $quantity)
    {
        $cartInfo = $this->getCartForOrderCreationByReference($cartReference);

        $cartProductsQuantity = \count($cartInfo->getProducts());

        if ($quantity !== $cartProductsQuantity) {
            throw new RuntimeException(sprintf(
                'Cart contains %d products instead of %d',
                $cartProductsQuantity,
                $quantity
            ));
        }
    }

    /**
     * @Then cart :cartReference should contain :quantity products excluding gifts
     *
     * @param string $cartReference
     * @param int $quantity
     */
    public function assertCartNumberOfProductsExcludingGifts(string $cartReference, int $quantity)
    {
        $cartInfo = $this->getCartForOrderCreationByReference($cartReference);

        $cartProductsQuantity = 0;
        foreach ($cartInfo->getProducts() as $product) {
            if (false === (bool) $product->isGift()) {
                ++$cartProductsQuantity;
            }
        }

        if ($quantity !== $cartProductsQuantity) {
            throw new RuntimeException(sprintf(
                'Cart contains %d products instead of %d',
                $cartProductsQuantity,
                $quantity
            ));
        }
    }

    /**
     * @Then I should get error that carrier is invalid
     */
    public function assertLastErrorIsInvalidCarrier(): void
    {
        $this->assertLastErrorIs(
            CartConstraintException::class,
            CartConstraintException::INVALID_CARRIER
        );
    }

    /**
     * @Then I should get an error that you have the maximum quantity available for this pack
     */
    public function assertLastErrorMaxQuantityAvailableForThisProduct(): void
    {
        $this->assertLastErrorIs(PackOutOfStockException::class);
    }

    /**
     * @param string $cartReference
     * @param string $productName
     *
     * @return bool
     */
    private function productIsInCart(string $cartReference, string $productName): bool
    {
        $productId = (int) $this->getSharedStorage()->get($productName);
        $cartInfo = $this->getCartForOrderCreationByReference($cartReference);

        foreach ($cartInfo->getProducts() as $cartProduct) {
            if ($cartProduct->getProductId() === $productId) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $cartReference
     *
     * @return CartForOrderCreation
     */
    private function getCartForOrderCreationByReference(string $cartReference): CartForOrderCreation
    {
        $cartId = $this->getSharedStorage()->get($cartReference);

        return $this->getQueryBus()->handle(
            (new GetCartForOrderCreation($cartId))
                ->setHideDiscounts(true)
        );
    }

    /**
     * @param string $voucherCode
     *
     * @return CartRule
     */
    private function createCommonCartRule(string $voucherCode): CartRule
    {
        $cartRule = new CartRule();
        $cartRule->name = [Configuration::get('PS_LANG_DEFAULT') => $voucherCode];
        $cartRule->priority = 1;
        $cartRule->quantity = 1;
        $cartRule->quantity_per_user = 1;
        $now = new DateTime();
        // sub 1s to avoid bad comparisons with strictly greater than
        $now->sub(new DateInterval('P2D'));
        $cartRule->date_from = $now->format('Y-m-d H:i:s');
        $now->add(new DateInterval('P1Y'));
        $cartRule->date_to = $now->format('Y-m-d H:i:s');
        $cartRule->active = true;
        $cartRule->code = $voucherCode;

        return $cartRule;
    }

    /**
     * @param string $productName
     *
     * @return int
     */
    private function getProductIdByName(string $productName)
    {
        $products = $this->getQueryBus()->handle(
            new SearchProducts(
                $productName,
                1,
                Context::getContext()->currency->iso_code
            )
        );

        if (empty($products)) {
            throw new RuntimeException(sprintf('Product with name "%s" was not found', $productName));
        }

        /** @var FoundProduct $product */
        $product = reset($products);

        return $product->getProductId();
    }

    /**
     * @param CartRule $cartRule
     */
    private function addCartRule(CartRule $cartRule): void
    {
        if (!$cartRule->add()) {
            throw new RuntimeException('Cannot add cart rule to database');
        }
    }

    /**
     * @Then I should get error that minimum quantity of :minQuantity must be added to cart
     *
     * @param int $minQuantity
     */
    public function assertLastErrorIsMinimumQuantityWhichMustBeAddedToCart(int $minQuantity)
    {
        /** @var MinimalQuantityException $lastError */
        $lastError = $this->assertLastErrorIs(
            MinimalQuantityException::class
        );
        if ($minQuantity !== $lastError->getMinimalQuantity()) {
            throw new RuntimeException(sprintf(
                'Minimal quantity in exception, expected %s but got %s',
                $minQuantity,
                $lastError->getMinimalQuantity()
            ));
        }
    }

    /**
     * @Then cart :cartReference total with tax included should be :expectedTotal
     */
    public function totalCartWithTaxShouldBe(string $cartReference, string $expectedTotal)
    {
        $cartInfo = $this->getCartForOrderCreationByReference($cartReference);
        $cartTotal = $cartInfo->getSummary()->getTotalPriceWithTaxes();
        if ($cartTotal !== $expectedTotal) {
            throw new \RuntimeException(sprintf('Expects %s, got %s instead', $expectedTotal, $cartTotal));
        }
    }

    /**
     * @When I create an empty anonymous cart :cartReference
     *
     * @param string $cartReference
     */
    public function createEmptyAnonymousCart(string $cartReference)
    {
        $cart = new Cart();
        $cart->id_currency = 1;
        $cart->id_guest = 1;
        $cart->save();
        SharedStorage::getStorage()->set($cartReference, (int) $cart->id);
    }

    /**
     * @When I assign customer :customerReference to cart :cartReference
     *
     * @param string $customerReference
     * @param string $cartReference
     */
    public function assignCustomerToCart(string $customerReference, string $cartReference)
    {
        $cartId = (int) SharedStorage::getStorage()->get($cartReference);
        $customerId = (int) SharedStorage::getStorage()->get($customerReference);

        $cart = new Cart($cartId);
        $cart->id_guest = 0;
        $cart->id_customer = $customerId;
        $cart->save();
        Context::getContext()->cart = $cart;
    }

    /**
     * @param string $reference
     *
     * @return Currency
     */
    private function getCurrency(string $reference): Currency
    {
        return new Currency(SharedStorage::getStorage()->get($reference));
    }

    /**
     * @When I delete cart :cartReference
     */
    public function iDeleteCart(string $cartReference)
    {
        $cartId = (int) SharedStorage::getStorage()->get($cartReference);
        try {
            $this->getCommandBus()->handle(new DeleteCartCommand($cartId));
        } catch (CartException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then cart :cartReference should be deleted
     */
    public function cartShouldBeDeleted(string $cartReference): void
    {
        $cartId = (int) SharedStorage::getStorage()->get($cartReference);
        $repository = new CartRepository();

        try {
            $repository->get(new CartId($cartId));
        } catch (CartException $e) {
            $this->setLastException($e);
        }

        $this->assertLastErrorIs(CartNotFoundException::class);
    }

    /**
     * @Then cart :cartReference should exist because cart is already ordered
     */
    public function cartShouldExistBecauseCartIsAlreadyOrdered(string $cartReference)
    {
        $this->assertLastErrorIs(CannotDeleteOrderedCartException::class);
        $this->cartShouldExist($cartReference);
    }

    /**
     * @When I bulk delete carts :cartReferences
     */
    public function iBulkDeleteCarts(string $cartReferences)
    {
        try {
            $this->getCommandBus()->handle(new BulkDeleteCartCommand($this->referencesToIds($cartReferences)));
        } catch (CartException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Given cart :cartReference should exist
     */
    public function cartShouldExist(string $cartReference)
    {
        $cartId = (int) SharedStorage::getStorage()->get($cartReference);
        $repository = new CartRepository();

        try {
            $repository->get(new CartId($cartId));
        } catch (CartException $e) {
            throw new RuntimeException(sprintf('cart %s should exist', $cartReference));
        }
    }
}
