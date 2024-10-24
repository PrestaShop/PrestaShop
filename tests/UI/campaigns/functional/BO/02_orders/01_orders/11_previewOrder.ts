// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';

// Import pages
// Import BO pages
import orderPageCustomerBlock from '@pages/BO/orders/view/customerBlock';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrdersViewBlockProductsPage,
  boOrdersViewBlockTabListPage,
  type BrowserContext,
  dataCarriers,
  dataPaymentMethods,
  dataProducts,
  FakerAddress,
  FakerCustomer,
  FakerOrderShipping,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicHomePage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_BO_orders_orders_previewOrder';

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
  let browserContext: BrowserContext;
  let page: Page;

  const customerData: FakerCustomer = new FakerCustomer({password: ''});
  const addressData: FakerAddress = new FakerAddress({country: 'France'});
  const editShippingAddressData: FakerAddress = new FakerAddress({country: 'France'});
  const editInvoiceAddressData: FakerAddress = new FakerAddress({country: 'France'});
  const shippingDetailsData: FakerOrderShipping = new FakerOrderShipping({
    trackingNumber: '123654789',
    carrier: dataCarriers.myCarrier.name,
    carrierID: dataCarriers.myCarrier.id,
  });

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // Pre-condition: Create order contains 11 products by guest in FO
  describe('PRE-TEST: Create order contains 11 products by guest in FO', async () => {
    it('should open FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFO', baseContext);

      // Go to FO and change language
      await foClassicHomePage.goToFo(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    [
      {args: {productName: dataProducts.demo_1.name}},
      {args: {productName: dataProducts.demo_3.name}},
      {args: {productName: dataProducts.demo_5.name}},
      {args: {productName: dataProducts.demo_6.name}},
      {args: {productName: dataProducts.demo_7.name}},
      {args: {productName: dataProducts.demo_8.name}},
      {args: {productName: dataProducts.demo_12.name}},
      {args: {productName: dataProducts.demo_11.name}},
      {args: {productName: dataProducts.demo_13.name}},
      {args: {productName: dataProducts.demo_14.name}},
      {args: {productName: dataProducts.demo_18.name}},
    ].forEach((test, index: number) => {
      it(`should search for the product '${test.args.productName}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `searchForProduct_${index}`, baseContext);

        await foClassicHomePage.searchProduct(page, test.args.productName);

        const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);
      });

      it('should add the product to cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

        await foClassicSearchResultsPage.goToProductPage(page, 1);
        // Add the product to the cart
        await foClassicProductPage.addProductToTheCart(page, 1, [], false);

        const notificationsNumber = await foClassicProductPage.getCartNotificationsNumber(page);
        expect(notificationsNumber).to.be.equal(index + 1);
      });
    });

    it('should go to shopping cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCart', baseContext);

      await foClassicProductPage.goToCartPage(page);

      const pageTitle = await foClassicCartPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicCartPage.pageTitle);
    });

    it('should proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      // Proceed to checkout the shopping cart
      await foClassicCartPage.clickOnProceedToCheckout(page);

      // Go to checkout page
      const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.eq(true);
    });

    it('should fill guest personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setPersonalInformation', baseContext);

      const isStepPersonalInfoCompleted = await foClassicCheckoutPage.setGuestPersonalInformation(page, customerData);
      expect(isStepPersonalInfoCompleted, 'Step personal information is not completed').to.eq(true);
    });

    it('should fill address form and go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setAddressStep', baseContext);

      const isStepAddressComplete = await foClassicCheckoutPage.setAddress(page, addressData);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should validate the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'validateOrder', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);

      // Payment step - Choose payment step
      await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);
      const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);

      // Check the confirmation message
      expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
    });
  });

  describe('Preview the created order', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    describe('Check the created order details', async () => {
      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

        await boDashboardPage.goToSubMenu(page, boDashboardPage.ordersParentLink, boDashboardPage.ordersLink);
        await boOrdersPage.closeSfToolBar(page);

        const pageTitle = await boOrdersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersPage.pageTitle);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

        const numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
        expect(numberOfOrders).to.be.above(0);
      });

      it(`should filter order by customer last name ${customerData.lastName}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer', baseContext);

        await boOrdersPage.filterOrders(page, 'input', 'customer', customerData.lastName);

        const numberOfOrders = await boOrdersPage.getNumberOfElementInGrid(page);
        expect(numberOfOrders).to.be.at.least(1);
      });

      it('should click on expand button to preview the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'previewOrder', baseContext);

        const isPreviewBlockVisible = await boOrdersPage.previewOrder(page);
        expect(isPreviewBlockVisible, 'Preview block is not visible').to.eq(true);
      });

      it('should check the shipping details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkShippingDetails', baseContext);

        const shippingDetails = await boOrdersPage.getShippingDetails(page);
        expect(shippingDetails, 'Shipping details are not correct!')
          .to.equal(`Carrier: ${dataCarriers.clickAndCollect.name} Tracking number: - Shipping details: `
            + `${customerData.firstName} ${customerData.lastName} ${addressData.company} ${addressData.address} `
            + `${addressData.postalCode} ${addressData.city} ${addressData.country} ${addressData.phone}`);
      });

      it('should check the guest email address', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkEmailAddress', baseContext);

        const emailAddress = await boOrdersPage.getCustomerEmail(page);
        expect(emailAddress, 'Email address is not correct!').to.equal(`Email: ${customerData.email}`);
      });

      it('should check the invoice address details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkInvoiceDetails', baseContext);

        const invoiceAddress = await boOrdersPage.getCustomerInvoiceAddressDetails(page);
        expect(invoiceAddress, 'Invoice details are not correct!')
          .to.equal(`Invoice details: ${customerData.firstName} ${customerData.lastName} `
            + `${addressData.company} ${addressData.address} ${addressData.postalCode} ${addressData.city} `
            + `${addressData.country} ${addressData.phone}`);
      });

      it('should check the products number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductsNumber', baseContext);

        const productsNumber = await boOrdersPage.getProductsNumberFromTable(page);
        expect(productsNumber, 'Products number is not correct!').to.equal(11);
      });

      [
        {args: {product: dataProducts.demo_1, productPrice: dataProducts.demo_1.finalPrice}},
        {args: {product: dataProducts.demo_3, productPrice: dataProducts.demo_3.finalPrice}},
        {args: {product: dataProducts.demo_5, productPrice: dataProducts.demo_5.price}},
        {args: {product: dataProducts.demo_6, productPrice: dataProducts.demo_6.combinations[0].price}},
        {args: {product: dataProducts.demo_7, productPrice: dataProducts.demo_7.price}},
        {args: {product: dataProducts.demo_8, productPrice: dataProducts.demo_8.price}},
        {args: {product: dataProducts.demo_11, productPrice: dataProducts.demo_11.finalPrice}},
        {args: {product: dataProducts.demo_12, productPrice: dataProducts.demo_12.price}},
        {args: {product: dataProducts.demo_13, productPrice: dataProducts.demo_13.price}},
        {args: {product: dataProducts.demo_14, productPrice: dataProducts.demo_14.price}},
      ].forEach((test, index: number) => {
        it(`should check the product '${test.args.product.name}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkProduct${index}`, baseContext);

          const productInformation = await boOrdersPage.getProductDetailsFromTable(page, index + 1);
          expect(productInformation).to.contains(test.args.product.name)
            .and.to.contains(test.args.product.reference)
            .and.to.contains(1)
            .and.to.contains(test.args.productPrice);
        });
      });

      it('should check that the last line in product list contain \'(1 more)\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'check1MoreText', baseContext);

        const lastProductsTableLine = await boOrdersPage.getProductDetailsFromTable(page, 12);
        expect(lastProductsTableLine).to.equal('more_horiz (1 more)');
      });

      it('should click on \'(1 more)\' link and check the last product in the list', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnMoreLink', baseContext);

        await boOrdersPage.clickOnMoreLink(page);

        const productInformation = await boOrdersPage.getProductDetailsFromTable(page, 11);
        expect(productInformation).to.contains(dataProducts.demo_18.name)
          .and.to.contains(dataProducts.demo_18.reference)
          .and.to.contains(1)
          .and.to.contains(dataProducts.demo_18.finalPrice);
      });
    });

    describe('Update the order from view order page then check the order details', async () => {
      it('should click on \'Open details\' button', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnOpenDetailsButton', baseContext);

        await boOrdersPage.openOrderDetails(page);

        const pageTitle = await boOrdersViewBlockProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersViewBlockProductsPage.pageTitle);
      });

      it('should add another product to the list', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addAnotherProductToTheList', baseContext);

        await boOrdersViewBlockProductsPage.searchProduct(page, dataProducts.demo_19.name);

        const textResult = await boOrdersViewBlockProductsPage.addProductToCart(page);
        expect(textResult).to.contains(boOrdersViewBlockProductsPage.successfulAddProductMessage);
      });

      it('should click on \'Carriers\' tab', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'displayCarriersTab', baseContext);

        const isTabOpened = await boOrdersViewBlockTabListPage.goToCarriersTab(page);
        expect(isTabOpened).to.eq(true);
      });

      it('should click on \'Edit\' link and check the modal', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditLink', baseContext);

        const isModalVisible = await boOrdersViewBlockTabListPage.clickOnEditLink(page);
        expect(isModalVisible, 'Edit shipping modal is not visible!').to.eq(true);
      });

      it('should edit the carrier', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editCarrier', baseContext);

        const textResult = await boOrdersViewBlockTabListPage.setShippingDetails(page, shippingDetailsData);
        expect(textResult).to.equal(boOrdersViewBlockTabListPage.successfulUpdateMessage);
      });

      it('should edit the shipping address', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editShippingAddress', baseContext);

        const shippingAddress = await orderPageCustomerBlock.editExistingShippingAddress(page, editShippingAddressData);
        expect(shippingAddress, 'Shipping address is not correct!')
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
        expect(invoiceAddress, 'Invoice address is not correct!')
          .to.contain(editInvoiceAddressData.firstName)
          .and.to.contain(editInvoiceAddressData.lastName)
          .and.to.contain(editInvoiceAddressData.address)
          .and.to.contain(editInvoiceAddressData.postalCode)
          .and.to.contain(editInvoiceAddressData.city)
          .and.to.contain(editInvoiceAddressData.country);
      });

      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage1', baseContext);

        await boDashboardPage.goToSubMenu(page, boDashboardPage.ordersParentLink, boDashboardPage.ordersLink);

        const pageTitle = await boOrdersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersPage.pageTitle);
      });

      it('should click on expand button to preview the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'previewOrder2', baseContext);

        const isPreviewBlockVisible = await boOrdersPage.previewOrder(page);
        expect(isPreviewBlockVisible, 'Preview block is not visible').to.eq(true);
      });

      it('should check the shipping details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkEditedShippingAddress', baseContext);

        const shippingDetails = await boOrdersPage.getShippingDetails(page);
        expect(shippingDetails, 'Shipping address is not correct!')
          .to.equal(`Carrier: ${shippingDetailsData.carrier} Tracking number: ${shippingDetailsData.trackingNumber}`
            + ` Shipping details: ${editShippingAddressData.firstName} ${editShippingAddressData.lastName}`
            + ` ${editShippingAddressData.company} ${editShippingAddressData.address}`
            + ` ${editShippingAddressData.secondAddress} ${editShippingAddressData.postalCode}`
            + ` ${editShippingAddressData.city} ${editShippingAddressData.country}`
            + ` ${editShippingAddressData.phone}`);
      });

      it('should check the edited invoice address details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkEditedInvoiceAddress', baseContext);

        const invoiceAddress = await boOrdersPage.getCustomerInvoiceAddressDetails(page);
        expect(invoiceAddress, 'Invoice address is not correct!')
          .to.equal(`Invoice details: ${editInvoiceAddressData.firstName} ${editInvoiceAddressData.lastName} `
            + `${editInvoiceAddressData.company} ${editInvoiceAddressData.address}`
            + ` ${editInvoiceAddressData.secondAddress} ${editInvoiceAddressData.postalCode}`
            + ` ${editInvoiceAddressData.city} ${editInvoiceAddressData.country} ${editInvoiceAddressData.phone}`);
      });

      it('should check the products number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductsNumber1', baseContext);

        const productsNumber = await boOrdersPage.getProductsNumberFromTable(page);
        expect(productsNumber, 'Products number is not correct!').to.equal(12);
      });

      describe('Check the products list', async () => {
        [
          {args: {product: dataProducts.demo_1, productPrice: dataProducts.demo_1.finalPrice}},
          {args: {product: dataProducts.demo_3, productPrice: dataProducts.demo_3.finalPrice}},
          {args: {product: dataProducts.demo_5, productPrice: dataProducts.demo_5.price}},
          {args: {product: dataProducts.demo_6, productPrice: dataProducts.demo_6.combinations[0].price}},
          {args: {product: dataProducts.demo_7, productPrice: dataProducts.demo_7.price}},
          {args: {product: dataProducts.demo_8, productPrice: dataProducts.demo_8.price}},
          {args: {product: dataProducts.demo_11, productPrice: dataProducts.demo_11.finalPrice}},
          {args: {product: dataProducts.demo_12, productPrice: dataProducts.demo_12.price}},
          {args: {product: dataProducts.demo_13, productPrice: dataProducts.demo_13.price}},
          {args: {product: dataProducts.demo_14, productPrice: dataProducts.demo_14.price}},
        ].forEach((test, index: number) => {
          it(`should check the product '${test.args.product.name}'`, async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkProduct${index}1`, baseContext);

            const productInformation = await boOrdersPage.getProductDetailsFromTable(page, index + 1);
            expect(productInformation).to.contains(test.args.product.name)
              .and.to.contains(test.args.product.reference)
              .and.to.contains(1)
              .and.to.contains(test.args.productPrice);
          });
        });

        it('should check that the last line in product list contain \'(2 more)\'', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'check2MoreText', baseContext);

          const lastProductsTableLine = await boOrdersPage.getProductDetailsFromTable(page, 13);
          expect(lastProductsTableLine).to.equal('more_horiz (2 more)');
        });

        it('should click on \'(2 more)\' link and check the last product in the list', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'clickOnMoreLink1', baseContext);

          await boOrdersPage.clickOnMoreLink(page, 13);

          let productInformation = await boOrdersPage.getProductDetailsFromTable(page, 11);
          expect(productInformation).to.contains(dataProducts.demo_18.name)
            .and.to.contains(dataProducts.demo_18.reference)
            .and.to.contains(1)
            .and.to.contains(dataProducts.demo_18.finalPrice);
          productInformation = await boOrdersPage.getProductDetailsFromTable(page, 12);
          expect(productInformation).to.contains(dataProducts.demo_19.name)
            .and.to.contains(dataProducts.demo_19.reference)
            .and.to.contains(1)
            .and.to.contains(dataProducts.demo_19.finalPrice);
        });
      });
    });
  });

  // Post-condition: Delete guest account
  deleteCustomerTest(customerData, baseContext);
});
