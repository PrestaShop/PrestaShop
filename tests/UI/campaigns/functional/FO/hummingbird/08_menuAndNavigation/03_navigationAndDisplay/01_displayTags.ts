import testContext from '@utils/testContext';
import {deleteProductTest} from '@commonTests/BO/catalog/product';
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabPricingPage,
  boProductsCreateTabStocksPage,
  boProductSettingsPage,
  type BrowserContext,
  dataProducts,
  FakerProduct,
  foHummingbirdHomePage,
  foHummingbirdProductPage,
  foHummingbirdSearchResultsPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_menuAndNavigation_navigationAndDisplay_displayTags';

/*
Pre-condition:
- Install the theme hummingbird
- Disable new product page
Scenario:
- Go to Fo and check the new tag
- Edit 'Number of days for which the product is considered 'New''
- Check that the new tag is no displayed in product page
- Add specific price to the product demo_6 in BO
- Check the discount tag in FO
- Create a pack of products in BO
- Check the pack tag in FO
- Change the created product quantity to 0 in BO
- Check the out-of-stock tag in FO
Post-condition:
- Reset number of days which product is considered new
- Delete specific price
- Enable new product page
- Uninstall the theme hummingbird
 */
describe('FO - Menu and Navigation - Navigate and display : Display of tag products (New, Promo, Pack...)', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const specificPriceData: FakerProduct = new FakerProduct({
    specificPrice: {
      attributes: null,
      discount: 10,
      startingAt: 1,
      reductionType: '€',
    },
  });
  const newProductData: FakerProduct = new FakerProduct({
    type: 'standard',
    coverImage: 'image.jpg',
    taxRule: 'FR Taux standard (20%)',
    tax: 20,
    quantity: 100,
    minimumQuantity: 2,
    status: true,
  });
  const packOfProducts: FakerProduct = new FakerProduct({
    name: 'Pack of products',
    coverImage: 'image.jpg',
    type: 'pack',
    pack: [
      {
        reference: 'demo_13',
        quantity: 1,
      },
      {
        reference: 'demo_7',
        quantity: 1,
      },
    ],
    price: 12.65,
    taxRule: 'No tax',
    quantity: 100,
    minimumQuantity: 2,
    stockLocation: 'stock 3',
    lowStockLevel: 3,
    behaviourOutOfStock: 'Default behavior',
  });

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    await utilsFile.generateImage('image.jpg');
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    await utilsFile.deleteFile('image.jpg');
  });

  describe('FO - Check the new tag', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openShopPage', baseContext);

      await foHummingbirdHomePage.goTo(page, global.FO.URL);

      const result = await foHummingbirdHomePage.isHomePage(page);
      expect(result).to.equal(true);
    });

    it(`should search for the product '${dataProducts.demo_6.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProductDemo6', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_6.name);

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageDemo6', baseContext);

      await foHummingbirdSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_6.name);
    });

    it('should check the new tag', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewTag', baseContext);

      const flagText = await foHummingbirdProductPage.getProductTag(page);
      expect(flagText).to.equal('New');
    });
  });

  describe('BO - Edit \'Number of days for which the product is considered \'New\'\'', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Shop parameters > Product Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage1', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.productSettingsLink,
      );

      const pageTitle = await boProductSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
    });

    it('should change the number of days to 0', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeNumberOfDays0', baseContext);

      const result = await boProductSettingsPage.updateNumberOfDays(page, 0);
      expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
    });
  });

  describe('FO - Check that the new tag is no displayed in product page', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO1', baseContext);

      await foHummingbirdHomePage.goTo(page, global.FO.URL);

      const result = await foHummingbirdHomePage.isHomePage(page);
      expect(result).to.equal(true);
    });

    it(`should search for the product '${dataProducts.demo_6.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProductDemo6_2', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_6.name);

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageDemo6_2', baseContext);

      await foHummingbirdSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_6.name);
    });

    it('should check that the new tag is not displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIsNewTagNotVisible', baseContext);

      const isTagVisible = await foHummingbirdProductPage.isProductTagVisible(page);
      expect(isTagVisible).to.equal(false);
    });
  });

  describe('BO - Create product with specific', async () => {
    it('should go back BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO1', baseContext);

      await foHummingbirdProductPage.goTo(page, global.BO.URL);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.productsLink,
      );
      await boProductsPage.closeSfToolBar(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should click on new product button and go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductPage', baseContext);

      const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.be.eq(true);

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

    it('should add a specific price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addSpecificPrice', baseContext);

      await boProductsCreateTabPricingPage.clickOnAddSpecificPriceButton(page);

      const message = await boProductsCreateTabPricingPage.setSpecificPrice(page, specificPriceData.specificPrice);
      expect(message).to.equal(boProductsCreatePage.successfulCreationMessage);
    });
  });

  describe('FO - Check the discount tag', async () => {
    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO2', baseContext);

      page = await boProductsCreatePage.previewProduct(page);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the discount tag', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountTag', baseContext);

      const flagText = await foHummingbirdProductPage.getProductTag(page);
      expect(flagText).to.equal(`-€${specificPriceData.specificPrice.discount.toFixed(2)}`);
    });
  });

  describe('BO - Create a pack of products', async () => {
    it('should go back BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      page = await foHummingbirdHomePage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage3', baseContext);

      await boDashboardPage.goToSubMenu(page, boDashboardPage.catalogParentLink, boDashboardPage.productsLink);
      await boProductsPage.closeSfToolBar(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible: boolean = await boProductsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.be.equal(true);
    });

    it('should choose \'Pack of products\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await boProductsPage.selectProductType(page, packOfProducts.type);

      const pageTitle: string = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle: string = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it(`create product '${packOfProducts.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

      const createProductMessage = await boProductsCreatePage.setProduct(page, packOfProducts);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });
  });

  describe('FO - Check the pack tag', async () => {
    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO3', baseContext);

      page = await boProductsCreatePage.previewProduct(page);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(packOfProducts.name);
    });

    it('should check the pack tag', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPackTag', baseContext);

      const flagText = await foHummingbirdProductPage.getProductTag(page);
      expect(flagText).to.equal('Pack');
    });
  });

  describe('BO - Change the created product quantity to 0', async () => {
    it('should go back BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO3', baseContext);

      page = await foHummingbirdHomePage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should edit the quantity', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editQuantity', baseContext);

      await boProductsCreateTabStocksPage.setProductQuantity(page, 0);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });
  });

  describe('FO - Check the out-of-stock tag', async () => {
    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO4', baseContext);

      page = await boProductsCreatePage.previewProduct(page);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(packOfProducts.name);
    });

    it('should check the out-of-stock and pack tags', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOutOfStockTag', baseContext);

      const flagText = await foHummingbirdProductPage.getProductTag(page);
      expect(flagText).to.contain('Pack')
        .and.contain('Out-of-Stock');
    });
  });

  // Post-condition: Reset 'Number of days for which the product is considered 'new''
  describe('POST-TEST : Reset \'Number of days for which the product is considered \'new\'\'', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO4', baseContext);

      page = await foHummingbirdHomePage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should go to \'Shop parameters > Product Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.productSettingsLink,
      );

      const pageTitle = await boProductSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
    });

    it('should change the number of days to 12', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeNumberOfDays', baseContext);

      const result = await boProductSettingsPage.updateNumberOfDays(page, 12);
      expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
    });
  });

  // Post-condition: Delete specific price
  deleteProductTest(newProductData, `${baseContext}_deleteProduct_1`);

  // Post-condition: Delete created product
  deleteProductTest(packOfProducts, `${baseContext}_deleteProduct_2`);

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest_3`);
});
