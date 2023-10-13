// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import customerServicePage from '@pages/BO/customerService/customerService';

// Import data
import CustomerServiceOptionsData from '@data/faker/customerServiceOptions';

import {BrowserContext, Page} from 'playwright';
import {expect} from 'chai';

const baseContext: string = 'functional_BO_customerService_customerService_customerServiceOptions';

describe('BO - Customer Service : Customer service options', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const optionsData: CustomerServiceOptionsData = new CustomerServiceOptionsData({
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
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Customer Service > Customer Service\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerServicePage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.customerServiceParentLink,
      dashboardPage.customerServiceLink,
    );

    const pageTitle = await customerServicePage.getPageTitle(page);
    expect(pageTitle).to.contains(customerServicePage.pageTitle);
  });

  it('should set customer service options', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setOptions', baseContext);

    const successMessage = await customerServicePage.setCustomerServiceOptions(page, optionsData);
    expect(successMessage).to.eq(customerServicePage.successfulUpdateMessage);
  });

  it('should check that the new block Sync is visible', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkSyncBlockVisible', baseContext);

    const isVisible = await customerServicePage.isRunSyncButtonVisible(page);
    expect(isVisible).to.eq(true);
  });
});
