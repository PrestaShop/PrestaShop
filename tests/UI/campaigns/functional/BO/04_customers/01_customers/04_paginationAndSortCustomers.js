require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const customersPage = require('@pages/BO/customers');
const addCustomerPage = require('@pages/BO/customers/add');

// Import data
const CustomerFaker = require('@data/faker/customer');

const baseContext = 'functional_BO_customers_customers_paginationAndSortCustomers';

let browserContext;
let page;
let numberOfCustomers = 0;

/*
Create 10 customers
Paginate between pages
Sort customers table
Delete customers with bulk actions
 */
describe('BO - Customers - Customers : Pagination and sort customers table', async () => {
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

    await dashboardPage.closeSfToolBar(page);

    const pageTitle = await customersPage.getPageTitle(page);
    await expect(pageTitle).to.contains(customersPage.pageTitle);
  });

  it('should reset all filters and get number of customers in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCustomers = await customersPage.resetAndGetNumberOfLines(page);
    await expect(numberOfCustomers).to.be.above(0);
  });

  // 1 : Create 10 new customers
  describe('Create 10 customers in BO', async () => {
    const creationTests = new Array(10).fill(0, 0, 10);

    creationTests.forEach((test, index) => {
      const createCustomerData = new CustomerFaker({email: `test@prestashop.com${index}`});

      it('should go to add new customer page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewCustomerPage${index}`, baseContext);

        await customersPage.goToAddNewCustomerPage(page);
        const pageTitle = await addCustomerPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addCustomerPage.pageTitleCreate);
      });

      it(`should create customer n°${index + 1} and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCustomer${index}`, baseContext);

        const textResult = await addCustomerPage.createEditCustomer(page, createCustomerData);
        await expect(textResult).to.equal(customersPage.successfulCreationMessage);

        const numberOfCustomersAfterCreation = await customersPage.getNumberOfElementInGrid(page);
        await expect(numberOfCustomersAfterCreation).to.be.equal(numberOfCustomers + 1 + index);
      });
    });
  });

  // 2 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await customersPage.selectPaginationLimit(page, '10');
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await customersPage.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await customersPage.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await customersPage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  // 3 : Sort customers
  describe('Sort customers table', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_customer', sortDirection: 'desc', isFloat: true,
        },
      },
      {args: {testIdentifier: 'sortBySocialTitleAsc', sortBy: 'social_title', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortBySocialTitleDesc', sortBy: 'social_title', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByFirstNameAsc', sortBy: 'firstname', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByFirstNameDesc', sortBy: 'firstname', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByLastNameCodeAsc', sortBy: 'lastname', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortLastNameDesc', sortBy: 'lastname', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByEmailAsc', sortBy: 'email', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByEmailDesc', sortBy: 'email', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortBySalesAsc', sortBy: 'total_spent', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortBySalesDesc', sortBy: 'total_spent', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByEnabledAsc', sortBy: 'active', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByEnabledDesc', sortBy: 'active', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByNewslettersAsc', sortBy: 'newsletter', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByNewslettersDesc', sortBy: 'newsletter', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByPartnerOffersAsc', sortBy: 'optin', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByPartnerOffersDesc', sortBy: 'optin', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByRegistrationAsc', sortBy: 'date_add', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByRegistrationDesc', sortBy: 'date_add', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByLastVisitAsc', sortBy: 'connect', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByLastVisitDesc', sortBy: 'connect', sortDirection: 'desc'}},
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_customer', sortDirection: 'asc', isFloat: true,
        },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await customersPage.getAllRowsColumnContent(page, test.args.sortBy);

        await customersPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await customersPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await customersPage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'asc') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  // 4 : Delete customers created with bulk actions
  describe('Delete customers with bulk actions', async () => {
    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await customersPage.filterCustomers(
        page,
        'input',
        'email',
        'test@prestashop.com',
      );

      const textResult = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      await expect(textResult).to.contains('test@prestashop.com');
    });

    it('should delete customers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCustomers', baseContext);

      const deleteTextResult = await customersPage.deleteCustomersBulkActions(page);
      await expect(deleteTextResult).to.be.equal(customersPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkDelete', baseContext);

      const numberOfCustomersAfterReset = await customersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCustomersAfterReset).to.be.equal(numberOfCustomers);
    });
  });
});
