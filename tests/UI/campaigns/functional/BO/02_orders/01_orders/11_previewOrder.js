require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');
const {deleteCustomerTest} = require('@commonTests/BO/customers/createDeleteCustomer');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const orderPageProductsBlock = require('@pages/BO/orders/view/productsBlock');
const orderPageTabListBlock = require('@pages/BO/orders/view/tabListBlock');
const orderPageCustomerBlock = require('@pages/BO/orders/view/customerBlock');

// Import FO pages
const homePage = require('@pages/FO/home');
const searchResultsPage = require('@pages/FO/searchResults');
const productPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');
const orderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');
const checkoutPage = require('@pages/FO/checkout');

// Import demo data
const {Products} = require('@data/demo/products');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {Carriers} = require('@data/demo/carriers');

// Import faker data
const CustomerFaker = require('@data/faker/customer');
const AddressFaker = require('@data/faker/address');

const baseContext = 'functional_BO_orders_orders_previewOrder';

// Browser and tab
let browserContext;
let page;

const customerData = new CustomerFaker({password: ''});
const addressData = new AddressFaker({country: 'France'});

const editShippingAddressData = new AddressFaker({country: 'France'});
const editInvoiceAddressData = new AddressFaker({country: 'France'});

const shippingDetailsData = {
  trackingNumber: '123654789',
  carrier: Carriers.myCarrier.name,
  carrierID: Carriers.myCarrier.id,
};

/*
Pre-condition:
- Create order contains 11 products by guest
Scenario:
- Go to orders page
- Filter by guest email
- Preview order and check all details
- Go to edit order page and edit (Address, carrier, add product)
- Go back to preview order and check all edited details
Post-condition
- Delete guest account
 */
