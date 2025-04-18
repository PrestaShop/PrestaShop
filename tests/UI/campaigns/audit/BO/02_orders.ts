import {expect} from 'chai';
import testContext from '@utils/testContext';

import {
  boCreditSlipsPage,
  boDashboardPage,
  boDeliverySlipsPage,
  boInvoicesPage,
  boLoginPage,
  boOrdersPage,
  boOrdersCreatePage,
  boOrdersViewBlockTabListPage,
  boShoppingCartsPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'audit_BO_orders';

describe('BO - Orders', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  before(async function () {
    utilsPlaywright.setErrorsCaptured(true);

    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  beforeEach(async () => {
    utilsPlaywright.resetJsErrors();
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Orders > Orders\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.ordersParentLink,
      boDashboardPage.ordersLink,
    );

    const pageTitle = await boOrdersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boOrdersPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Orders > Orders > New Order\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage', baseContext);

    await boOrdersPage.goToCreateOrderPage(page);

    const pageTitle = await boOrdersCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boOrdersCreatePage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Orders > Invoices\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.ordersParentLink,
      boDashboardPage.invoicesLink,
    );
    await boInvoicesPage.closeSfToolBar(page);

    const pageTitle = await boInvoicesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boInvoicesPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Orders > Credit slips\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCreditSlipsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.ordersParentLink,
      boDashboardPage.creditSlipsLink,
    );
    await boCreditSlipsPage.closeSfToolBar(page);

    const pageTitle = await boCreditSlipsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCreditSlipsPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Orders > Delivery slips\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDeliverySlipsPage', baseContext);

    await boOrdersViewBlockTabListPage.goToSubMenu(
      page,
      boOrdersViewBlockTabListPage.ordersParentLink,
      boOrdersViewBlockTabListPage.deliverySlipslink,
    );
    await boDeliverySlipsPage.closeSfToolBar(page);

    const pageTitle = await boDeliverySlipsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDeliverySlipsPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Orders > Shopping carts\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.ordersParentLink,
      boDashboardPage.shoppingCartsLink,
    );

    const pageTitle = await boShoppingCartsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boShoppingCartsPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });
});
