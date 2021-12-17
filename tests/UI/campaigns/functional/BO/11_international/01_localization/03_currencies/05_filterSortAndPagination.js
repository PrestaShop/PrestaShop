require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const localizationPage = require('@pages/BO/international/localization');
const currenciesPage = require('@pages/BO/international/currencies');
const addCurrencyPage = require('@pages/BO/international/currencies/add');

// Import Data
const {Currencies} = require('@data/demo/currencies');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_currencies_filterSortAndPagination';

let browserContext;
let page;
let numberOfCurrencies = 0;

/*
Create 10 official currencies
Filter currencies by: ID, currency, symbol, iso code, enabled
Pagination next and previous
Sort currencies by : ID, iso code, exchange rate, enabled
Delete the created currencies
 */
describe('BO - International - Currencies : Filter, sort and pagination', async () => {
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

  it('should go to \'International > Localization\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.internationalParentLink,
      dashboardPage.localizationLink,
    );

    await localizationPage.closeSfToolBar(page);

    const pageTitle = await localizationPage.getPageTitle(page);
    await expect(pageTitle).to.contains(localizationPage.pageTitle);
  });

  it('should go to \'Currencies\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesPage', baseContext);

    await localizationPage.goToSubTabCurrencies(page);

    const pageTitle = await currenciesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(currenciesPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfCurrencies = await currenciesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfCurrencies).to.be.above(0);
  });

  const currencies = [Currencies.mad, Currencies.all, Currencies.chileanPeso, Currencies.dzd, Currencies.tnd,
    Currencies.try, Currencies.usd, Currencies.aed, Currencies.lyd, Currencies.lsl,
  ];

  // 1 - Create 10 currencies
  currencies.forEach((currency, index) => {
    describe(`Create official currency '${currency.name}'`, async () => {
      it('should go to create new currency page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewCurrencyPage${index}`, baseContext);

        await currenciesPage.goToAddNewCurrencyPage(page);

        const pageTitle = await addCurrencyPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addCurrencyPage.pageTitle);
      });

      it('should create currency', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createOfficialCurrency${index}`, baseContext);

        // Create and check successful message
        const textResult = await addCurrencyPage.addOfficialCurrency(page, currency);
        await expect(textResult).to.contains(currenciesPage.successfulCreationMessage);

        // Check number of currencies after creation
        const numberOfCurrenciesAfterCreation = await currenciesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfCurrenciesAfterCreation).to.be.equal(numberOfCurrencies + 1 + index);
      });
    });
  });

  // 2 - Filter currencies table
  describe('Filter currencies', async () => {
    [
      {
        args:
          {
            testIdentifier: 'filterById',
            filterType: 'input',
            filterBy: 'id_currency',
            filterValue: 1,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByName',
            filterType: 'input',
            filterBy: 'name',
            filterValue: Currencies.all.name,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterBySymbol',
            filterType: 'input',
            filterBy: 'symbol',
            filterValue: Currencies.all.symbol,
          },

      },
      {
        args:
          {
            testIdentifier: 'filterByIsoCode',
            filterType: 'input',
            filterBy: 'iso_code',
            filterValue: Currencies.all.isoCode,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByEnabled',
            filterType: 'select',
            filterBy: 'active',
            filterValue: Currencies.all.enabled,
          },
      },
    ].forEach((test, index) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        // Filter
        await currenciesPage.filterTable(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );
        if (test.args.filterBy === 'active') {
          const currencyStatus = await currenciesPage.getStatus(page, 1);
          await expect(currencyStatus).to.be.equal(test.args.filterValue);
        } else {
          const currency = await currenciesPage.getTextColumnFromTableCurrency(page, 1, test.args.filterBy);
          await expect(currency).to.contains(test.args.filterValue);
        }

        // Check number of currencies
        const numberOfCurrenciesAfterFilter = await currenciesPage.getNumberOfElementInGrid(page);
        await expect(numberOfCurrenciesAfterFilter).to.be.most(numberOfCurrencies + 10);
      });

      it('should reset filter', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilter${index}`, baseContext);

        const numberOfCurrenciesAfterReset = await currenciesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfCurrenciesAfterReset).to.be.equal(numberOfCurrencies + 10);
      });
    });
  });

  // 3 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the item number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await currenciesPage.selectPaginationLimit(page, '10');
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await currenciesPage.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await currenciesPage.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await currenciesPage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  // 4 : Sort table
  describe('Sort currencies table', async () => {
    const tests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_currency', sortDirection: 'desc', isFloat: true,
        },
      },
      {args: {testIdentifier: 'sortByIsoCodeAsc', sortBy: 'iso_code', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByIsoCodeDesc', sortBy: 'iso_code', sortDirection: 'desc'}},
      {
        args: {
          testIdentifier: 'sortByExchangeRateAsc', sortBy: 'conversion_rate', sortDirection: 'asc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByExchangeRateDesc', sortBy: 'conversion_rate', sortDirection: 'desc', isFloat: true,
        },
      },
      {args: {testIdentifier: 'sortByEnabledAsc', sortBy: 'active', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByEnabledDesc', sortBy: 'active', sortDirection: 'desc'}},
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_currency', sortDirection: 'asc', isFloat: true,
        },
      },
    ];

    tests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await currenciesPage.getAllRowsColumnContent(page, test.args.sortBy);

        await currenciesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await currenciesPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await currenciesPage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'asc') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  // 4 : Delete currencies created
  describe('Delete currencies', async () => {
    currencies.forEach((currency, index) => {
      it(`should filter list by currency name '${currency.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterToDelete${index}`, baseContext);

        await currenciesPage.filterTable(page, 'input', 'name', currency.name);

        const currencyName = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'name');
        await expect(currencyName).to.contains(currency.name);
      });

      it('should delete currency', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteCurrency${index}`, baseContext);

        const result = await currenciesPage.deleteCurrency(page, 1);
        await expect(result).to.be.equal(currenciesPage.successfulDeleteMessage);
      });

      it('should reset filter', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilterAfterDelete${index}`, baseContext);

        const numberOfCurrenciesAfterReset = await currenciesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfCurrenciesAfterReset).to.be.equal(numberOfCurrencies + 10 - index - 1);
      });
    });
  });
});
