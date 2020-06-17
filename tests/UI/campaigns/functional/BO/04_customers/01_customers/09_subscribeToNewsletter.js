/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
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
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const CustomersPage = require('@pages/BO/customers');
const ModuleManagerPage = require('@pages/BO/modules/moduleManager');
const PsEmailSubscriptionPage = require('@pages/BO/modules/psEmailSubscription');


const baseContext = 'BO_customers_customers_subscribeToNewsletter';
let numberOfCustomers = 0;

let browserContext;
let page;

const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    customersPage: new CustomersPage(page),
    moduleManagerPage: new ModuleManagerPage(page),
    psEmailSubscriptionPage: new PsEmailSubscriptionPage(page),
  };
};

describe('Check customer subscription to newsletter from BO', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO
  loginCommon.loginBO();

  it('should go to customers page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.customersParentLink,
      this.pageObjects.dashboardPage.customersLink,
    );

    await this.pageObjects.customersPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.customersPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.customersPage.pageTitle);
  });

  it('should reset all filters and get Number of customers in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilters', baseContext);
    numberOfCustomers = await this.pageObjects.customersPage.resetAndGetNumberOfLines();
    await expect(numberOfCustomers).to.be.above(0);
  });

  it(`should filter by email ${DefaultAccount.email}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterByEmail', baseContext);

    await this.pageObjects.customersPage.filterCustomers(
      'input',
      'email',
      DefaultAccount.email,
    );

    const numberOfCustomersAfterFilter = await this.pageObjects.customersPage.getNumberOfElementInGrid();
    await expect(numberOfCustomersAfterFilter).to.equal(1);
  });

  const tests = [
    {args: {action: 'disable', value: false}},
    {args: {action: 'enable', value: true}},
  ];

  tests.forEach((test) => {
    it(`should ${test.args.action} newsletters`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}NewsLetters`, baseContext);

      await this.pageObjects.customersPage.updateToggleColumnValue(1, 'newsletter', test.args.value);

      const newsletterStatus = await this.pageObjects.customersPage.getToggleColumnValue(1, 'newsletter');
      await expect(newsletterStatus).to.be.equal(test.args.value);
    });

    it('should go to module manager page', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `goToModuleManageTo${test.args.action}`,
        baseContext,
      );

      await this.pageObjects.customersPage.goToSubMenu(
        this.pageObjects.customersPage.modulesParentLink,
        this.pageObjects.customersPage.moduleManagerLink,
      );

      const pageTitle = await this.pageObjects.moduleManagerPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.moduleManagerPage.pageTitle);
    });

    it(`should go to '${psEmailSubscription.name}' module`, async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `goToEmailSubscriptionModulePageAfter${test.args.action}`,
        baseContext,
      );

      // Search and go to configure module page
      await this.pageObjects.moduleManagerPage.searchModule(psEmailSubscription.tag, psEmailSubscription.name);
      await this.pageObjects.moduleManagerPage.goToConfigurationPage(psEmailSubscription.name);

      const pageTitle = await this.pageObjects.psEmailSubscriptionPage.getPageTitle();
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
      const listOfEmails = await this.pageObjects.psEmailSubscriptionPage.getListOfNewsletterRegistrationEmails();

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

      await this.pageObjects.psEmailSubscriptionPage.goToSubMenu(
        this.pageObjects.psEmailSubscriptionPage.customersParentLink,
        this.pageObjects.psEmailSubscriptionPage.customersLink,
      );

      const pageTitle = await this.pageObjects.customersPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.customersPage.pageTitle);
    });
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetAfterAll', baseContext);

    const numberOfCustomersAfterReset = await this.pageObjects.customersPage.resetAndGetNumberOfLines();
    await expect(numberOfCustomersAfterReset).to.equal(numberOfCustomers);
  });
});
