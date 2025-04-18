import testContext from '@utils/testContext';
import {deleteProductTest} from '@commonTests/BO/catalog/product';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  type BrowserContext,
  FakerProduct,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_menuAndNavigation_navigationAndDisplay_displayOnSaleFlag';

/*
Pre-condition:
- Disable new product page
- Create new product with enable 'On sale' flag
- Preview product on check 'On sale' flag
Post-condition:
- Delete created product
- Enable new product page
 */
describe('FO - Navigation and display : Display \'On sale\' flag', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const onSaleProductData: FakerProduct = new FakerProduct({
    name: 'On sale product',
    type: 'standard',
    onSale: true,
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // Pre-condition : Create product with on sale flag
  describe('BO - Create product with enabled flag \'On sale\'', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage3', baseContext);

      await boDashboardPage.goToSubMenu(page, boDashboardPage.catalogParentLink, boDashboardPage.productsLink);
      await boProductsPage.closeSfToolBar(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should click on new product button and go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductPage', baseContext);

      const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.be.eq(true);

      await boProductsPage.selectProductType(page, onSaleProductData.type);

      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      const createProductMessage = await boProductsCreatePage.setProduct(page, onSaleProductData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });
  });

  describe('FO - Check \'On sale\' flag', async () => {
    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO2', baseContext);

      page = await boProductsCreatePage.previewProduct(page);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(onSaleProductData.name);
    });

    it('should check the discount flag', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountTag', baseContext);

      const flagText = await foClassicProductPage.getProductTag(page);
      expect(flagText).to.contains('On sale!');
    });
  });

  // Post-condition: Delete created product
  deleteProductTest(onSaleProductData, `${baseContext}_deleteProduct`);
});
