import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabShippingPage,
  type BrowserContext,
  FakerProduct,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'regression_products_deliveryTimePersistence';

/*
Set a specific delivery time on a product and save it
Switch the product back to the default delivery time and save it
Check that the specific delivery times are still stored
 */
describe('Regression - Products : Keep the specific delivery time when switching to the default one', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const deliveryTimeInStock: string = '5 days custom';
  const deliveryTimeOutOfStock: string = '15 days custom';
  const productData: FakerProduct = new FakerProduct({
    type: 'standard',
    quantity: 10,
    status: true,
  });

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

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

  it('should create a standard product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

    const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
    expect(isModalVisible).to.equals(true);

    await boProductsPage.selectProductType(page, productData.type);
    await boProductsPage.clickOnAddNewProduct(page);

    const createProductMessage = await boProductsCreatePage.setProduct(page, productData);
    expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
  });

  it('should select \'Specific delivery time\' and save', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setSpecificDeliveryTime', baseContext);

    await boProductsCreatePage.goToTab(page, 'shipping');
    await boProductsCreateTabShippingPage.setDeliveryTime(page, 'Specific delivery time');

    const message = await boProductsCreatePage.saveProduct(page);
    expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
  });

  it('should set both specific delivery times and save', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setSpecificDeliveryTimes', baseContext);

    await boProductsCreatePage.goToTab(page, 'shipping');
    await boProductsCreateTabShippingPage.setDeliveryTimeInStockProducts(page, deliveryTimeInStock);
    await boProductsCreateTabShippingPage.setDeliveryTimeOutOfStockProducts(page, deliveryTimeOutOfStock);

    const message = await boProductsCreatePage.saveProduct(page);
    expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
  });

  it('should check that both specific delivery times are stored', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkSpecificDeliveryTimesStored', baseContext);

    await boProductsCreatePage.reloadPage(page);
    await boProductsCreatePage.goToTab(page, 'shipping');

    const inStock = await boProductsCreateTabShippingPage.getValue(page, 'delivery_in_stock', '1');
    expect(inStock).to.eq(deliveryTimeInStock);

    const outOfStock = await boProductsCreateTabShippingPage.getValue(page, 'delivery_out_stock', '1');
    expect(outOfStock).to.eq(deliveryTimeOutOfStock);
  });

  it('should select \'Default delivery time\' and save', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setDefaultDeliveryTime', baseContext);

    await boProductsCreateTabShippingPage.setDeliveryTime(page, 'Default delivery time');

    const message = await boProductsCreatePage.saveProduct(page);
    expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
  });

  it('should check that the default delivery time is selected', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDefaultDeliveryTimeSelected', baseContext);

    await boProductsCreatePage.reloadPage(page);
    await boProductsCreatePage.goToTab(page, 'shipping');

    const value = await boProductsCreateTabShippingPage.getValue(page, 'additional_delivery_times');
    expect(value).to.eq('1');
  });

  it('should check that both specific delivery times are still stored', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkSpecificDeliveryTimesKept', baseContext);

    const inStock = await boProductsCreateTabShippingPage.getValue(page, 'delivery_in_stock', '1');
    expect(inStock).to.eq(deliveryTimeInStock);

    const outOfStock = await boProductsCreateTabShippingPage.getValue(page, 'delivery_out_stock', '1');
    expect(outOfStock).to.eq(deliveryTimeOutOfStock);
  });

  it('should delete the product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

    const message = await boProductsCreatePage.deleteProduct(page);
    expect(message).to.eq(boProductsPage.successfulDeleteMessage);
  });
});
