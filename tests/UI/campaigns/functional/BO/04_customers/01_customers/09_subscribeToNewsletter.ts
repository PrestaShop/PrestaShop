// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import customersPage from '@pages/BO/customers';
import dashboardPage from '@pages/BO/dashboard';
import moduleManagerPage from '@pages/BO/modules/moduleManager';
import psEmailSubscriptionPage from '@pages/BO/modules/psEmailSubscription';

// Import data
import {DefaultCustomer} from '@data/demo/customer';
import {psEmailSubscription} from '@data/demo/modules';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_customers_customers_subscribeToNewsletter';

describe('BO - Customers - Customers : Check customer subscription to newsletter from BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCustomers: number = 0;

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

  it('should go to \'Customers > Customers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.customersParentLink,
      dashboardPage.customersLink,
    );
    await customersPage.closeSfToolBar(page);

    const pageTitle = await customersPage.getPageTitle(page);
    await expect(pageTitle).to.contains(customersPage.pageTitle);
  });

  it('should reset all filters and get number of customers in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilters', baseContext);

    numberOfCustomers = await customersPage.resetAndGetNumberOfLines(page);
    await expect(numberOfCustomers).to.be.above(0);
  });

  it(`should filter by email '${DefaultCustomer.email}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterByEmail', baseContext);

    await customersPage.filterCustomers(page, 'input', 'email', DefaultCustomer.email);

    const numberOfCustomersAfterFilter = await customersPage.getNumberOfElementInGrid(page);
    await expect(numberOfCustomersAfterFilter).to.equal(1);
  });

  [
    {args: {action: 'disable', value: false}},
    {args: {action: 'enable', value: true}},
  ].forEach((test, index) => {
    it(`should ${test.args.action} newsletters`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}NewsLetters`, baseContext);

      await customersPage.setNewsletterStatus(page, 1, test.args.value);

      const newsletterStatus = await customersPage.getNewsletterStatus(page, 1);
      await expect(newsletterStatus).to.be.equal(test.args.value);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToModuleManageTo${index}`, baseContext);

      await customersPage.goToSubMenu(
        page,
        customersPage.modulesParentLink,
        customersPage.moduleManagerLink,
      );

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      await expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it(`should go to '${psEmailSubscription.name}' module`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToEmailSubscriptionModule${index}`, baseContext);

      // Search and go to configure module page
      await moduleManagerPage.searchModule(page, psEmailSubscription.tag, psEmailSubscription.name);
      await moduleManagerPage.goToConfigurationPage(page, psEmailSubscription.name);

      const pageTitle = await psEmailSubscriptionPage.getPageSubtitle(page);
      await expect(pageTitle).to.contains(psEmailSubscription.name);
    });

    it('should check customer registration to newsletter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkCustomerRegistration${index}`, baseContext);

      // Get list of emails registered to newsletter
      const listOfEmails = await psEmailSubscriptionPage.getListOfNewsletterRegistrationEmails(page);

      if (test.args.value) {
        await expect(listOfEmails).to.include(DefaultCustomer.email);
      } else {
        await expect(listOfEmails).to.not.include(DefaultCustomer.email);
      }
    });

    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `gotoCustomersPage${index}`, baseContext);

      await psEmailSubscriptionPage.goToSubMenu(
        page,
        psEmailSubscriptionPage.customersParentLink,
        psEmailSubscriptionPage.customersLink,
      );

      const pageTitle = await customersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetAfterAll', baseContext);

    const numberOfCustomersAfterReset = await customersPage.resetAndGetNumberOfLines(page);
    await expect(numberOfCustomersAfterReset).to.equal(numberOfCustomers);
  });
});
