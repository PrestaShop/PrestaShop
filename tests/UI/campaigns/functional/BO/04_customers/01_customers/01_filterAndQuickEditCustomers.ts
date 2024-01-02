// Import utils
import date from '@utils/date';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import customersPage from '@pages/BO/customers';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import Customers from '@data/demo/customers';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_customers_customers_filterAndQuickEditCustomers';

/*
Filter customers table by Id, social title, first name, last name, email, active, newsletter and optin
Quick edit customer enable/disable - status, newsletter and partner offers
 */
describe('BO - Customers - Customers : Filter and quick edit Customers table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCustomers: number = 0;

  const today: string = date.getDateFormat('mm/dd/yyyy');

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
    expect(pageTitle).to.contains(customersPage.pageTitle);
  });

  it('should reset all filters and get number of customers in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCustomers = await customersPage.resetAndGetNumberOfLines(page);
    expect(numberOfCustomers).to.be.above(0);
  });

  // 1 : Filter Customers with all inputs and selects in grid table
  describe('Filter customers table', async () => {
    [
      {
        args:
          {
            testIdentifier: 'filterId',
            filterType: 'input',
            filterBy: 'id_customer',
            filterValue: Customers.johnDoe.id.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterSocialTitle',
            filterType: 'select',
            filterBy: 'social_title',
            filterValue: Customers.johnDoe.socialTitle,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterFirstName',
            filterType: 'input',
            filterBy: 'firstname',
            filterValue: Customers.johnDoe.firstName,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterLastName',
            filterType: 'input',
            filterBy: 'lastname',
            filterValue: Customers.johnDoe.lastName,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterEmail',
            filterType: 'input',
            filterBy: 'email',
            filterValue: Customers.johnDoe.email,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterActive',
            filterType: 'select',
            filterBy: 'active',
            filterValue: Customers.johnDoe.enabled ? '1' : '0',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterNewsletter',
            filterType: 'select',
            filterBy: 'newsletter',
            filterValue: Customers.johnDoe.newsletter ? '1' : '0',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterOptin',
            filterType: 'select',
            filterBy: 'optin',
            filterValue: '0',
          },
      },
    ].forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}`, baseContext);

        if (['filterActive', 'filterNewsletter', 'filterOptin'].includes(test.args.testIdentifier)) {
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
        expect(numberOfCustomersAfterFilter).to.be.at.most(numberOfCustomers);

        for (let i = 1; i <= numberOfCustomersAfterFilter; i++) {
          switch (test.args.filterBy) {
            case 'active': {
              const customerStatus = await customersPage.getCustomerStatus(page, i);
              expect(customerStatus).to.equals(test.args.filterValue === '1');
              break;
            }

            case 'newsletter': {
              const newsletterStatus = await customersPage.getNewsletterStatus(page, i);
              expect(newsletterStatus).to.equals(test.args.filterValue === '1');
              break;
            }

            case 'optin': {
              const partnerOffersStatus = await customersPage.getPartnerOffersStatus(page, i);
              expect(partnerOffersStatus).to.equals(test.args.filterValue === '1');
              break;
            }

            default: {
              const textColumn = await customersPage.getTextColumnFromTableCustomers(
                page,
                i,
                test.args.filterBy,
              );
              expect(textColumn).to.contains(test.args.filterValue);
              break;
            }
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfCustomersAfterReset = await customersPage.resetAndGetNumberOfLines(page);
        expect(numberOfCustomersAfterReset).to.equals(numberOfCustomers);
      });
    });

    it('should filter by registration \'Date from\' and \'Date to\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByDate', baseContext);

      // Filter orders
      await customersPage.filterCustomersByRegistration(page, today, today);
      // Get number of elements
      const numberOfCustomersAfterFilter = await customersPage.getNumberOfElementInGrid(page);

      for (let i = 1; i <= numberOfCustomersAfterFilter; i++) {
        const textColumn = await customersPage.getTextColumnFromTableCustomers(page, i, 'date_add');
        expect(textColumn).to.contains(today);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numberOfCustomersAfterReset = await customersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomersAfterReset).to.equal(numberOfCustomers);
    });
  });

  // 2 : Editing customers from grid table
  describe('Quick edit customers', async () => {
    it(`should filter by email '${Customers.johnDoe.email}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      await customersPage.filterCustomers(
        page,
        'input',
        'email',
        Customers.johnDoe.email,
      );

      const numberOfCustomersAfterFilter = await customersPage.getNumberOfElementInGrid(page);
      expect(numberOfCustomersAfterFilter).to.be.at.above(0);
    });

    describe('Quick edit customer status', async () => {
      [
        {args: {action: 'disable', value: false}},
        {args: {action: 'enable', value: true}},
      ].forEach((test, index) => {
        it(`should ${test.args.action} customer status`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `quickEditStatus${index}`, baseContext);

          const resultMessage = await customersPage.setCustomerStatus(page, 1, test.args.value);

          if (resultMessage) {
            expect(resultMessage).to.contains(customersPage.successfulUpdateStatusMessage);
          }

          const customerStatus = await customersPage.getCustomerStatus(page, 1);
          expect(customerStatus).to.equals(test.args.value);
        });
      });
    });

    describe('Quick edit newsletter status', async () => {
      [
        {args: {action: 'enable', value: true}},
        {args: {action: 'disable', value: false}},
      ].forEach((test, index) => {
        it(`should ${test.args.action} customer newsletter status`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `quickEditNewsletter${index}`, baseContext);

          const resultMessage = await customersPage.setNewsletterStatus(page, 1, test.args.value);

          if (resultMessage) {
            expect(resultMessage).to.contains(customersPage.successfulUpdateStatusMessage);
          }

          const customerStatus = await customersPage.getNewsletterStatus(page, 1);
          expect(customerStatus).to.equals(test.args.value);
        });
      });
    });

    describe('Quick edit partner offers status', async () => {
      [
        {args: {action: 'enable', value: true}},
        {args: {action: 'disable', value: false}},
      ].forEach((test, index) => {
        it(`should ${test.args.action} customer partner offers status`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `quickEditOptin${index}`, baseContext);

          const resultMessage = await customersPage.setPartnerOffersStatus(page, 1, test.args.value);

          if (resultMessage) {
            expect(resultMessage).to.contains(customersPage.successfulUpdateStatusMessage);
          }

          const customerStatus = await customersPage.getPartnerOffersStatus(page, 1);
          expect(customerStatus).to.be.equal(test.args.value);
        });
      });
    });

    after(async () => {
      await customersPage.resetAndGetNumberOfLines(page);
    });
  });
});
