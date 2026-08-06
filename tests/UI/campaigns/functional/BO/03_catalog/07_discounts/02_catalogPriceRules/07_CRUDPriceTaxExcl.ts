import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  // Import BO pages
  boCartRulesPage,
  boCatalogPriceRulesCreatePage,
  boCatalogPriceRulesPage,
  boDashboardPage,
  boLoginPage,
  // Import FO pages
  foHummingbirdCartPage,
  foHummingbirdSearchResultsPage,
  foHummingbirdHomePage,
  foHummingbirdProductPage,
  // Import data
  dataProducts,
  FakerCatalogPriceRule,
  // Import type
  type BrowserContext,
  type Page,
  utilsPlaywright,
  utilsCore,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_discounts_catalogPriceRules_CRUDPriceTaxExcluded';

describe('BO - Catalog - Discounts - Catalog price Rules : CRUD Price (tax excl.)', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const catalogPriceRuleData: FakerCatalogPriceRule = new FakerCatalogPriceRule({
    name: 'test',
    currency: 'All currencies',
    country: 'All countries',
    group: 'All groups',
    price: 100,
    leaveInitialPrice: false,
    reductionType: 'Amount',
    reductionTax: 'Tax included',
    fromQuantity: 1,
    reduction: 10,
  });
  const editCatalogPriceRuleData: FakerCatalogPriceRule = new FakerCatalogPriceRule({
    ...catalogPriceRuleData,
    leaveInitialPrice: true,
  });

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Create catalog price rule in BO and check it in FO', async () => {
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

    it('should go to add catalog price rules page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goAddCatalogPriceRulesPage', baseContext);

      await boCatalogPriceRulesPage.goToAddNewCatalogPriceRulePage(page);

      const pageTitle = await boCatalogPriceRulesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCatalogPriceRulesCreatePage.pageTitle);
    });

    it('should create new catalog price rule with Price tax excluded', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCatalogPriceRule', baseContext);

      const validationMessage = await boCatalogPriceRulesCreatePage.setCatalogPriceRule(page, catalogPriceRuleData);
      expect(validationMessage).to.contains(boCatalogPriceRulesPage.successfulCreationMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop_1', baseContext);

      // View my shop and init pages
      page = await boCatalogPriceRulesCreatePage.viewMyShop(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it(`should search for the product '${dataProducts.demo_6.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_6.name);

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foHummingbirdSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_6.name);
    });

    it('should check the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscount', baseContext);

      // Check discount
      const columnValue = await foHummingbirdProductPage.getDiscountAmount(page);
      expect(columnValue).to.equal(`(Save €${catalogPriceRuleData.reduction.toFixed(2)})`);

      // Check final price
      const finalPrice = await foHummingbirdProductPage.getProductInformation(page);
      expect(finalPrice.price.toFixed(2)).to.equal(
        (
          catalogPriceRuleData.price + utilsCore.percentage(catalogPriceRuleData.price, 20) - catalogPriceRuleData.reduction
        ).toFixed(2),
      );
    });

    it('should add the product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foHummingbirdProductPage.addProductToTheCart(page);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
    });

    it('should check the discount in cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountInTheCart', baseContext);

      const productDetail = await foHummingbirdCartPage.getProductDetail(page, 1);
      await Promise.all([
        expect(productDetail.regularPrice).to.equal(
          catalogPriceRuleData.price + utilsCore.percentage(catalogPriceRuleData.price, 20)),
        expect(productDetail.price.toFixed(2)).to.equal(
          (
            catalogPriceRuleData.price + utilsCore.percentage(catalogPriceRuleData.price, 20) - catalogPriceRuleData.reduction
          ).toFixed(2),
        ),
        expect(productDetail.discountAmount).to.equal(`-€${catalogPriceRuleData.reduction.toFixed(2)}`),
      ]);
    });
  });

  describe('Edit catalog price rules and check it in FO - Leave initial price', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      page = await foHummingbirdCartPage.changePage(browserContext, 0);

      const pageTitle = await boCatalogPriceRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCatalogPriceRulesPage.pageTitle);
    });

    it('should edit the catalog price rules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editCatalogPriceRules', baseContext);

      await boCatalogPriceRulesPage.goToEditCatalogPriceRulePage(page, catalogPriceRuleData.name);

      const pageTitle = await boCatalogPriceRulesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCatalogPriceRulesCreatePage.editPageTitle);

      const validationMessage = await boCatalogPriceRulesCreatePage.setCatalogPriceRule(page, editCatalogPriceRuleData);
      expect(validationMessage).to.contains(boCatalogPriceRulesPage.successfulUpdateMessage);
    });

    it('should go back to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToFO', baseContext);

      page = await boCatalogPriceRulesCreatePage.changePage(browserContext, 1);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdCartPage.pageTitle);
    });

    it('should check the discount in cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountInTheCart_2', baseContext);

      await foHummingbirdCartPage.reloadPage(page);

      const productDetail = await foHummingbirdCartPage.getProductDetail(page, 1);
      await Promise.all([
        expect(productDetail.regularPrice).to.equal(dataProducts.demo_6.combinations[0].priceTI),
        expect(productDetail.price.toFixed(2)).to.equal(
          (
            dataProducts.demo_6.combinations[0].priceTI - catalogPriceRuleData.reduction
          ).toFixed(2),
        ),
        expect(productDetail.discountAmount).to.equal(`-€${catalogPriceRuleData.reduction.toFixed(2)}`),
      ]);
    });
  });

  describe('Delete created catalog price rules', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO_3', baseContext);

      page = await foHummingbirdCartPage.changePage(browserContext, 0);

      const pageTitle = await boCatalogPriceRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCatalogPriceRulesPage.pageTitle);
    });

    it('should delete catalog price rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCatalogPriceRule', baseContext);

      const deleteTextResult = await boCatalogPriceRulesPage.deleteCatalogPriceRule(page, editCatalogPriceRuleData.name);
      expect(deleteTextResult).to.contains(boCatalogPriceRulesPage.successfulDeleteMessage);
    });
  });
});
