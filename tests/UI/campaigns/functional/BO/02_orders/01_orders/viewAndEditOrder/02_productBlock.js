require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const viewOrderPage = require('@pages/BO/orders/view');
const taxesPage = require('@pages/BO/international/taxes');
const productsPage = require('@pages/BO/catalog/products');
const addProductPage = require('@pages/BO/catalog/products/add');
const cartRulesPage = require('@pages/BO/catalog/discounts');
const addCartRulePage = require('@pages/BO/catalog/discounts/add');
const customersPage = require('@pages/BO/customers');

// Import FO pages
const foHomePage = require('@pages/FO/home');
const foProductPage = require('@pages/FO/product');
const foCartPage = require('@pages/FO/cart');
const foCheckoutPage = require('@pages/FO/checkout');
const foOrderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');

// Import faker data
const CustomerFaker = require('@data/faker/customer');
const AddressFaker = require('@data/faker/address');
const ProductFaker = require('@data/faker/product');
const CartRuleFaker = require('@data/faker/cartRule');

// Import demo data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {Products} = require('@data/demo/products');

const baseContext = 'functional_BO_orders_orders_viewAndEditOrder_productBlock';

let browserContext;
let page;

const customerData = new CustomerFaker({password: ''});
const addressData = new AddressFaker({country: 'France'});

const productOutOfStockAllowed = new ProductFaker({
  name: 'Out of stock allowed',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: -12,
  minimumQuantity: 1,
  lowStockLevel: 3,
  behaviourOutOfStock: 'Allow orders',
});
const productOutOfStockNotAllowed = new ProductFaker({
  name: 'Out of stock not allowed',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: -36,
  minimumQuantity: 3,
  stockLocation: 'stock 3',
  lowStockLevel: 3,
  behaviourOutOfStock: 'Deny orders',
});
const packOfProducts = new ProductFaker({
  name: 'Pack of products',
  type: 'Pack of products',
  pack: {demo_13: 1, demo_7: 1},
  taxRule: 'No tax',
  quantity: 197,
  minimumQuantity: 3,
  stockLocation: 'stock 3',
  lowStockLevel: 3,
  behaviourOutOfStock: 'Default behavior',
});
const virtualProduct = new ProductFaker({
  name: 'Virtual product',
  type: 'Virtual product',
  taxRule: 'No tax',
  quantity: 20,
});
const combinationProduct = new ProductFaker({
  name: 'Product with combination',
  type: 'Standard product',
  productHasCombinations: true,
  taxRule: 'No tax',
  quantity: 197,
  minimumQuantity: 1,
  lowStockLevel: 3,
  behaviourOutOfStock: 'Default behavior',
});
const simpleProduct = new ProductFaker({
  name: 'Simple product',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: 50,
  price: 20,
  minimumQuantity: 1,
  stockLocation: 'stock 1',
  lowStockLevel: 3,
  behaviourOutOfStock: 'Default behavior',
});

const productWithSpecificPrice = new ProductFaker({
  name: 'Product with specific price',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: 20,
  specificPrice: {
    discount: 50,
    startingAt: 2,
    reductionType: '%',
  },
});

const productWithEcoTax = new ProductFaker({
  name: 'Product with ecotax',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: 20,
  minimumQuantity: 1,
  ecoTax: 10,
});

const productWithCartRule = new ProductFaker({
  name: 'Product with cart rule',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: 50,
  minimumQuantity: 1,
  stockLocation: 'stock 1',
  lowStockLevel: 3,
  behaviourOutOfStock: 'Default behavior',
});

const newCartRuleData = new CartRuleFaker(
  {
    code: '4QABV6L3',
    discountType: 'Percent',
    discountPercent: 20,
    applyDiscountTo: 'Specific product',
    product: productWithCartRule.name,
  },
);
const productQuantity = 0;
const newQuantity = 2;
const newPrice = 25;
let productNumber = 0;

