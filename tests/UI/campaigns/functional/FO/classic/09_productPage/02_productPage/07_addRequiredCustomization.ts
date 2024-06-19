// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {deleteProductTest} from '@commonTests/BO/catalog/product';

// Import BO pages
import loginCommon from '@commonTests/BO/loginBO';
import productsPage from '@pages/BO/catalog/products';
import createProductPage from '@pages/BO/catalog/products/add';
import detailsTab from '@pages/BO/catalog/products/add/detailsTab';

// Import FO pages
import {productPage as foProductPage} from '@pages/FO/classic/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  FakerProduct,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_productPage_productPage_addRequiredCustomization';

describe('FO - Product page - Product page : Add a required customization', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  // Data to create standard product with 2 customizations
  const newProductData: FakerProduct = new FakerProduct({
    type: 'standard',
    quantity: 100,
    minimumQuantity: 1,
    status: true,
    customizations: [
      {
        label: 'Lorem ipsum',
        type: 'Text',
        required: true,
      },
      {
        label: 'Lorem ipsumm',
        type: 'File',
        required: true,
      }],
  });

  describe('Create product with 2 customizations and check it in FO', async () => {
    // before and after functions
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
      await utilsFile.generateImage('file_1.jpg');
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
      await utilsFile.deleteFile('file_1.jpg');
    });

    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.productsLink,
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

    it('should create 2 customizations', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCustomizations', baseContext);

      await detailsTab.addNewCustomizations(page, newProductData);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      // Click on preview button
      page = await createProductPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the customization section', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductCustomizations', baseContext);

      const productCondition = await foProductPage.isCustomizationBlockVisible(page);
      expect(productCondition).to.eq(true);
    });

    it('should check that add to card button is disabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddToCartButtonDisabled', baseContext);

      const isAddToCartButtonDisabled = await foProductPage.isAddToCartButtonDisplayed(page);
      expect(isAddToCartButtonDisabled).to.equal(true);
    });

    it('should set the text customization and save', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setTextCustomizations', baseContext);

      await foProductPage.setProductCustomizations(page, ['prestashop']);

      const isMessageVisible = await foProductPage.isCustomizationMessageVisible(page, 1);
      expect(isMessageVisible).to.equal(false);
    });

    it('should set the file customizations and save', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setFileCustomization', baseContext);

      await foProductPage.setProductFileCustomizations(page, ['file_1.jpg'], 2);

      const firstCustomImage = await foProductPage.isCustomizationImageVisible(page, 2);
      expect(firstCustomImage).to.equal(false);
    });

    it('should check that add to card button is still disabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddToCartButtonDisabled2', baseContext);

      const isAddToCartButtonDisabled = await foProductPage.isAddToCartButtonDisplayed(page);
      expect(isAddToCartButtonDisabled).to.equal(true);
    });

    it('should add the text and the file customizations and save', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setCustomizations', baseContext);

      await foProductPage.setProductCustomizations(page, ['prestashop'], false);
      await foProductPage.setProductFileCustomizations(page, ['file_1.jpg'], 2);

      const firstCustomMessage = await foProductPage.getCustomizationsMessages(page, 1);
      expect(firstCustomMessage).to.equal('Your customization: prestashop');

      const secondCustomImage = await foProductPage.getCustomizationImage(page, 2);
      expect(secondCustomImage).to.contains('deletePicture');
    });

    it('should check that add to card button is enabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddToCartButtonEnabled', baseContext);

      const isAddToCartButtonEnabled = await foProductPage.isAddToCartButtonEnabled(page);
      expect(isAddToCartButtonEnabled).to.equal(true);
    });
  });

  // Post-condition: Delete specific price
  deleteProductTest(newProductData, `${baseContext}_postTest`);
});
