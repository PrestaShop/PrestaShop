// Import utils
import testContext from '@utils/testContext';

// Import pages
// Import BO pages
import cartRulesPage from '@pages/BO/catalog/discounts';
import catalogPriceRulesPage from '@pages/BO/catalog/discounts/catalogPriceRules';
import addCatalogPriceRulePage from '@pages/BO/catalog/discounts/catalogPriceRules/add';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataProducts,
  FakerCatalogPriceRule,
  foClassicHomePage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_discounts_catalogPriceRules_CRUDCatalogPriceRule';

/*
Create new catalog price rules
Check the rule in FO
Update catalog price rules
Check the updated rule in FO
Delete catalog price rules
*/
describe('BO - Catalog - Discounts : CRUD catalog price rules', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const newCatalogPriceRuleData: FakerCatalogPriceRule = new FakerCatalogPriceRule({
    currency: 'All currencies',
    country: 'All countries',
    group: 'All groups',
    reductionType: 'Amount',
    reductionTax: 'Tax included',
    fromQuantity: 3,
    reduction: 20,
  });
  const editCatalogPriceRuleData: FakerCatalogPriceRule = new FakerCatalogPriceRule({
    currency: 'All currencies',
    country: 'All countries',
    group: 'All groups',
    reductionType: 'Amount',
    reductionTax: 'Tax included',
    fromQuantity: 4,
    reduction: 15,
  });
  const productPrice: number = dataProducts.demo_1.finalPrice;
  const defaultDiscount: string = 'Save 20%';
  const priceAfterNewDiscount: number = 8.68;
  const discountAmountForNewDiscount: string = 'Save €20.00';
  const priceAfterUpdatedDiscount: number = 13.68;
  const discountAmountForUpdatedDiscount: string = 'Save €15.00';

  // before and after functions
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

  // 1 - Create catalog price rule
  describe('Create catalog price rule', async () => {
    it('should go to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.discountsLink,
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

    it('should create new catalog price rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCatalogPriceRule', baseContext);

      await catalogPriceRulesPage.goToAddNewCatalogPriceRulePage(page);

      const pageTitle = await addCatalogPriceRulePage.getPageTitle(page);
      expect(pageTitle).to.contains(addCatalogPriceRulePage.pageTitle);

      const validationMessage = await addCatalogPriceRulePage.setCatalogPriceRule(page, newCatalogPriceRuleData);
      expect(validationMessage).to.contains(catalogPriceRulesPage.successfulCreationMessage);
    });
  });

  // 2 - Check catalog price rule in FO
  describe('Check catalog price rule in FO', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop_1', baseContext);

      // View my shop and init pages
      page = await addCatalogPriceRulePage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage_1', baseContext);

      await foClassicHomePage.goToProductPage(page, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_1.name);
    });

    it('should check the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscount_1', baseContext);

      // Check quantity for discount value
      const quantityDiscountValue = await foClassicProductPage.getQuantityDiscountValue(page);
      expect(quantityDiscountValue).to.equal(newCatalogPriceRuleData.fromQuantity);

      // Check unit discount value
      const unitDiscountValue = await foClassicProductPage.getDiscountValue(page);
      expect(unitDiscountValue).to.equal(`€${newCatalogPriceRuleData.reduction}.00`);

      // Check discount percentage
      let columnValue = await foClassicProductPage.getDiscountPercentage(page);
      expect(columnValue).to.equal(defaultDiscount);

      // Check final price
      let finalPrice = await foClassicProductPage.getProductInformation(page);
      expect(finalPrice.price).to.equal(productPrice);

      // Set quantity of the product
      await foClassicProductPage.setQuantity(page, newCatalogPriceRuleData.fromQuantity);

      // Check discount value
      columnValue = await foClassicProductPage.getDiscountAmount(page);
      expect(columnValue).to.equal(discountAmountForNewDiscount);

      // Check final price
      finalPrice = await foClassicProductPage.getProductInformation(page);
      expect(finalPrice.price).to.equal(priceAfterNewDiscount);
    });
  });

  // 3 - Update catalog price rule
  describe('Update catalog price rule', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBoToUpdate', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await catalogPriceRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(catalogPriceRulesPage.pageTitle);
    });

    it('should update the created catalog price rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCatalogPriceRule', baseContext);

      await catalogPriceRulesPage.goToEditCatalogPriceRulePage(page, newCatalogPriceRuleData.name);

      const pageTitle = await addCatalogPriceRulePage.getPageTitle(page);
      expect(pageTitle).to.contains(addCatalogPriceRulePage.editPageTitle);

      const validationMessage = await addCatalogPriceRulePage.setCatalogPriceRule(page, editCatalogPriceRuleData);
      expect(validationMessage).to.contains(catalogPriceRulesPage.successfulUpdateMessage);
    });
  });

  // 4 - Check updated catalog price rule in FO
  describe('Check updated catalog price rule in FO', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop_2', baseContext);

      // View my shop and init pages
      page = await addCatalogPriceRulePage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductPage_2', baseContext);

      await foClassicHomePage.goToProductPage(page, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_1.name);
    });

    it('should check the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedCatalogPriceRule', baseContext);

      // Check quantity for discount value
      const quantityDiscountValue = await foClassicProductPage.getQuantityDiscountValue(page);
      expect(quantityDiscountValue).to.equal(editCatalogPriceRuleData.fromQuantity);

      // Check unit discount value
      const unitDiscountValue = await foClassicProductPage.getDiscountValue(page);
      expect(unitDiscountValue).to.equal(`€${editCatalogPriceRuleData.reduction}.00`);

      // Check discount percentage
      let columnValue = await foClassicProductPage.getDiscountPercentage(page);
      expect(columnValue).to.equal(defaultDiscount);

      // Check final price
      let finalPrice = await foClassicProductPage.getProductInformation(page);
      expect(finalPrice.price).to.equal(productPrice);

      // Set quantity of the product
      await foClassicProductPage.setQuantity(page, editCatalogPriceRuleData.fromQuantity);

      // Check discount value
      columnValue = await foClassicProductPage.getDiscountAmount(page);
      expect(columnValue).to.equal(discountAmountForUpdatedDiscount);

      // Check final price
      finalPrice = await foClassicProductPage.getProductInformation(page);
      expect(finalPrice.price).to.equal(priceAfterUpdatedDiscount);
    });
  });

  // 5 - Delete catalog price rule
  describe('Delete catalog price rule', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBoToDelete', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await catalogPriceRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(catalogPriceRulesPage.pageTitle);
    });

    it('should delete catalog price rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCatalogPriceRule', baseContext);

      const deleteTextResult = await catalogPriceRulesPage.deleteCatalogPriceRule(page, editCatalogPriceRuleData.name);
      expect(deleteTextResult).to.contains(catalogPriceRulesPage.successfulDeleteMessage);
    });
  });
});
