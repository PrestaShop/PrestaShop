import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import common tests
import {deleteProductTest} from '@commonTests/BO/catalog/product';
import setFeatureFlag from '@commonTests/BO/advancedParameters/newFeatures';

import {
  // BO pages
  boDashboardPage,
  boLoginPage,
  boCarriersPage,
  boCarriersCreatePage,
  boOrdersPage,
  boOrdersCreatePage,
  boFeatureFlagPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabShippingPage,
  boOrdersViewBlockProductsPage,
  boOrdersViewBlockTabListPage,
  // FO pages
  foHummingbirdHomePage,
  foHummingbirdMyAccountPage,
  foHummingbirdMyOrderHistoryPage,
  foHummingbirdMyOrderDetailsPage,
  foHummingbirdLoginPage,
  // Data
  dataCustomers,
  dataOrderStatuses,
  dataZones,
  dataAddresses,
  dataPaymentMethods,
  FakerCarrier,
  FakerProduct,
  FakerOrder,
  // Utils
  type BrowserContext,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_orders_orders_createOrders_multiCarrier';

/*
Pre-condition:
- Enable multiCarrier
- Create 2 carriers
- Create 3 product
Scenario:
- Create simple order with the created product
- Check and update shipments tab( Split/Merge, Add tracking number)
Post-condition:
- Disable multiCarrier
- Delete products
- Delete carriers
 */
describe('BO - Orders - Create order : Multi Carrier', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let secondShipmentNumber: number = 0;

  const firstCarrierData: FakerCarrier = new FakerCarrier({
    // General settings
    name: 'Voiture',
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
    name: 'Fourgonnette',
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
    name: 'Assiette',
    price: 10.95,
    taxRule: 'No tax',
    quantity: 20,
    type: 'standard',
    status: true,
    packageDimensionWeight: 2,
  });
  const secondProductData: FakerProduct = new FakerProduct({
    name: 'Chaise',
    price: 15.55,
    taxRule: 'No tax',
    quantity: 20,
    type: 'standard',
    status: true,
    packageDimensionWeight: 2,
  });
  const thirdProductData: FakerProduct = new FakerProduct({
    name: 'Table',
    price: 30.55,
    taxRule: 'No tax',
    quantity: 20,
    type: 'standard',
    status: true,
    packageDimensionWeight: 2,
  });

  const orderToMake: FakerOrder = new FakerOrder({
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

  // 1 - Pre-condition: Enable improved_shipment
  setFeatureFlag(boFeatureFlagPage.featureFlagImprovedShipment, true, `${baseContext}_preTest`);

  // 2 - Pre-condition: Create 2 carriers
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

  // 3 - Pre-condition: Create 3 products
  describe('Pre-condition: Create 3 products', async () => {
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

    describe(`Create the first product: ${firstProductData.name}`, async () => {
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
    });

    describe(`Create the second product: ${secondProductData.name}`, async () => {
      it('should create the second product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'duplicateProduct', baseContext);

        await boProductsCreatePage.clickOnNewProductButton(page);
        await boProductsPage.selectProductType(page, secondProductData.type);
        await boProductsPage.clickOnAddNewProduct(page);

        const createProductMessage = await boProductsCreatePage.setProduct(page, secondProductData);
        expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
      });

      it('should go to shipping tab and edit package dimension', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editPackageDimension2', baseContext);

        await boProductsCreateTabShippingPage.setPackageDimension(page, secondProductData);

        const message = await boProductsCreatePage.saveProduct(page);
        expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
      });

      it('should select the first and second created carrier', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'selectFirstSecondCarrier', baseContext);

        await boProductsCreateTabShippingPage.setPackageDimension(page, secondProductData);
        await boProductsCreateTabShippingPage.selectAvailableCarrier(page, 5);
        await boProductsCreateTabShippingPage.selectAvailableCarrier(page, 6);

        const message = await boProductsCreatePage.saveProduct(page);
        expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
      });
    });

    describe(`Create the third product: ${thirdProductData.name}`, async () => {
      it('should create the third product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createThirdProduct', baseContext);

        await boProductsCreatePage.clickOnNewProductButton(page);
        await boProductsPage.selectProductType(page, thirdProductData.type);
        await boProductsPage.clickOnAddNewProduct(page);

        const createProductMessage = await boProductsCreatePage.setProduct(page, thirdProductData);
        expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
      });

      it('should go to shipping tab and edit package dimension', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editPackageDimension3', baseContext);

        await boProductsCreateTabShippingPage.setPackageDimension(page, thirdProductData);

        const message = await boProductsCreatePage.saveProduct(page);
        expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
      });

      it('should select the second created carrier', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'selectSecondCarrier', baseContext);

        await boProductsCreateTabShippingPage.selectAvailableCarrier(page, 6);

        const message = await boProductsCreatePage.saveProduct(page);
        expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
      });
    });
  });

  // 1 - Create simple order with the created product
  describe('Create new order with the created products', async () => {
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

    it('should go to create order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage', baseContext);

      await boOrdersPage.goToCreateOrderPage(page);

      const pageTitle = await boOrdersCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersCreatePage.pageTitle);
    });

    it('should choose the default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseCustomer', baseContext);

      await boOrdersCreatePage.searchCustomer(page, orderToMake.customer.email);

      const isHistoryBlockVisible = await boOrdersCreatePage.chooseCustomer(page, 1);
      expect(isHistoryBlockVisible).to.eq(true);
    });

    it('should add the created products to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductsToCart', baseContext);

      for (let i = 0; i < orderToMake.products.length; i++) {
        await boOrdersCreatePage.addProductToCart(
          page, orderToMake.products[i].product, `${orderToMake.products[i].product.name
          } - €${orderToMake.products[i].product.price}`, orderToMake.products[i].quantity);
      }

      let result = await boOrdersCreatePage.getProductDetailsFromTable(page, 1);
      await Promise.all([
        expect(result.description).to.equal(firstProductData.name),
        expect(result.reference).to.equal(firstProductData.reference),
      ]);
      result = await boOrdersCreatePage.getProductDetailsFromTable(page, 2);
      await Promise.all([
        expect(result.description).to.equal(secondProductData.name),
        expect(result.reference).to.equal(secondProductData.reference),
      ]);
      result = await boOrdersCreatePage.getProductDetailsFromTable(page, 3);
      await Promise.all([
        expect(result.description).to.equal(thirdProductData.name),
        expect(result.reference).to.equal(thirdProductData.reference),
      ]);
    });

    it('should check the list of carriers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkListOfCarriers', baseContext);

      const deliveryOptions = await boOrdersCreatePage.getDeliveryOptions(page);
      expect(deliveryOptions).to
        .equal(`${firstCarrierData.name} - ${firstCarrierData.transitName}${secondCarrierData.name}`
          + ` - ${secondCarrierData.transitName}`);
    });

    it(`should choose the delivery option '${orderToMake.deliveryOption.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseCarrier', baseContext);

      const shippingPriceTTC = await boOrdersCreatePage.setDeliveryOption(
        page, orderToMake.deliveryOption.name, orderToMake.deliveryOption.freeShipping);
      expect(shippingPriceTTC).to.equal('€0.00');
    });

    it('should choose payment method and confirm the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmTheOrder', baseContext);

      await boOrdersCreatePage.setPaymentMethod(page, orderToMake.paymentMethod.moduleName);
      await boOrdersCreatePage.setOrderStatus(page, orderToMake.status);

      const isOrderCreated = await boOrdersCreatePage.clickOnCreateOrderButton(page, true);
      expect(isOrderCreated, 'The order is created!').to.eq(true);
    });

    it('should check the view order page title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPageTitle', baseContext);

      const pageTitle = await boOrdersViewBlockProductsPage.getPageTitle(page);
      expect(pageTitle).to.contain(boOrdersViewBlockProductsPage.pageTitle);
    });
  });

  // 2 - Check and update shipments tab
  describe('Check and update shipments tab', async () => {
    it('should check the 3 tabs', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTabName', baseContext);

      const statusTabName = await boOrdersViewBlockTabListPage.getTabName(page, 1);
      expect(statusTabName).to.contain('Status (1)');

      const documentsTabName = await boOrdersViewBlockTabListPage.getTabName(page, 2);
      expect(documentsTabName).to.contain('Documents (1)');

      const shipmentsTabName = await boOrdersViewBlockTabListPage.getTabName(page, 3);
      expect(shipmentsTabName).to.contain('Shipments (2)');
    });

    it('should click on the tab \'Shipments\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShipmentsTab', baseContext);

      const isTabOpened = await boOrdersViewBlockTabListPage.goToShipmentsTab(page);
      expect(isTabOpened).to.equal(true);
    });

    it('should click on \'Split\' link and check the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnSplitLink', baseContext);

      const isModalVisible = await boOrdersViewBlockTabListPage.clickOnSplitLink(page, 1);
      expect(isModalVisible, 'Split shipping modal is not visible!').to.equal(true);
    });

    it('should choose the first product and check the list of carriers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseFirstProduct', baseContext);

      await boOrdersViewBlockTabListPage.selectProductAndQuantityInSplitShipment(page, 1, 1);

      const listOfCarriers = await boOrdersViewBlockTabListPage.getListOfCarriersInSplitShipment(page);
      expect(listOfCarriers).to.equal(`Select a carrier${firstCarrierData.name}`);
    });

    it('should uncheck the first product and choose the second product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseSecondProduct', baseContext);

      await boOrdersViewBlockTabListPage.unselectProductInSplitShipment(page, 1);
      await boOrdersViewBlockTabListPage.selectProductAndQuantityInSplitShipment(page, 2, 1);

      const listOfCarriers = await boOrdersViewBlockTabListPage.getListOfCarriersInSplitShipment(page);
      expect(listOfCarriers).to.equal(`Select a carrier${secondCarrierData.name}${firstCarrierData.name}`);
    });

    it('should choose the second carrier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseSecondCarrier', baseContext);

      const isSplitButtonDisabled = await boOrdersViewBlockTabListPage.selectCarrierInSplitShipment(page, secondCarrierData.name);
      expect(isSplitButtonDisabled).to.equal(1);
    });

    it('should click on split shipment button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnSplitButton', baseContext);

      const isModalNotVisible = await boOrdersViewBlockTabListPage.clickOnSplitShipmentButton(page);
      expect(isModalNotVisible).to.equal(true);
    });

    it('should check the number of lines in shipments tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfLinesInShipmentsTab', baseContext);

      const isTabOpened = await boOrdersViewBlockTabListPage.goToShipmentsTab(page);
      expect(isTabOpened).to.equal(true);

      const shipmentsTabName = await boOrdersViewBlockTabListPage.getTabName(page, 3);
      expect(shipmentsTabName).to.contain('Shipments (3)');
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      page = await boOrdersViewBlockTabListPage.viewMyShop(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await foHummingbirdHomePage.goToLoginPage(page);

      const pageTitle = await foHummingbirdLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foHummingbirdLoginPage.pageTitle);
    });

    it('should sign in with customer credentials', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foHummingbirdLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foHummingbirdLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to Your account > Order history and details page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage2', baseContext);

      await foHummingbirdHomePage.goToMyAccountPage(page);
      await foHummingbirdMyAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle = await foHummingbirdMyOrderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdMyOrderHistoryPage.pageTitle);
    });

    it('should go to order details page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrderDetails2', baseContext);

      await foHummingbirdMyOrderHistoryPage.goToDetailsPage(page);

      const pageTitle = await foHummingbirdMyOrderDetailsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdMyOrderDetailsPage.pageTitle);
    });

    it('should check the number of carriers from Shipment tracking details table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfCarriers', baseContext);

      const numberOfCarriers = await foHummingbirdMyOrderDetailsPage.getNumberOfCarriersFromShipmentDetailsTable(page);
      expect(numberOfCarriers).to.equal(3);
    });

    it('should check the Shipment tracking details table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersTable', baseContext);

      let carrier = await foHummingbirdMyOrderDetailsPage.getCarrierDataFromTable(page, 1);
      expect(carrier).to.equal(firstCarrierData.name);

      carrier = await foHummingbirdMyOrderDetailsPage.getCarrierDataFromTable(page, 2);
      expect(carrier).to.equal(secondCarrierData.name);

      carrier = await foHummingbirdMyOrderDetailsPage.getCarrierDataFromTable(page, 3);
      expect(carrier).to.equal(secondCarrierData.name);
    });

    it('should check the number of products from Product details table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts', baseContext);

      const numberOfProducts = await foHummingbirdMyOrderDetailsPage.getNumberOfRowsFromProductDetailsTable(page);
      expect(numberOfProducts).to.equal(4);
    });

    it('should check product details table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductDetails', baseContext);

      let productName = await foHummingbirdMyOrderDetailsPage.getOrderProductColumn(page, 1);
      expect(productName).to.contain(firstProductData.name)
        .and.to.contain(firstCarrierData.name);
      let productQuantity = await foHummingbirdMyOrderDetailsPage.getProductQuantity(page, 1);
      expect(productQuantity).to.equal(3);

      productName = await foHummingbirdMyOrderDetailsPage.getOrderProductColumn(page, 2);
      expect(productName).to.contain(secondProductData.name)
        .and.to.contain(firstCarrierData.name);
      productQuantity = await foHummingbirdMyOrderDetailsPage.getProductQuantity(page, 2);
      expect(productQuantity).to.equal(3);

      productName = await foHummingbirdMyOrderDetailsPage.getOrderProductColumn(page, 3);
      expect(productName).to.contain(thirdProductData.name)
        .and.to.contain(secondCarrierData.name);
      productQuantity = await foHummingbirdMyOrderDetailsPage.getProductQuantity(page, 3);
      expect(productQuantity).to.equal(2);

      productName = await foHummingbirdMyOrderDetailsPage.getOrderProductColumn(page, 4);
      expect(productName).to.contain(secondProductData.name)
        .and.to.contain(secondCarrierData.name);
      productQuantity = await foHummingbirdMyOrderDetailsPage.getProductQuantity(page, 4);
      expect(productQuantity).to.equal(1);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

      page = await foHummingbirdMyOrderDetailsPage.changePage(browserContext, 0);

      const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
    });

    it('should click on merge the third carrier line', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnMergeCarrier', baseContext);

      secondShipmentNumber = await boOrdersViewBlockTabListPage.getShipmentNumber(page, 2);

      const isModalVisible = await boOrdersViewBlockTabListPage.clickOnMergeLink(page, 3);
      expect(isModalVisible, 'Merge shipping modal is not visible!').to.equal(true);
    });

    it('should choose the first product and the second carrier in the list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseProductAnCarrier', baseContext);

      await boOrdersViewBlockTabListPage.selectProductInMergeShipment(page, 1);

      const isButtonNotDisabled = await boOrdersViewBlockTabListPage
        .selectCarrierInMergeShipment(page, `Shipment ${secondShipmentNumber} - carrier ${secondCarrierData.name}`);
      expect(isButtonNotDisabled).to.equal(true);
    });

    it('should click on merge shipment button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnMergeShipmentButton', baseContext);

      const isModalNotVisible = await boOrdersViewBlockTabListPage.clickOnMergeShipmentButton(page);
      expect(isModalNotVisible).to.equal(true);
    });

    it('should check the number of lines in shipments tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfLinesInShipmentsTab2', baseContext);

      const isTabOpened = await boOrdersViewBlockTabListPage.goToShipmentsTab(page);
      expect(isTabOpened).to.equal(true);

      const shipmentsTabName = await boOrdersViewBlockTabListPage.getTabName(page, 3);
      expect(shipmentsTabName).to.contain('Shipments (2)');
    });

    it('should go back to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToFO', baseContext);

      page = await boOrdersViewBlockTabListPage.changePage(browserContext, 1);
      await foHummingbirdMyOrderHistoryPage.reloadPage(page);

      const pageTitle = await foHummingbirdMyOrderDetailsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdMyOrderDetailsPage.pageTitle);
    });

    it('should check the number of carriers from Shipment tracking details table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfCarriers_2', baseContext);

      const numberOfCarriers = await foHummingbirdMyOrderDetailsPage.getNumberOfCarriersFromShipmentDetailsTable(page);
      expect(numberOfCarriers).to.equal(2);
    });

    it('should check the Shipment tracking details table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersTable_2', baseContext);

      let carrier = await foHummingbirdMyOrderDetailsPage.getCarrierDataFromTable(page, 1);
      expect(carrier).to.equal(firstCarrierData.name);

      carrier = await foHummingbirdMyOrderDetailsPage.getCarrierDataFromTable(page, 2);
      expect(carrier).to.equal(secondCarrierData.name);
    });

    it('should check the number of products in Product details table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts_2', baseContext);

      const numberOfProducts = await foHummingbirdMyOrderDetailsPage.getNumberOfRowsFromProductDetailsTable(page);
      expect(numberOfProducts).to.equal(4);
    });

    it('should check product details table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductDetails_2', baseContext);

      let productName = await foHummingbirdMyOrderDetailsPage.getOrderProductColumn(page, 1);
      expect(productName).to.contain(firstProductData.name)
        .and.to.contain(firstCarrierData.name);
      let productQuantity = await foHummingbirdMyOrderDetailsPage.getProductQuantity(page, 1);
      expect(productQuantity).to.equal(3);

      productName = await foHummingbirdMyOrderDetailsPage.getOrderProductColumn(page, 2);
      expect(productName).to.contain(secondProductData.name)
        .and.to.contain(firstCarrierData.name);
      productQuantity = await foHummingbirdMyOrderDetailsPage.getProductQuantity(page, 2);
      expect(productQuantity).to.equal(3);

      productName = await foHummingbirdMyOrderDetailsPage.getOrderProductColumn(page, 3);
      expect(productName).to.contain(thirdProductData.name)
        .and.to.contain(secondCarrierData.name);
      productQuantity = await foHummingbirdMyOrderDetailsPage.getProductQuantity(page, 3);
      expect(productQuantity).to.equal(2);

      productName = await foHummingbirdMyOrderDetailsPage.getOrderProductColumn(page, 4);
      expect(productName).to.contain(secondProductData.name)
        .and.to.contain(secondCarrierData.name);
      productQuantity = await foHummingbirdMyOrderDetailsPage.getProductQuantity(page, 4);
      expect(productQuantity).to.equal(1);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo_2', baseContext);

      page = await foHummingbirdMyOrderDetailsPage.changePage(browserContext, 0);

      const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
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

    it('should check that the tracking number is visible in the shipment table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTrackingNumber', baseContext);

      const isTabOpened = await boOrdersViewBlockTabListPage.goToShipmentsTab(page);
      expect(isTabOpened).to.equal(true);

      const trackingNumber = await boOrdersViewBlockTabListPage.getTrackingNumber(page, 1);
      expect(trackingNumber).to.equal('TN12345678');
    });

    it('should click on split link of the first shipment', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnSplitLink2', baseContext);

      const isModalVisible = await boOrdersViewBlockTabListPage.clickOnSplitLink(page, 1);
      expect(isModalVisible, 'Split shipping modal is not visible!').to.equal(true);
    });

    it('should check the alert mesage in the split modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAlertText', baseContext);

      const alertText = await boOrdersViewBlockTabListPage.getAlertTextFromSplitModal(page);
      expect(alertText).to.equal(boOrdersViewBlockTabListPage.alertTextInSplitModal);
    });

    it('should select a product and a carrier and check that the Split shipment button is disabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkButtonStillDisabled', baseContext);

      await boOrdersViewBlockTabListPage.selectProductAndQuantityInSplitShipment(page, 2, 1);

      const isSplitButtonDisabled = await boOrdersViewBlockTabListPage.selectCarrierInSplitShipment(page, secondCarrierData.name);
      expect(isSplitButtonDisabled).to.equal(1);
    });

    it('should close the split modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeSplitModal', baseContext);

      const isSplitModalNotVisible = await boOrdersViewBlockTabListPage.closeSplitModal(page);
      expect(isSplitModalNotVisible).to.equal(true);
    });

    it('should click on merge link of the first shipment', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'mergeFirstShipment', baseContext);

      secondShipmentNumber = await boOrdersViewBlockTabListPage.getShipmentNumber(page, 2);

      const isModalVisible = await boOrdersViewBlockTabListPage.clickOnMergeLink(page, 1);
      expect(isModalVisible, 'Merge shipping modal is not visible!').to.equal(true);
    });

    it('should check the alert mesage in the split modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAlertTextInMergeModal', baseContext);

      const alertText = await boOrdersViewBlockTabListPage.getAlertTextFromMergeModal(page);
      expect(alertText).to.equal(boOrdersViewBlockTabListPage.alertTextInMergeModal);
    });

    it('should select the first product and check that the list of carriers is enabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkListCarriersEnabled', baseContext);

      await boOrdersViewBlockTabListPage.selectProductInMergeShipment(page, 1);

      const isCarrierDisabled = await boOrdersViewBlockTabListPage.checkCarrierStatusInMergeModal(page);
      expect(isCarrierDisabled).to.equal(0);
    });

    it('should check that the Merge shipment button is disabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMergeButtonDisabled', baseContext);

      const isButtonNotDisabled = await boOrdersViewBlockTabListPage
        .selectCarrierInMergeShipment(page, `Shipment ${secondShipmentNumber} - carrier ${secondCarrierData.name}`);
      expect(isButtonNotDisabled).to.equal(false);
    });

    it('should close the merge modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeMergeModal', baseContext);

      const isMergeModalNotVisible = await boOrdersViewBlockTabListPage.closeMergeModal(page);
      expect(isMergeModalNotVisible).to.equal(true);
    });

    it('should click on merge link of the second shipment', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'mergeSecondShipment', baseContext);

      const isModalVisible = await boOrdersViewBlockTabListPage.clickOnMergeLink(page, 2);
      expect(isModalVisible, 'Merge shipping modal is not visible!').to.equal(true);
    });

    it('should select the second product and check that the list of carriers is disbled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkListCarriersDisabled2', baseContext);

      await boOrdersViewBlockTabListPage.selectProductInMergeShipment(page, 1);

      const isCarrierDisabled = await boOrdersViewBlockTabListPage.checkCarrierStatusInMergeModal(page);
      expect(isCarrierDisabled).to.equal(1);
    });

    it('should close the merge modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeMergeModal2', baseContext);

      const isMergeModalNotVisible = await boOrdersViewBlockTabListPage.closeMergeModal(page);
      expect(isMergeModalNotVisible).to.equal(true);
    });
  });

  // 1 - Post-condition: Disable improved_shipment
  setFeatureFlag(boFeatureFlagPage.featureFlagImprovedShipment, false, `${baseContext}_postTest_1`);

  // 2 - Post-condition: Delete created products
  [
    {args: {testIdentifier: 'postTest_2', productData: firstProductData}},
    {args: {testIdentifier: 'postTest_3', productData: secondProductData}},
    {args: {testIdentifier: 'postTest_4', productData: thirdProductData}},
  ].forEach((test) => {
    deleteProductTest(test.args.productData, `${baseContext}${test.args.testIdentifier}`);
  });

  // 3 - Post-condition: Delete created carriers
  describe('Post-condition: Delete created carriers', async () => {
    it('should go to \'Shipping > Carriers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPage3', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shippingLink,
        boDashboardPage.carriersLink,
      );

      const pageTitle = await boCarriersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCarriersPage.pageTitle);
    });
    [
      {args: {carrier: firstCarrierData}},
      {args: {carrier: secondCarrierData}},
    ].forEach((test, index) => {
      it('should filter list by name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterCarriersForDelete${index}`, baseContext);

        await boCarriersPage.resetFilter(page);
        await boCarriersPage.filterTable(page, 'input', 'name', test.args.carrier.name);

        const carrierName = await boCarriersPage.getTextColumn(page, 1, 'name');
        expect(carrierName).to.contains(test.args.carrier.name);
      });

      it('should delete carrier', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteCarrier${index}`, baseContext);

        const textResult = await boCarriersPage.deleteCarrier(page, 1);
        expect(textResult).to.contains(boCarriersPage.successfulDeleteMessage);
        await boCarriersPage.resetFilter(page);
      });
    });
  });
});
