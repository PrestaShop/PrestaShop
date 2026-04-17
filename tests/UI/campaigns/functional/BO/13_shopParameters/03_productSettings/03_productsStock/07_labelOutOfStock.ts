import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductSettingsPage,
  type BrowserContext,
  FakerProduct,
  foHummingbirdHomePage,
  foHummingbirdProductPage,
  foHummingbirdSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productsStock_labelOutOfStock';

describe('BO - Shop Parameters - product Settings : Set label out-of-stock with allowed/denied backorders', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const productData: FakerProduct = new FakerProduct({
    type: 'standard',
    quantity: 0,
    labelWhenOutOfStock: ' ',
    labelWhenInStock: ' ',
  });

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Set label out-of-stock with allowed/denied backorders', async () => {
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

    it('should click on new product button and go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductPage', baseContext);

      const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.be.equal(true);

      await boProductsPage.selectProductType(page, productData.type);

      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should create product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

      const createProductMessage = await boProductsCreatePage.setProduct(page, productData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should go to \'Shop parameters > Product Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

      await boProductsCreatePage.goToSubMenu(
        page,
        boProductsCreatePage.shopParametersParentLink,
        boProductsCreatePage.productSettingsLink,
      );

      const pageTitle = await boProductSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
    });

    const tests = [
      {
        action: 'enable',
        enable: true,
        backordersAction: 'allowed',
        label: 'You can order',
      },
      {
        action: 'enable',
        enable: true,
        backordersAction: 'allowed',
        label: ' ',
      },
      {
        action: 'disable',
        enable: false,
        backordersAction: 'denied',
        label: ' ',
      },
      {
        action: 'disable',
        enable: false,
        backordersAction: 'denied',
        label: 'Out-of-Stock',
      },
    ];

    tests.forEach((arg, index: number) => {
      it(`should ${arg.action} allow ordering of out-of-stock products`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${arg.action}AllowOrderingOutOfStock${index}`,
          baseContext,
        );

        const result = await boProductSettingsPage.setAllowOrderingOutOfStockStatus(page, arg.enable);
        expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
      });

      it(`should set Label of out-of-stock products with ${arg.backordersAction} backorders`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `setLabelOutOfStock${index}`,
          baseContext,
        );

        let result;

        if (arg.enable) {
          result = await boProductSettingsPage.setLabelOosAllowedBackorders(page, arg.label);
        } else {
          result = await boProductSettingsPage.setLabelOosDeniedBackorders(page, arg.label);
        }

        expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `viewMyShop${arg.action}${index}`,
          baseContext,
        );

        page = await boProductSettingsPage.viewMyShop(page);

        const isHomePage = await foHummingbirdHomePage.isHomePage(page);
        expect(isHomePage, 'Home page was not opened').to.eq(true);
      });

      it('should search for the product and go to product page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `goToProductPage${arg.action}${index}`,
          baseContext,
        );

        // Search and go to product page
        await foHummingbirdHomePage.searchProduct(page, productData.name);
        await foHummingbirdSearchResultsPage.goToProductPage(page, 1);

        const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(productData.name);
      });

      it('should check label out-of-stock', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `checkOrderingOutOfStock${arg.action}${index}`,
          baseContext,
        );

        const hasLabel: boolean = arg.label.trim() !== '';

        // Check quantity and availability label
        const lastQuantityIsVisible = await foHummingbirdProductPage.isAddToCartButtonEnabled(page);
        expect(lastQuantityIsVisible).to.be.equal(arg.enable);

        const hasProductAvailabilityLabel = await foHummingbirdProductPage.hasProductAvailabilityLabel(page);
        expect(hasProductAvailabilityLabel).to.equals(hasLabel);

        if (hasLabel) {
          const availabilityLabel = await foHummingbirdProductPage.getProductAvailabilityLabel(page);
          expect(availabilityLabel).to.contains(arg.label);
        }
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);

        page = await foHummingbirdProductPage.closePage(browserContext, page, 0);

        const pageTitle = await boProductSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
      });
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToDeleteProduct', baseContext);

      await boProductSettingsPage.goToSubMenu(
        page,
        boProductSettingsPage.catalogParentLink,
        boProductSettingsPage.productsLink,
      );

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should click on delete product button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnDeleteProduct', baseContext);

      const isModalVisible = await boProductsPage.clickOnDeleteProductButton(page);
      expect(isModalVisible).to.be.equal(true);
    });

    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const textMessage = await boProductsPage.clickOnConfirmDialogButton(page);
      expect(textMessage).to.equal(boProductsPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilters', baseContext);

      await boProductsPage.resetFilter(page);

      const numberOfProducts = await boProductsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });
  });
});
