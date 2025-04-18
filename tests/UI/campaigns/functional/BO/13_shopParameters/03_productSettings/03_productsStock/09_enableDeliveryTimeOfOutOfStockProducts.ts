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
  foClassicHomePage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productsStock_enableDeliveryTimeOfOutOfStockProducts';

describe('BO - Shop Parameters - Product Settings : Enable delivery time out-of-stocks products', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const productData: FakerProduct = new FakerProduct({type: 'standard', quantity: 0});

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

  describe('Create a product', async () => {
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

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      const createProductMessage = await boProductsCreatePage.setProduct(page, productData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });
  });

  describe('Enable delivery time out-of-stock', () => {
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
      {args: {action: 'enable', enable: true, deliveryTimeText: '8-9 days'}},
      {args: {action: 'disable', enable: false, deliveryTimeText: ''}},
    ];
    tests.forEach((test, index: number) => {
      describe(`Check delivery time of out-of-stock products ${test.args.enable} status`, async () => {
        it(`should ${test.args.action} delivery time of out-of-stock products in BO`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}StockManagement`, baseContext);

          await boProductSettingsPage.setAllowOrderingOutOfStockStatus(page, test.args.enable);

          const result = await boProductSettingsPage.setDeliveryTimeOutOfStock(
            page,
            test.args.deliveryTimeText,
          );
          expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
        });

        it('should view my shop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

          page = await boProductSettingsPage.viewMyShop(page);

          await foClassicHomePage.changeLanguage(page, 'en');

          const isFoHomePage = await foClassicHomePage.isHomePage(page);
          expect(isFoHomePage, 'Fail to open FO home page').to.eq(true);
        });

        it('should check delivery time block visibility', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `deliveryTimeBlockVisible${index}`, baseContext);

          await foClassicHomePage.searchProduct(page, productData.name);
          await foClassicSearchResultsPage.goToProductPage(page, 1);

          const isDeliveryTimeBlockVisible = await foClassicProductPage.isDeliveryInformationVisible(page);
          expect(isDeliveryTimeBlockVisible).to.equal(test.args.enable);
        });

        if (test.args.enable) {
          it('should check delivery time text', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `deliveryTimeBlockText${index}`, baseContext);

            const deliveryTimeText = await foClassicProductPage.getDeliveryInformationText(page);
            expect(deliveryTimeText).to.equal(test.args.deliveryTimeText);
          });
        }

        it('should go back to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);

          page = await foClassicProductPage.closePage(browserContext, page, 0);

          const pageTitle = await boProductSettingsPage.getPageTitle(page);
          expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
        });
      });
    });
  });

  describe('Delete the product created for test ', async () => {
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
