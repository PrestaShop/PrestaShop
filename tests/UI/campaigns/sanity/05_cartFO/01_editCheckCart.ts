// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  type BrowserContext,
  dataProducts,
  foClassicCartPage,
  foClassicHomePage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'sanity_cartFO_editCheckCart';

/*
  Open the FO home page
  Add the first product to the cart
  Add the second product to the cart
  Check the cart
  Edit the cart and check it
 */
describe('FO - Cart : Check Cart in FO', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let totalATI: number = 0;
  let itemsNumber: number = 0;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Cart FO: edit check cart', async () => {
    // Steps
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await foClassicHomePage.goTo(page, global.FO.URL);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage1', baseContext);

      await foClassicHomePage.goToProductPage(page, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_1.name);
    });

    it('should add product to cart and check that the number of products was updated in cart header', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart1', baseContext);

      await foClassicProductPage.addProductToTheCart(page);
      // getNumberFromText is used to get the notifications number in the cart
      const notificationsNumber = await foClassicHomePage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(1);
    });

    it('should go to the home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await foClassicHomePage.goToHomePage(page);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to the second product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage2', baseContext);

      await foClassicHomePage.goToProductPage(page, 2);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_3.name);
    });

    it('should add product to cart and check that the number of products was updated in cart header', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

      await foClassicProductPage.addProductToTheCart(page);

      // getNumberFromText is used to get the notifications number in the cart
      const notificationsNumber = await foClassicHomePage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(2);
    });

    it('should check the first product details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductDetail1', baseContext);

      const result = await foClassicCartPage.getProductDetail(page, 1);
      await Promise.all([
        expect(result.name).to.equal(dataProducts.demo_1.name),
        expect(result.price).to.equal(dataProducts.demo_1.finalPrice),
        expect(result.quantity).to.equal(1),
      ]);
    });

    it('should check the second product details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductDetail2', baseContext);

      const result = await foClassicCartPage.getProductDetail(page, 2);
      await Promise.all([
        expect(result.name).to.equal(dataProducts.demo_3.name),
        expect(result.price).to.equal(dataProducts.demo_3.finalPrice),
        expect(result.quantity).to.equal(1),
      ]);
    });

    it('should get the ATI price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalATI', baseContext);

      // getNumberFromText is used to get the price ATI
      totalATI = await foClassicCartPage.getATIPrice(page);
      // @todo : https://github.com/PrestaShop/PrestaShop/issues/9779
      // expect(totalATI.toString()).to.be.equal((dataProducts.demo_3.finalPrice + dataProducts.demo_1.finalPrice)
      // .toFixed(2));
    });

    it('should get the product number and check that is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProductsInCart', baseContext);

      // getNumberFromText is used to get the products number
      itemsNumber = await foClassicCartPage.getProductsNumber(page);
      expect(itemsNumber).to.be.equal(2);
    });

    it('should edit the quantity of the first product ordered', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editProductQuantity1', baseContext);

      await foClassicCartPage.editProductQuantity(page, 1, 3);

      // getNumberFromText is used to get the new price ATI
      const totalPrice = await foClassicCartPage.getATIPrice(page);
      expect(totalPrice).to.be.above(totalATI);

      // getNumberFromText is used to get the new products number
      const productsNumber = await foClassicCartPage.getProductsNumber(page);
      expect(productsNumber).to.be.above(itemsNumber);
    });

    it('should edit the quantity of the second product ordered', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editProductQuantity2', baseContext);

      await foClassicCartPage.editProductQuantity(page, 2, 2);

      // getNumberFromText is used to get the new price ATI
      const totalPrice = await foClassicCartPage.getATIPrice(page);
      expect(totalPrice).to.be.above(totalATI);

      // getNumberFromText is used to get the new products number
      const productsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
      expect(productsNumber).to.be.above(itemsNumber);
    });
  });
});
