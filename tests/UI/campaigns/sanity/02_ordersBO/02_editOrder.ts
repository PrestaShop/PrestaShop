// Import utils
import testContext from '@utils/testContext';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrdersViewBlockProductsPage,
  type BrowserContext,
  dataOrderStatuses,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'sanity_ordersBO_editOrder';

/*
  Connect to the BO
  Edit the first order
  Logout from the BO
 */
describe('BO - Orders - Orders : Edit Order BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // Steps
  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to the \'Orders > Orders\' page', async function () {
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
    await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrder', baseContext);

    await boOrdersPage.goToOrder(page, 1);

    const pageTitle = await boOrdersViewBlockProductsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boOrdersViewBlockProductsPage.pageTitle);
  });

  it('should modify the product quantity and check the validation', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editOrderQuantity', baseContext);

    const newQuantity = await boOrdersViewBlockProductsPage.modifyProductQuantity(page, 1, 5);
    expect(newQuantity, 'Quantity was not updated').to.equal(5);
  });

  it('should modify the order status and check the validation', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editOrderStatus', baseContext);

    const orderStatus = await boOrdersViewBlockProductsPage.modifyOrderStatus(page, dataOrderStatuses.paymentAccepted.name);
    expect(orderStatus).to.equal(dataOrderStatuses.paymentAccepted.name);
  });

  // Logout from BO
  it('should log out from BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'logoutBO', baseContext);

    await boDashboardPage.logoutBO(page);

    const pageTitle = await boLoginPage.getPageTitle(page);
    expect(pageTitle).to.contains(boLoginPage.pageTitle);
  });
});
