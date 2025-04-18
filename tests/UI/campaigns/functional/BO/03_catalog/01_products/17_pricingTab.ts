import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCartRulesPage,
  boCatalogPriceRulesPage,
  boCatalogPriceRulesCreatePage,
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabPricingPage,
  type BrowserContext,
  FakerCatalogPriceRule,
  FakerProduct,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_products_pricingTab';

describe('BO - Catalog - Products : Pricing tab', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create standard product
  const newProductData: FakerProduct = new FakerProduct({
    type: 'standard',
    quantity: 10,
    minimumQuantity: 1,
    price: 100,
    taxRule: 'No tax',
    status: true,
  });
  // Data to create specific price
  const specificPriceData: FakerProduct = new FakerProduct({
    specificPrice: {
      attributes: null,
      discount: 20,
      startingAt: 1,
      reductionType: '€',
    },
  });
  // Data to edit specific price
  const editSpecificPriceData: FakerProduct = new FakerProduct({
    specificPrice: {
      attributes: null,
      discount: 30,
      startingAt: 1,
      reductionType: '€',
    },
  });
  // Data to create new catalog price rule
  const newCatalogPriceRuleData: FakerCatalogPriceRule = new FakerCatalogPriceRule({
    currency: 'All currencies',
    country: 'All countries',
    group: 'All groups',
    reductionType: 'Amount',
    reductionTax: 'Tax included',
    fromQuantity: 3,
    reduction: 20,
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // 1 - Create product
  describe('Create product', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.productsLink,
      );
      await boProductsPage.closeSfToolBar(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should choose \'Standard product\' and go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await boProductsPage.selectProductType(page, newProductData.type);
      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      const createProductMessage = await boProductsCreatePage.setProduct(page, newProductData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });
  });

  // 2 - Check options in pricing tab
  describe('Check all options in Details tab', async () => {
    it('should check summary block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock', baseContext);

      const result = await boProductsCreateTabPricingPage.getSummary(page);
      await Promise.all([
        expect(result.priceTaxExcludedValue).to.eq('€100.00 tax excl.'),
        expect(result.priceTaxIncludedValue).to.eq('€100.00 tax incl.'),
        expect(result.marginValue).to.eq('€100.00 margin'),
        expect(result.marginRateValue).to.eq('100.00% margin rate'),
        expect(result.wholesalePriceValue).to.eq('€0.00 cost price'),
      ]);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      page = await boProductsCreatePage.previewProduct(page);
      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the product price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice', baseContext);

      const productPrice = await foClassicProductPage.getProductPrice(page);
      expect(productPrice).to.eq('€100.00');
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should edit the product price and the tax rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editRetailPrice', baseContext);

      await boProductsCreateTabPricingPage.setTaxRule(page, 'FR Taux standard (20%)');

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should check summary block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock2', baseContext);

      const result = await boProductsCreateTabPricingPage.getSummary(page);
      await Promise.all([
        expect(result.priceTaxExcludedValue).to.eq('€100.00 tax excl.'),
        expect(result.priceTaxIncludedValue).to.eq('€120.00 tax incl.'),
        expect(result.marginValue).to.eq('€100.00 margin'),
        expect(result.marginRateValue).to.eq('100.00% margin rate'),
        expect(result.wholesalePriceValue).to.eq('€0.00 cost price'),
      ]);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct2', baseContext);

      page = await boProductsCreatePage.previewProduct(page);
      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the product price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice2', baseContext);

      const productPrice = await foClassicProductPage.getProductPrice(page);
      expect(productPrice).to.eq('€120.00');
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should add a cost price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addCostPrice', baseContext);

      await boProductsCreateTabPricingPage.setCostPrice(page, 35);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should check summary block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock3', baseContext);

      const result = await boProductsCreateTabPricingPage.getSummary(page);
      await Promise.all([
        expect(result.priceTaxExcludedValue).to.eq('€100.00 tax excl.'),
        expect(result.priceTaxIncludedValue).to.eq('€120.00 tax incl.'),
        expect(result.marginValue).to.eq('€65.00 margin'),
        expect(result.marginRateValue).to.eq('65.00% margin rate'),
        expect(result.wholesalePriceValue).to.eq('€35.00 cost price'),
      ]);
    });

    it('should edit Retail price per unit section', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editRetailPriceParUnit', baseContext);

      await boProductsCreateTabPricingPage.setDisplayRetailPricePerUnit(page, true);
      await boProductsCreateTabPricingPage.setRetailPricePerUnit(page, true, 10, 'per unit');

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should check summary block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock4', baseContext);

      const result = await boProductsCreateTabPricingPage.getSummary(page);
      await Promise.all([
        expect(result.priceTaxExcludedValue).to.eq('€100.00 tax excl.'),
        expect(result.priceTaxIncludedValue).to.eq('€120.00 tax incl.'),
        expect(result.marginValue).to.eq('€65.00 margin'),
        expect(result.marginRateValue).to.eq('65.00% margin rate'),
        expect(result.wholesalePriceValue).to.eq('€35.00 cost price'),
      ]);

      const unitPrice = await boProductsCreateTabPricingPage.getUnitPriceValue(page);
      expect(unitPrice).to.eq('€10.00 per unit');
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct3', baseContext);

      page = await boProductsCreatePage.previewProduct(page);
      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the price per unit', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUnitPrice', baseContext);

      const flagText = await foClassicProductPage.getProductUnitPrice(page);
      expect(flagText).to.eq('€12.00 per unit');
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO3', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should check Display On sale flag', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDisplayOnSaleFlag', baseContext);

      await boProductsCreateTabPricingPage.setDisplayOnSaleFlag(page);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct4', baseContext);

      page = await boProductsCreatePage.previewProduct(page);
      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the on sale flag', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOnSaleFlag', baseContext);

      const flagText = await foClassicProductPage.getProductTag(page);
      expect(flagText).to.contains('On sale!');
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO4', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should add a specific price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addSpecificPrice', baseContext);

      await boProductsCreateTabPricingPage.clickOnAddSpecificPriceButton(page);

      const message = await boProductsCreateTabPricingPage.setSpecificPrice(page, specificPriceData.specificPrice);
      expect(message).to.equal(boProductsCreatePage.successfulCreationMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct5', baseContext);

      page = await boProductsCreatePage.previewProduct(page);
      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the product price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSpecificPrice', baseContext);

      const productPrice = await foClassicProductPage.getProductPrice(page);
      expect(productPrice).to.eq('€100.00');
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO5', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should edit specific price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editSpecificPrice', baseContext);

      await boProductsCreateTabPricingPage.clickOnEditSpecificPriceIcon(page, 1);

      const message = await boProductsCreateTabPricingPage.setSpecificPrice(page, editSpecificPriceData.specificPrice);
      expect(message).to.equal('Update successful');
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct6', baseContext);

      page = await boProductsCreatePage.previewProduct(page);
      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the product price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedSpecificPrice', baseContext);

      const productPrice = await foClassicProductPage.getProductPrice(page);
      expect(productPrice).to.eq('€90.00');
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO6', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should delete specific price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteSpecificPrice', baseContext);

      const successMessage = await boProductsCreateTabPricingPage.deleteSpecificPrice(page, 1);
      expect(successMessage).to.eq(boProductsCreateTabPricingPage.successfulDeleteMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct7', baseContext);

      page = await boProductsCreatePage.previewProduct(page);
      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the product price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeletedSpecificPrice', baseContext);

      const productPrice = await foClassicProductPage.getProductPrice(page);
      expect(productPrice).to.eq('€120.00');
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO7', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should click on show catalog price rule button then on manage catalog price rules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnManageCatalogPriceRuleLink', baseContext);

      await boProductsCreateTabPricingPage.clickOnShowCatalogPriceRuleButton(page);
      page = await boProductsCreateTabPricingPage.clickOnManageCatalogPriceRuleLink(page);

      const pageTitle = await boCatalogPriceRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCatalogPriceRulesPage.pageTitle);
    });

    it('should create a new catalog price rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCatalogPriceRule', baseContext);

      await boCatalogPriceRulesPage.goToAddNewCatalogPriceRulePage(page);

      const validationMessage = await boCatalogPriceRulesCreatePage.setCatalogPriceRule(page, newCatalogPriceRuleData);
      expect(validationMessage).to.contains(boCatalogPriceRulesPage.successfulCreationMessage);
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage', baseContext);

      page = await boCatalogPriceRulesPage.closePage(browserContext, page, 0);
      await boProductsCreatePage.reloadPage(page);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should click on show catalog price rule button and check the catalog price rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnShowCatalogPriceRuleButton', baseContext);

      await boProductsCreateTabPricingPage.clickOnShowCatalogPriceRuleButton(page);

      const result = await boProductsCreateTabPricingPage.getCatalogPriceRuleData(page, 1);
      await Promise.all([
        expect(result.name).to.eq(newCatalogPriceRuleData.name),
        expect(result.currency).to.eq(newCatalogPriceRuleData.currency),
        expect(result.country).to.eq(newCatalogPriceRuleData.country),
        expect(result.group).to.eq(newCatalogPriceRuleData.group),
        expect(result.store).to.eq(global.INSTALL.SHOP_NAME),
        expect(result.discount).to.eq('-€20.00 (tax incl.)'),
        expect(result.fromQuantity).to.eq(newCatalogPriceRuleData.fromQuantity),
      ]);
    });

    it('should click on hide catalog price rules button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnHideCatalogPriceRuleButton', baseContext);

      const isCatalogPriceRulesTableVisible = await boProductsCreateTabPricingPage.clickOnHideCatalogPriceRulesButton(page);
      expect(isCatalogPriceRulesTableVisible).to.eq(false);
    });
  });

  // POST-TEST: Delete product
  describe('POST-TEST: Delete product', async () => {
    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const deleteProductMessage = await boProductsCreatePage.deleteProduct(page);
      expect(deleteProductMessage).to.equal(boProductsPage.successfulDeleteMessage);
    });
  });

  // POST-TEST : Delete catalog price rules
  describe('POST-TEST : Delete catalog price rule', async () => {
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

    it('should delete catalog price rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCatalogPriceRule', baseContext);

      const deleteTextResult = await boCatalogPriceRulesPage.deleteCatalogPriceRule(page, newCatalogPriceRuleData.name);
      expect(deleteTextResult).to.contains(boCatalogPriceRulesPage.successfulDeleteMessage);
    });
  });
});
