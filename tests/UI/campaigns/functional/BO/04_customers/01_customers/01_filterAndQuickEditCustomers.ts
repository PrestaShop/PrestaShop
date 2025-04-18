// Import utils
import testContext from '@utils/testContext';

import {
  boCustomersPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCustomers,
  dataGroups,
  type Page,
  utilsDate,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_BO_customers_customers_filterAndQuickEditCustomers';

/*
Filter customers table by Id, social title, first name, last name, email, active, newsletter and optin
Quick edit customer enable/disable - status, newsletter and partner offers
 */
describe('BO - Customers - Customers : Filter and quick edit', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCustomers: number = 0;

  const today: string = utilsDate.getDateFormat('mm/dd/yyyy');

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
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

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
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCustomers = await boCustomersPage.resetAndGetNumberOfLines(page);
    expect(numberOfCustomers).to.be.above(0);
  });

  // 1 : Filter Customers with all inputs and selects in grid table
  describe('Filter customers table', async () => {
    [
      {
        testIdentifier: 'filterId',
        filterType: 'input',
        filterBy: 'id_customer',
        filterValue: dataCustomers.johnDoe.id.toString(),
      },
      {
        testIdentifier: 'filterSocialTitle',
        filterType: 'select',
        filterBy: 'social_title',
        filterValue: dataCustomers.johnDoe.socialTitle,
      },
      {
        testIdentifier: 'filterFirstName',
        filterType: 'input',
        filterBy: 'firstname',
        filterValue: dataCustomers.johnDoe.firstName,
      },
      {
        testIdentifier: 'filterLastName',
        filterType: 'input',
        filterBy: 'lastname',
        filterValue: dataCustomers.johnDoe.lastName,
      },
      {
        testIdentifier: 'filterEmail',
        filterType: 'input',
        filterBy: 'email',
        filterValue: dataCustomers.johnDoe.email,
      },
      {
        testIdentifier: 'filterGroup',
        filterType: 'select',
        filterBy: 'default_group',
        filterValue: dataGroups.guest.name,
      },
      {
        testIdentifier: 'filterActive',
        filterType: 'select',
        filterBy: 'active',
        filterValue: dataCustomers.johnDoe.enabled ? '1' : '0',
      },
      {
        testIdentifier: 'filterNewsletter',
        filterType: 'select',
        filterBy: 'newsletter',
        filterValue: dataCustomers.johnDoe.newsletter ? '1' : '0',
      },
      {
        testIdentifier: 'filterPartnerOffers',
        filterType: 'select',
        filterBy: 'optin',
        filterValue: dataCustomers.johnDoe.partnerOffers ? '1' : '0',
      },
    ].forEach((arg: {testIdentifier: string, filterType: string, filterBy: string, filterValue: string}) => {
      it(`should filter by ${arg.filterBy} '${arg.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${arg.testIdentifier}`, baseContext);

        if (['filterActive', 'filterNewsletter', 'filterPartnerOffers'].includes(arg.testIdentifier)) {
          await boCustomersPage.filterCustomersSwitch(
            page,
            arg.filterBy,
            arg.filterValue,
          );
        } else {
          await boCustomersPage.filterCustomers(
            page,
            arg.filterType,
            arg.filterBy,
            arg.filterValue,
          );
        }
        const numberOfCustomersAfterFilter = await boCustomersPage.getNumberOfElementInGrid(page);
        expect(numberOfCustomersAfterFilter).to.be.at.most(numberOfCustomers);

        for (let i = 1; i <= numberOfCustomersAfterFilter; i++) {
          switch (arg.filterBy) {
            case 'active': {
              const customerStatus = await boCustomersPage.getCustomerStatus(page, i);
              expect(customerStatus).to.equals(arg.filterValue === '1');
              break;
            }

            case 'newsletter': {
              const newsletterStatus = await boCustomersPage.getNewsletterStatus(page, i);
              expect(newsletterStatus).to.equals(arg.filterValue === '1');
              break;
            }

            case 'optin': {
              const partnerOffersStatus = await boCustomersPage.getPartnerOffersStatus(page, i);
              expect(partnerOffersStatus).to.equals(arg.filterValue === '1');
              break;
            }

            default: {
              const textColumn = await boCustomersPage.getTextColumnFromTableCustomers(
                page,
                i,
                arg.filterBy,
              );
              expect(textColumn).to.contains(arg.filterValue);
              break;
            }
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${arg.testIdentifier}Reset`, baseContext);

        const numberOfCustomersAfterReset = await boCustomersPage.resetAndGetNumberOfLines(page);
        expect(numberOfCustomersAfterReset).to.equals(numberOfCustomers);
      });
    });

    it('should filter by registration \'Date from\' and \'Date to\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByDate', baseContext);

      // Filter orders
      await boCustomersPage.filterCustomersByRegistration(page, today, today);
      // Get number of elements
      const numberOfCustomersAfterFilter = await boCustomersPage.getNumberOfElementInGrid(page);

      for (let i = 1; i <= numberOfCustomersAfterFilter; i++) {
        const textColumn = await boCustomersPage.getTextColumnFromTableCustomers(page, i, 'date_add');
        expect(textColumn).to.contains(today);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numberOfCustomersAfterReset = await boCustomersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomersAfterReset).to.equal(numberOfCustomers);
    });
  });

  // 2 : Editing customers from grid table
  describe('Quick edit customers', async () => {
    it(`should filter by email '${dataCustomers.johnDoe.email}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      await boCustomersPage.filterCustomers(
        page,
        'input',
        'email',
        dataCustomers.johnDoe.email,
      );

      const numberOfCustomersAfterFilter = await boCustomersPage.getNumberOfElementInGrid(page);
      expect(numberOfCustomersAfterFilter).to.be.at.above(0);
    });

    describe('Quick edit customer status', async () => {
      [
        {args: {action: 'disable', value: false}},
        {args: {action: 'enable', value: true}},
      ].forEach((test, index) => {
        it(`should ${test.args.action} customer status`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `quickEditStatus${index}`, baseContext);

          const resultMessage = await boCustomersPage.setCustomerStatus(page, 1, test.args.value);

          if (resultMessage) {
            expect(resultMessage).to.contains(boCustomersPage.successfulUpdateStatusMessage);
          }

          const customerStatus = await boCustomersPage.getCustomerStatus(page, 1);
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

          const resultMessage = await boCustomersPage.setNewsletterStatus(page, 1, test.args.value);

          if (resultMessage) {
            expect(resultMessage).to.contains(boCustomersPage.successfulUpdateStatusMessage);
          }

          const customerStatus = await boCustomersPage.getNewsletterStatus(page, 1);
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

          const resultMessage = await boCustomersPage.setPartnerOffersStatus(page, 1, test.args.value);

          if (resultMessage) {
            expect(resultMessage).to.contains(boCustomersPage.successfulUpdateStatusMessage);
          }

          const customerStatus = await boCustomersPage.getPartnerOffersStatus(page, 1);
          expect(customerStatus).to.be.equal(test.args.value);
        });
      });
    });

    after(async () => {
      await boCustomersPage.resetAndGetNumberOfLines(page);
    });
  });
});
