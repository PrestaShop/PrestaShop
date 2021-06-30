require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const customersPage = require('@pages/BO/customers');

// Import data
const {DefaultCustomer} = require('@data/demo/customer');

const baseContext = 'functional_BO_customers_customers_filterAndQuickEditCustomers';

let browserContext;
let page;
let numberOfCustomers = 0;

// Get today date format (yyy-mm-dd)
const today = new Date();
const mm = (`0${today.getMonth() + 1}`).slice(-2); // Current month
const dd = (`0${today.getDate()}`).slice(-2); // Current day
const yyyy = today.getFullYear(); // Current year
const dateToday = `${yyyy}-${mm}-${dd}`;

// Filter and quick edit customers
describe('BO - Customers : Filter and quick edit customers', async () => {
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
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

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
            filterValue: DefaultCustomer.id,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterSocialTitle',
            filterType: 'select',
            filterBy: 'social_title',
            filterValue: DefaultCustomer.socialTitle,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterFirstName',
            filterType: 'input',
            filterBy: 'firstname',
            filterValue: DefaultCustomer.firstName,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterLastName',
            filterType: 'input',
            filterBy: 'lastname',
            filterValue: DefaultCustomer.lastName,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterEmail',
            filterType: 'input',
            filterBy: 'email',
            filterValue: DefaultCustomer.email,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterActive',
            filterType: 'select',
            filterBy: 'active',
            filterValue: DefaultCustomer.enabled,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterNewsletter',
            filterType: 'select',
            filterBy: 'newsletter',
            filterValue: DefaultCustomer.newsletter,
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
          switch (test.args.filterBy) {
            case 'active': {
              const customerStatus = await customersPage.getCustomerStatus(page, i);
              await expect(customerStatus).to.equal(test.args.filterValue);
              break;
            }

            case 'newsletter': {
              const newsletterStatus = await customersPage.getNewsletterStatus(page, i);
              await expect(newsletterStatus).to.equal(test.args.filterValue);
              break;
            }

            case 'optin': {
              const partnerOffersStatus = await customersPage.getPartnerOffersStatus(page, i);
              await expect(partnerOffersStatus).to.equal(test.args.filterValue);
              break;
            }

            default: {
              const textColumn = await customersPage.getTextColumnFromTableCustomers(
                page,
                i,
                test.args.filterBy,
              );

              await expect(textColumn).to.contains(test.args.filterValue);
              break;
            }
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfCustomersAfterReset = await customersPage.resetAndGetNumberOfLines(page);
        await expect(numberOfCustomersAfterReset).to.equal(numberOfCustomers);
      });
    });

    it('should filter by registration \'Date from\' and \'Date to\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByDate', baseContext);

      // Filter orders
      await customersPage.filterCustomersByRegistration(page, dateToday, dateToday);

      // Get number of elements
      const numberOfCustomersAfterFilter = await customersPage.getNumberOfElementInGrid(page);

      for (let i = 1; i <= numberOfCustomersAfterFilter; i++) {
        const textColumn = await customersPage.getTextColumn(page, 'date_add', i);
        await expect(textColumn).to.contains(dateToday);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numberOfCustomersAfterReset = await customersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCustomersAfterReset).to.equal(numberOfCustomers);
    });
  });

  // 2 : Editing customers from grid table
  describe('Quick edit customers', async () => {
    it(`should filter by Email '${DefaultCustomer.email}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      await customersPage.filterCustomers(
        page,
        'input',
        'email',
        DefaultCustomer.email,
      );

      const numberOfCustomersAfterFilter = await customersPage.getNumberOfElementInGrid(page);
      await expect(numberOfCustomersAfterFilter).to.be.at.above(0);
    });

    [
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
    ].forEach((test) => {
      it(`should ${test.args.action} '${test.args.column}' column for customer`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}`, baseContext);

        const isActionPerformed = await customersPage.updateToggleColumnValue(
          page,
          1,
          test.args.column,
          test.args.value,
        );

        if (isActionPerformed) {
          const resultMessage = await customersPage.getAlertSuccessBlockParagraphContent(page);
          await expect(resultMessage).to.contains(customersPage.successfulUpdateStatusMessage);
        }

        const customerStatus = await customersPage.getToggleColumnValue(page, 1, test.args.column);
        await expect(customerStatus).to.be.equal(test.args.value);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterQuickEdit', baseContext);

      const numberOfCustomersAfterReset = await customersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCustomersAfterReset).to.be.equal(numberOfCustomers);
    });
  });
});
