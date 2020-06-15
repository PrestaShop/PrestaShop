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
const loginCommon = require('@commonTests/loginBO');

// Import data
const {DefaultAccount} = require('@data/demo/customer');
// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const CustomersPage = require('@pages/BO/customers');
// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_customers_customers_filterAndQuickEditCustomers';


let browserContext;
let page;
let numberOfCustomers = 0;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    customersPage: new CustomersPage(page),
  };
};

// Filter And Quick Edit Customers
describe('Filter And Quick Edit Customers', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO and go to customers page
  loginCommon.loginBO();

  it('should go to Customers page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.customersParentLink,
      this.pageObjects.dashboardPage.customersLink,
    );

    const pageTitle = await this.pageObjects.customersPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.customersPage.pageTitle);
  });

  it('should reset all filters and get Number of customers in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCustomers = await this.pageObjects.customersPage.resetAndGetNumberOfLines();
    await expect(numberOfCustomers).to.be.above(0);
  });

  // 1 : Filter Customers with all inputs and selects in grid table
  describe('Filter Customers', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterId',
            filterType: 'input',
            filterBy: 'id_customer',
            filterValue: DefaultAccount.id,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterSocialTitle',
            filterType: 'select',
            filterBy: 'social_title',
            filterValue: DefaultAccount.socialTitle,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterFirstName',
            filterType: 'input',
            filterBy: 'firstname',
            filterValue: DefaultAccount.firstName,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterLastName',
            filterType: 'input',
            filterBy: 'lastname',
            filterValue: DefaultAccount.lastName,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterEmail',
            filterType: 'input',
            filterBy: 'email',
            filterValue: DefaultAccount.email,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterActive',
            filterType: 'select',
            filterBy: 'active',
            filterValue: DefaultAccount.enabled,
          },
        expected: 'check',
      },
      {
        args:
          {
            testIdentifier: 'filterNewsletter',
            filterType: 'select',
            filterBy: 'newsletter',
            filterValue: DefaultAccount.newsletter,
          },
        expected: 'check',
      },
      {
        args:
          {
            testIdentifier: 'filterOptin',
            filterType: 'select',
            filterBy: 'optin',
            filterValue: false,
          },
        expected: 'clear',
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}`, baseContext);

        if (typeof test.args.filterValue === 'boolean') {
          await this.pageObjects.customersPage.filterCustomersSwitch(
            test.args.filterBy,
            test.args.filterValue,
          );
        } else {
          await this.pageObjects.customersPage.filterCustomers(
            test.args.filterType,
            test.args.filterBy,
            test.args.filterValue,
          );
        }
        const numberOfCustomersAfterFilter = await this.pageObjects.customersPage.getNumberOfElementInGrid();

        await expect(numberOfCustomersAfterFilter).to.be.at.most(numberOfCustomers);

        for (let i = 1; i <= numberOfCustomersAfterFilter; i++) {
          const textColumn = await this.pageObjects.customersPage.getTextColumnFromTableCustomers(
            i,
            test.args.filterBy,
          );

          if (test.expected !== undefined) {
            await expect(textColumn).to.contains(test.expected);
          } else {
            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfCustomersAfterReset = await this.pageObjects.customersPage.resetAndGetNumberOfLines();
        await expect(numberOfCustomersAfterReset).to.equal(numberOfCustomers);
      });
    });
  });

  // 2 : Editing customers from grid table
  describe('Quick Edit Customers', async () => {
    it('should filter by Email \'pub@prestashop.com\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      await this.pageObjects.customersPage.filterCustomers(
        'input',
        'email',
        DefaultAccount.email,
      );

      const numberOfCustomersAfterFilter = await this.pageObjects.customersPage.getNumberOfElementInGrid();
      await expect(numberOfCustomersAfterFilter).to.be.at.above(0);
    });

    const tests = [
      {
        args: {
          testIdentifier: 'disableStatus', action: 'disable', column: 'active', value: false,
        },
      },
      {
        args: {
          testIdentifier: 'enableStatus', action: 'enable', column: 'active', value: true,
        },
      },
      {
        args: {
          testIdentifier: 'enableNewsletter', action: 'enable newsletter', column: 'newsletter', value: true,
        },
      },
      {
        args: {
          testIdentifier: 'disableNewsletter', action: 'disable newsletter', column: 'newsletter', value: false,
        },
      },
      {
        args: {
          testIdentifier: 'enablePartnerOffers', action: 'enable partner offers', column: 'optin', value: true,
        },
      },
      {
        args: {
          testIdentifier: 'disablePartnerOffers', action: 'disable partner offers', column: 'optin', value: false,
        },
      },
    ];

    tests.forEach((test) => {
      it(`should ${test.args.action} for first customer`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}`, baseContext);

        const isActionPerformed = await this.pageObjects.customersPage.updateToggleColumnValue(
          1,
          test.args.column,
          test.args.value,
        );

        if (isActionPerformed) {
          const resultMessage = await this.pageObjects.customersPage.getTextContent(
            this.pageObjects.customersPage.alertSuccessBlockParagraph,
          );
          await expect(resultMessage).to.contains(this.pageObjects.customersPage.successfulUpdateStatusMessage);
        }

        const customerStatus = await this.pageObjects.customersPage.getToggleColumnValue(1, test.args.column);
        await expect(customerStatus).to.be.equal(test.args.value);
      });
    });
  });
});
