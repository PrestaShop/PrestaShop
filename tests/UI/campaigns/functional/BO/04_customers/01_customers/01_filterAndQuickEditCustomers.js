require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import data
const {DefaultAccount} = require('@data/demo/customer');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const customersPage = require('@pages/BO/customers');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_customers_customers_filterAndQuickEditCustomers';


let browserContext;
let page;
let numberOfCustomers = 0;

// Filter And Quick Edit Customers
describe('Filter And Quick Edit Customers', async () => {
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

  it('should go to Customers page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.customersParentLink,
      dashboardPage.customersLink,
    );

    const pageTitle = await customersPage.getPageTitle(page);
    await expect(pageTitle).to.contains(customersPage.pageTitle);
  });

  it('should reset all filters and get Number of customers in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCustomers = await customersPage.resetAndGetNumberOfLines(page);
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
      },
      {
        args:
          {
            testIdentifier: 'filterNewsletter',
            filterType: 'select',
            filterBy: 'newsletter',
            filterValue: DefaultAccount.newsletter,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterOptin',
            filterType: 'select',
            filterBy: 'optin',
            filterValue: false,
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}`, baseContext);

        if (typeof test.args.filterValue === 'boolean') {
          await customersPage.filterCustomersSwitch(
            page,
            test.args.filterBy,
            test.args.filterValue,
          );
        } else {
          await customersPage.filterCustomers(
            page,
            test.args.filterType,
            test.args.filterBy,
            test.args.filterValue,
          );
        }
        const numberOfCustomersAfterFilter = await customersPage.getNumberOfElementInGrid(page);

        await expect(numberOfCustomersAfterFilter).to.be.at.most(numberOfCustomers);

        for (let i = 1; i <= numberOfCustomersAfterFilter; i++) {
          if (typeof test.args.filterValue === 'boolean') {
            const toggleValue = await customersPage.getToggleColumnValue(page, i, test.args.filterBy);
            await expect(toggleValue).to.equal(test.args.filterValue);
          } else {
            const textColumn = await customersPage.getTextColumnFromTableCustomers(
              page,
              i,
              test.args.filterBy,
            );

            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfCustomersAfterReset = await customersPage.resetAndGetNumberOfLines(page);
        await expect(numberOfCustomersAfterReset).to.equal(numberOfCustomers);
      });
    });
  });

  // 2 : Editing customers from grid table
  describe('Quick Edit Customers', async () => {
    it('should filter by Email \'pub@prestashop.com\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      await customersPage.filterCustomers(
        page,
        'input',
        'email',
        DefaultAccount.email,
      );

      const numberOfCustomersAfterFilter = await customersPage.getNumberOfElementInGrid(page);
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

        const isActionPerformed = await customersPage.updateToggleColumnValue(
          page,
          1,
          test.args.column,
          test.args.value,
        );

        if (isActionPerformed) {
          const resultMessage = await customersPage.getTextContent(
            page,
            customersPage.alertSuccessBlockParagraph,
          );
          await expect(resultMessage).to.contains(customersPage.successfulUpdateStatusMessage);
        }

        const customerStatus = await customersPage.getToggleColumnValue(page, 1, test.args.column);
        await expect(customerStatus).to.be.equal(test.args.value);
      });
    });
  });
});
