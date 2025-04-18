import {expect} from 'chai';
import testContext from '@utils/testContext';

import {
  boCustomerServicePage,
  boDashboardPage,
  boLoginPage,
  boMerchandiseReturnsPage,
  boOrderMessagesPage,
  boOrderMessagesCreatePage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'audit_BO_customerService';

describe('BO - Customer Service', async () => {
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

  it('should go to \'Customer Service > Customer Service\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerServicePage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.customerServiceParentLink,
      boDashboardPage.customerServiceLink,
    );

    const pageTitle = await boCustomerServicePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCustomerServicePage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Customer Service > Order messages\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrderMessagesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.customerServiceParentLink,
      boDashboardPage.orderMessagesLink,
    );

    const pageTitle = await boOrderMessagesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boOrderMessagesPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Customer Service > Order messages > Order message\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrderMessagesEditPage', baseContext);

    await boOrderMessagesPage.gotoEditOrderMessage(page, 1);

    const pageTitle = await boOrderMessagesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boOrderMessagesCreatePage.pageTitleEdit);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Customer Service > Order messages > Add new order message\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrderMessagesAddPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.customerServiceParentLink,
      boDashboardPage.orderMessagesLink,
    );
    await boOrderMessagesPage.goToAddNewOrderMessagePage(page);

    const pageTitle = await boOrderMessagesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boOrderMessagesCreatePage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Customer Service > Merchandise Returns\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToMerchandiseReturnsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.customerServiceParentLink,
      boDashboardPage.merchandiseReturnsLink,
    );

    const pageTitle = await boMerchandiseReturnsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boMerchandiseReturnsPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });
});
