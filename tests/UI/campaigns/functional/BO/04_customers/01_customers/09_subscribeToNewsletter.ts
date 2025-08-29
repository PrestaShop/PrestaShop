// Import utils
import testContext from '@utils/testContext';

import {
  boCustomersPage,
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  type BrowserContext,
  dataCustomers,
  dataModules,
  modPsEmailSubscriptionBoMain,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_BO_customers_customers_subscribeToNewsletter';

describe('BO - Customers - Customers : Check customer subscription to newsletter from BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCustomers: number = 0;

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

  it('should go to \'Customers > Customers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.customersParentLink,
      boDashboardPage.customersLink,
    );
    await boCustomersPage.closeSfToolBar(page);

    const pageTitle = await boCustomersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCustomersPage.pageTitle);
  });

  it('should reset all filters and get number of customers in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilters', baseContext);

    numberOfCustomers = await boCustomersPage.resetAndGetNumberOfLines(page);
    expect(numberOfCustomers).to.be.above(0);
  });

  it(`should filter by email '${dataCustomers.johnDoe.email}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterByEmail', baseContext);

    await boCustomersPage.filterCustomers(page, 'input', 'email', dataCustomers.johnDoe.email);

    const numberOfCustomersAfterFilter = await boCustomersPage.getNumberOfElementInGrid(page);
    expect(numberOfCustomersAfterFilter).to.equal(1);
  });

  [
    {args: {action: 'disable', value: false}},
    {args: {action: 'enable', value: true}},
  ].forEach((test, index: number) => {
    it(`should ${test.args.action} newsletters`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}NewsLetters`, baseContext);

      await boCustomersPage.setNewsletterStatus(page, 1, test.args.value);

      const newsletterStatus = await boCustomersPage.getNewsletterStatus(page, 1);
      expect(newsletterStatus).to.be.equal(test.args.value);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToModuleManageTo${index}`, baseContext);

      await boCustomersPage.goToSubMenu(
        page,
        boCustomersPage.modulesParentLink,
        boCustomersPage.moduleManagerLink,
      );

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it(`should go to '${dataModules.psEmailSubscription.name}' module`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToEmailSubscriptionModule${index}`, baseContext);

      // Search and go to configure module page
      await boModuleManagerPage.searchModule(page, dataModules.psEmailSubscription);
      await boModuleManagerPage.goToConfigurationPage(page, dataModules.psEmailSubscription.tag);

      const pageTitle = await modPsEmailSubscriptionBoMain.getPageSubtitle(page);
      expect(pageTitle).to.contains(dataModules.psEmailSubscription.name);
    });

    it('should check customer registration to newsletter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkCustomerRegistration${index}`, baseContext);

      // Get list of emails registered to newsletter
      const listOfEmails = await modPsEmailSubscriptionBoMain.getListOfNewsletterRegistrationEmails(page);

      if (test.args.value) {
        expect(listOfEmails).to.include(dataCustomers.johnDoe.email);
      } else {
        expect(listOfEmails).to.not.include(dataCustomers.johnDoe.email);
      }
    });

    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `gotoCustomersPage${index}`, baseContext);

      await modPsEmailSubscriptionBoMain.goToSubMenu(
        page,
        modPsEmailSubscriptionBoMain.customersParentLink,
        modPsEmailSubscriptionBoMain.customersLink,
      );

      const pageTitle = await boCustomersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersPage.pageTitle);
    });
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetAfterAll', baseContext);

    const numberOfCustomersAfterReset = await boCustomersPage.resetAndGetNumberOfLines(page);
    expect(numberOfCustomersAfterReset).to.equal(numberOfCustomers);
  });
});
