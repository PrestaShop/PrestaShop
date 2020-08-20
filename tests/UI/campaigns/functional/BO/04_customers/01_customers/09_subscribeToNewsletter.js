require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login test
const loginCommon = require('@commonTests/loginBO');

// Import data
const {DefaultAccount} = require('@data/demo/customer');
const {psEmailSubscription} = require('@data/demo/modules');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const customersPage = require('@pages/BO/customers');
const moduleManagerPage = require('@pages/BO/modules/moduleManager');
const psEmailSubscriptionPage = require('@pages/BO/modules/psEmailSubscription');


const baseContext = 'BO_customers_customers_subscribeToNewsletter';
let numberOfCustomers = 0;

let browserContext;
let page;

describe('Check customer subscription to newsletter from BO', async () => {
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

  it('should go to customers page', async function () {
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

  it('should reset all filters and get Number of customers in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilters', baseContext);

    numberOfCustomers = await customersPage.resetAndGetNumberOfLines(page);
    await expect(numberOfCustomers).to.be.above(0);
  });

  it(`should filter by email ${DefaultAccount.email}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterByEmail', baseContext);

    await customersPage.filterCustomers(
      page,
      'input',
      'email',
      DefaultAccount.email,
    );

    const numberOfCustomersAfterFilter = await customersPage.getNumberOfElementInGrid(page);
    await expect(numberOfCustomersAfterFilter).to.equal(1);
  });

  const tests = [
    {args: {action: 'disable', value: false}},
    {args: {action: 'enable', value: true}},
  ];

  tests.forEach((test) => {
    it(`should ${test.args.action} newsletters`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}NewsLetters`, baseContext);

      await customersPage.updateToggleColumnValue(page, 1, 'newsletter', test.args.value);

      const newsletterStatus = await customersPage.getToggleColumnValue(page, 1, 'newsletter');
      await expect(newsletterStatus).to.be.equal(test.args.value);
    });

    it('should go to module manager page', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `goToModuleManageTo${test.args.action}`,
        baseContext,
      );

      await customersPage.goToSubMenu(
        page,
        customersPage.modulesParentLink,
        customersPage.moduleManagerLink,
      );

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      await expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it(`should go to '${psEmailSubscription.name}' module`, async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `goToEmailSubscriptionModulePageAfter${test.args.action}`,
        baseContext,
      );

      // Search and go to configure module page
      await moduleManagerPage.searchModule(page, psEmailSubscription.tag, psEmailSubscription.name);
      await moduleManagerPage.goToConfigurationPage(page, psEmailSubscription.name);

      const pageTitle = await psEmailSubscriptionPage.getPageSubtitle(page);
      await expect(pageTitle).to.contains(psEmailSubscription.name);
    });

    it('should check customer registration to newsletter', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `checkCustomerRegistrationAfter${test.args.action}`,
        baseContext,
      );

      // Get list of emails registered to newsletter
      const listOfEmails = await psEmailSubscriptionPage.getListOfNewsletterRegistrationEmails(page);

      if (test.args.value) {
        await expect(listOfEmails).to.include(DefaultAccount.email);
      } else {
        await expect(listOfEmails).to.not.include(DefaultAccount.email);
      }
    });

    it('should go to customers page', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `gotoCustomersPageAfterCheck${test.args.action}`,
        baseContext,
      );

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
