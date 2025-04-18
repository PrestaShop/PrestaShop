import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabSEOPage,
  boProductSettingsPage,
  type BrowserContext,
  FakerProduct,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  const productData: FakerProduct = new FakerProduct({type: 'standard', status: false});
  const editProductData: FakerProduct = new FakerProduct({
    name: 'testForceFriendlyURL',
    type: 'standard',
    status: false,
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Enable/Disable force update friendly URL', async () => {
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
      expect(isModalVisible).to.be.eq(true);
    });

    it('should choose \'Standard product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await boProductsPage.selectProductType(page, productData.type);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      await boProductsCreatePage.closeSfToolBar(page);

      const createProductMessage = await boProductsCreatePage.setProduct(page, productData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
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

        await boProductsCreatePage.goToSubMenu(
          page,
          boProductsCreatePage.shopParametersParentLink,
          boProductsCreatePage.productSettingsLink,
        );

        const pageTitle = await boProductSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
      });

      it(`should ${test.args.action} force update friendly URL`, async function () {
        await testContext.addContextItem(this,
          'testIdentifier',
          `forceUpdateFriendlyURL${index}`,
          baseContext,
        );

        const result = await boProductSettingsPage.setForceUpdateFriendlyURLStatus(page, test.args.enable);
        expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
      });

      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductsPage${index}`, baseContext);

        await boProductSettingsPage.goToSubMenu(
          page,
          boProductSettingsPage.catalogParentLink,
          boProductSettingsPage.productsLink,
        );

        const pageTitle = await boProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it('should go to the created product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductPage${index}`, baseContext);

        await boProductsPage.resetFilter(page);
        await boProductsPage.goToProductPage(page, 1);

        const pageTitle = await boProductsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
      });

      it('should update the product name and check the friendly URL', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `UpdateProductAndCheckFriendlyURL${index}`, baseContext);

        const validationMessage = await boProductsCreatePage.setProduct(page, test.args.editProduct);
        expect(validationMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);

        const friendlyURL = await boProductsCreateTabSEOPage.getValue(page, 'link_rewrite', '1');
        expect(friendlyURL).to.equal(test.args.friendlyURL.toLowerCase());
      });
    });

    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const testResult = await boProductsCreatePage.deleteProduct(page);
      expect(testResult).to.equal(boProductsPage.successfulDeleteMessage);
    });
  });
});
