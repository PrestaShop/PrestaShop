// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/products';
import addProductPage from '@pages/BO/catalog/products/add';
import seoTab from '@pages/BO/catalog/products/add/seoTab';
import productSettingsPage from '@pages/BO/shopParameters/productSettings';

// Import data
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productsGeneral_forceUpdateFriendlyURL';

/*
Enable force update friendly URL
Create then edit product
Check that the friendly URL is updated successfully
Disable force update friendly URL
 */
describe('BO - Shop Parameters - Product Settings : Enable/Disable force update friendly URL', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const productData: ProductData = new ProductData({type: 'standard', status: false});
  const editProductData: ProductData = new ProductData({
    name: 'testForceFriendlyURL',
    type: 'standard',
    status: false,
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Enable/Disable force update friendly URL', async () => {
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
      expect(isModalVisible).to.be.eq(true);
    });

    it('should choose \'Standard product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await productsPage.selectProductType(page, productData.type);

      const pageTitle = await addProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await addProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      await addProductPage.closeSfToolBar(page);

      const createProductMessage = await addProductPage.setProduct(page, productData);
      expect(createProductMessage).to.equal(addProductPage.successfulUpdateMessage);
    });

    const tests = [
      {
        args:
          {
            action: 'enable', enable: true, editProduct: editProductData, friendlyURL: editProductData.name,
          },
      },
      {
        args:
          {
            action: 'disable', enable: false, editProduct: productData, friendlyURL: editProductData.name,
          },
      },
    ];
    tests.forEach((test, index: number) => {
      it('should go to \'Shop parameters > Product Settings\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductSettingsPageTo${index}`, baseContext);

        await addProductPage.goToSubMenu(
          page,
          addProductPage.shopParametersParentLink,
          addProductPage.productSettingsLink,
        );

        const pageTitle = await productSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(productSettingsPage.pageTitle);
      });

      it(`should ${test.args.action} force update friendly URL`, async function () {
        await testContext.addContextItem(this,
          'testIdentifier',
          `forceUpdateFriendlyURL${index}`,
          baseContext,
        );

        const result = await productSettingsPage.setForceUpdateFriendlyURLStatus(page, test.args.enable);
        expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
      });

      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductsPage${index}`, baseContext);

        await productSettingsPage.goToSubMenu(
          page,
          productSettingsPage.catalogParentLink,
          productSettingsPage.productsLink,
        );

        const pageTitle = await productsPage.getPageTitle(page);
        expect(pageTitle).to.contains(productsPage.pageTitle);
      });

      it('should go to the created product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductPage${index}`, baseContext);

        await productsPage.resetFilter(page);
        await productsPage.goToProductPage(page, 1);

        const pageTitle = await addProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(addProductPage.pageTitle);
      });

      it('should update the product name and check the friendly URL', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `UpdateProductAndCheckFriendlyURL${index}`, baseContext);

        const validationMessage = await addProductPage.setProduct(page, test.args.editProduct);
        expect(validationMessage).to.equal(addProductPage.successfulUpdateMessage);

        const friendlyURL = await seoTab.getValue(page, 'link_rewrite', '1');
        expect(friendlyURL).to.equal(test.args.friendlyURL.toLowerCase());
      });
    });

    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const testResult = await addProductPage.deleteProduct(page);
      expect(testResult).to.equal(productsPage.successfulDeleteMessage);
    });
  });
});
