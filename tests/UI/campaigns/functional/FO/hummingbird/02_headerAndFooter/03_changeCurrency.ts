// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';
import {createCurrencyTest, deleteCurrencyTest} from '@commonTests/BO/international/currency';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import currenciesPage from '@pages/BO/international/currencies';
import localizationPage from '@pages/BO/international/localization';
// Import FO pages
import homePage from '@pages/FO/hummingbird/home';
import searchResultsPage from '@pages/FO/hummingbird/searchResults';

// Import data
import Currencies from '@data/demo/currencies';
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_hummingbird_headerAndFooter_changeCurrency';

/*
Pre-condition: Check the block currencies is not displayed in header of FO
Pre-condition: Create a new currency
Scenario:
 - Go to FO and check that the currencies list is not visible in the header
 - Create new currency
 - Go to FO and switch to the new currency
 - Check that the price of the product is changed
 - Switch back to the original currency and check the price of the first product
Post-condition:
- Delete the created currency
*/

describe('FO - Header and Footer : Change currency', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let exchangeRateValue: number = 0;

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest_1`);

  describe('Check links in header page', async () => {
    before(async function () {
      browserContext = await helper.createBrowserContext(this.browser);
      page = await helper.newTab(browserContext);
    });

    after(async () => {
      await helper.closeBrowserContext(browserContext);
    });

    // Pre-condition: Check the block currencies is not displayed in header of FO
    describe('PRE-TEST: Check that the block currencies is not displayed in header of FO', async () => {
      it('should go to FO home page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

        await homePage.goToFo(page);

        const isHomePage = await homePage.isHomePage(page);
        expect(isHomePage).to.be.eq(true);
      });

      it('should check that the currencies block is not visible', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCurrenciesLink', baseContext);

        const isVisible = await homePage.isCurrencyVisible(page);
        expect(isVisible).to.be.eq(false);
      });
    });

    // Pre-condition: Create currency
    createCurrencyTest(Currencies.mad, `${baseContext}_preTest_2`);

    describe('Filter by iso code of currency and get the exchange rate value ', async () => {
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
        expect(pageTitle).to.contains(localizationPage.pageTitle);
      });

      it('should go to \'Currencies\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesPage', baseContext);

        await localizationPage.goToSubTabCurrencies(page);

        const pageTitle = await currenciesPage.getPageTitle(page);
        expect(pageTitle).to.contains(currenciesPage.pageTitle);
      });

      it(`should get the currency exchange rate value '${Currencies.mad.isoCode}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getExchangeRate', baseContext);

        // Filter
        await currenciesPage.filterTable(page, 'input', 'iso_code', Currencies.mad.isoCode);

        // Check exchange rate
        exchangeRateValue = await currenciesPage.getExchangeRateValue(page, 1);
        expect(exchangeRateValue).to.be.above(0);
      });
    });

    describe('Switch to another currency and check the product price', async () => {
      it('should go to FO home page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFO2', baseContext);

        await homePage.goToFo(page);

        const isHomePage = await homePage.isHomePage(page);
        expect(isHomePage).to.be.eq(true);
      });

      it('should change FO currency', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changeFoCurrency', baseContext);

        // Check currency
        await homePage.changeCurrency(page, Currencies.mad.isoCode, Currencies.mad.symbol);

        const shopCurrency = await homePage.getDefaultCurrency(page);
        expect(shopCurrency).to.contains(Currencies.mad.isoCode);
      });

      it('should search product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

        await homePage.searchProduct(page, Products.demo_11.name);

        const pageTitle = await searchResultsPage.getPageTitle(page);
        expect(pageTitle).to.equal(searchResultsPage.pageTitle);
      });

      it('should check the product price', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice', baseContext);

        const newExchangeRateValue = (exchangeRateValue * Products.demo_11.finalPrice).toFixed(Currencies.mad.decimals);

        const productPrice = await searchResultsPage.getProductPrice(page);
        expect(productPrice).to.contains(`${Currencies.mad.symbol}${newExchangeRateValue}`);
      });

      it('should switch back to the default currency', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          'switchBackDefaultFoCurrency',
          baseContext,
        );

        // Check currency
        await homePage.changeCurrency(page, Currencies.euro.isoCode, Currencies.euro.symbol);

        const shopCurrency = await homePage.getDefaultCurrency(page);
        expect(shopCurrency).to.contains(Currencies.euro.isoCode);
      });

      it('should check the product price', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice2', baseContext);

        const exchangeRate = Math.round(Currencies.euro.exchangeRate * Products.demo_11.finalPrice);

        const productPrice = await searchResultsPage.getProductPrice(page);
        expect(productPrice).to.contains(`${Currencies.euro.symbol}${exchangeRate}`);
      });
    });
  });

  // Post-condition - Delete currency
  deleteCurrencyTest(Currencies.mad, `${baseContext}_postTest_1`);

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest_2`);
});
