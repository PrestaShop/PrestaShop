import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCustomerServicePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerCustomerServiceOptions,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_customerService_customerService_customerServiceOptions';

describe('BO - Customer Service : Customer service options', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const optionsData: FakerCustomerServiceOptions = new FakerCustomerServiceOptions({
    imapUrl: 'outlook.office365.com',
    imapPort: '993',
    imapUser: 'presta_test@outlook.fr',
    imapPassword: 'prestashop_demo',
    deleteMessage: true,
    createNewThreads: true,
    imapOptionsSsl: true,
  });

  // before and after functions
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

  it('should go to \'Customer Service > Customer Service\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerServicePage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.customerServiceParentLink,
      boDashboardPage.customerServiceLink,
    );

    const pageTitle = await boCustomerServicePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCustomerServicePage.pageTitle);
  });

  it('should set customer service options', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setOptions', baseContext);

    const successMessage = await boCustomerServicePage.setCustomerServiceOptions(page, optionsData);
    expect(successMessage).to.eq(boCustomerServicePage.successfulUpdateMessage);
  });

  it('should check that the new block Sync is visible', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkSyncBlockVisible', baseContext);

    const isVisible = await boCustomerServicePage.isRunSyncButtonVisible(page);
    expect(isVisible).to.eq(true);
  });
});
