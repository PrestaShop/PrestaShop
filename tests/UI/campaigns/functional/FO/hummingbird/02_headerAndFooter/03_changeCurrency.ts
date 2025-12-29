// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';
import {createCurrencyTest, deleteCurrencyTest} from '@commonTests/BO/international/currency';

// Import pages
import {
  boDashboardPage,
  boLoginPage,
  boLocalizationPage,
  boCurrenciesPage,
  type BrowserContext,
  dataCurrencies,
  dataProducts,
  foHummingbirdHomePage,
  foHummingbirdSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

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
  enableTheme('hummingbird', `${baseContext}_preTest_1`);

  describe('Check links in header page', async () => {
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    // Pre-condition: Check the block currencies is not displayed in header of FO
    describe('PRE-TEST: Check that the block currencies is not displayed in header of FO', async () => {
      it('should go to FO home page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

        await foHummingbirdHomePage.goToFo(page);

        const isHomePage = await foHummingbirdHomePage.isHomePage(page);
        expect(isHomePage).to.be.eq(true);
      });

      it('should check that the currencies block is not visible', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCurrenciesLink', baseContext);

        const isVisible = await foHummingbirdHomePage.isCurrencyVisible(page);
        expect(isVisible).to.be.eq(false);
      });
    });

    // Pre-condition: Create currency
    createCurrencyTest(dataCurrencies.mad, `${baseContext}_preTest_2`);

    describe('Filter by iso code of currency and get the exchange rate value ', async () => {
      it('should login in BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

        await boLoginPage.goTo(page, global.BO.URL);
        await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(boDashboardPage.pageTitle);
      });

      it('should go to \'International > Localization\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.internationalParentLink,
          boDashboardPage.localizationLink,
        );
        await boLocalizationPage.closeSfToolBar(page);

        const pageTitle = await boLocalizationPage.getPageTitle(page);
        expect(pageTitle).to.contains(boLocalizationPage.pageTitle);
      });

      it('should go to \'Currencies\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesPage', baseContext);

        await boLocalizationPage.goToSubTabCurrencies(page);

        const pageTitle = await boCurrenciesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCurrenciesPage.pageTitle);
      });

      it(`should get the currency exchange rate value '${dataCurrencies.mad.isoCode}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getExchangeRate', baseContext);

        // Filter
        await boCurrenciesPage.filterTable(page, 'input', 'iso_code', dataCurrencies.mad.isoCode);

        // Check exchange rate
        exchangeRateValue = await boCurrenciesPage.getExchangeRateValue(page, 1);
        expect(exchangeRateValue).to.be.above(0);
      });
    });

    describe('Switch to another currency and check the product price', async () => {
      it('should go to FO home page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFO2', baseContext);

        await foHummingbirdHomePage.goToFo(page);

        const isHomePage = await foHummingbirdHomePage.isHomePage(page);
        expect(isHomePage).to.be.eq(true);
      });

      it('should change FO currency', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changeFoCurrency', baseContext);

        // Check currency
        await foHummingbirdHomePage.changeCurrency(page, dataCurrencies.mad.isoCode, dataCurrencies.mad.symbol);

        const shopCurrency = await foHummingbirdHomePage.getDefaultCurrency(page);
        expect(shopCurrency).to.contains(dataCurrencies.mad.isoCode);
      });

      it('should search product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

        await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_11.name);

        const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
        expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);
      });

      it('should check the product price', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice', baseContext);

        const newExchangeRateValue = (exchangeRateValue * dataProducts.demo_11.finalPrice).toFixed(dataCurrencies.mad.decimals);

        const productPrice = await foHummingbirdSearchResultsPage.getProductPrice(page);
        expect(productPrice).to.contains(`${dataCurrencies.mad.symbol}${newExchangeRateValue}`);
      });

      it('should switch back to the default currency', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          'switchBackDefaultFoCurrency',
          baseContext,
        );

        // Check currency
        await foHummingbirdHomePage.changeCurrency(page, dataCurrencies.euro.isoCode, dataCurrencies.euro.symbol);

        const shopCurrency = await foHummingbirdHomePage.getDefaultCurrency(page);
        expect(shopCurrency).to.contains(dataCurrencies.euro.isoCode);
      });

      it('should check the product price', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice2', baseContext);

        const exchangeRate = Math.round(dataCurrencies.euro.exchangeRate * dataProducts.demo_11.finalPrice);

        const productPrice = await foHummingbirdSearchResultsPage.getProductPrice(page);
        expect(productPrice).to.contains(`${dataCurrencies.euro.symbol}${exchangeRate}`);
      });
    });
  });

  // Post-condition - Delete currency
  deleteCurrencyTest(dataCurrencies.mad, `${baseContext}_postTest_1`);

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest_2`);
});