/*
Pre-condition:
- Create order by guest in FO
- Enable eco tax
- Create 9 products in BO:
  - Out of stock allowed
  - Out of stock not allowed
  - Simple
  - Standard with combination
  - Pack
  - Virtual
  - With specific price
  - With ecoTax
  - With cart rule
- Create cart rule for the product with cart rule
Scenario:
Check product block content:
  - Check number of products
  - Update the price of an ordered
  - Update the quantity of an ordered product
  - Add and check product out of stock not allowed
  - Add and check product out of stock allowed
  - Add all types of products
  - Pagination next and previous
  - Delete product
Post-condition:
- Delete guest customer
- Disable ecoTax
- Delete created products
- Delete cart rule
*/
describe('BO - Orders - View and edit order : Check product block in view order page', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should go to FO page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    await foHomePage.goToFo(page);
    await foHomePage.changeLanguage(page, 'en');

    const isHomePage = await foHomePage.isHomePage(page);
    await expect(isHomePage, 'Fail to open FO home page').to.be.true;
  });

  // Pre-condition - Create order by guest
  describe('Create order by guest in FO', async () => {
    it('should add product to cart and proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foHomePage.goToHomePage(page);

      // Go to the fourth product page
      await foHomePage.goToProductPage(page, 4);

      // Add the created product to the cart
      await foProductPage.addProductToTheCart(page, productQuantity);

      // Proceed to checkout the shopping cart
      await foCartPage.clickOnProceedToCheckout(page);

      // Go to checkout page
      const isCheckoutPage = await foCheckoutPage.isCheckoutPage(page);
      await expect(isCheckoutPage).to.be.true;
    });

    it('should fill guest personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setPersonalInformation', baseContext);

      const isStepPersonalInfoCompleted = await foCheckoutPage.setGuestPersonalInformation(page, customerData);
      await expect(isStepPersonalInfoCompleted, 'Step personal information is not completed').to.be.true;
    });

    it('should fill address form and go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setAddressStep', baseContext);

      const isStepAddressComplete = await foCheckoutPage.setAddress(page, addressData);
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
    });

    it('should validate the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'validateOrder', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await foCheckoutPage.goToPaymentStep(page);
      await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;

      // Payment step - Choose payment step
      await foCheckoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);
      const cardTitle = await foOrderConfirmationPage.getOrderConfirmationCardTitle(page);

      // Check the confirmation message
      await expect(cardTitle).to.contains(foOrderConfirmationPage.orderConfirmationCardTitle);
      productNumber += 1;
    });
  });

  // Pre-condition - Create 9 products, Enable ecoTax, Create cart rule
  describe('Create 9 products in BO', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'International > Taxes\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.internationalParentLink, dashboardPage.taxesLink);

      await taxesPage.closeSfToolBar(page);

      const pageTitle = await taxesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(taxesPage.pageTitle);
    });

    it('should enable EcoTax', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableEcoTax', baseContext);

      const textResult = await taxesPage.enableEcoTax(page, true);
      await expect(textResult).to.be.equal('Update successful');
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.catalogParentLink, dashboardPage.productsLink);

      await productsPage.closeSfToolBar(page);

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should reset all filters and get number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersBeforeCreate', baseContext);

      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProducts).to.be.above(0);
    });

    [productOutOfStockAllowed,
      productOutOfStockNotAllowed,
      packOfProducts,
      virtualProduct,
      combinationProduct,
      simpleProduct,
      productWithSpecificPrice,
      productWithEcoTax,
      productWithCartRule,
    ].forEach((product, index) => {
      describe(`Create product : '${product.name}'`, async () => {
        it('should go to add product page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToAddProductPage${index}`, baseContext);

          if (index === 0) {
            await productsPage.goToAddProductPage(page);
          } else {
            await addProductPage.goToAddProductPage(page);
          }

          const pageTitle = await addProductPage.getPageTitle(page);
          await expect(pageTitle).to.contains(addProductPage.pageTitle);
        });

        it('should create Product', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `createProduct${index}`, baseContext);

          let createProductMessage = '';
          if (product === virtualProduct || product === productWithSpecificPrice) {
            createProductMessage = await addProductPage.createEditBasicProduct(page, product);
          } else {
            createProductMessage = await addProductPage.setProduct(page, product);
          }
          if (product === productWithSpecificPrice) {
            await addProductPage.addSpecificPrices(page, productWithSpecificPrice.specificPrice);
          }
          if (product === productWithEcoTax) {
            await addProductPage.addEcoTax(page, productWithEcoTax.ecoTax);
          }
          await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
        });
      });
    });

    describe(`Create cart rule and apply the discount to : '${productWithCartRule.name}'`, async () => {
      it('should go to \'Catalog > Discounts\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.catalogParentLink,
          dashboardPage.discountsLink,
        );

        const pageTitle = await cartRulesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(cartRulesPage.pageTitle);
      });

      it('should go to new cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage', baseContext);

        await cartRulesPage.goToAddNewCartRulesPage(page);
        const pageTitle = await addCartRulePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addCartRulePage.pageTitle);
      });

      it('should create new cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createCartRule', baseContext);

        const validationMessage = await addCartRulePage.createEditCartRules(page, newCartRuleData);
        await expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);
      });
    });
  });

  // 1 - Go to view order page
  describe('Go to view order page', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilters', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${customerData.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterTable', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', customerData.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(customerData.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewOrderPage', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });
  });

  // 2 - Check product block
  describe('Check product block', async () => {
    it('should delete the ordered product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const textResult = await viewOrderPage.deleteProduct(page, 1);
      await expect(textResult).to.contains(viewOrderPage.successfulDeleteProductMessage);
      productNumber -= 1;
    });

    it('should check number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts0', baseContext);

      const productCount = await viewOrderPage.getProductsNumber(page);
      await expect(productCount).to.equal(productNumber);
    });

    describe('Add \'Simple product\' 2 times and check the error message', async () => {
      it(`should search for the product '${simpleProduct.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchSimpleProduct1', baseContext);
        await viewOrderPage.searchProduct(page, simpleProduct.name);
        const result = await viewOrderPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.stockLocation).to.equal(simpleProduct.stockLocation),
          expect(result.available).to.equal(simpleProduct.quantity - 1),
          expect(result.price).to.equal(simpleProduct.price),
        ]);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addSimpleProductToTheCart1', baseContext);

        const textResult = await viewOrderPage.addProductToCart(page);
        await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);
        productNumber += 1;
      });

      it('should check the ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkSimpleProductDetails', baseContext);

        const result = await viewOrderPage.getProductDetails(page, 1);
        await Promise.all([
          expect(result.name).to.equal(simpleProduct.name),
          expect(result.reference).to.equal(`Reference number: ${simpleProduct.reference}`),
          expect(result.basePrice).to.equal(simpleProduct.price),
          expect(result.quantity).to.equal(1),
          expect(result.available).to.equal(simpleProduct.quantity - 1),
          expect(result.total).to.equal(simpleProduct.price),
        ]);
      });

      it('should add the same product and check the error message', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'orderSimpleProduct2', baseContext);

        await viewOrderPage.searchProduct(page, simpleProduct.name);

        const textResult = await viewOrderPage.addProductToCart(page);
        await expect(textResult).to.contains(viewOrderPage.errorAddSameProduct);
      });

      it('should click on cancel button', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOncancelButton1', baseContext);

        await viewOrderPage.cancelAddProductToCart(page);

        const isVisible = await viewOrderPage.isAddProductTableRowVisible(page);
        await expect(isVisible).to.be.false;
      });
    });

    describe('Add \'Standard product with combination\'', async () => {
      it(`should search for the product '${combinationProduct.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchCombinationProduct', baseContext);

        await viewOrderPage.searchProduct(page, combinationProduct.name);
        const result = await viewOrderPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.available).to.equal(combinationProduct.quantity - 1),
          expect(result.price).to.equal(combinationProduct.price),
        ]);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addCombinationProduct', baseContext);

        const textResult = await viewOrderPage.addProductToCart(page);
        await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);
        productNumber += 1;
      });

      it('should check the ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCombinationProductDetails', baseContext);

        const result = await viewOrderPage.getProductDetails(page, 1);
        await Promise.all([
          expect(result.name).to.equal(`${combinationProduct.name} (Size: `
            + `${combinationProduct.combinations.size[0]} - Color: ${combinationProduct.combinations.color[0]})`),
          expect(result.reference).to.equal(`Reference number: ${combinationProduct.reference}`),
          expect(result.basePrice).to.equal(combinationProduct.price),
          expect(result.quantity).to.equal(1),
          expect(result.available).to.equal(combinationProduct.quantity - 1),
          expect(result.total).to.equal(combinationProduct.price),
        ]);
      });
    });

    describe('Add \'Virtual product\'', async () => {
      it(`should search for the product '${virtualProduct.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchVirtualProduct', baseContext);

        await viewOrderPage.searchProduct(page, virtualProduct.name);
        const result = await viewOrderPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.available).to.equal(virtualProduct.quantity - 1),
          expect(result.price).to.equal(virtualProduct.price),
        ]);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addVirtualProductToTheCart', baseContext);

        const textResult = await viewOrderPage.addProductToCart(page);
        await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);
        productNumber += 1;
      });

      it('should check the ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkVirtualProductDetails', baseContext);

        const result = await viewOrderPage.getProductDetails(page, 1);
        await Promise.all([
          expect(result.name).to.equal(virtualProduct.name),
          expect(result.reference).to.equal(`Reference number: ${virtualProduct.reference}`),
          expect(result.basePrice).to.equal(virtualProduct.price),
          expect(result.quantity).to.equal(1),
          expect(result.available).to.equal(virtualProduct.quantity - 1),
          expect(result.total).to.equal(virtualProduct.price),
        ]);
      });
    });

    describe('Add \'Pack of products\'', async () => {
      it(`should search for the product '${packOfProducts.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchPackOfProducts', baseContext);

        await viewOrderPage.searchProduct(page, packOfProducts.name);
        const result = await viewOrderPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.stockLocation).to.equal(packOfProducts.stockLocation),
          expect(result.available).to.be.above(0),
          expect(result.price).to.equal(packOfProducts.price),
        ]);
      });

      it('should try to add the product to the cart and check the error message for the minimal '
        + 'quantity', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addPackOfProductsToTheCart', baseContext);

        const textResult = await viewOrderPage.addProductToCart(page);
        await expect(textResult).to.contains(viewOrderPage.errorMinimumQuantityMessage);
      });

      it('should increase the quantity and check ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkPackOfProductsDetails', baseContext);

        const textResult = await viewOrderPage.addProductToCart(page, packOfProducts.minimumQuantity);
        await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);

        const result = await viewOrderPage.getProductDetails(page, 1);
        await Promise.all([
          expect(result.name).to.equal(packOfProducts.name),
          expect(result.reference).to.equal(`Reference number: ${packOfProducts.reference}`),
          expect(result.basePrice).to.equal(packOfProducts.price),
          expect(result.quantity).to.equal(packOfProducts.minimumQuantity),
          expect(result.available).to.equal(packOfProducts.quantity - packOfProducts.minimumQuantity),
          expect(result.total).to.equal(packOfProducts.price * packOfProducts.minimumQuantity),
        ]);
        productNumber += 1;
      });
    });

    describe('Add \'Customized product\'', async () => {
      it(`should search for the product '${Products.demo_14.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchCustomizedProduct', baseContext);

        await viewOrderPage.searchProduct(page, Products.demo_14.name);
        const result = await viewOrderPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.available).to.be.above(0),
          expect(result.price).to.equal(Products.demo_14.priceTaxIncl),
        ]);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addCustomizedProductToTheCart', baseContext);

        const textResult = await viewOrderPage.addProductToCart(page);
        await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);
        productNumber += 1;
      });

      it('should check the ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCustomizedProductDetails', baseContext);

        const result = await viewOrderPage.getProductDetails(page, 1);
        await Promise.all([
          expect(result.name).to.equal(Products.demo_14.name),
          expect(result.reference).to.equal(`Reference number: ${Products.demo_14.reference}`),
          expect(result.basePrice).to.equal(Products.demo_14.priceTaxIncl),
          expect(result.quantity).to.equal(1),
          expect(result.total).to.equal(Products.demo_14.priceTaxIncl),
        ]);
      });
    });

    describe('Add product \'Out of stock allowed\'', async () => {
      it(`should search for the product '${productOutOfStockAllowed.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchProductOutOfStockAllowed', baseContext);

        await viewOrderPage.searchProduct(page, productOutOfStockAllowed.name);
        const result = await viewOrderPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.stockLocation).to.equal(productOutOfStockAllowed.stockLocation),
          expect(result.available).to.equal(productOutOfStockAllowed.quantity - 1),
          expect(result.price).to.equal(productOutOfStockAllowed.price),
        ]);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductOutOfStockAllowed', baseContext);

        const textResult = await viewOrderPage.addProductToCart(page);
        await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);
        productNumber += 1;
      });

      it('should check the ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductOutOfStockAllowedDetails', baseContext);

        const result = await viewOrderPage.getProductDetails(page, 1);
        await Promise.all([
          expect(result.name).to.equal(productOutOfStockAllowed.name),
          expect(result.basePrice).to.equal(productOutOfStockAllowed.price),
          expect(result.quantity).to.equal(1),
          expect(result.available).to.equal(productOutOfStockAllowed.quantity - 1),
          expect(result.total).to.equal(productOutOfStockAllowed.price),
        ]);
      });

      it('should update quantity of the product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityOutOfStockAllowed', baseContext);

        const quantity = await viewOrderPage.modifyProductQuantity(page, 1, newQuantity);
        await expect(quantity, 'Quantity was not updated').to.equal(newQuantity);
      });
    });

    describe('Add product \'Out of stock not allowed\'', async () => {
      it(`should search for the product '${productOutOfStockNotAllowed.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchProductOutOfStockNotAllowed', baseContext);

        await viewOrderPage.searchProduct(page, productOutOfStockNotAllowed.name);
        const result = await viewOrderPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.stockLocation).to.equal(productOutOfStockNotAllowed.stockLocation),
          expect(result.available).to.equal(productOutOfStockNotAllowed.quantity - 1),
          expect(result.price).to.equal(productOutOfStockNotAllowed.price),
        ]);
      });

      it('should check that add button is disabled', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkThatAddButtonIsDisabled', baseContext);

        const isDisabled = await viewOrderPage.isAddButtonDisabled(page);
        await expect(isDisabled).to.be.true;
      });

      it('should increase the quantity of the product and check that cancel button still disabled', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityOutOfStockNotAllowed', baseContext);

        await viewOrderPage.addQuantity(page, newQuantity);

        const isDisabled = await viewOrderPage.isAddButtonDisabled(page);
        await expect(isDisabled).to.be.true;
      });

      it('should click on cancel button', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnCancelButton2', baseContext);

        await viewOrderPage.cancelAddProductToCart(page);

        const isVisible = await viewOrderPage.isAddProductTableRowVisible(page);
        await expect(isVisible).to.be.false;
      });
    });

    describe('Add \'Product with specific price\'', async () => {
      it(`should search for the product '${productWithSpecificPrice.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchProductWithSpecificPrice', baseContext);

        await viewOrderPage.searchProduct(page, productWithSpecificPrice.name);
        const result = await viewOrderPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.available).to.be.above(0),
          expect(result.price).to.equal(productWithSpecificPrice.price),
        ]);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductWithSpecificPriceToTheCart', baseContext);

        const textResult = await viewOrderPage.addProductToCart(page);
        await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);
        productNumber += 1;
      });

      it('should check the ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductWithSpecificPriceDetails', baseContext);

        const result = await viewOrderPage.getProductDetails(page, 1);
        await Promise.all([
          expect(result.name).to.equal(productWithSpecificPrice.name),
          expect(result.basePrice).to.equal(productWithSpecificPrice.price),
          expect(result.quantity).to.equal(1),
          expect(result.available).to.equal(productWithSpecificPrice.quantity - 1),
          expect(result.total).to.equal(productWithSpecificPrice.price),
        ]);
      });
    });

    describe('Add \'Product with eco tax\'', async () => {
      it(`should search for the product '${productWithEcoTax.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchProductWithEcoTax', baseContext);

        await viewOrderPage.searchProduct(page, productWithEcoTax.name);
        const result = await viewOrderPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.stockLocation).to.equal(productWithEcoTax.stockLocation),
          expect(result.available).to.be.above(0),
          expect(result.price).to.equal(productWithEcoTax.price),
        ]);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductWithEcoTax', baseContext);

        const textResult = await viewOrderPage.addProductToCart(page);
        await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);
        productNumber += 1;
      });

      it('should check the ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductWithEcoTaxDetails', baseContext);

        const result = await viewOrderPage.getProductDetails(page, 1);
        await Promise.all([
          expect(result.name).to.equal(productWithEcoTax.name),
          expect(result.basePrice).to.equal(productWithEcoTax.price),
          expect(result.quantity).to.equal(1),
          expect(result.available).to.equal(productWithEcoTax.quantity - 1),
          expect(result.total).to.equal(productWithEcoTax.price),
        ]);
      });
    });

    describe('Add \'Product with tax rule\'', async () => {
      it(`should search for the product '${productWithCartRule.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchProductWithTaxRule', baseContext);

        await viewOrderPage.searchProduct(page, productWithCartRule.name);
        const result = await viewOrderPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.stockLocation).to.equal(productWithCartRule.stockLocation),
          expect(result.available).to.be.above(0),
          expect(result.price).to.equal(productWithCartRule.price),
        ]);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductWithTaxRuleToThecart', baseContext);

        const textResult = await viewOrderPage.addProductToCart(page);
        await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);
        productNumber += 1;
      });

      it('should check the ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductWithCartRuleDetails', baseContext);

        const result = await viewOrderPage.getProductDetails(page, 1);
        await Promise.all([
          expect(result.name).to.equal(productWithCartRule.name),
          expect(result.basePrice).to.equal(productWithCartRule.price),
          expect(result.quantity).to.equal(1),
          expect(result.available).to.equal(productWithCartRule.quantity - 1),
          expect(result.total).to.equal(productWithCartRule.price),
        ]);
      });
    });

    describe('Update price and quantity of the first ordered product in the list', async () => {
      it('should update the quantity', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateQuantity', baseContext);

        const quantity = await viewOrderPage.modifyProductQuantity(page, 1, newQuantity);
        await expect(quantity, 'Quantity was not updated').to.equal(newQuantity);
      });

      it('should update the price', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updatePrice', baseContext);

        await viewOrderPage.modifyProductPrice(page, 1, newPrice);

        const result = await viewOrderPage.getProductDetails(page, 1);
        await Promise.all([
          expect(result.basePrice, 'Base price was not updated').to.equal(25),
          expect(result.total, 'Total price was not updated').to.equal(newPrice * newQuantity),
        ]);
      });
    });

    describe('Check items per page', async () => {
      it('should check number of products', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts', baseContext);

        const productCount = await viewOrderPage.getProductsNumber(page);
        await expect(productCount).to.equal(productNumber);
      });

      describe('Paginate between pages', async () => {
        it('should display 8 items', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'displayDefaultItemsNumber', baseContext);

          const isNextLinkVisible = await viewOrderPage.selectPaginationLimit(page, '8');
          await expect(isNextLinkVisible).to.be.true;
        });

        it('should click on next', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

          const paginationNumber = await viewOrderPage.paginationNext(page);
          await expect(paginationNumber).to.equal('2');
        });

        it('should click on previous', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

          const paginationNumber = await viewOrderPage.paginationPrevious(page);
          await expect(paginationNumber).to.equal('1');
        });

        it('should display 20 items', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'displayAllItems', baseContext);

          const isNextLinkVisible = await viewOrderPage.selectPaginationLimit(page, '20');
          await expect(isNextLinkVisible).to.be.false;
        });

        it('should display 8 items', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'redisplayDefaultItemsNumber', baseContext);

          const isNextLinkVisible = await viewOrderPage.selectPaginationLimit(page, '8');
          await expect(isNextLinkVisible).to.be.true;
        });
      });
    });
  });

  // Post-condition - Delete the created customer
  describe('Delete the created customer', async () => {
    it('should go \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.customersParentLink, dashboardPage.customersLink);

      await customersPage.closeSfToolBar(page);

      const pageTitle = await customersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it(`should filter list by email': '${customerData.email}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await customersPage.filterCustomers(page, 'input', 'email', customerData.email);

      const textResult = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      await expect(textResult).to.contains(customerData.email);
    });

    it('should delete customer and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCustomer', baseContext);

      const deleteTextResult = await customersPage.deleteCustomer(page, 1);
      await expect(deleteTextResult).to.be.equal(customersPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfCustomersAfterReset = await customersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCustomersAfterReset).to.be.above(0);
    });
  });

  // Post-condition - Delete the created products
  describe('Delete the created products', async () => {
    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToDelete', baseContext);

      await addProductPage.goToSubMenu(page, addProductPage.catalogParentLink, addProductPage.productsLink);

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    [productOutOfStockAllowed,
      productOutOfStockNotAllowed,
      packOfProducts,
      virtualProduct,
      combinationProduct,
      simpleProduct,
      productWithSpecificPrice,
      productWithEcoTax,
      productWithCartRule,
    ].forEach((product, index) => {
      it(`should delete product '${product.name}' from DropDown Menu`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteProduct${index}`, baseContext);

        const deleteTextResult = await productsPage.deleteProduct(page, product);
        await expect(deleteTextResult).to.equal(productsPage.productDeletedSuccessfulMessage);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFiltersAfterDelete${index}`, baseContext);

        const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfProducts).to.be.above(0);
      });
    });
  });

  // Post-condition - Delete cart rule
  describe('Delete the created cart rule', async () => {
    it('should go to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.discountsLink,
      );

      const pageTitle = await cartRulesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(cartRulesPage.pageTitle);
    });

    it('should delete cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCartRule', baseContext);

      const validationMessage = await cartRulesPage.deleteCartRule(page);
      await expect(validationMessage).to.contains(cartRulesPage.successfulDeleteMessage);
    });
  });

  // Post-condition - Disable EcoTax
  describe('Disable Eco tax', async () => {
    it('should go to \'International > Taxes\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage2', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.internationalParentLink, dashboardPage.taxesLink);

      await taxesPage.closeSfToolBar(page);

      const pageTitle = await taxesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(taxesPage.pageTitle);
    });

    it('should disable EcoTax', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableEcoTax', baseContext);

      const textResult = await taxesPage.enableEcoTax(page, false);
      await expect(textResult).to.be.equal('Update successful');
    });
  });
});