describe('BO - Orders : Preview order', async () => {
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Pre-condition: Create order contains 11 products by guest in FO
  describe('PRE-TEST: Create order contains 11 products by guest in FO', async () => {
    it('should open FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFO', baseContext);

      // Go to FO and change language
      await homePage.goToFo(page);

      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    const tests = [
      {args: {productName: Products.demo_1.name}},
      {args: {productName: Products.demo_3.name}},
      {args: {productName: Products.demo_5.name}},
      {args: {productName: Products.demo_6.name}},
      {args: {productName: Products.demo_7.name}},
      {args: {productName: Products.demo_8.name}},
      {args: {productName: Products.demo_12.name}},
      {args: {productName: Products.demo_11.name}},
      {args: {productName: Products.demo_13.name}},
      {args: {productName: Products.demo_14.name}},
      {args: {productName: Products.demo_18.name}},
    ];

    tests.forEach((test, index) => {
      it(`should search for the product '${test.args.productName}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `searchForProduct_${index}`, baseContext);

        await homePage.searchProduct(page, test.args.productName);

        const pageTitle = await searchResultsPage.getPageTitle(page);
        await expect(pageTitle).to.equal(searchResultsPage.pageTitle);
      });

      it('should add the product to cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

        await searchResultsPage.goToProductPage(page, 1);

        // Add the product to the cart
        await productPage.addProductToTheCart(page, 1, {size: null, color: null}, false);

        const notificationsNumber = await productPage.getCartNotificationsNumber(page);
        await expect(notificationsNumber).to.be.equal(index + 1);
      });
    });

    it('should go to shopping cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCart', baseContext);

      await productPage.goToCartPage(page);

      const pageTitle = await cartPage.getPageTitle(page);
      await expect(pageTitle).to.contains(cartPage.pageTitle);
    });

    it('should proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      // Go to checkout page
      const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
      await expect(isCheckoutPage).to.be.true;
    });

    it('should fill guest personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setPersonalInformation', baseContext);

      const isStepPersonalInfoCompleted = await checkoutPage.setGuestPersonalInformation(page, customerData);
      await expect(isStepPersonalInfoCompleted, 'Step personal information is not completed').to.be.true;
    });

    it('should fill address form and go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setAddressStep', baseContext);

      const isStepAddressComplete = await checkoutPage.setAddress(page, addressData);
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
    });

    it('should validate the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'validateOrder', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;

      // Payment step - Choose payment step
      await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);
      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);

      // Check the confirmation message
      await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });
  });

  describe('Preview the created order', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    describe('Check the created order details', async () => {
      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

        await dashboardPage.goToSubMenu(page, dashboardPage.ordersParentLink, dashboardPage.ordersLink);

        await ordersPage.closeSfToolBar(page);

        const pageTitle = await ordersPage.getPageTitle(page);
        await expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

        const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
        await expect(numberOfOrders).to.be.above(0);
      });

      it(`should filter order by customer last name ${customerData.lastName}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer', baseContext);

        await ordersPage.filterOrders(page, 'input', 'customer', customerData.lastName);

        const numberOfOrders = await ordersPage.getNumberOfElementInGrid(page);
        await expect(numberOfOrders).to.be.at.least(1);
      });

      it('should click on expand button to preview the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'previewOrder', baseContext);

        const isPreviewBlockVisible = await ordersPage.previewOrder(page);
        await expect(isPreviewBlockVisible, 'Preview block is not visible').to.be.true;
      });

      it('should check the shipping details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkShippingDetails', baseContext);

        const shippingDetails = await ordersPage.getShippingDetails(page);
        await expect(shippingDetails, 'Shipping details are not correct!')
          .to.equal(`Carrier: ${Carriers.default.name} Tracking number: - Shipping details: `
            + `${customerData.firstName} ${customerData.lastName} ${addressData.company} ${addressData.address} `
            + `${addressData.postalCode} ${addressData.city} ${addressData.country} ${addressData.phone}`);
      });

      it('should check the guest email address', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkEmailAddress', baseContext);

        const emailAddress = await ordersPage.getCustomerEmail(page);
        await expect(emailAddress, 'Email address is not correct!').to.equal(`Email: ${customerData.email}`);
      });

      it('should check the invoice address details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkInvoiceDetails', baseContext);

        const invoiceAddress = await ordersPage.getCustomerInvoiceAddressDetails(page);
        await expect(invoiceAddress, 'Invoice details are not correct!')
          .to.equal(`Invoice details: ${customerData.firstName} ${customerData.lastName} `
            + `${addressData.company} ${addressData.address} ${addressData.postalCode} ${addressData.city} `
            + `${addressData.country} ${addressData.phone}`);
      });

      it('should check the products number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductsNumber', baseContext);

        const productsNumber = await ordersPage.getProductsNumberFromTable(page);
        await expect(productsNumber, 'Products number is not correct!').to.equal(11);
      });

      [
        {args: {product: Products.demo_1, productPrice: Products.demo_1.finalPrice}},
        {args: {product: Products.demo_3, productPrice: Products.demo_3.finalPrice}},
        {args: {product: Products.demo_5, productPrice: Products.demo_5.priceTaxIncl}},
        {args: {product: Products.demo_6, productPrice: Products.demo_6.priceTaxIncl}},
        {args: {product: Products.demo_7, productPrice: Products.demo_7.price}},
        {args: {product: Products.demo_8, productPrice: Products.demo_8.price}},
        {args: {product: Products.demo_11, productPrice: Products.demo_11.finalPrice}},
        {args: {product: Products.demo_12, productPrice: Products.demo_12.price_ttc}},
        {args: {product: Products.demo_13, productPrice: Products.demo_13.price}},
        {args: {product: Products.demo_14, productPrice: Products.demo_14.priceTaxIncl}},
      ].forEach((test, index) => {
        it(`should check the product '${test.args.product.name}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkProduct${index}`, baseContext);

          const productInformation = await ordersPage.getProductDetailsFromTable(page, index + 1);
          await expect(productInformation).to.contains(test.args.product.name)
            .and.to.contains(test.args.product.reference)
            .and.to.contains(1)
            .and.to.contains(test.args.productPrice);
        });
      });

      it('should check that the last line in product list contain \'(1 more)\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'check1MoreText', baseContext);

        const lastProductsTableLine = await ordersPage.getProductDetailsFromTable(page, 12);
        await expect(lastProductsTableLine).to.equal('more_horiz (1 more)');
      });

      it('should click on \'(1 more)\' link and check the last product in the list', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnMoreLink', baseContext);

        await ordersPage.clickOnMoreLink(page);
        const productInformation = await ordersPage.getProductDetailsFromTable(page, 11);
        await expect(productInformation).to.contains(Products.demo_18.name)
          .and.to.contains(Products.demo_18.reference)
          .and.to.contains(1)
          .and.to.contains(Products.demo_18.finalPrice);
      });
    });

    describe('Update the order from view order page then check the order details', async () => {
      it('should click on \'Open details\' button', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnOpenDetailsButton', baseContext);

        await ordersPage.openOrderDetails(page);

        const pageTitle = await orderPageProductsBlock.getPageTitle(page);
        await expect(pageTitle).to.contains(orderPageProductsBlock.pageTitle);
      });

      it('should add another product to the list', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addAnotherProductToTheList', baseContext);

        await orderPageProductsBlock.searchProduct(page, Products.demo_19.name);

        const textResult = await orderPageProductsBlock.addProductToCart(page);
        await expect(textResult).to.contains(orderPageProductsBlock.successfulAddProductMessage);
      });

      it('should click on \'Carriers\' tab', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'displayCarriersTab', baseContext);

        const isTabOpened = await orderPageTabListBlock.goToCarriersTab(page);
        await expect(isTabOpened).to.be.true;
      });

      it('should click on \'Edit\' link and check the modal', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditLink', baseContext);

        const isModalVisible = await orderPageTabListBlock.clickOnEditLink(page);
        await expect(isModalVisible, 'Edit shipping modal is not visible!').to.be.true;
      });

      it('should edit the carrier', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editCarrier', baseContext);

        const textResult = await orderPageTabListBlock.setShippingDetails(page, shippingDetailsData);
        await expect(textResult).to.equal(orderPageTabListBlock.successfulUpdateMessage);
      });

      it('should edit the shipping address', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editShippingAddress', baseContext);

        const shippingAddress = await orderPageCustomerBlock.editExistingShippingAddress(page, editShippingAddressData);
        await expect(shippingAddress, 'Shipping address is not correct!')
          .to.contain(editShippingAddressData.firstName)
          .and.to.contain(editShippingAddressData.lastName)
          .and.to.contain(editShippingAddressData.address)
          .and.to.contain(editShippingAddressData.postalCode)
          .and.to.contain(editShippingAddressData.city)
          .and.to.contain(editShippingAddressData.country);
      });

      it('should edit the delivery address', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editDeliveryAddress', baseContext);

        const invoiceAddress = await orderPageCustomerBlock.editExistingInvoiceAddress(page, editInvoiceAddressData);
        await expect(invoiceAddress, 'Invoice address is not correct!')
          .to.contain(editInvoiceAddressData.firstName)
          .and.to.contain(editInvoiceAddressData.lastName)
          .and.to.contain(editInvoiceAddressData.address)
          .and.to.contain(editInvoiceAddressData.postalCode)
          .and.to.contain(editInvoiceAddressData.city)
          .and.to.contain(editInvoiceAddressData.country);
      });

      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage1', baseContext);

        await dashboardPage.goToSubMenu(page, dashboardPage.ordersParentLink, dashboardPage.ordersLink);

        const pageTitle = await ordersPage.getPageTitle(page);
        await expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should click on expand button to preview the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'previewOrder2', baseContext);

        const isPreviewBlockVisible = await ordersPage.previewOrder(page);
        await expect(isPreviewBlockVisible, 'Preview block is not visible').to.be.true;
      });

      it('should check the shipping details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkEditedShippingAddress', baseContext);

        const shippingDetails = await ordersPage.getShippingDetails(page);
        await expect(shippingDetails, 'Shipping address is not correct!')
          .to.equal(`Carrier: ${shippingDetailsData.carrier} Tracking number: ${shippingDetailsData.trackingNumber}`
            + ` Shipping details: ${editShippingAddressData.firstName} ${editShippingAddressData.lastName}`
            + ` ${editShippingAddressData.company} ${editShippingAddressData.address}`
            + ` ${editShippingAddressData.secondAddress} ${editShippingAddressData.postalCode}`
            + ` ${editShippingAddressData.city} ${editShippingAddressData.country}`
            + ` ${editShippingAddressData.phone}`);
      });

      it('should check the edited invoice address details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkEditedInvoiceAddress', baseContext);

        const invoiceAddress = await ordersPage.getCustomerInvoiceAddressDetails(page);
        await expect(invoiceAddress, 'Invoice address is not correct!')
          .to.equal(`Invoice details: ${editInvoiceAddressData.firstName} ${editInvoiceAddressData.lastName} `
            + `${editInvoiceAddressData.company} ${editInvoiceAddressData.address}`
            + ` ${editInvoiceAddressData.secondAddress} ${editInvoiceAddressData.postalCode}`
            + ` ${editInvoiceAddressData.city} ${editInvoiceAddressData.country} ${editInvoiceAddressData.phone}`);
      });

      it('should check the products number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductsNumber1', baseContext);

        const productsNumber = await ordersPage.getProductsNumberFromTable(page);
        await expect(productsNumber, 'Products number is not correct!').to.equal(12);
      });

      describe('Check the products list', async () => {
        [
          {args: {product: Products.demo_1, productPrice: Products.demo_1.finalPrice}},
          {args: {product: Products.demo_3, productPrice: Products.demo_3.finalPrice}},
          {args: {product: Products.demo_5, productPrice: Products.demo_5.priceTaxIncl}},
          {args: {product: Products.demo_6, productPrice: Products.demo_6.priceTaxIncl}},
          {args: {product: Products.demo_7, productPrice: Products.demo_7.price}},
          {args: {product: Products.demo_8, productPrice: Products.demo_8.price}},
          {args: {product: Products.demo_11, productPrice: Products.demo_11.finalPrice}},
          {args: {product: Products.demo_12, productPrice: Products.demo_12.price_ttc}},
          {args: {product: Products.demo_13, productPrice: Products.demo_13.price}},
          {args: {product: Products.demo_14, productPrice: Products.demo_14.priceTaxIncl}},
        ].forEach((test, index) => {
          it(`should check the product '${test.args.product.name}'`, async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkProduct${index}1`, baseContext);

            const productInformation = await ordersPage.getProductDetailsFromTable(page, index + 1);
            await expect(productInformation).to.contains(test.args.product.name)
              .and.to.contains(test.args.product.reference)
              .and.to.contains(1)
              .and.to.contains(test.args.productPrice);
          });
        });

        it('should check that the last line in product list contain \'(2 more)\'', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'check2MoreText', baseContext);

          const lastProductsTableLine = await ordersPage.getProductDetailsFromTable(page, 13);
          await expect(lastProductsTableLine).to.equal('more_horiz (2 more)');
        });

        it('should click on \'(2 more)\' link and check the last product in the list', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'clickOnMoreLink1', baseContext);

          await ordersPage.clickOnMoreLink(page, 13);

          let productInformation = await ordersPage.getProductDetailsFromTable(page, 11);
          await expect(productInformation).to.contains(Products.demo_18.name)
            .and.to.contains(Products.demo_18.reference)
            .and.to.contains(1)
            .and.to.contains(Products.demo_18.finalPrice);
          productInformation = await ordersPage.getProductDetailsFromTable(page, 12);
          await expect(productInformation).to.contains(Products.demo_19.name)
            .and.to.contains(Products.demo_19.reference)
            .and.to.contains(1)
            .and.to.contains(Products.demo_19.finalPrice);
        });
      });
    });
  });

  // Post-condition: Delete guest account
  deleteCustomerTest(customerData, baseContext);
});
