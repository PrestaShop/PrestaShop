// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import createProductPage from '@pages/BO/catalog/products/add';
import pricingTab from '@pages/BO/catalog/products/add/pricingTab';
import productsPage from '@pages/BO/catalog/products';
import createCatalogPriceRulePage from '@pages/BO/catalog/discounts/catalogPriceRules/add';
import cartRulesPage from '@pages/BO/catalog/discounts';

// Import FO pages
import {foProductPage} from '@pages/FO/classic/product';
import catalogPriceRulesPage from '@pages/BO/catalog/discounts/catalogPriceRules';

// Import data
import ProductData from '@data/faker/product';
import CatalogPriceRuleData from '@data/faker/catalogPriceRule';

import type {BrowserContext, Page} from 'playwright';
import {expect} from 'chai';

const baseContext: string = 'functional_BO_catalog_products_pricingTab';

describe('BO - Catalog - Products : Pricing tab', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create standard product
  const newProductData: ProductData = new ProductData({
    type: 'standard',
    quantity: 10,
    minimumQuantity: 1,
    price: 100,
    taxRule: 'No tax',
    status: true,
  });
  // Data to create specific price
  const specificPriceData: ProductData = new ProductData({
    specificPrice: {
      attributes: null,
      discount: 20,
      startingAt: 1,
      reductionType: '€',
    },
  });
  // Data to edit specific price
  const editSpecificPriceData: ProductData = new ProductData({
    specificPrice: {
      attributes: null,
      discount: 30,
      startingAt: 1,
      reductionType: '€',
    },
  });
  // Data to create new catalog price rule
  const newCatalogPriceRuleData: CatalogPriceRuleData = new CatalogPriceRuleData({
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
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // 1 - Create product
  describe('Create product', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.productsLink,
      );
      await productsPage.closeSfToolBar(page);

      const pageTitle = await productsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await productsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should choose \'Standard product\' and go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await productsPage.selectProductType(page, newProductData.type);
      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      const createProductMessage = await createProductPage.setProduct(page, newProductData);
      expect(createProductMessage).to.equal(createProductPage.successfulUpdateMessage);
    });
  });

  // 2 - Check options in pricing tab
  describe('Check all options in Details tab', async () => {
    it('should check summary block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock', baseContext);

      const result = await pricingTab.getSummary(page);
      await Promise.all([
        expect(result.priceTaxExcludedValue).to.eq('€100.00 tax excl.'),
        expect(result.priceTaxIncludedValue).to.eq('€100.00 tax incl.'),
        expect(result.marginValue).to.eq('€100.00 margin'),
        expect(result.marginRateValue).to.eq('100.00% margin rate'),
        expect(result.WholesalePriceValue).to.eq('€0.00 cost price'),
      ]);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      page = await createProductPage.previewProduct(page);
      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the product price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice', baseContext);

      const productPrice = await foProductPage.getProductPrice(page);
      expect(productPrice).to.eq('€100.00');
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should edit the product price and the tax rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editRetailPrice', baseContext);

      await pricingTab.setTaxRule(page, 'FR Taux standard (20%)');

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should check summary block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock2', baseContext);

      const result = await pricingTab.getSummary(page);
      await Promise.all([
        expect(result.priceTaxExcludedValue).to.eq('€100.00 tax excl.'),
        expect(result.priceTaxIncludedValue).to.eq('€120.00 tax incl.'),
        expect(result.marginValue).to.eq('€100.00 margin'),
        expect(result.marginRateValue).to.eq('100.00% margin rate'),
        expect(result.WholesalePriceValue).to.eq('€0.00 cost price'),
      ]);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct2', baseContext);

      page = await createProductPage.previewProduct(page);
      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the product price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice2', baseContext);

      const productPrice = await foProductPage.getProductPrice(page);
      expect(productPrice).to.eq('€120.00');
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should add a cost price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addCostPrice', baseContext);

      await pricingTab.setCostPrice(page, 35);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should check summary block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock3', baseContext);

      const result = await pricingTab.getSummary(page);
      await Promise.all([
        expect(result.priceTaxExcludedValue).to.eq('€100.00 tax excl.'),
        expect(result.priceTaxIncludedValue).to.eq('€120.00 tax incl.'),
        expect(result.marginValue).to.eq('€65.00 margin'),
        expect(result.marginRateValue).to.eq('65.00% margin rate'),
        expect(result.WholesalePriceValue).to.eq('€35.00 cost price'),
      ]);
    });

    it('should edit Retail price per unit section', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editRetailPriceParUnit', baseContext);

      await pricingTab.setDisplayRetailPricePerUnit(page, true);
      await pricingTab.setRetailPricePerUnit(page, true, 10, 'per unit');

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should check summary block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock4', baseContext);

      const result = await pricingTab.getSummary(page);
      await Promise.all([
        expect(result.priceTaxExcludedValue).to.eq('€100.00 tax excl.'),
        expect(result.priceTaxIncludedValue).to.eq('€120.00 tax incl.'),
        expect(result.marginValue).to.eq('€65.00 margin'),
        expect(result.marginRateValue).to.eq('65.00% margin rate'),
        expect(result.WholesalePriceValue).to.eq('€35.00 cost price'),
      ]);

      const unitPrice = await pricingTab.getUnitPriceValue(page);
      expect(unitPrice).to.eq('€10.00 per unit');
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct3', baseContext);

      page = await createProductPage.previewProduct(page);
      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the price per unit', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUnitPrice', baseContext);

      const flagText = await foProductPage.getProductUnitPrice(page);
      expect(flagText).to.eq('€12.00 per unit');
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO3', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should check Display On sale flag', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDisplayOnSaleFlag', baseContext);

      await pricingTab.setDisplayOnSaleFlag(page);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct4', baseContext);

      page = await createProductPage.previewProduct(page);
      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the on sale flag', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOnSaleFlag', baseContext);

      const flagText = await foProductPage.getProductTag(page);
      expect(flagText).to.contains('On sale!');
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO4', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should add a specific price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addSpecificPrice', baseContext);

      await pricingTab.clickOnAddSpecificPriceButton(page);

      const message = await pricingTab.setSpecificPrice(page, specificPriceData.specificPrice);
      expect(message).to.equal(createProductPage.successfulCreationMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct5', baseContext);

      page = await createProductPage.previewProduct(page);
      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the product price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSpecificPrice', baseContext);

      const productPrice = await foProductPage.getProductPrice(page);
      expect(productPrice).to.eq('€100.00');
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO5', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should edit specific price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editSpecificPrice', baseContext);

      await pricingTab.clickOnEditSpecificPriceIcon(page, 1);

      const message = await pricingTab.setSpecificPrice(page, editSpecificPriceData.specificPrice);
      expect(message).to.equal('Update successful');
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct6', baseContext);

      page = await createProductPage.previewProduct(page);
      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the product price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedSpecificPrice', baseContext);

      const productPrice = await foProductPage.getProductPrice(page);
      expect(productPrice).to.eq('€90.00');
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO6', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should delete specific price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteSpecificPrice', baseContext);

      const successMessage = await pricingTab.deleteSpecificPrice(page, 1);
      expect(successMessage).to.eq(pricingTab.successfulDeleteMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct7', baseContext);

      page = await createProductPage.previewProduct(page);
      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the product price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeletedSpecificPrice', baseContext);

      const productPrice = await foProductPage.getProductPrice(page);
      expect(productPrice).to.eq('€120.00');
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO7', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should click on show catalog price rule button then on manage catalog price rules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnManageCatalogPriceRuleLink', baseContext);

      await pricingTab.clickOnShowCatalogPriceRuleButton(page);
      page = await pricingTab.clickOnManageCatalogPriceRuleLink(page);

      const pageTitle = await catalogPriceRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(catalogPriceRulesPage.pageTitle);
    });

    it('should create a new catalog price rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCatalogPriceRule', baseContext);

      await catalogPriceRulesPage.goToAddNewCatalogPriceRulePage(page);

      const validationMessage = await createCatalogPriceRulePage.setCatalogPriceRule(page, newCatalogPriceRuleData);
      expect(validationMessage).to.contains(catalogPriceRulesPage.successfulCreationMessage);
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage', baseContext);

      page = await catalogPriceRulesPage.closePage(browserContext, page, 0);
      await createProductPage.reloadPage(page);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should click on show catalog price rule button and check the catalog price rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnShowCatalogPriceRuleButton', baseContext);

      await pricingTab.clickOnShowCatalogPriceRuleButton(page);

      const result = await pricingTab.getCatalogPriceRuleData(page, 1);
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

      const isCatalogPriceRulesTableVisible = await pricingTab.clickOnHideCatalogPriceRulesButton(page);
      expect(isCatalogPriceRulesTableVisible).to.eq(false);
    });
  });

  // POST-TEST: Delete product
  describe('POST-TEST: Delete product', async () => {
    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const deleteProductMessage = await createProductPage.deleteProduct(page);
      expect(deleteProductMessage).to.equal(productsPage.successfulDeleteMessage);
    });
  });

  // POST-TEST : Delete catalog price rules
  describe('POST-TEST : Delete catalog price rule', async () => {
    it('should go to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.discountsLink,
      );

      const pageTitle = await cartRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(cartRulesPage.pageTitle);
    });

    it('should go to \'Catalog Price Rules\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCatalogPriceRulesTab', baseContext);

      await cartRulesPage.goToCatalogPriceRulesTab(page);

      const pageTitle = await catalogPriceRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(catalogPriceRulesPage.pageTitle);
    });

    it('should delete catalog price rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCatalogPriceRule', baseContext);

      const deleteTextResult = await catalogPriceRulesPage.deleteCatalogPriceRule(page, newCatalogPriceRuleData.name);
      expect(deleteTextResult).to.contains(catalogPriceRulesPage.successfulDeleteMessage);
    });
  });
});
