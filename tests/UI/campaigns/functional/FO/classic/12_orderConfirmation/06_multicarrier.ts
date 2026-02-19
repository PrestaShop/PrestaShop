// Import utils
import testContext from '@utils/testContext';

// Import common tests
import setFeatureFlag from '@commonTests/BO/advancedParameters/newFeatures';
import {deleteProductTest} from '@commonTests/BO/catalog/product';
import {disableTheme, enableTheme} from '@commonTests/BO/design/hummingbird';

import {
  dataCustomers,
  dataAddresses,
  dataPaymentMethods,
  dataOrderStatuses,
  dataZones,
  FakerProduct,
  FakerOrder,
  FakerCarrier,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicHomePage,
  foClassicSearchResultsPage,
  foClassicLoginPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicModalQuickViewPage,
  foClassicModalBlockCartPage,
  foClassicMyAddressesPage,
  foClassicMyAccountPage,
  foClassicMyOrderHistoryPage,
  foClassicMyOrderDetailsPage,
  boOrdersPage,
  boOrdersViewBlockTabListPage,
  boFeatureFlagPage,
  boLoginPage,
  boDashboardPage,
  boCarriersPage,
  boCarriersCreatePage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabShippingPage,
  boOrderSettingsPage,
  type Page,
  type BrowserContext,
  utilsPlaywright,
  utilsFile,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_FO_classic_orderConfirmation_multicarrier';

/*
Pre-condition:
- Enable the theme classic
- Enable multiCarrier
- Create 2 carriers
- Create 3 product
- Enable Classic
- Enable final summary
Scenario:
- Add the created products to the cart
- Check products and shipment data in delivery step
- Check products and shipment data in payment step
- Check products and shipment data in payment confirmation page
- Check products and shipment data in order details page
- Add tracking number in BO then check it in FO
Post-condition:
- Disable the theme classic
- Disable multiCarrier
- Delete products
- Delete carriers
- Disable Classic
- Disable final summary
 */

describe('FO - Checkout - Shipping methods : MultiCarrier', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let secondCarrierId: string = '1';

  const firstCarrierData: FakerCarrier = new FakerCarrier({
    // General settings
    name: 'Carrier for product 1',
    speedGrade: 7,
    // Shipping locations and cost
    handlingCosts: false,
    freeShipping: true,
    ranges: [
      {
        weightMin: 0,
        weightMax: 5,
        zones: [
          {
            zone: dataZones.europe,
            price: 5,
          },
          {
            zone: dataZones.northAmerica,
            price: 2,
          },
        ],
      },
    ],
    // Size weight and group access
    maxWidth: 200,
    maxHeight: 200,
    maxDepth: 200,
    maxWeight: 500,
    enable: true,
  });
  const secondCarrierData: FakerCarrier = new FakerCarrier({
    // General settings
    name: 'Carrier for product 2 and 3',
    speedGrade: 7,
    // Shipping locations and cost
    handlingCosts: false,
    freeShipping: true,
    ranges: [
      {
        weightMin: 0,
        weightMax: 5,
        zones: [
          {
            zone: dataZones.europe,
            price: 5,
          },
          {
            zone: dataZones.northAmerica,
            price: 2,
          },
        ],
      },
    ],
    // Size weight and group access
    maxWidth: 200,
    maxHeight: 200,
    maxDepth: 200,
    maxWeight: 500,
    enable: true,
  });

  const firstProductData: FakerProduct = new FakerProduct({
    name: 'First product',
    price: 10.95,
    taxRule: 'No tax',
    quantity: 20,
    type: 'standard',
    status: true,
    packageDimensionWeight: 2,
  });
  const secondProductData: FakerProduct = new FakerProduct({
    name: 'Second product',
    price: 15.55,
    taxRule: 'No tax',
    quantity: 20,
    type: 'standard',
    status: true,
    packageDimensionWeight: 2,
  });
  const thirdProductData: FakerProduct = new FakerProduct({
    name: 'Third product',
    price: 30.55,
    taxRule: 'No tax',
    quantity: 20,
    type: 'standard',
    status: true,
    packageDimensionWeight: 2,
  });
  const productVData: FakerProduct = new FakerProduct({
    name: 'Virtual product',
    price: 10,
    taxRule: 'No tax',
    quantity: 20,
    type: 'virtual',
    status: true,
  });

  const orderData: FakerOrder = new FakerOrder({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: firstProductData,
        quantity: 3,
      },
      {
        product: secondProductData,
        quantity: 4,
      },
      {
        product: thirdProductData,
        quantity: 2,
      },
      {
        product: productVData,
        quantity: 2,
      },
    ],
    deliveryAddress: dataAddresses.address_2,
    invoiceAddress: dataAddresses.address_2,
    deliveryOption: {
      name: `${firstCarrierData.name} - ${firstCarrierData.transitName}`,
      freeShipping: true,
    },
    paymentMethod: dataPaymentMethods.cashOnDelivery,
    status: dataOrderStatuses.paymentAccepted,
  });

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Create images
    await Promise.all([
      utilsFile.generateImage(`${firstCarrierData.name}.jpg`),
      utilsFile.generateImage(`${secondCarrierData.name}.jpg`),
    ]);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    /* Delete the generated images */
    await Promise.all([
      utilsFile.deleteFile(`${firstCarrierData.name}.jpg`),
      utilsFile.deleteFile(`${secondCarrierData.name}.jpg`),
    ]);
  });

  // 1 - Pre-condition : Enable the theme classic
  enableTheme('classic', `${baseContext}_preTest_0`);

  // 2 - Pre-condition: Enable improved_shipment
  setFeatureFlag(boFeatureFlagPage.featureFlagImprovedShipment, true, `${baseContext}_preTest_1`);

  // 3 - Pre-condition: Create 2 carriers
  describe('Pre-condition: Create 2 carriers', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Shipping > Carriers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shippingLink,
        boDashboardPage.carriersLink,
      );

      const pageTitle = await boCarriersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCarriersPage.pageTitle);
    });

    it('should go to add new carrier page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddCarrierPage', baseContext);

      await boCarriersPage.goToAddNewCarrierPage(page);

      const pageTitle = await boCarriersCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCarriersCreatePage.pageTitleCreate);
    });

    it('should create the first carrier and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCarrier', baseContext);

      const textResult = await boCarriersCreatePage.createEditCarrier(page, firstCarrierData);
      expect(textResult).to.contains(boCarriersPage.successfulCreationMessage);
    });

    it('should go to \'Shipping > Carriers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shippingLink,
        boDashboardPage.carriersLink,
      );

      const pageTitle = await boCarriersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCarriersPage.pageTitle);
    });

    it('should go to add new carrier page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddCarrierPage2', baseContext);

      await boCarriersPage.goToAddNewCarrierPage(page);

      const pageTitle = await boCarriersCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCarriersCreatePage.pageTitleCreate);
    });

    it('should create the second carrier and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCarrier2', baseContext);

      const textResult = await boCarriersCreatePage.createEditCarrier(page, secondCarrierData);
      expect(textResult).to.contains(boCarriersPage.successfulCreationMessage);
    });
  });

  // 4 - Pre-condition: Create 4 products
  describe('Pre-condition: Create 4 products', async () => {
    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.productsLink,
      );

      await boProductsPage.closeSfToolBar(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.equals(true);
    });

    it('should choose \'Standard product\' and go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await boProductsPage.selectProductType(page, firstProductData.type);
      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      const createProductMessage = await boProductsCreatePage.setProduct(page, firstProductData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should go to shipping tab and set package dimension', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editPackageDimension', baseContext);

      await boProductsCreateTabShippingPage.setPackageDimension(page, firstProductData);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should select the first created carrier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectFirstCarrier', baseContext);

      await boProductsCreateTabShippingPage.selectAvailableCarrier(page, 5);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should duplicate the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'duplicateProduct', baseContext);

      const textMessage = await boProductsCreatePage.duplicateProduct(page);
      expect(textMessage).to.equal(boProductsCreatePage.successfulDuplicateMessage);
    });

    it('should edit the duplicated product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editSecondProduct', baseContext);

      const createProductMessage = await boProductsCreatePage.setProduct(page, secondProductData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should go to shipping tab and edit package dimension', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editPackageDimension2', baseContext);

      await boProductsCreateTabShippingPage.setPackageDimension(page, secondProductData);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should select the second created carrier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectSecondCarrier2', baseContext);

      await boProductsCreateTabShippingPage.clearChoiceCarrier(page);
      await boProductsCreateTabShippingPage.selectAvailableCarrier(page, 6);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should duplicate the second product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'duplicateProduct2', baseContext);

      const textMessage = await boProductsCreatePage.duplicateProduct(page);
      expect(textMessage).to.equal(boProductsCreatePage.successfulDuplicateMessage);
    });

    it('should edit the second duplicated product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editThirdProduct', baseContext);

      const createProductMessage = await boProductsCreatePage.setProduct(page, thirdProductData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should select the first and second created carrier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectSecondCarrier', baseContext);

      await boProductsCreateTabShippingPage.setPackageDimension(page, secondProductData);
      await boProductsCreateTabShippingPage.selectAvailableCarrier(page, 5);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should click on new product button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'duplicateProduct3', baseContext);

      await boProductsCreatePage.clickOnNewProductButton(page);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should edit the third duplicated product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editFourthProduct', baseContext);

      await boProductsCreatePage.chooseProductType(page, productVData.type);

      const createProductMessage = await boProductsCreatePage.setProduct(page, productVData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });
  });

  // 5 - Enable final summary
  describe('Pre-condition: Enable final summary', async () => {
    it('should go to \'Shop Parameters > Order Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderSettingsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.orderSettingsLink,
      );
      await boOrderSettingsPage.closeSfToolBar(page);

      const pageTitle = await boOrderSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrderSettingsPage.pageTitle);
    });

    it('should enable final summary', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableFinalSummary', baseContext);

      const result = await boOrderSettingsPage.setFinalSummaryStatus(page, true);
      expect(result).to.contains(boOrderSettingsPage.successfulUpdateMessage);
    });
  });

  // 6 - Get created carriers ID
  describe('Pre-condition: Get created carriers id', async () => {
    it('should go to \'Shipping> Carriers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPage3', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shippingLink,
        boDashboardPage.carriersLink,
      );

      const pageTitle = await boCarriersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCarriersPage.pageTitle);
    });

    it('should filter by the second created carrier name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterSecondCarrier', baseContext);

      await boCarriersPage.filterTable(page, 'input', 'name', secondCarrierData.name);
      secondCarrierId = await boCarriersPage.getTextColumn(page, 1, 'id_carrier');
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numberOfCarriersAfterReset = await boCarriersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCarriersAfterReset).to.not.equal(4);
    });
  });

  // Steps
  describe('Add the created products to the cart', async () => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      await foClassicHomePage.goToFo(page);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
    });

    it('should sign in with customer credentials', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foClassicLoginPage.customerLogin(page, orderData.customer);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    [
      {orderToMake: orderData.products[0]},
      {orderToMake: orderData.products[1]},
      {orderToMake: orderData.products[2]},
      {orderToMake: orderData.products[3]},
    ].forEach((test, index) => {
      it(`should search for the product '${test.orderToMake.product.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

        // Go to home page
        await foClassicLoginPage.goToHomePage(page);
        await foClassicHomePage.searchProduct(page, test.orderToMake.product.name);
      });

      it('should quick view the product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `quickView${index}`, baseContext);

        await foClassicSearchResultsPage.quickViewProduct(page, 1);

        const isModalVisible = await foClassicModalQuickViewPage.isQuickViewProductModalVisible(page);
        expect(isModalVisible).to.eq(true);
      });

      it('should set quantity and add to cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setQuantityAndAddToCart${index}`, baseContext);

        await foClassicModalQuickViewPage.setQuantityAndAddToCart(page, test.orderToMake.quantity);

        const isQuickViewModalClosed = await foClassicModalBlockCartPage.closeBlockCartModal(page);
        expect(isQuickViewModalClosed).to.eq(true);
      });
    });

    it('should check the cart notifications number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsNumber', baseContext);

      const notificationsNumber = await foClassicHomePage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.equal(11);
    });
  });

  describe('Check products and shipment data in delivery step', async () => {
    it('should go to the shopping cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartPage', baseContext);

      await foClassicMyAddressesPage.goToCartPage(page);

      const pageTitle = await foClassicCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicCartPage.pageTitle);
    });

    it('should proceed to checkout and go to checkout page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckout', baseContext);

      await foClassicCartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.eq(true);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Address step - Go to delivery step
      const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.equal(true);
    });

    it('should check the carrier data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstCarrierData', baseContext);

      const carrierData = await foClassicCheckoutPage.getCarrierData(page, parseInt(secondCarrierId, 10));
      await Promise.all([
        expect(carrierData.name).to.equal(`${firstCarrierData.name}, ${secondCarrierData.name}`),
        expect(carrierData.transitName).to.equal(`${firstCarrierData.transitName}, ${secondCarrierData.transitName}`),
        expect(carrierData.priceText).to.equal('Free'),
      ]);
    });
  });

  describe('Check products and shipment data in payment step', async () => {
    it('should go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should check the first product in the list and the delivery option', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstProduct', baseContext);

      const firstProduct = await foClassicCheckoutPage.getOrderConfirmationProduct(page, 1);
      expect(firstProduct).to.equal(firstProductData.name);

      const firstCarrier = await foClassicCheckoutPage.getOrderConfirmationCarrierInfo(page, 1);
      expect(firstCarrier).to.equal(`Carrier: ${firstCarrierData.name}`);
    });

    it('should check the second product in the list and the delivery option', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSecondProduct', baseContext);

      const firstCarrier = await foClassicCheckoutPage.getOrderConfirmationCarrierInfo(page, 2);
      expect(firstCarrier).to.equal(`Carrier: ${firstCarrierData.name}`);

      const secondProduct = await foClassicCheckoutPage.getOrderConfirmationProduct(page, 2);
      expect(secondProduct).to.contain(thirdProductData.name);
    });

    it('should check the third product in the list and the delivery option', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThirdProduct', baseContext);

      const secondCarrier = await foClassicCheckoutPage.getOrderConfirmationCarrierInfo(page, 3);
      expect(secondCarrier).to.contains(`Carrier: ${secondCarrierData.name}`);

      const thirdProduct = await foClassicCheckoutPage.getOrderConfirmationProduct(page, 3);
      expect(thirdProduct).to.contain(secondProductData.name);
    });

    it('should check that the virtual product has no delivery service', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkVirtualProductNoDeliveryService', baseContext);

      const deliveryOption = await foClassicCheckoutPage.getOrderConfirmationVirtualInfo(page, 4);
      expect(deliveryOption).to.contain('Virtual product, no delivery service.');

      const virtualProduct = await foClassicCheckoutPage.getOrderConfirmationProduct(page, 4);
      expect(virtualProduct).to.contain(productVData.name);
    });
  });

  describe('Check products and shipment data in payment confirmation page', async () => {
    it('should choose payment method and confirm the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      // Payment step - Choose payment step
      await foClassicCheckoutPage.choosePaymentAndOrder(page, orderData.paymentMethod.moduleName);

      // Check the confirmation message
      const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should check the first product in the list and the delivery option', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstProduct2', baseContext);

      const firstProduct = await foClassicCheckoutPage.getOrderConfirmationProduct(page, 1);
      expect(firstProduct).to.equal(firstProductData.name);

      const firstCarrier = await foClassicCheckoutPage.getOrderConfirmationCarrierInfo(page, 1);
      expect(firstCarrier).to.equal(`Carrier: ${firstCarrierData.name}`);
    });

    it('should check the second product in the list and the delivery option', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSecondProduct2', baseContext);

      const firstCarrier = await foClassicCheckoutPage.getOrderConfirmationCarrierInfo(page, 2);
      expect(firstCarrier).to.equal(`Carrier: ${firstCarrierData.name}`);

      const secondProduct = await foClassicCheckoutPage.getOrderConfirmationProduct(page, 2);
      expect(secondProduct).to.contain(thirdProductData.name);
    });

    it('should check the third product in the list and the delivery option', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThirdProduct2', baseContext);

      const secondCarrier = await foClassicCheckoutPage.getOrderConfirmationCarrierInfo(page, 3);
      expect(secondCarrier).to.contains(`Carrier: ${secondCarrierData.name}`);

      const thirdProduct = await foClassicCheckoutPage.getOrderConfirmationProduct(page, 3);
      expect(thirdProduct).to.contain(secondProductData.name);
    });

    it('should check that the virtual product has no delivery service', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkVirtualProductNoDeliveryService2', baseContext);

      const deliveryOption = await foClassicCheckoutPage.getOrderConfirmationVirtualInfo(page, 4);
      expect(deliveryOption).to.contain('Virtual product, no delivery service.');

      const virtualProduct = await foClassicCheckoutPage.getOrderConfirmationProduct(page, 4);
      expect(virtualProduct).to.contain(productVData.name);
    });
  });

  describe('Check products and shipment data in order details page', async () => {
    it('should go to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await foClassicHomePage.goToMyAccountPage(page);
      await foClassicMyAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle = await foClassicMyOrderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foClassicMyOrderHistoryPage.pageTitle);
    });

    it('should go to order details page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrderDetails', baseContext);

      await foClassicMyOrderHistoryPage.goToDetailsPage(page);

      const pageTitle = await foClassicMyOrderDetailsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicMyOrderDetailsPage.pageTitle);
    });

    it('should check the carriers table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersTable', baseContext);

      let carrier = await foClassicMyOrderDetailsPage.getCarrierDataFromTable(page, 1, '2');
      expect(carrier).to.equal(firstCarrierData.name);

      carrier = await foClassicMyOrderDetailsPage.getCarrierDataFromTable(page, 2, '2');
      expect(carrier).to.equal(secondCarrierData.name);
    });

    it('should check product details table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductDetails', baseContext);

      let productName = await foClassicMyOrderDetailsPage.getProductName(page, 1, 1);
      expect(productName).to.contain(firstProductData.name);

      productName = await foClassicMyOrderDetailsPage.getProductName(page, 2, 1);
      expect(productName).to.contain(thirdProductData.name);

      productName = await foClassicMyOrderDetailsPage.getProductName(page, 3, 1);
      expect(productName).to.contain(secondProductData.name);

      productName = await foClassicMyOrderDetailsPage.getProductName(page, 4, 1);
      expect(productName).to.contain(productVData.name);
    });
  });

  describe('Add tracking number in BO then check it in FO', async () => {
    it('should go to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );
      await boOrdersPage.closeSfToolBar(page);

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPage', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
    });

    it('should click on the tab \'Shipments\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShipmentsTab', baseContext);

      const isTabOpened = await boOrdersViewBlockTabListPage.goToShipmentsTab(page);
      expect(isTabOpened).to.equal(true);
    });

    it('should click on edit shipment link of the first carrier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditShipmentLink', baseContext);

      const isEditModalVisible = await boOrdersViewBlockTabListPage.clickOnEditShipmentLink(page, 1);
      expect(isEditModalVisible).to.equal(true);
    });

    it('should add a tracking number and save', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addTrackingNumber', baseContext);

      const isModalNotVisible = await boOrdersViewBlockTabListPage.editShipment(page, 'TN12345678', firstCarrierData.name);
      expect(isModalNotVisible).to.equal(true);
    });

    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO2', baseContext);

      await foClassicHomePage.goToFo(page);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage2', baseContext);

      await foClassicHomePage.goToMyAccountPage(page);
      await foClassicMyAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle = await foClassicMyOrderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foClassicMyOrderHistoryPage.pageTitle);
    });

    it('should go to order details page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrderDetails2', baseContext);

      await foClassicMyOrderHistoryPage.goToDetailsPage(page);

      const pageTitle = await foClassicMyOrderDetailsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicMyOrderDetailsPage.pageTitle);
    });

    it('should check the carriers table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersTable2', baseContext);

      const trackingNumber = await foClassicMyOrderDetailsPage.getCarrierDataFromTable(page, 1, '4');
      expect(trackingNumber).to.equal('TN12345678');
    });
  });

  // 1 - Post-condition: Disable improved_shipment
  setFeatureFlag(boFeatureFlagPage.featureFlagImprovedShipment, false, `${baseContext}_postTest_1`);

  // 2 - Post-condition: Delete created products
  [
    firstProductData,
    secondProductData,
    thirdProductData,
    productVData,
  ].forEach((product, index) => {
    deleteProductTest(product, `${baseContext}_postTest_${index + 2}`);
  });

  // 3 - Post-condition: Delete created carriers
  describe('Post-condition: Delete created carriers', async () => {
    it('should go to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Shipping > Carriers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPage4', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shippingLink,
        boDashboardPage.carriersLink,
      );

      const pageTitle = await boCarriersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCarriersPage.pageTitle);
    });
    [
      {carrier: firstCarrierData},
      {carrier: secondCarrierData},
    ].forEach((test, index) => {
      it('should filter list by name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterCarriersForDelete${index}`, baseContext);

        await boCarriersPage.resetFilter(page);
        await boCarriersPage.filterTable(page, 'input', 'name', test.carrier.name);

        const carrierName = await boCarriersPage.getTextColumn(page, 1, 'name');
        expect(carrierName).to.contains(test.carrier.name);
      });

      it('should delete carrier', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteCarrier${index}`, baseContext);

        const textResult = await boCarriersPage.deleteCarrier(page, 1);
        expect(textResult).to.contains(boCarriersPage.successfulDeleteMessage);
        await boCarriersPage.resetFilter(page);
      });
    });
  });

  // 4 - Post-condition: Disable final summary
  describe('Post-condition: Disable final summary', async () => {
    it('should go to \'Shop Parameters > Order Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderSettingsPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.orderSettingsLink,
      );
      await boOrderSettingsPage.closeSfToolBar(page);

      const pageTitle = await boOrderSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrderSettingsPage.pageTitle);
    });

    it('should disable final summary', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableFinalSummary', baseContext);

      const result = await boOrderSettingsPage.setFinalSummaryStatus(page, true);
      expect(result).to.contains(boOrderSettingsPage.successfulUpdateMessage);
    });
  });

  // 5 - Post-condition : Disable the theme classic
  disableTheme('classic', `${baseContext}_postTest_6`);
});
