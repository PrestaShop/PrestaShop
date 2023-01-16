// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import productSettingsPage from '@pages/BO/shopParameters/productSettings';
import productsPage from '@pages/BO/catalog/products';
import addProductPage from '@pages/BO/catalog/products/add';

// Import data
import ProductFaker from '@data/faker/product';

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

  const productData: ProductFaker = new ProductFaker({type: 'Standard product', status: false});
  const editProductData: ProductFaker = new ProductFaker({name: 'testForceFriendlyURL', type: 'Standard product', status: false});

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

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
    await expect(pageTitle).to.contains(productsPage.pageTitle);
  });

  it('should go to create product page and create a product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

    await productsPage.goToAddProductPage(page);

    const validationMessage = await addProductPage.createEditBasicProduct(page, productData);
    await expect(validationMessage).to.equal(addProductPage.settingUpdatedMessage);
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
  tests.forEach((test, index) => {
    it('should go to \'Shop parameters > Product Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToProductSettingsPageTo${index}`, baseContext);

      await addProductPage.goToSubMenu(
        page,
        addProductPage.shopParametersParentLink,
        addProductPage.productSettingsLink,
      );

      const pageTitle = await productSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });

    it(`should ${test.args.action} force update friendly URL`, async function () {
      await testContext.addContextItem(this,
        'testIdentifier',
        `forceUpdateFriendlyURL${index}`,
        baseContext,
      );

      const result = await productSettingsPage.setForceUpdateFriendlyURLStatus(page, test.args.enable);
      await expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToProductsPage${index}`, baseContext);

      await productSettingsPage.goToSubMenu(
        page,
        productSettingsPage.catalogParentLink,
        productSettingsPage.productsLink,
      );

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should go to the created product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToProductPage${index}`, baseContext);

      await productsPage.resetFilter(page);
      await productsPage.goToProductPage(page, 1);

      const pageTitle = await addProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should update the product name and check the friendly URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `UpdateProductAndCheckFriendlyURL${index}`, baseContext);

      const validationMessage = await addProductPage.createEditBasicProduct(page, test.args.editProduct);
      await expect(validationMessage).to.equal(addProductPage.settingUpdatedMessage);

      const friendlyURL = await addProductPage.getFriendlyURL(page);
      await expect(friendlyURL).to.equal(test.args.friendlyURL.toLowerCase());
    });
  });
  it('should delete product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

    const testResult = await addProductPage.deleteProduct(page);
    await expect(testResult).to.equal(productsPage.productDeletedSuccessfulMessage);
  });
});
