// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import customersPage from '@pages/BO/customers';
import dashboardPage from '@pages/BO/dashboard';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';
import psEmailSubscriptionPage from '@pages/BO/modules/psEmailSubscription';

// Import data
import Customers from '@data/demo/customers';
import Modules from '@data/demo/modules';

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
    expect(pageTitle).to.contains(customersPage.pageTitle);
  });

  it('should reset all filters and get number of customers in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilters', baseContext);

    numberOfCustomers = await customersPage.resetAndGetNumberOfLines(page);
    expect(numberOfCustomers).to.be.above(0);
  });

  it(`should filter by email '${Customers.johnDoe.email}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterByEmail', baseContext);

    await customersPage.filterCustomers(page, 'input', 'email', Customers.johnDoe.email);

    const numberOfCustomersAfterFilter = await customersPage.getNumberOfElementInGrid(page);
    expect(numberOfCustomersAfterFilter).to.equal(1);
  });

  [
    {args: {action: 'disable', value: false}},
    {args: {action: 'enable', value: true}},
  ].forEach((test, index: number) => {
    it(`should ${test.args.action} newsletters`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}NewsLetters`, baseContext);

      await customersPage.setNewsletterStatus(page, 1, test.args.value);

      const newsletterStatus = await customersPage.getNewsletterStatus(page, 1);
      expect(newsletterStatus).to.be.equal(test.args.value);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToModuleManageTo${index}`, baseContext);

      await customersPage.goToSubMenu(
        page,
        customersPage.modulesParentLink,
        customersPage.moduleManagerLink,
      );

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it(`should go to '${Modules.psEmailSubscription.name}' module`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToEmailSubscriptionModule${index}`, baseContext);

      // Search and go to configure module page
      await moduleManagerPage.searchModule(page, Modules.psEmailSubscription);
      await moduleManagerPage.goToConfigurationPage(page, Modules.psEmailSubscription.tag);

      const pageTitle = await psEmailSubscriptionPage.getPageSubtitle(page);
      expect(pageTitle).to.contains(Modules.psEmailSubscription.name);
    });

    it('should check customer registration to newsletter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkCustomerRegistration${index}`, baseContext);

      // Get list of emails registered to newsletter
      const listOfEmails = await psEmailSubscriptionPage.getListOfNewsletterRegistrationEmails(page);

      if (test.args.value) {
        expect(listOfEmails).to.include(Customers.johnDoe.email);
      } else {
        expect(listOfEmails).to.not.include(Customers.johnDoe.email);
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
      expect(pageTitle).to.contains(customersPage.pageTitle);
    });
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetAfterAll', baseContext);

    const numberOfCustomersAfterReset = await customersPage.resetAndGetNumberOfLines(page);
    expect(numberOfCustomersAfterReset).to.equal(numberOfCustomers);
  });
});
