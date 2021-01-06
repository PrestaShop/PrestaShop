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

      it('should create official currency', async function () {
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
});
