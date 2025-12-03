import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCartRulesPage,
  boCatalogPriceRulesCreatePage,
  boCatalogPriceRulesPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCurrencies,
  dataProducts,
  dataTaxes,
  FakerCatalogPriceRule,
  foClassicHomePage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  type Page,
  utilsCore,
  utilsDate,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_discounts_catalogPriceRules_reductionType';

describe('BO - Catalog price Rules : CRUD - Reduction type', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const pastDate: string = utilsDate.getDateFormat('yyyy-mm-dd', 'past');
  const futureDate: string = utilsDate.getDateFormat('yyyy-mm-dd', 'future');
  const catalogPriceRuleData0: FakerCatalogPriceRule = new FakerCatalogPriceRule({
    name: 'Test',
    currency: dataCurrencies.euro.name,
    country: 'All countries',
    group: 'All groups',
    reductionType: 'Amount',
    reductionTax: 'Tax excluded',
    reduction: 20,
    fromQuantity: 1,
    fromDate: pastDate,
    toDate: futureDate,
  });
  const catalogPriceRuleData1: FakerCatalogPriceRule = new FakerCatalogPriceRule({
    name: 'Test',
    currency: dataCurrencies.euro.name,
    country: 'All countries',
    group: 'All groups',
    reductionType: 'Amount',
    reductionTax: 'Tax included',
    reduction: 20,
    fromQuantity: 1,
    fromDate: pastDate,
    toDate: futureDate,
  });
  const catalogPriceRuleData2: FakerCatalogPriceRule = new FakerCatalogPriceRule({
    name: 'Test',
    currency: dataCurrencies.euro.name,
    country: 'All countries',
    group: 'All groups',
    reductionType: 'Percentage',
    reductionTax: 'Tax excluded',
    reduction: 20,
    fromQuantity: 1,
    fromDate: pastDate,
    toDate: futureDate,
  });
  const catalogPriceRuleData3: FakerCatalogPriceRule = new FakerCatalogPriceRule({
    name: 'Test',
    currency: dataCurrencies.euro.name,
    country: 'All countries',
    group: 'All groups',
    reductionType: 'Percentage',
    reductionTax: 'Tax included',
    reduction: 20,
    fromQuantity: 1,
    fromDate: pastDate,
    toDate: futureDate,
  });

  describe('CRUD - Reduction type', async () => {
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

    it('should go to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.discountsLink,
      );

      const pageTitle = await boCartRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
    });

    it('should go to \'Catalog Price Rules\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCatalogPriceRulesTab', baseContext);

      await boCartRulesPage.goToCatalogPriceRulesTab(page);

      const pageTitle = await boCatalogPriceRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCatalogPriceRulesPage.pageTitle);
    });

    it('should go the page "Add new catalog price rule"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewCatalogPriceRule', baseContext);

      await boCatalogPriceRulesPage.goToAddNewCatalogPriceRulePage(page);

      const pageTitle = await boCatalogPriceRulesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCatalogPriceRulesCreatePage.pageTitle);
    });

    it('should create new catalog price rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCatalogPriceRule', baseContext);

      const validationMessage = await boCatalogPriceRulesCreatePage.setCatalogPriceRule(page, catalogPriceRuleData0);
      expect(validationMessage).to.contains(boCatalogPriceRulesPage.successfulCreationMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      page = await boCatalogPriceRulesPage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it(`should search for the product '${dataProducts.demo_6.name}' and go to product page`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foClassicHomePage.searchProduct(page, dataProducts.demo_6.name);
      await foClassicSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_6.name);
    });

    it('should check the product price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice', baseContext);

      // Price - (20€ + 20% on 20€)
      const calculatedPrice = dataProducts.demo_6.combinations[0].price
        - (catalogPriceRuleData0.reduction
          + utilsCore.percentage(catalogPriceRuleData0.reduction, parseInt(dataTaxes.DefaultFrTax.rate, 10))
        );

      const productPrice = await foClassicProductPage.getProductPrice(page);
      expect(productPrice).to.eq(`€${calculatedPrice.toFixed(2)}`);
    });

    it('should return to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToBO', baseContext);

      page = await foClassicProductPage.changePage(browserContext, 0);

      const pageTitle = await boCatalogPriceRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCatalogPriceRulesPage.pageTitle);
    });

    it('should update new catalog price rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCatalogPriceRule0', baseContext);

      await boCatalogPriceRulesPage.goToEditCatalogPriceRulePage(page, catalogPriceRuleData0.name);

      const pageTitle = await boCatalogPriceRulesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCatalogPriceRulesCreatePage.editPageTitle);

      const validationMessage = await boCatalogPriceRulesCreatePage.setCatalogPriceRule(page, catalogPriceRuleData1);
      expect(validationMessage).to.contains(boCatalogPriceRulesPage.successfulUpdateMessage);
    });

    it('should return to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToFO', baseContext);

      page = await boCatalogPriceRulesPage.changePage(browserContext, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_6.name);
    });

    it('should check the product price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice0', baseContext);

      await foClassicProductPage.reloadPage(page);

      // Price - 20€
      const calculatedPrice = dataProducts.demo_6.combinations[0].price - catalogPriceRuleData0.reduction;

      const productPrice = await foClassicProductPage.getProductPrice(page);
      expect(productPrice).to.eq(`€${calculatedPrice.toFixed(2)}`);
    });

    it('should return to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToBO1', baseContext);

      page = await foClassicProductPage.changePage(browserContext, 0);

      const pageTitle = await boCatalogPriceRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCatalogPriceRulesPage.pageTitle);
    });

    it('should update new catalog price rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCatalogPriceRule1', baseContext);

      await boCatalogPriceRulesPage.goToEditCatalogPriceRulePage(page, catalogPriceRuleData1.name);

      const pageTitle = await boCatalogPriceRulesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCatalogPriceRulesCreatePage.editPageTitle);

      const validationMessage = await boCatalogPriceRulesCreatePage.setCatalogPriceRule(page, catalogPriceRuleData2);
      expect(validationMessage).to.contains(boCatalogPriceRulesPage.successfulUpdateMessage);
    });

    it('should return to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToFO1', baseContext);

      page = await boCatalogPriceRulesPage.changePage(browserContext, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_6.name);
    });

    it('should check the product price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice1', baseContext);

      await foClassicProductPage.reloadPage(page);

      // (Price Without Tax - 20% Catalog Price) + 20% Tax
      const calculatedPriceWOTax = dataProducts.demo_6.combinations[0].price / (
        (100 + parseInt(dataTaxes.DefaultFrTax.rate, 10)) / 100
      );
      const calculatedPriceWOTaxAndReduction = calculatedPriceWOTax
        - utilsCore.percentage(calculatedPriceWOTax, catalogPriceRuleData2.reduction);
      const calculatedPrice = calculatedPriceWOTaxAndReduction + utilsCore.percentage(
        calculatedPriceWOTaxAndReduction,
        parseInt(dataTaxes.DefaultFrTax.rate, 10),
      );

      const productPrice = await foClassicProductPage.getProductPrice(page);
      expect(productPrice).to.eq(`€${calculatedPrice.toFixed(2)}`);
    });

    it('should return to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToBO2', baseContext);

      page = await foClassicProductPage.changePage(browserContext, 0);

      const pageTitle = await boCatalogPriceRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCatalogPriceRulesPage.pageTitle);
    });

    it('should update new catalog price rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCatalogPriceRule2', baseContext);

      await boCatalogPriceRulesPage.goToEditCatalogPriceRulePage(page, catalogPriceRuleData1.name);

      const pageTitle = await boCatalogPriceRulesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCatalogPriceRulesCreatePage.editPageTitle);

      const validationMessage = await boCatalogPriceRulesCreatePage.setCatalogPriceRule(page, catalogPriceRuleData3);
      expect(validationMessage).to.contains(boCatalogPriceRulesPage.successfulUpdateMessage);
    });

    it('should return to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToFO2', baseContext);

      page = await boCatalogPriceRulesPage.changePage(browserContext, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_6.name);
    });

    it('should check the product price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice2', baseContext);

      await foClassicProductPage.reloadPage(page);

      // Price - (20% Price)
      const calculatedPricePercent = utilsCore.percentage(
        dataProducts.demo_6.combinations[0].price,
        catalogPriceRuleData3.reduction,
      );
      const calculatedPrice = dataProducts.demo_6.combinations[0].price - calculatedPricePercent;

      const productPrice = await foClassicProductPage.getProductPrice(page);
      expect(productPrice).to.eq(`€${calculatedPrice.toFixed(2)}`);
    });

    it('should return to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToBO3', baseContext);

      page = await foClassicProductPage.changePage(browserContext, 0);

      const pageTitle = await boCatalogPriceRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCatalogPriceRulesPage.pageTitle);
    });

    it('should delete catalog price rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCatalogPriceRule', baseContext);

      const deleteTextResult = await boCatalogPriceRulesPage.deleteCatalogPriceRule(page, catalogPriceRuleData3.name);
      expect(deleteTextResult).to.contains(boCatalogPriceRulesPage.successfulDeleteMessage);
    });
  });
});
