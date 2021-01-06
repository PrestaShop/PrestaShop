require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const localizationPage = require('@pages/BO/international/localization');
const currenciesPage = require('@pages/BO/international/currencies');
const addCurrencyPage = require('@pages/BO/international/currencies/add');
const foHomePage = require('@pages/FO/home');

// Import Data
const {Currencies} = require('@data/demo/currencies');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_currencies_CreateOfficialCurrency';

let browserContext;
let page;
let numberOfCurrencies = 0;

/*
 */
describe('Create official currency and check it in FO', async () => {
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

  it('should go to localization page', async function () {
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

  it('should go to currencies page', async function () {
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

  // Filter currencies with all inputs and selects in grid table
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

        // Check number of currencies
        const numberOfCurrenciesAfterFilter = await currenciesPage.getNumberOfElementInGrid(page);
        await expect(numberOfCurrenciesAfterFilter).to.be.at.most(numberOfCurrencies + 11);
      });

      it('should reset filter', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilter${index}`, baseContext);

        const numberOfCurrenciesAfterReset = await currenciesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfCurrenciesAfterReset).to.be.equal(numberOfCurrencies + 10);
      });
    });
  });
});
