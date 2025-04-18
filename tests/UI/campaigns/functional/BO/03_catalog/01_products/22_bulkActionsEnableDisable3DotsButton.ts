import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  type BrowserContext,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_products_bulkActionsEnableDisable3DotsButton';

describe('BO - Catalog - Products list : Bulk actions, Enable/Disable, 3-dot button', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;
  let productName: string = '';

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

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

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAndGetNumber', baseContext);

      numberOfProducts = await boProductsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.gt(0);
    });

    it('should check the Bulk Actions checkbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBulkActionsCheckbox', baseContext);

      const isBulkDeleteButtonEnabled = await boProductsPage.bulkSelectProducts(page);
      expect(isBulkDeleteButtonEnabled).to.eq(true);

      for (let iRow = 1; iRow <= numberOfProducts; iRow++) {
        const isChecked = await boProductsPage.isRowChecked(page, iRow);
        expect(isChecked).to.eq(true);
      }
    });

    it('should uncheck the Bulk Actions checkbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uncheckBulkActionsCheckbox', baseContext);

      const isBulkDeleteButtonEnabled = await boProductsPage.bulkSelectProducts(page);
      expect(isBulkDeleteButtonEnabled).to.eq(false);

      for (let iRow = 1; iRow <= numberOfProducts; iRow++) {
        const isChecked = await boProductsPage.isRowChecked(page, iRow);
        expect(isChecked).to.eq(false);
      }
    });

    describe('Deactivate/Activate the selection', async () => {
      it('should check the Bulk Actions checkbox', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkSelectionForDeactivate', baseContext);

        const isBulkDeleteButtonEnabled = await boProductsPage.bulkSelectProducts(page);
        expect(isBulkDeleteButtonEnabled).to.eq(true);
      });

      it('should deactivate the selection', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'deactivateSelection', baseContext);

        const textModal = await boProductsPage.clickOnBulkActionsProducts(page, 'disable');
        expect(textModal).to.equal(`Deactivating ${numberOfProducts} products`);

        const textMessage = await boProductsPage.bulkActionsProduct(page, 'disable');
        expect(textMessage).to.equal(`Deactivating ${numberOfProducts} / ${numberOfProducts} products`);

        const isModalVisible = await boProductsPage.closeBulkActionsProgressModal(page, 'disable');
        expect(isModalVisible).to.eq(true);

        for (let iRow = 1; iRow <= numberOfProducts; iRow++) {
          const isActive = await boProductsPage.getTextColumn(page, 'active', iRow);
          expect(isActive).to.eq(false);
        }
      });

      it('should check the Bulk Actions checkbox', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkSelectionForActivate', baseContext);

        const isBulkDeleteButtonEnabled = await boProductsPage.bulkSelectProducts(page);
        expect(isBulkDeleteButtonEnabled).to.eq(true);
      });

      it('should activate the selection', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'activateSelection', baseContext);

        const textModal = await boProductsPage.clickOnBulkActionsProducts(page, 'enable');
        expect(textModal).to.equal(`Activating ${numberOfProducts} products`);

        const textMessage = await boProductsPage.bulkActionsProduct(page, 'enable');
        expect(textMessage).to.equal(`Activating ${numberOfProducts} / ${numberOfProducts} products`);

        const isModalVisible = await boProductsPage.closeBulkActionsProgressModal(page, 'enable');
        expect(isModalVisible).to.eq(true);

        for (let iRow = 1; iRow <= numberOfProducts; iRow++) {
          const isActive = await boProductsPage.getTextColumn(page, 'active', iRow);
          expect(isActive).to.eq(true);
        }
      });
    });

    describe('Duplicate the selection', async () => {
      it('should check the Bulk Actions checkbox', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkSelectionForDuplicate', baseContext);

        const isBulkDeleteButtonEnabled = await boProductsPage.bulkSelectProducts(page);
        expect(isBulkDeleteButtonEnabled).to.eq(true);
      });

      it('should duplicate the selection', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'duplicateSelection', baseContext);

        const textModal = await boProductsPage.clickOnBulkActionsProducts(page, 'duplicate');
        expect(textModal).to.equal(`Duplicating ${numberOfProducts} products`);

        const textMessage = await boProductsPage.bulkActionsProduct(page, 'duplicate');
        expect(textMessage).to.equal(`Duplicating ${numberOfProducts} / ${numberOfProducts} products`);

        const isModalVisible = await boProductsPage.closeBulkActionsProgressModal(page, 'duplicate');
        expect(isModalVisible).to.eq(true);
      });

      it('should filter by Status at "No"', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterStatusByNo', baseContext);

        await boProductsPage.filterProducts(page, 'active', 'No', 'select');

        const numberOfProductsAfterReset = await boProductsPage.getNumberOfProductsFromList(page);
        expect(numberOfProductsAfterReset).to.be.equals(numberOfProducts);

        for (let iRow = 1; iRow <= numberOfProducts; iRow++) {
          const isActive = await boProductsPage.getTextColumn(page, 'active', iRow);
          expect(isActive).to.eq(false);

          const productName = await boProductsPage.getTextColumn(page, 'product_name', iRow);
          expect(productName).to.contains('copy of');
        }
      });
    });

    describe('Delete the selection', async () => {
      it('should check the Bulk Actions checkbox', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkSelectionForDelete', baseContext);

        const isBulkDeleteButtonEnabled = await boProductsPage.bulkSelectProducts(page);
        expect(isBulkDeleteButtonEnabled).to.eq(true);
      });

      it('should delete the selection', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'deleteSelection', baseContext);

        const textModal = await boProductsPage.clickOnBulkActionsProducts(page, 'delete');
        expect(textModal).to.equal(`Deleting ${numberOfProducts} products`);

        const textMessage = await boProductsPage.bulkActionsProduct(page, 'delete');
        expect(textMessage).to.equal(`Deleting ${numberOfProducts} / ${numberOfProducts} products`);

        const isModalVisible = await boProductsPage.closeBulkActionsProgressModal(page, 'delete');
        expect(isModalVisible).to.eq(true);
      });

      it('should reset filter', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterStatus', baseContext);

        await boProductsPage.resetFilter(page);

        const numberOfProductsAfterReset = await boProductsPage.getNumberOfProductsFromList(page);
        expect(numberOfProductsAfterReset).to.be.equals(numberOfProducts);
      });
    });

    describe('Contextual Menu : Preview', async () => {
      it('should click on the Preview in Contextual Menu', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickPreviewRow', baseContext);

        productName = await boProductsPage.getTextColumn(page, 'product_name', 1) as string;

        page = await boProductsPage.clickOnPreviewProductButton(page);
        await foClassicProductPage.changeLanguage(page, 'en');

        const result = await foClassicProductPage.getProductInformation(page);
        expect(result.name).to.contains(productName);
      });

      it('should return on the back office', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'returnOnBackOfficeAfterPreview', baseContext);

        // Go back to BO
        page = await foClassicProductPage.closePage(browserContext, page, 0);

        const pageTitle = await boProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });
    });

    describe('Contextual Menu : Duplicate', async () => {
      it('should click on the Duplicate in Contextual Menu', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickDuplicateRow', baseContext);

        const isModalVisible = await boProductsPage.clickOnDuplicateProductButton(page);
        expect(isModalVisible).to.eq(true);

        const textMessage = await boProductsPage.clickOnConfirmDialogButton(page);
        expect(textMessage).to.equal(boProductsCreatePage.successfulDuplicateMessage);
      });

      it('should check that the product is duplicated', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductDuplicated', baseContext);

        const productName = await boProductsCreatePage.getProductName(page, 'en');
        expect(productName).to.contains('copy of');
      });

      it('should return to the list', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'returnToTheList', baseContext);

        await boProductsCreatePage.goToCatalogPage(page);

        const pageTitle = await boProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it('should filter by Status at "No"', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterStatusByNo2', baseContext);

        await boProductsPage.filterProducts(page, 'active', 'No', 'select');

        const numberOfProductsAfterReset = await boProductsPage.getNumberOfProductsFromList(page);
        expect(numberOfProductsAfterReset).to.be.equals(1);

        const isActive = await boProductsPage.getTextColumn(page, 'active', 1);
        expect(isActive).to.eq(false);

        const productName = await boProductsPage.getTextColumn(page, 'product_name', 1);
        expect(productName).to.contains('copy of');
      });

      it('should click on the Preview in Contextual Menu', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickPreviewDuplicatedRow', baseContext);

        productName = await boProductsPage.getTextColumn(page, 'product_name', 1) as string;

        page = await boProductsPage.clickOnPreviewProductButton(page);
        // @todo : https://github.com/PrestaShop/PrestaShop/issues/34191
        // await foClassicProductPage.changeLanguage(page, 'en');

        const result = await foClassicProductPage.getProductInformation(page);
        expect(result.name).to.contains(productName);
      });

      it('should check that the product is displayed on the front-office', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductDuplicatedFrontOffice', baseContext);

        const pageTitle = await foClassicProductPage.getWarningMessage(page);
        expect(pageTitle).to.equals(foClassicProductPage.messageNotVisibleToCustomers);
      });

      it('should return on the back office', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'returnOnBackOfficeAfterDuplicate', baseContext);

        // Go back to BO
        page = await foClassicProductPage.closePage(browserContext, page, 0);

        const pageTitle = await boProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });
    });

    describe('Contextual Menu : Delete', async () => {
      it('should click on the Delete / Cancel in Contextual Menu', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickDeleteCancelRow', baseContext);

        const isModalVisible = await boProductsPage.clickOnDeleteProductButton(page, 1);
        expect(isModalVisible).to.eq(true);

        const isModalVisibleAfterCancel = await boProductsPage.clickOnCancelDialogButton(page);
        expect(isModalVisibleAfterCancel).to.eq(false);

        const numberOfProductsAfterReset = await boProductsPage.getNumberOfProductsFromList(page);
        expect(numberOfProductsAfterReset).to.be.equals(1);
      });

      it('should click on the Delete / Delete in Contextual Menu', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickDeleteDeleteRow', baseContext);

        const isModalVisible = await boProductsPage.clickOnDeleteProductButton(page, 1);
        expect(isModalVisible).to.eq(true);

        const textMessage = await boProductsPage.clickOnConfirmDialogButton(page);
        expect(textMessage).to.equal(boProductsCreatePage.successfulDeleteMessage);
      });

      it('should reset filter', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

        await boProductsPage.resetFilter(page);

        const numberOfProductsAfterReset = await boProductsPage.getNumberOfProductsFromList(page);
        expect(numberOfProductsAfterReset).to.be.equals(numberOfProducts);
      });
    });
  });
});
