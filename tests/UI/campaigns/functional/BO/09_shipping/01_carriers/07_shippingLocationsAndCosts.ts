// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import createProductsPage from '@pages/BO/catalog/products/add';
import {checkoutPage} from '@pages/FO/classic/checkout';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

// Import data
import {
  boCarriersCreatePage,
  boCarriersPage,
  boDashboardPage,
  boProductsPage,
  dataCarriers,
  dataCustomers,
  dataProducts,
  dataZones,
  FakerCarrier,
  FakerProduct,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicHomePage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';
import shippingTab from '@pages/BO/catalog/products/add/shippingTab';

const baseContext: string = 'functional_BO_shipping_carriers_shippingLocationsAndCosts';

describe('BO - Shipping - Carriers : Shipping locations and costs', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCarriers: number = 0;
  let idCarrier: number;
  const handlingCost: number = 2;

  const carrierData: FakerCarrier = new FakerCarrier({
    // General settings
    name: 'Test',
    speedGrade: 7,
    transitName: '2 days',
    trackingURL: '',
    // Shipping locations and cost
    handlingCosts: false,
    freeShipping: false,
    billing: 'According to total weight',
    taxRule: 'No tax',
    outOfRangeBehavior: 'Apply the cost of the highest defined range',
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
      {
        weightMin: 5,
        weightMax: 10,
        zones: [
          {
            zone: dataZones.europe,
            price: 10,
          },
          {
            zone: dataZones.northAmerica,
            price: 4,
          },
        ],
      },
      {
        weightMin: 10,
        weightMax: 20,
        zones: [
          {
            zone: dataZones.europe,
            price: 20,
          },
          {
            zone: dataZones.northAmerica,
            price: 8,
          },
        ],
      },
    ],
    // Size weight and group access
    maxWidth: 200,
    maxHeight: 200,
    maxDepth: 200,
    maxWeight: 50,
    enable: true,
  });
  const carrierDataFreeShipping: FakerCarrier = new FakerCarrier({
    // General settings
    name: 'Test',
    speedGrade: 7,
    transitName: '2 days',
    trackingURL: '',
    // Shipping locations and cost
    handlingCosts: false,
    freeShipping: true,
    billing: 'According to total weight',
    taxRule: 'No tax',
    outOfRangeBehavior: 'Apply the cost of the highest defined range',
    // Size weight and group access
    maxWidth: 200,
    maxHeight: 200,
    maxDepth: 200,
    maxWeight: 50,
    enable: true,
  });
  const carrierDataHandlingCosts: FakerCarrier = new FakerCarrier({
    // General settings
    name: 'Test',
    speedGrade: 7,
    transitName: '2 days',
    trackingURL: '',
    // Shipping locations and cost
    handlingCosts: true,
    freeShipping: false,
    billing: 'According to total weight',
    taxRule: 'No tax',
    outOfRangeBehavior: 'Apply the cost of the highest defined range',
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
      {
        weightMin: 5,
        weightMax: 10,
        zones: [
          {
            zone: dataZones.europe,
            price: 10,
          },
          {
            zone: dataZones.northAmerica,
            price: 4,
          },
        ],
      },
      {
        weightMin: 10,
        weightMax: 20,
        zones: [
          {
            zone: dataZones.europe,
            price: 20,
          },
          {
            zone: dataZones.northAmerica,
            price: 8,
          },
        ],
      },
    ],
    // Size weight and group access
    maxWidth: 200,
    maxHeight: 200,
    maxDepth: 200,
    maxWeight: 50,
    enable: true,
  });
  const carrierDataTax: FakerCarrier = new FakerCarrier({
    // General settings
    name: 'Test',
    speedGrade: 7,
    transitName: '2 days',
    trackingURL: '',
    // Shipping locations and cost
    handlingCosts: true,
    freeShipping: false,
    billing: 'According to total weight',
    taxRule: 'FR Taux standard (20%)',
    outOfRangeBehavior: 'Apply the cost of the highest defined range',
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
      {
        weightMin: 5,
        weightMax: 10,
        zones: [
          {
            zone: dataZones.europe,
            price: 10,
          },
          {
            zone: dataZones.northAmerica,
            price: 4,
          },
        ],
      },
      {
        weightMin: 10,
        weightMax: 20,
        zones: [
          {
            zone: dataZones.europe,
            price: 20,
          },
          {
            zone: dataZones.northAmerica,
            price: 8,
          },
        ],
      },
    ],
    // Size weight and group access
    maxWidth: 200,
    maxHeight: 200,
    maxDepth: 200,
    maxWeight: 50,
    enable: true,
  });
  const carrierDataRanges: FakerCarrier = new FakerCarrier({
    // General settings
    name: 'Test',
    speedGrade: 7,
    transitName: '2 days',
    trackingURL: '',
    // Shipping locations and cost
    handlingCosts: true,
    freeShipping: false,
    billing: 'According to total weight',
    taxRule: 'FR Taux standard (20%)',
    outOfRangeBehavior: 'Apply the cost of the highest defined range',
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
      {
        weightMin: 5,
        weightMax: 10,
        zones: [
          {
            zone: dataZones.europe,
            price: 10,
          },
          {
            zone: dataZones.northAmerica,
            price: 4,
          },
        ],
      },
      {
        weightMin: 10,
        weightMax: 20,
        zones: [
          {
            zone: dataZones.europe,
            price: 20,
          },
          {
            zone: dataZones.northAmerica,
            price: 8,
          },
        ],
      },
    ],
    // Size weight and group access
    maxWidth: 200,
    maxHeight: 200,
    maxDepth: 200,
    maxWeight: 50,
    enable: true,
  });
  const carrierDataBilling: FakerCarrier = new FakerCarrier({
    // General settings
    name: 'Test',
    speedGrade: 7,
    transitName: '2 days',
    trackingURL: '',
    // Shipping locations and cost
    handlingCosts: true,
    freeShipping: false,
    billing: 'According to total price',
    taxRule: 'FR Taux standard (20%)',
    outOfRangeBehavior: 'Apply the cost of the highest defined range',
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
      {
        weightMin: 5,
        weightMax: 10,
        zones: [
          {
            zone: dataZones.europe,
            price: 10,
          },
          {
            zone: dataZones.northAmerica,
            price: 4,
          },
        ],
      },
      {
        weightMin: 10,
        weightMax: 20,
        zones: [
          {
            zone: dataZones.europe,
            price: 20,
          },
          {
            zone: dataZones.northAmerica,
            price: 8,
          },
        ],
      },
    ],
    // Size weight and group access
    maxWidth: 200,
    maxHeight: 200,
    maxDepth: 200,
    maxWeight: 50,
    enable: true,
  });
  const carrierDataRemoveRanges: FakerCarrier = new FakerCarrier({
    // General settings
    name: 'Test',
    speedGrade: 7,
    transitName: '2 days',
    trackingURL: '',
    // Shipping locations and cost
    handlingCosts: true,
    freeShipping: false,
    billing: 'According to total price',
    taxRule: 'FR Taux standard (20%)',
    outOfRangeBehavior: 'Apply the cost of the highest defined range',
    ranges: [
      {
        weightMin: 0,
        weightMax: 5,
        zones: [
          {
            zone: dataZones.oceania,
            price: 10,
          },
        ],
      },
    ],
    // Size weight and group access
    maxWidth: 200,
    maxHeight: 200,
    maxDepth: 200,
    maxWeight: 50,
    enable: true,
  });
  const editProductData: FakerProduct = {...dataProducts.demo_11};
  editProductData.packageDimensionWeight = 30;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Create images
    await Promise.all([
      utilsFile.generateImage(`${carrierData.name}.jpg`),
    ]);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    // Delete the generated images
    await Promise.all([
      utilsFile.deleteFile(`${carrierData.name}.jpg`),
    ]);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
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

  it('should reset all filters and get number of carriers in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfCarriers = await boCarriersPage.resetAndGetNumberOfLines(page);
    expect(numberOfCarriers).to.be.above(0);
  });

  it('should go to add new carrier page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddCarrierPage', baseContext);

    await boCarriersPage.goToAddNewCarrierPage(page);

    const pageTitle = await boCarriersCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersCreatePage.pageTitleCreate);
  });

  it('should create carrier and check result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createCarrier', baseContext);

    const textResult = await boCarriersCreatePage.createEditCarrier(page, carrierData);
    expect(textResult).to.contains(boCarriersPage.successfulCreationMessage);

    const numberCarriersAfterCreation = await boCarriersPage.getNumberOfElementInGrid(page);
    expect(numberCarriersAfterCreation).to.be.equal(numberOfCarriers + 1);

    idCarrier = parseInt(await boCarriersPage.getTextColumn(page, numberOfCarriers + 1, 'id_carrier'), 10);
  });

  it('should view my shop', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

    // Click on view my shop
    page = await boCarriersPage.viewMyShop(page);
    // Change language
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage, 'Home page is not displayed').to.eq(true);
  });

  it(`should search for the product '${dataProducts.demo_11.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchForProduct', baseContext);

    await foClassicHomePage.searchProduct(page, dataProducts.demo_11.name);

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);
  });

  it('should add the product to cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

    await foClassicSearchResultsPage.goToProductPage(page, 1);
    // Add the product to the cart
    await foClassicProductPage.addProductToTheCart(page, 1, [], false);

    const notificationsNumber = await foClassicProductPage.getCartNotificationsNumber(page);
    expect(notificationsNumber).to.be.equal(1);
  });

  it('should go to shopping cart page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCart', baseContext);

    await foClassicProductPage.goToCartPage(page);

    const pageTitle = await foClassicCartPage.getPageTitle(page);
    expect(pageTitle).to.contains(foClassicCartPage.pageTitle);
  });

  it('should proceed to checkout', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckout', baseContext);

    // Proceed to checkout the shopping cart
    await foClassicCartPage.clickOnProceedToCheckout(page);

    // Go to checkout page
    const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
    expect(isCheckoutPage).to.eq(true);
  });

  it('should fill guest personal information', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setPersonalInformation', baseContext);

    await foClassicCheckoutPage.clickOnSignIn(page);

    const isCustomerConnected = await foClassicCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
    expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
  });

  it('should choose the delivery address', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'chooseAndConfirmAddressStep', baseContext);

    await checkoutPage.chooseDeliveryAddress(page, 1);

    const isDeliveryStep = await foClassicCheckoutPage.goToDeliveryStep(page);
    expect(isDeliveryStep).to.eq(true);
  });

  it('should check the carriers', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCarriers', baseContext);

    const carriers = await foClassicCheckoutPage.getAllCarriersNames(page);
    expect(carriers).to.deep.equal([dataCarriers.clickAndCollect.name, carrierData.name, dataCarriers.myCarrier.name]);

    const carrierInfo = await checkoutPage.getCarrierData(page, idCarrier);
    await Promise.all([
      expect(carrierInfo.name).to.equal(carrierData.name),
      expect(carrierInfo.transitName).to.equal(carrierData.transitName),
      expect(carrierInfo.price).to.equal(carrierData.ranges[0].zones[0].price),
      expect(carrierInfo.priceText).to.equal(`€${carrierData.ranges[0].zones[0].price!.toFixed(2)} tax incl.`),
    ]);
  });

  it('should go back to BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'firstGoBackToBO', baseContext);

    page = await foClassicCheckoutPage.changePage(browserContext, 0);

    const pageTitle = await boCarriersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersPage.pageTitle);
  });

  it('should filter list by name', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

    await boCarriersPage.resetFilter(page);
    await boCarriersPage.filterTable(
      page,
      'input',
      'name',
      carrierData.name,
    );

    const carrierName = await boCarriersPage.getTextColumn(page, 1, 'name');
    expect(carrierName).to.contains(carrierData.name);

    idCarrier = parseInt(await boCarriersPage.getTextColumn(page, 1, 'id_carrier'), 10);
  });

  it('should go to edit carrier page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditCarrierPageStatus', baseContext);

    await boCarriersPage.goToEditCarrierPage(page, 1);

    const pageTitle = await boCarriersCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersCreatePage.pageTitleEdit);
  });

  it('should update the carrier to free shipping', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateCarrierFreeShipping', baseContext);

    const textResult = await boCarriersCreatePage.createEditCarrier(page, carrierDataFreeShipping);
    expect(textResult).to.contains(boCarriersPage.successfulUpdateMessage);

    idCarrier = parseInt(await boCarriersPage.getTextColumn(page, 1, 'id_carrier'), 10);
  });

  it('should check the carriers', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCarriers', baseContext);

    page = await boCarriersPage.changePage(browserContext, 1);
    await foClassicCheckoutPage.reloadPage(page);

    const carriers = await foClassicCheckoutPage.getAllCarriersNames(page);
    expect(carriers).to.deep.equal([
      carrierDataFreeShipping.name,
      dataCarriers.clickAndCollect.name,
      dataCarriers.myCarrier.name,
    ]);

    const carrierInfo = await checkoutPage.getCarrierData(page, idCarrier);
    await Promise.all([
      expect(carrierInfo.name).to.equal(carrierData.name),
      expect(carrierInfo.transitName).to.equal(carrierData.transitName),
      expect(carrierInfo.priceText).to.equal('Free'),
    ]);
  });

  it('should go to edit carrier page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditCarrierHandlingCosts', baseContext);

    page = await foClassicCheckoutPage.changePage(browserContext, 0);
    await boCarriersPage.goToEditCarrierPage(page, 1);

    const pageTitle = await boCarriersCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersCreatePage.pageTitleEdit);
  });

  it('should update the carrier with handling costs', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateCarrierHandlingCosts', baseContext);

    const textResult = await boCarriersCreatePage.createEditCarrier(page, carrierDataHandlingCosts);
    expect(textResult).to.contains(boCarriersPage.successfulUpdateMessage);

    idCarrier = parseInt(await boCarriersPage.getTextColumn(page, 1, 'id_carrier'), 10);
  });

  it('should check the carriers', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersAfterHandlingCosts', baseContext);

    page = await boCarriersPage.changePage(browserContext, 1);
    await foClassicCheckoutPage.reloadPage(page);

    const carriers = await foClassicCheckoutPage.getAllCarriersNames(page);
    expect(carriers).to.deep.equal([
      dataCarriers.clickAndCollect.name,
      carrierDataHandlingCosts.name,
      dataCarriers.myCarrier.name,
    ]);

    const carrierInfo = await checkoutPage.getCarrierData(page, idCarrier);
    await Promise.all([
      expect(carrierInfo.name).to.equal(carrierData.name),
      expect(carrierInfo.transitName).to.equal(carrierData.transitName),
      expect(carrierInfo.price).to.equal(carrierData.ranges[0].zones[0].price! + handlingCost),
      expect(carrierInfo.priceText).to.equal(`€${(carrierData.ranges[0].zones[0].price! + handlingCost).toFixed(2)} tax incl.`),
    ]);
  });

  it('should go to edit carrier page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditCarrierPageGroupAccess', baseContext);

    page = await foClassicCheckoutPage.changePage(browserContext, 0);
    await boCarriersPage.goToEditCarrierPage(page, 1);

    const pageTitle = await boCarriersCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersCreatePage.pageTitleEdit);
  });

  it('should update the carrier tax', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateCarrierTax', baseContext);

    const textResult = await boCarriersCreatePage.createEditCarrier(page, carrierDataTax);
    expect(textResult).to.contains(boCarriersPage.successfulUpdateMessage);

    idCarrier = parseInt(await boCarriersPage.getTextColumn(page, 1, 'id_carrier'), 10);
  });

  it('should check the carriers', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersAfterHandlingCosts', baseContext);

    page = await boCarriersPage.changePage(browserContext, 1);
    await foClassicCheckoutPage.reloadPage(page);

    const carriers = await foClassicCheckoutPage.getAllCarriersNames(page);
    expect(carriers).to.deep.equal([
      dataCarriers.clickAndCollect.name,
      dataCarriers.myCarrier.name,
      carrierDataHandlingCosts.name,
    ]);

    const carrierInfo = await checkoutPage.getCarrierData(page, idCarrier);
    const priceWithTax: number = ((carrierData.ranges[0].zones[0].price! + handlingCost) / 100) * (100 + 20);
    await Promise.all([
      expect(carrierInfo.name).to.equal(carrierData.name),
      expect(carrierInfo.transitName).to.equal(carrierData.transitName),
      expect(carrierInfo.price).to.equal(priceWithTax),
      expect(carrierInfo.priceText).to.equal(`€${priceWithTax.toFixed(2)} tax incl.`),
    ]);
  });

  it('should go to \'Catalog > Products\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

    page = await boCarriersPage.changePage(browserContext, 0);
    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.catalogParentLink,
      boDashboardPage.productsLink,
    );
    await boProductsPage.closeSfToolBar(page);

    const pageTitle = await boProductsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boProductsPage.pageTitle);
  });

  it(`should filter a product named "${dataProducts.demo_11.name}"`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterProduct', baseContext);

    await boProductsPage.resetFilter(page);
    await boProductsPage.filterProducts(page, 'product_name', dataProducts.demo_11.name, 'input');

    const numberOfProductsAfterFilter = await boProductsPage.getNumberOfProductsFromList(page);
    expect(numberOfProductsAfterFilter).to.equal(1);

    const textColumn = await boProductsPage.getTextColumn(page, 'product_name', 1);
    expect(textColumn).to.equal(dataProducts.demo_11.name);
  });

  it('should edit the product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditPage', baseContext);

    await boProductsPage.goToProductPage(page, 1);

    const pageTitle: string = await createProductsPage.getPageTitle(page);
    expect(pageTitle).to.contains(createProductsPage.pageTitle);
  });

  it('should go to shipping tab and edit package dimension', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editPackageDimension', baseContext);

    await shippingTab.setPackageDimension(page, editProductData);

    const message = await createProductsPage.saveProduct(page);
    expect(message).to.eq(createProductsPage.successfulUpdateMessage);
  });

  it('should check the carriers', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersPositionAfterWeight', baseContext);

    page = await createProductsPage.changePage(browserContext, 1);
    await foClassicCheckoutPage.reloadPage(page);

    const carriers = await foClassicCheckoutPage.getAllCarriersNames(page);
    expect(carriers).to.deep.equal([
      dataCarriers.clickAndCollect.name,
      dataCarriers.myCarrier.name,
      carrierData.name,
    ]);

    const carrierInfo = await checkoutPage.getCarrierData(page, idCarrier);
    const priceWithTax: number = ((carrierData.ranges[2].zones[0].price! + handlingCost) / 100) * (100 + 20);
    await Promise.all([
      expect(carrierInfo.name).to.equal(carrierData.name),
      expect(carrierInfo.transitName).to.equal(carrierData.transitName),
      expect(carrierInfo.price).to.equal(priceWithTax),
      expect(carrierInfo.priceText).to.equal(`€${priceWithTax.toFixed(2)} tax incl.`),
    ]);
  });

  it('should go to \'Shipping > Carriers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPage', baseContext);

    page = await checkoutPage.changePage(browserContext, 0);
    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shippingLink,
      boDashboardPage.carriersLink,
    );

    const pageTitle = await boCarriersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersPage.pageTitle);
  });

  it('should filter list by name', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

    await boCarriersPage.resetFilter(page);
    await boCarriersPage.filterTable(
      page,
      'input',
      'name',
      carrierData.name,
    );

    const carrierName = await boCarriersPage.getTextColumn(page, 1, 'name');
    expect(carrierName).to.contains(carrierData.name);

    idCarrier = parseInt(await boCarriersPage.getTextColumn(page, 1, 'id_carrier'), 10);
  });

  it('should go to edit carrier page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditCarrierPageStatus', baseContext);

    await boCarriersPage.goToEditCarrierPage(page, 1);

    const pageTitle = await boCarriersCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersCreatePage.pageTitleEdit);
  });

  it('should update the carrier ranges', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateCarrierTax', baseContext);

    const textResult = await boCarriersCreatePage.createEditCarrier(page, carrierDataRanges);
    expect(textResult).to.contains(boCarriersPage.successfulUpdateMessage);

    idCarrier = parseInt(await boCarriersPage.getTextColumn(page, 1, 'id_carrier'), 10);
  });

  it('should check the carriers', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersPositionAfterWeight', baseContext);

    page = await createProductsPage.changePage(browserContext, 1);
    await foClassicCheckoutPage.reloadPage(page);

    const carriers = await foClassicCheckoutPage.getAllCarriersNames(page);
    expect(carriers).to.deep.equal([
      dataCarriers.clickAndCollect.name,
      dataCarriers.myCarrier.name,
      carrierData.name,
    ]);

    const carrierInfo = await checkoutPage.getCarrierData(page, idCarrier);
    const priceWithTax: number = ((carrierData.ranges[2].zones[0].price! + handlingCost) / 100) * (100 + 20);
    await Promise.all([
      expect(carrierInfo.name).to.equal(carrierData.name),
      expect(carrierInfo.transitName).to.equal(carrierData.transitName),
      expect(carrierInfo.price).to.equal(priceWithTax),
      expect(carrierInfo.priceText).to.equal(`€${priceWithTax.toFixed(2)} tax incl.`),
    ]);
  });

  it('should return to cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersPositionAfterWeight', baseContext);

    await foClassicCheckoutPage.clickOnHeaderLink(page, 'Logo');
    await foClassicHomePage.goToCartPage(page);

    const pageTitle = await foClassicCartPage.getPageTitle(page);
    expect(pageTitle).to.contains(foClassicCartPage.pageTitle);
  });

  it('should change quantity to 2 and proceed to checkout', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersPositionAfterWeight', baseContext);

    await foClassicCartPage.editProductQuantity(page, 1, 2);

    // Proceed to checkout the shopping cart
    await foClassicCartPage.clickOnProceedToCheckout(page);

    // Go to checkout page
    const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
    expect(isCheckoutPage).to.eq(true);
  });

  it('should choose the delivery address', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'chooseAndConfirmAddressStep', baseContext);

    await checkoutPage.chooseDeliveryAddress(page, 1);

    const isDeliveryStep = await foClassicCheckoutPage.goToDeliveryStep(page);
    expect(isDeliveryStep).to.eq(true);
  });

  it('should check the carriers position', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersPosition', baseContext);

    const carriers = await foClassicCheckoutPage.getAllCarriersNames(page);
    expect(carriers).to.deep.equal([dataCarriers.clickAndCollect.name, dataCarriers.myCarrier.name]);
  });

  it('should return to cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersPositionAfterWeight', baseContext);

    await foClassicCheckoutPage.clickOnHeaderLink(page, 'Logo');
    await foClassicHomePage.goToCartPage(page);

    const pageTitle = await foClassicCartPage.getPageTitle(page);
    expect(pageTitle).to.contains(foClassicCartPage.pageTitle);
  });

  it('should change quantity to 1 and proceed to checkout', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersPositionAfterWeight', baseContext);

    await foClassicCartPage.editProductQuantity(page, 1, 1);

    // Proceed to checkout the shopping cart
    await foClassicCartPage.clickOnProceedToCheckout(page);

    // Go to checkout page
    const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
    expect(isCheckoutPage).to.eq(true);
  });

  it('should choose the delivery address', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'chooseAndConfirmAddressStep', baseContext);

    await checkoutPage.chooseDeliveryAddress(page, 1);

    const isDeliveryStep = await foClassicCheckoutPage.goToDeliveryStep(page);
    expect(isDeliveryStep).to.eq(true);
  });

  it('should check the carriers position', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersPosition', baseContext);

    const carriers = await foClassicCheckoutPage.getAllCarriersNames(page);
    expect(carriers).to.deep.equal([dataCarriers.clickAndCollect.name, dataCarriers.myCarrier.name, carrierData.name]);

    const carrierInfo = await checkoutPage.getCarrierData(page, idCarrier);
    const priceWithTax: number = ((carrierData.ranges[2].zones[0].price! + handlingCost) / 100) * (100 + 20);
    await Promise.all([
      expect(carrierInfo.name).to.equal(carrierData.name),
      expect(carrierInfo.transitName).to.equal(carrierData.transitName),
      expect(carrierInfo.price).to.equal(priceWithTax),
      expect(carrierInfo.priceText).to.equal(`€${priceWithTax.toFixed(2)} tax incl.`),
    ]);
  });

  it('should go to edit carrier page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditCarrierPageGroupAccess', baseContext);

    page = await foClassicCheckoutPage.changePage(browserContext, 0);
    await boCarriersPage.goToEditCarrierPage(page, 1);

    const pageTitle = await boCarriersCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersCreatePage.pageTitleEdit);
  });

  it('should update the carrier billing', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateCarrierBilling', baseContext);

    const textResult = await boCarriersCreatePage.createEditCarrier(page, carrierDataBilling);
    expect(textResult).to.contains(boCarriersPage.successfulUpdateMessage);

    idCarrier = parseInt(await boCarriersPage.getTextColumn(page, 1, 'id_carrier'), 10);
  });

  it('should check the carriers', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersPosition', baseContext);

    page = await boCarriersPage.changePage(browserContext, 1);
    await foClassicCheckoutPage.reloadPage(page);

    const carriers = await foClassicCheckoutPage.getAllCarriersNames(page);
    expect(carriers).to.deep.equal([dataCarriers.clickAndCollect.name, dataCarriers.myCarrier.name, carrierData.name]);

    const carrierInfo = await checkoutPage.getCarrierData(page, idCarrier);
    const priceWithTax: number = ((carrierData.ranges[2].zones[0].price! + handlingCost) / 100) * (100 + 20);
    await Promise.all([
      expect(carrierInfo.name).to.equal(carrierData.name),
      expect(carrierInfo.transitName).to.equal(carrierData.transitName),
      expect(carrierInfo.price).to.equal(priceWithTax),
      expect(carrierInfo.priceText).to.equal(`€${priceWithTax.toFixed(2)} tax incl.`),
    ]);
  });

  it('should go to edit carrier page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditCarrierPageGroupAccess', baseContext);

    page = await foClassicCheckoutPage.changePage(browserContext, 0);
    await boCarriersPage.goToEditCarrierPage(page, 1);

    const pageTitle = await boCarriersCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersCreatePage.pageTitleEdit);
  });

  it('should remove carrier ranges', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateCarrierTax', baseContext);

    const textResult = await boCarriersCreatePage.createEditCarrier(page, carrierDataRemoveRanges);
    expect(textResult).to.contains(boCarriersPage.successfulUpdateMessage);

    idCarrier = parseInt(await boCarriersPage.getTextColumn(page, 1, 'id_carrier'), 10);
  });

  it('should check carriers', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersPosition', baseContext);

    page = await boCarriersPage.changePage(browserContext, 1);
    await foClassicCheckoutPage.reloadPage(page);

    const carriers = await foClassicCheckoutPage.getAllCarriersNames(page);
    expect(carriers).to.deep.equal([dataCarriers.clickAndCollect.name, dataCarriers.myCarrier.name]);
  });

  it('should delete carrier', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteCarrier', baseContext);

    page = await foClassicCheckoutPage.changePage(browserContext, 0);

    const textResult = await boCarriersPage.deleteCarrier(page, 1);
    expect(textResult).to.contains(boCarriersPage.successfulDeleteMessage);

    const numberOfCarriersAfterDelete = await boCarriersPage.resetAndGetNumberOfLines(page);
    expect(numberOfCarriersAfterDelete).to.be.equal(numberOfCarriers);
  });

  it('should go to \'Catalog > Products\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

    page = await boCarriersPage.changePage(browserContext, 0);
    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.catalogParentLink,
      boDashboardPage.productsLink,
    );
    await boProductsPage.closeSfToolBar(page);

    const pageTitle = await boProductsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boProductsPage.pageTitle);
  });

  it(`should filter a product named "${dataProducts.demo_11.name}"`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterProduct', baseContext);

    await boProductsPage.resetFilter(page);
    await boProductsPage.filterProducts(page, 'product_name', dataProducts.demo_11.name, 'input');

    const numberOfProductsAfterFilter = await boProductsPage.getNumberOfProductsFromList(page);
    expect(numberOfProductsAfterFilter).to.equal(1);

    const textColumn = await boProductsPage.getTextColumn(page, 'product_name', 1);
    expect(textColumn).to.equal(dataProducts.demo_11.name);
  });

  it('should edit the product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditPage', baseContext);

    await boProductsPage.goToProductPage(page, 1);

    const pageTitle: string = await createProductsPage.getPageTitle(page);
    expect(pageTitle).to.contains(createProductsPage.pageTitle);
  });

  it('should go to shipping tab and edit package dimension', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editPackageDimension', baseContext);

    await shippingTab.setPackageDimension(page, dataProducts.demo_11);

    const message = await createProductsPage.saveProduct(page);
    expect(message).to.eq(createProductsPage.successfulUpdateMessage);
  });
});
